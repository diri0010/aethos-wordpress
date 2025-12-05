<?php

/**
 * Vector Storage Management Class
 *
 * Handles CRUD operations for vector embeddings
 *
 * @since      1.0.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Vector_Storage {

    /**
     * Get the vectors table name
     */
    private function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'aethos_vectors';
    }

    /**
     * Get the sync log table name
     */
    private function get_sync_log_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'aethos_sync_log';
    }

    /**
     * Store a vector for a content chunk
     *
     * @param int    $post_id       Post ID
     * @param int    $chunk_index   Chunk index (0-based)
     * @param string $chunk_text    Text content of the chunk
     * @param string $embedding     JSON-encoded embedding vector
     * @param array  $metadata      Additional metadata
     * @return int|false Insert ID on success, false on failure
     */
    public function store_vector($post_id, $chunk_index, $chunk_text, $embedding, $metadata = array()) {
        global $wpdb;
        $table_name = $this->get_table_name();

        // Get post type
        $post = get_post($post_id);
        if (!$post) {
            return false;
        }

        // Calculate hash for change detection
        $hash = md5($chunk_text);

        // Estimate token count (rough approximation: 1 token ≈ 4 characters)
        $token_count = (int) ceil(strlen($chunk_text) / 4);

        // Get post URL (works for posts, pages, custom post types, WooCommerce products)
        $post_url = get_permalink($post_id);

        // Prepare data
        $data = array(
            'post_id' => $post_id,
            'post_type' => $post->post_type,
            'post_url' => $post_url,
            'chunk_index' => $chunk_index,
            'chunk_text' => $chunk_text,
            'embedding' => is_array($embedding) ? json_encode($embedding) : $embedding,
            'token_count' => $token_count,
            'metadata' => json_encode($metadata),
            'hash' => $hash
        );

        // Insert or update
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE post_id = %d AND chunk_index = %d",
            $post_id,
            $chunk_index
        ));

        if ($existing) {
            // Update existing
            $result = $wpdb->update(
                $table_name,
                $data,
                array('id' => $existing->id),
                array('%d', '%s', '%s', '%d', '%s', '%s', '%d', '%s', '%s'),
                array('%d')
            );
            return $result !== false ? $existing->id : false;
        } else {
            // Insert new
            $result = $wpdb->insert(
                $table_name,
                $data,
                array('%d', '%s', '%s', '%d', '%s', '%s', '%d', '%s', '%s')
            );
            return $result !== false ? $wpdb->insert_id : false;
        }
    }

    /**
     * Get all vectors for a post
     *
     * @param int $post_id Post ID
     * @return array Array of vector objects
     */
    public function get_post_vectors($post_id) {
        global $wpdb;
        $table_name = $this->get_table_name();

        $vectors = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE post_id = %d ORDER BY chunk_index ASC",
            $post_id
        ));

        return $vectors ? $vectors : array();
    }

    /**
     * Delete all vectors for a post
     *
     * @param int $post_id Post ID
     * @return int|false Number of rows deleted, false on failure
     */
    public function delete_post_vectors($post_id) {
        global $wpdb;
        $table_name = $this->get_table_name();

        return $wpdb->delete(
            $table_name,
            array('post_id' => $post_id),
            array('%d')
        );
    }

    /**
     * Get all vectors (paginated)
     *
     * @param array $args Query arguments
     * @return array Array of vector objects
     */
    public function get_all_vectors($args = array()) {
        global $wpdb;
        $table_name = $this->get_table_name();

        $defaults = array(
            'post_type' => '',
            'limit' => 100,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );

        $args = wp_parse_args($args, $defaults);

        $where = '';
        if (!empty($args['post_type'])) {
            $where = $wpdb->prepare(" WHERE post_type = %s", $args['post_type']);
        }

        $query = $wpdb->prepare(
            "SELECT * FROM $table_name $where ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d",
            $args['limit'],
            $args['offset']
        );

        $vectors = $wpdb->get_results($query);

        return $vectors ? $vectors : array();
    }

    /**
     * Check if content has changed (hash comparison)
     *
     * @param int    $post_id     Post ID
     * @param int    $chunk_index Chunk index
     * @param string $chunk_text  New chunk text
     * @return bool True if content changed, false if same
     */
    public function has_content_changed($post_id, $chunk_index, $chunk_text) {
        global $wpdb;
        $table_name = $this->get_table_name();

        $new_hash = md5($chunk_text);

        $existing_hash = $wpdb->get_var($wpdb->prepare(
            "SELECT hash FROM $table_name WHERE post_id = %d AND chunk_index = %d",
            $post_id,
            $chunk_index
        ));

        if ($existing_hash === null) {
            return true; // New chunk
        }

        return $existing_hash !== $new_hash;
    }

    /**
     * Scan and store vectors for a post (complete pipeline)
     *
     * @param int $post_id Post ID to scan
     * @return array Result with success status and statistics
     */
    public function scan_and_store_post($post_id) {
        $result = array(
            'success' => false,
            'post_id' => $post_id,
            'chunks_created' => 0,
            'chunks_updated' => 0,
            'chunks_skipped' => 0,
            'vectors_stored' => 0,
            'error' => null
        );

        try {
            // Initialize required classes
            $scanner = new Aethos_Content_Scanner();
            $embeddings = new Aethos_Embeddings();

            // Check if post should be scanned
            if (!$scanner->should_scan_post($post_id)) {
                $result['error'] = 'Post does not meet scanning criteria';
                return $result;
            }

            // Get post chunks with metadata
            $chunks = $scanner->get_post_chunks($post_id);

            if (empty($chunks)) {
                $result['error'] = 'No content chunks generated';
                return $result;
            }

            // Delete old chunks that no longer exist
            $existing_vectors = $this->get_post_vectors($post_id);
            if (count($existing_vectors) > count($chunks)) {
                // Remove extra chunks
                for ($i = count($chunks); $i < count($existing_vectors); $i++) {
                    global $wpdb;
                    $table_name = $this->get_table_name();
                    $wpdb->delete(
                        $table_name,
                        array('post_id' => $post_id, 'chunk_index' => $i),
                        array('%d', '%d')
                    );
                }
            }

            // Process each chunk
            $texts_to_embed = array();
            $chunks_to_process = array();

            error_log("Aethos: Post $post_id has " . count($chunks) . " chunks");

            foreach ($chunks as $chunk_data) {
                $chunk_index = $chunk_data['index'];
                $chunk_text = $chunk_data['text'];

                // Check if content has changed
                if ($this->has_content_changed($post_id, $chunk_index, $chunk_text)) {
                    $texts_to_embed[] = $chunk_text;
                    $chunks_to_process[] = $chunk_data;
                    error_log("Aethos: Post $post_id chunk $chunk_index needs processing");
                } else {
                    $result['chunks_skipped']++;
                    error_log("Aethos: Post $post_id chunk $chunk_index skipped (unchanged)");
                }
            }

            error_log("Aethos: Post $post_id - " . count($texts_to_embed) . " chunks to embed, " . $result['chunks_skipped'] . " skipped");

            // Generate embeddings for changed chunks (batch)
            if (!empty($texts_to_embed)) {
                $generated_embeddings = $embeddings->batch_generate_embeddings($texts_to_embed);

                if ($generated_embeddings === false) {
                    $result['error'] = 'Failed to generate embeddings';
                    return $result;
                }

                // Store each embedding
                foreach ($chunks_to_process as $index => $chunk_data) {
                    $embedding = $generated_embeddings[$index];

                    // Validate embedding
                    if (!$embeddings->validate_embedding($embedding)) {
                        error_log("Aethos: Invalid embedding for post $post_id, chunk {$chunk_data['index']}");
                        continue;
                    }

                    // Check if this is new or update
                    $is_new = true;
                    foreach ($existing_vectors as $existing_vector) {
                        if ($existing_vector->chunk_index == $chunk_data['index']) {
                            $is_new = false;
                            break;
                        }
                    }

                    // Store vector
                    $stored = $this->store_vector(
                        $post_id,
                        $chunk_data['index'],
                        $chunk_data['text'],
                        $embedding,
                        $chunk_data['metadata']
                    );

                    if ($stored) {
                        $result['vectors_stored']++;
                        if ($is_new) {
                            $result['chunks_created']++;
                        } else {
                            $result['chunks_updated']++;
                        }
                    }
                }
            } else {
                error_log("Aethos: Post $post_id - no chunks to process (all skipped)");
            }

            $result['success'] = true;

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            error_log('Aethos scan_and_store_post error: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Get vector statistics
     *
     * @return array Statistics array
     */
    public function get_stats() {
        global $wpdb;
        $table_name = $this->get_table_name();

        $stats = array(
            'total_vectors' => 0,
            'total_posts' => 0,
            'total_tokens' => 0,
            'by_post_type' => array()
        );

        // Total vectors
        $stats['total_vectors'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        // Total unique posts
        $stats['total_posts'] = (int) $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM $table_name");

        // Total tokens
        $stats['total_tokens'] = (int) $wpdb->get_var("SELECT SUM(token_count) FROM $table_name");

        // By post type
        $by_type = $wpdb->get_results(
            "SELECT post_type, COUNT(*) as count, SUM(token_count) as tokens 
             FROM $table_name 
             GROUP BY post_type"
        );

        if ($by_type) {
            foreach ($by_type as $row) {
                $stats['by_post_type'][$row->post_type] = array(
                    'count' => (int) $row->count,
                    'tokens' => (int) $row->tokens
                );
            }
        }

        return $stats;
    }

    /**
     * Log a sync operation
     *
     * @param string $sync_type Type of sync (full, incremental, single)
     * @param string $status    Status (pending, processing, completed, failed)
     * @param array  $data      Additional data
     * @return int|false Log ID on success, false on failure
     */
    public function log_sync($sync_type, $status, $data = array()) {
        global $wpdb;
        $table_name = $this->get_sync_log_table_name();

        $log_data = array(
            'sync_type' => $sync_type,
            'status' => $status
        );

        if (isset($data['posts_scanned'])) {
            $log_data['posts_scanned'] = $data['posts_scanned'];
        }
        if (isset($data['vectors_created'])) {
            $log_data['vectors_created'] = $data['vectors_created'];
        }
        if (isset($data['vectors_updated'])) {
            $log_data['vectors_updated'] = $data['vectors_updated'];
        }
        if (isset($data['vectors_deleted'])) {
            $log_data['vectors_deleted'] = $data['vectors_deleted'];
        }
        if (isset($data['error_message'])) {
            $log_data['error_message'] = $data['error_message'];
        }
        if (isset($data['completed_at'])) {
            $log_data['completed_at'] = $data['completed_at'];
        }

        $result = $wpdb->insert($table_name, $log_data);

        return $result !== false ? $wpdb->insert_id : false;
    }

    /**
     * Update sync log status
     *
     * @param int    $log_id Log ID
     * @param string $status New status
     * @param array  $data   Additional data to update
     * @return bool True on success, false on failure
     */
    public function update_sync_log($log_id, $status, $data = array()) {
        global $wpdb;
        $table_name = $this->get_sync_log_table_name();

        $update_data = array('status' => $status);

        if ($status === 'completed' || $status === 'failed') {
            $update_data['completed_at'] = current_time('mysql');
        }

        $update_data = array_merge($update_data, $data);

        return $wpdb->update(
            $table_name,
            $update_data,
            array('id' => $log_id)
        ) !== false;
    }
}
