<?php

/**
 * Content Scanner Class
 *
 * Scans WordPress content and prepares it for vector embedding
 *
 * @since      1.0.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Content_Scanner {

    /**
     * Get posts to scan based on KB settings
     *
     * @param array $args Additional query arguments
     * @return array Array of post IDs
     */
    public function get_posts_to_scan($args = array()) {
        $defaults = array(
            'post_type' => $this->get_enabled_post_types(),
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => 'modified',
            'order' => 'DESC'
        );

        $args = wp_parse_args($args, $defaults);

        $query = new WP_Query($args);
        $post_ids = $query->posts;

        // Filter by include/exclude rules
        $filtered_ids = array();
        foreach ($post_ids as $post_id) {
            if ($this->should_scan_post($post_id)) {
                $filtered_ids[] = $post_id;
            }
        }

        return $filtered_ids;
    }

    /**
     * Get enabled post types from KB settings
     *
     * @return array Array of enabled post types
     */
    private function get_enabled_post_types() {
        $enabled_types = array();

        // Check pages - use correct option name
        if (get_option('aethos_kb_include_all_pages', true)) {
            $enabled_types[] = 'page';
        }

        // Check posts - use correct option name
        if (get_option('aethos_kb_include_all_posts', true)) {
            $enabled_types[] = 'post';
        }

        // Check WooCommerce products
        if (class_exists('WooCommerce') && get_option('aethos_kb_include_all_woo_products', true)) {
            $enabled_types[] = 'product';
        }

        // Check custom post types - each CPT has its own option
        $cpt_args = array(
            'public' => true,
            '_builtin' => false
        );
        $custom_post_types = get_post_types($cpt_args, 'objects');
        
        foreach ($custom_post_types as $cpt) {
            if ($cpt->name === 'product') continue; // Skip WooCommerce (handled above)
            
            // Check if this specific CPT is enabled
            if (get_option("aethos_kb_include_all_{$cpt->name}", true)) {
                $enabled_types[] = $cpt->name;
            }
        }

        return !empty($enabled_types) ? $enabled_types : array('page');
    }

    /**
     * Extract content from a post
     *
     * @param int $post_id Post ID
     * @return array|false Array with title, content, url, excerpt or false on failure
     */
    public function extract_post_content($post_id) {
        $post = get_post($post_id);

        if (!$post) {
            return false;
        }

        $content = $post->post_content;

        // For custom post types or empty content, append visible custom fields
        if (empty(trim($content)) || !in_array($post->post_type, array('post', 'page', 'product'))) {
            $meta = get_post_meta($post_id);
            $custom_content = array();
            
            foreach ($meta as $key => $values) {
                // Skip hidden keys (start with _)
                if (substr($key, 0, 1) === '_') continue;
                
                // Get the first value
                $value = isset($values[0]) ? $values[0] : '';
                
                // Skip if it looks like serialized data
                if (is_serialized($value)) continue;
                
                // Skip if it's not a string or numeric
                if (!is_string($value) && !is_numeric($value)) continue;
                
                // Skip short values (likely flags, IDs, or short strings)
                if (strlen($value) < 3) continue;
                
                // Add to content with a label if it looks like a description
                $custom_content[] = $value;
            }
            
            if (!empty($custom_content)) {
                $content .= "\n\n" . implode("\n\n", $custom_content);
            }
        }

        return array(
            'title' => $post->post_title,
            'content' => $content,
            'excerpt' => $post->post_excerpt,
            'url' => get_permalink($post_id),
            'post_type' => $post->post_type,
            'modified' => $post->post_modified
        );
    }

    /**
     * Clean HTML and shortcodes from content
     *
     * @param string $html HTML content
     * @return string Cleaned text
     */
    public function clean_content($html) {
        // Remove shortcodes
        $text = strip_shortcodes($html);

        // Remove HTML comments
        $text = preg_replace('/<!--(.|\s)*?-->/', '', $text);

        // Remove script and style tags with their content
        $text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $text);
        $text = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $text);

        // Convert common HTML entities to preserve meaning
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Replace block-level elements with newlines
        $text = preg_replace('/<\/(p|div|h[1-6]|li|blockquote|pre)>/i', "\n\n", $text);
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);

        // Strip remaining HTML tags
        $text = strip_tags($text);

        // Clean up whitespace
        $text = preg_replace('/[ \t]+/', ' ', $text); // Multiple spaces to single
        $text = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $text); // Multiple newlines to double
        $text = trim($text);

        return $text;
    }

    /**
     * Chunk text into optimal sizes for embedding
     *
     * @param string $text      Text to chunk
     * @param int    $max_tokens Maximum tokens per chunk (default 800)
     * @param int    $overlap    Token overlap between chunks (default 50)
     * @return array Array of text chunks
     */
    public function chunk_text($text, $max_tokens = 800, $overlap = 50) {
        if (empty($text)) {
            return array();
        }

        // Split into paragraphs
        $paragraphs = preg_split('/\n\n+/', $text);
        $paragraphs = array_filter($paragraphs); // Remove empty

        $chunks = array();
        $current_chunk = '';
        $current_tokens = 0;

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (empty($paragraph)) {
                continue;
            }

            $para_tokens = $this->estimate_tokens($paragraph);

            // If single paragraph exceeds max, split by sentences
            if ($para_tokens > $max_tokens) {
                // Save current chunk if not empty
                if (!empty($current_chunk)) {
                    $chunks[] = trim($current_chunk);
                    $current_chunk = '';
                    $current_tokens = 0;
                }

                // Split large paragraph by sentences
                $sentences = preg_split('/(?<=[.!?])\s+/', $paragraph);
                foreach ($sentences as $sentence) {
                    $sent_tokens = $this->estimate_tokens($sentence);

                    if ($current_tokens + $sent_tokens > $max_tokens) {
                        if (!empty($current_chunk)) {
                            $chunks[] = trim($current_chunk);
                            // Add overlap from previous chunk
                            $current_chunk = $this->get_overlap_text($current_chunk, $overlap);
                            $current_tokens = $this->estimate_tokens($current_chunk);
                        }
                    }

                    $current_chunk .= ' ' . $sentence;
                    $current_tokens += $sent_tokens;
                }
            } else {
                // Check if adding this paragraph exceeds limit
                if ($current_tokens + $para_tokens > $max_tokens) {
                    if (!empty($current_chunk)) {
                        $chunks[] = trim($current_chunk);
                        // Add overlap from previous chunk
                        $current_chunk = $this->get_overlap_text($current_chunk, $overlap);
                        $current_tokens = $this->estimate_tokens($current_chunk);
                    }
                }

                $current_chunk .= "\n\n" . $paragraph;
                $current_tokens += $para_tokens;
            }
        }

        // Add final chunk
        if (!empty($current_chunk)) {
            $chunks[] = trim($current_chunk);
        }

        return $chunks;
    }

    /**
     * Get overlap text from end of chunk
     *
     * @param string $text   Text to get overlap from
     * @param int    $tokens Number of tokens for overlap
     * @return string Overlap text
     */
    private function get_overlap_text($text, $tokens) {
        $words = explode(' ', $text);
        $overlap_words = (int) ($tokens * 0.75); // Approximate words from tokens

        if (count($words) <= $overlap_words) {
            return $text;
        }

        $overlap = array_slice($words, -$overlap_words);
        return implode(' ', $overlap);
    }

    /**
     * Estimate token count for text
     * Rough approximation: 1 token ≈ 4 characters or 0.75 words
     *
     * @param string $text Text to estimate
     * @return int Estimated token count
     */
    private function estimate_tokens($text) {
        $char_count = strlen($text);
        $word_count = str_word_count($text);

        // Use average of both methods
        $tokens_by_chars = (int) ceil($char_count / 4);
        $tokens_by_words = (int) ceil($word_count / 0.75);

        return (int) (($tokens_by_chars + $tokens_by_words) / 2);
    }

    /**
     * Get all chunks for a post with metadata
     *
     * @param int $post_id Post ID
     * @return array Array of chunks with metadata
     */
    public function get_post_chunks($post_id) {
        $content_data = $this->extract_post_content($post_id);

        if (!$content_data) {
            return array();
        }

        // Combine title and content
        $full_text = $content_data['title'] . "\n\n" . $content_data['content'];

        // Clean the content
        $clean_text = $this->clean_content($full_text);

        // Chunk the text
        $chunks = $this->chunk_text($clean_text);

        // Add metadata to each chunk
        $chunks_with_metadata = array();
        foreach ($chunks as $index => $chunk_text) {
            $chunks_with_metadata[] = array(
                'text' => $chunk_text,
                'index' => $index,
                'metadata' => array(
                    'post_id' => $post_id,
                    'post_type' => $content_data['post_type'],
                    'title' => $content_data['title'],
                    'url' => $content_data['url'],
                    'chunk_index' => $index,
                    'total_chunks' => count($chunks)
                )
            );
        }

        return $chunks_with_metadata;
    }

    /**
     * Check if post should be scanned based on include/exclude rules
     *
     * @param int $post_id Post ID
     * @return bool True if should scan, false otherwise
     */
    public function should_scan_post($post_id) {
        $post = get_post($post_id);

        if (!$post) {
            return false;
        }

        // Check post status
        if ($post->post_status !== 'publish') {
            return false;
        }

        // Check if post type is enabled
        $enabled_types = $this->get_enabled_post_types();
        if (!in_array($post->post_type, $enabled_types)) {
            return false;
        }

        // Check password protection
        if (post_password_required($post_id)) {
            return false;
        }

        // Check excluded posts list (from Discovered Content tab) - using KB option now
        $excluded_posts = get_option('aethos_kb_excluded_posts', array());
        if (!is_array($excluded_posts)) $excluded_posts = array();
        if (in_array($post_id, $excluded_posts)) {
            return false;
        }

        // Check Knowledge Base inclusion/exclusion rules
        if ($post->post_type === 'page') {
            // Check if pages are included
            $include_all_pages = get_option('aethos_kb_include_all_pages', true);
            
            if (!$include_all_pages) {
                // Check if this page is in included list
                $included_pages = get_option('aethos_kb_included_pages', array());
                if (!is_array($included_pages)) $included_pages = array();
                if (!in_array($post_id, $included_pages)) {
                    return false;
                }
            }
            
            // Check if this page is in excluded list
            $excluded_pages = get_option('aethos_kb_excluded_pages', array());
            if (!is_array($excluded_pages)) $excluded_pages = array();
            if (in_array($post_id, $excluded_pages)) {
                return false;
            }
        }

        if ($post->post_type === 'post') {
            // Check categories
            $include_all_categories = get_option('aethos_kb_include_all_categories', true);
            // Check if post has any excluded categories
            $excluded_categories = get_option('aethos_kb_excluded_categories', array());
            if (!is_array($excluded_categories)) $excluded_categories = array();
            $post_categories = wp_get_post_categories($post_id);
            
            if (!empty(array_intersect($post_categories, $excluded_categories))) {
                return false;
            }
        }

        if ($post->post_type === 'product') {
            // Check WooCommerce products
            $include_all_products = get_option('aethos_kb_include_all_woo_products', true);
            
            if (!$include_all_products) {
                $included_products = get_option('aethos_kb_included_woo_products', array());
                if (!is_array($included_products)) $included_products = array();
                if (!in_array($post_id, $included_products)) {
                    return false;
                }
            }
            
            $excluded_products = get_option('aethos_kb_excluded_woo_products', array());
            if (!is_array($excluded_products)) $excluded_products = array();
            if (in_array($post_id, $excluded_products)) {
                return false;
            }
            
            // Check WooCommerce categories
            $include_all_woo_categories = get_option('aethos_kb_include_all_woo_categories', true);
            
            if (!$include_all_woo_categories) {
                $included_woo_categories = get_option('aethos_kb_included_woo_categories', array());
                if (!is_array($included_woo_categories)) $included_woo_categories = array();
                $product_categories = wp_get_post_terms($post_id, 'product_cat', array('fields' => 'ids'));
                
                if (empty(array_intersect($product_categories, $included_woo_categories))) {
                    return false;
                }
            }
            
            $excluded_woo_categories = get_option('aethos_kb_excluded_woo_categories', array());
            if (!is_array($excluded_woo_categories)) $excluded_woo_categories = array();
            $product_categories = wp_get_post_terms($post_id, 'product_cat', array('fields' => 'ids'));
            
            if (!empty(array_intersect($product_categories, $excluded_woo_categories))) {
                return false;
            }
        }

        // Check custom post types
        if (!in_array($post->post_type, array('post', 'page', 'product'))) {
            // This is a custom post type
            $include_all = get_option("aethos_kb_include_all_{$post->post_type}", true);
            
            if (!$include_all) {
                // Check if this post is in included list
                $included_items = get_option("aethos_kb_included_{$post->post_type}", array());
                if (!is_array($included_items)) $included_items = array();
                if (!in_array($post_id, $included_items)) {
                    return false;
                }
            }
            
            // Check if this post is in excluded list
            $excluded_items = get_option("aethos_kb_excluded_{$post->post_type}", array());
            if (!is_array($excluded_items)) $excluded_items = array();
            if (in_array($post_id, $excluded_items)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get scan statistics
     *
     * @return array Statistics array
     */
    public function get_scan_stats() {
        $post_ids = $this->get_posts_to_scan();

        $stats = array(
            'total_posts' => count($post_ids),
            'by_post_type' => array(),
            'estimated_chunks' => 0
        );

        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);
            if (!$post) {
                continue;
            }

            // Count by post type
            if (!isset($stats['by_post_type'][$post->post_type])) {
                $stats['by_post_type'][$post->post_type] = 0;
            }
            $stats['by_post_type'][$post->post_type]++;

            // Estimate chunks
            $chunks = $this->get_post_chunks($post_id);
            $stats['estimated_chunks'] += count($chunks);
        }

        return $stats;
    }
}
