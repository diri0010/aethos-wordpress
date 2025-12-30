<?php
/**
 * Vector Search Implementation
 * 
 * Handles similarity search using existing aethos_vectors table.
 * Uses PHP-based cosine similarity calculation with optimizations.
 * 
 * @package Aethos
 */

class Aethos_Vector_Search {
    
    /**
     * Vector dimensions for OpenAI text-embedding-3-small
     */
    const DIMENSIONS = 1536;
    
    /**
     * Default similarity threshold
     */
    const DEFAULT_THRESHOLD = 0.7;
    
    /**
     * Max candidates to load for vector comparison
     */
    const MAX_CANDIDATES = 100;
    
    /**
     * @var string Table name
     */
    private $table_name;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'aethos_vectors';
    }
    
    /**
     * Search for similar vectors
     * 
     * @param array $query_embedding The query embedding vector
     * @param int   $limit           Max results to return
     * @param float $threshold       Minimum similarity score (0-1)
     * @return array Array of results with chunk and score
     */
    public function search($query_embedding, $limit = 5, $threshold = null) {
        $threshold = $threshold ?? self::DEFAULT_THRESHOLD;
        
        if (count($query_embedding) !== self::DIMENSIONS) {
            aethos_log("Invalid query embedding dimension: " . count($query_embedding));
            return [];
        }
        
        return $this->php_vector_search($query_embedding, $limit, $threshold);
    }
    
    /**
     * Get vectors for a specific page URL
     * Used to provide context about the page the user is currently viewing
     * 
     * @param string $url The page URL to fetch vectors for
     * @param int    $limit Max chunks to return
     * @return array Array of chunk texts for the page
     */
    public function get_by_url($url, $limit = 3) {
        global $wpdb;
        
        if (empty($url)) {
            return [];
        }
        
        // Extract just the path from the URL for more flexible matching
        $parsed = parse_url($url);
        $path = isset($parsed['path']) ? rtrim($parsed['path'], '/') : '';
        
        // If path is empty or just root, return empty
        if (empty($path) || $path === '' || $path === '/') {
            return [];
        }
        
        // Also try matching with the full URL
        $full_url = rtrim($url, '/');
        
        // First try exact path match, then partial match
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT chunk_text, post_id, post_url 
             FROM {$this->table_name} 
             WHERE post_url LIKE %s OR post_url LIKE %s
             ORDER BY id ASC
             LIMIT %d",
            '%' . $wpdb->esc_like($path) . '%',
            '%' . $wpdb->esc_like($full_url) . '%',
            $limit
        ), ARRAY_A);
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            aethos_log("get_by_url: Looking for path '$path' from '$url', found " . count($results ?: []) . " results");
        }
        
        return $results ?: [];
    }
    
    
    /**
     * Optimized PHP-based cosine similarity search
     * 
     * Uses pre-computed magnitudes and filters excluded posts.
     * 
     * @param array $query_embedding
     * @param int   $limit
     * @param float $threshold
     * @return array
     */
    private function php_vector_search($query_embedding, $limit, $threshold) {
        global $wpdb;
        
        // Get excluded post IDs from all KB settings
        $excluded_post_ids = $this->get_excluded_post_ids();
        
        // Build exclusion clause if needed
        $exclusion_clause = '';
        if (!empty($excluded_post_ids)) {
            $placeholders = implode(',', array_fill(0, count($excluded_post_ids), '%d'));
            $exclusion_clause = $wpdb->prepare(" AND post_id NOT IN ($placeholders)", $excluded_post_ids);
        }
        
        // Optimized: Fetch vectors with pre-computed magnitude, limit candidates, exclude filtered posts
        $results = $wpdb->get_results(
            "SELECT id, chunk_text, embedding, magnitude, post_id, post_url, post_type 
             FROM {$this->table_name} 
             WHERE magnitude IS NOT NULL $exclusion_clause
             LIMIT " . self::MAX_CANDIDATES, 
            ARRAY_A
        );
        
        if (empty($results)) {
            // Fallback: fetch all without magnitude filter (for backward compatibility)
            $results = $wpdb->get_results(
                "SELECT id, chunk_text, embedding, magnitude, post_id, post_url, post_type 
                 FROM {$this->table_name} 
                 WHERE 1=1 $exclusion_clause
                 LIMIT " . self::MAX_CANDIDATES, 
                ARRAY_A
            );
        }
        
        if (empty($results)) {
            return [];
        }
        
        // Calculate query magnitude once
        $query_magnitude = sqrt(array_sum(array_map(fn($x) => $x * $x, $query_embedding)));
        
        $scored = [];
        foreach ($results as $row) {
            $stored_vector = json_decode($row['embedding'], true);
            
            if (!$stored_vector || !is_array($stored_vector)) continue;
            
            // Use pre-computed magnitude if available, otherwise calculate
            $stored_magnitude = !empty($row['magnitude']) 
                ? (float) $row['magnitude'] 
                : sqrt(array_sum(array_map(fn($x) => $x * $x, $stored_vector)));
            
            if ($stored_magnitude <= 0) continue;
            
            // Dot Product
            $dot_product = 0;
            foreach ($query_embedding as $i => $val) {
                if (isset($stored_vector[$i])) {
                    $dot_product += $val * $stored_vector[$i];
                }
            }
            
            $similarity = ($query_magnitude * $stored_magnitude) > 0
                ? $dot_product / ($query_magnitude * $stored_magnitude)
                : 0;
            
            if ($similarity >= $threshold) {
                $scored[] = [
                    'chunk_id' => (int) $row['id'],
                    'content' => $row['chunk_text'],
                    'score' => round($similarity, 4),
                    'source' => [
                        'post_id' => (int) $row['post_id'],
                        'url' => $row['post_url'],
                        'type' => $row['post_type']
                    ]
                ];
            }
        }
        
        // Sort by score descending
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        
        return array_slice($scored, 0, $limit);
    }
    
    /**
     * Get all excluded post IDs from KB settings
     * 
     * Aggregates exclusions from:
     * - Discovered Content exclusions
     * - KB Pages exclusions
     * - KB Posts exclusions
     * - KB WooCommerce exclusions
     * - KB Custom Post Type exclusions
     * 
     * @return array Array of excluded post IDs
     */
    private function get_excluded_post_ids() {
        $excluded = array();
        
        // Discovered Content exclusions
        $kb_excluded_posts = get_option('aethos_kb_excluded_posts', array());
        if (is_array($kb_excluded_posts)) {
            $excluded = array_merge($excluded, $kb_excluded_posts);
        }
        
        // KB Pages exclusions
        $excluded_pages = get_option('aethos_kb_excluded_pages', array());
        if (is_array($excluded_pages)) {
            $excluded = array_merge($excluded, $excluded_pages);
        }
        
        // KB WooCommerce Products exclusions
        $excluded_woo_products = get_option('aethos_kb_excluded_woo_products', array());
        if (is_array($excluded_woo_products)) {
            $excluded = array_merge($excluded, $excluded_woo_products);
        }
        
        // Dynamic: Check custom post type exclusions
        $cpt_args = array(
            'public' => true,
            '_builtin' => false
        );
        $custom_post_types = get_post_types($cpt_args, 'objects');
        
        foreach ($custom_post_types as $cpt) {
            if ($cpt->name === 'product') continue; // Already handled above
            
            $excluded_cpt = get_option("aethos_kb_excluded_{$cpt->name}", array());
            if (is_array($excluded_cpt)) {
                $excluded = array_merge($excluded, $excluded_cpt);
            }
        }
        
        // Remove duplicates and ensure integers
        $excluded = array_unique(array_map('intval', $excluded));
        
        return array_values($excluded);
    }
}
