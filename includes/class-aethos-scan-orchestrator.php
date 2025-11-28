<?php
/**
 * Scan Orchestrator Class
 * Coordinates full-site scans and single-post scans
 */

class Aethos_Scan_Orchestrator {

    private $storage;
    private $scanner;

    public function __construct() {
        $this->storage = new Aethos_Vector_Storage();
        $this->scanner = new Aethos_Content_Scanner();
    }

    /**
     * Start a full site scan (batch-aware)
     *
     * @param int $offset Starting offset for batch
     * @param int $limit Number of posts to process in this batch
     * @return array Scan results
     */
    public function start_full_scan($offset = 0, $limit = 5) {
        $log_id = $this->storage->log_sync('full', 'processing');
        
        try {
            // Get total count first
            $all_posts = $this->scanner->get_posts_to_scan(array('posts_per_page' => -1));
            $total_posts = count($all_posts);
            
            // Get batch of posts
            $posts = array_slice($all_posts, $offset, $limit);
            $batch_size = count($posts);
            
            error_log("Aethos: Batch scan - offset: $offset, limit: $limit, total: $total_posts, processing: $batch_size");
            
            $vectors_created = 0;
            $vectors_updated = 0;
            $errors = array();

            foreach ($posts as $post_id) {
                try {
                    error_log("Aethos: Scanning post $post_id");
                    $result = $this->storage->scan_and_store_post($post_id);
                    
                    if ($result['success']) {
                        $vectors_created += $result['chunks_created'];
                        $vectors_updated += $result['chunks_updated'];
                    } else {
                        $errors[] = "Post $post_id: " . $result['error'];
                    }
                } catch (Exception $e) {
                    $errors[] = "Post $post_id: " . $e->getMessage();
                }
            }

            // Update log
            $this->storage->update_sync_log($log_id, 'completed', array(
                'posts_scanned' => $batch_size,
                'vectors_created' => $vectors_created,
                'vectors_updated' => $vectors_updated,
                'error_message' => !empty($errors) ? implode('; ', $errors) : null
            ));

            // Update last scan timestamp only when complete
            if ($offset + $batch_size >= $total_posts) {
                update_option('aethos_last_full_scan', time());
            }

            return array(
                'success' => true,
                'total_posts' => $total_posts,
                'processed_count' => $batch_size,
                'offset' => $offset,
                'vectors_created' => $vectors_created,
                'vectors_updated' => $vectors_updated,
                'items_found' => $vectors_created + $vectors_updated,
                'errors' => $errors,
                'has_more' => ($offset + $batch_size) < $total_posts
            );

        } catch (Exception $e) {
            $this->storage->update_sync_log($log_id, 'failed', array(
                'error_message' => $e->getMessage()
            ));

            return array(
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }

    /**
     * Scan a single post
     *
     * @param int $post_id Post ID
     * @return array Scan result
     */
    public function scan_single_post($post_id) {
        $log_id = $this->storage->log_sync('single', 'processing');

        try {
            $result = $this->storage->scan_and_store_post($post_id);

            if ($result['success']) {
                $this->storage->update_sync_log($log_id, 'completed', array(
                    'posts_scanned' => 1,
                    'vectors_created' => $result['chunks_created'],
                    'vectors_updated' => $result['chunks_updated']
                ));

                return array(
                    'success' => true,
                    'vectors_created' => $result['chunks_created'],
                    'vectors_updated' => $result['chunks_updated']
                );
            } else {
                $this->storage->update_sync_log($log_id, 'failed', array(
                    'error_message' => $result['error']
                ));

                return array(
                    'success' => false,
                    'error' => $result['error']
                );
            }

        } catch (Exception $e) {
            $this->storage->update_sync_log($log_id, 'failed', array(
                'error_message' => $e->getMessage()
            ));

            return array(
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }

    /**
     * Get scan progress (for future async implementation)
     *
     * @return array Progress data
     */
    public function get_scan_progress() {
        global $wpdb;
        $sync_log_table = $wpdb->prefix . 'aethos_sync_log';

        $latest = $wpdb->get_row(
            "SELECT * FROM $sync_log_table WHERE status = 'processing' ORDER BY started_at DESC LIMIT 1"
        );

        if ($latest) {
            return array(
                'in_progress' => true,
                'posts_scanned' => $latest->posts_scanned,
                'vectors_created' => $latest->vectors_created,
                'vectors_updated' => $latest->vectors_updated
            );
        }

        return array('in_progress' => false);
    }
}
