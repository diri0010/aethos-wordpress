<?php

/**
 * Background Processor Class
 *
 * Manages the queue of posts to be scanned and processes them in batches via WP Cron.
 *
 * @since      1.0.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Background_Processor {

    /**
     * The table name for the background processing queue.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $table_name    The table name.
     */
    private $table_name;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'aethos_background_processing';
    }

    /**
     * Add a post to the processing queue.
     *
     * @since    1.0.0
     * @param    int       $post_id    The ID of the post to queue.
     * @param    string    $action     The action to perform ('update' or 'delete').
     * @return   bool                  True on success, false on failure.
     */
    public function push_to_queue( $post_id, $action = 'update' ) {
        global $wpdb;

        // Check if item already exists in pending state
        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT id FROM {$this->table_name} WHERE post_id = %d AND status = 'pending'",
            $post_id
        ));

        if ( $existing ) {
            // Update existing item
            return $wpdb->update(
                $this->table_name,
                array(
                    'action' => $action,
                    'updated_at' => current_time( 'mysql' ),
                    'attempts' => 0 // Reset attempts on new update
                ),
                array( 'id' => $existing->id ),
                array( '%s', '%s', '%d' ),
                array( '%d' )
            );
        } else {
            // Insert new item
            return $wpdb->insert(
                $this->table_name,
                array(
                    'post_id' => $post_id,
                    'action' => $action,
                    'status' => 'pending',
                    'priority' => 10,
                    'created_at' => current_time( 'mysql' ),
                    'updated_at' => current_time( 'mysql' )
                ),
                array( '%d', '%s', '%s', '%d', '%s', '%s' )
            );
        }
    }

    /**
     * Dispatch the background process.
     * Checks if the cron event is scheduled, and if not, schedules it.
     *
     * @since    1.0.0
     */
    public function dispatch() {
        if ( ! wp_next_scheduled( 'aethos_bg_process_batch' ) ) {
            wp_schedule_single_event( time(), 'aethos_bg_process_batch' );
        }
    }

    /**
     * Process a batch of items from the queue.
     *
     * @since    1.0.0
     */
    public function process_batch() {
        global $wpdb;

        // Get batch of pending items
        $batch_size = 5;
        $items = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE status = 'pending' ORDER BY priority ASC, created_at ASC LIMIT %d",
            $batch_size
        ));

        if ( empty( $items ) ) {
            return;
        }

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-scan-orchestrator.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-vector-storage.php';
        
        $orchestrator = new Aethos_Scan_Orchestrator();
        $storage = new Aethos_Vector_Storage();

        foreach ( $items as $item ) {
            // Mark as processing
            $wpdb->update(
                $this->table_name,
                array( 'status' => 'processing' ),
                array( 'id' => $item->id ),
                array( '%s' ),
                array( '%d' )
            );

            try {
                if ( $item->action === 'delete' ) {
                    $storage->delete_post_vectors( $item->post_id );
                    $success = true;
                } else {
                    // Update/Create
                    $result = $orchestrator->scan_single_post( $item->post_id );
                    $success = $result['success'];
                }

                if ( $success ) {
                    $wpdb->update(
                        $this->table_name,
                        array( 'status' => 'completed', 'updated_at' => current_time( 'mysql' ) ),
                        array( 'id' => $item->id ),
                        array( '%s', '%s' ),
                        array( '%d' )
                    );
                } else {
                    throw new Exception( isset($result['error']) ? $result['error'] : 'Unknown error' );
                }

            } catch ( Exception $e ) {
                // Handle failure
                $attempts = $item->attempts + 1;
                $status = ( $attempts >= 3 ) ? 'failed' : 'pending'; // Retry up to 3 times
                
                $wpdb->update(
                    $this->table_name,
                    array( 
                        'status' => $status, 
                        'attempts' => $attempts,
                        'updated_at' => current_time( 'mysql' )
                    ),
                    array( 'id' => $item->id ),
                    array( '%s', '%d', '%s' ),
                    array( '%d' )
                );
                
                aethos_log("Background Processing Error (ID: {$item->id}): " . $e->getMessage());
            }
        }

        // Check if there are more items
        $remaining = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'pending'" );
        if ( $remaining > 0 ) {
            $this->dispatch();
        }
    }

    /**
     * Get the status of the queue.
     *
     * @since    1.0.0
     * @return   array    Queue status statistics.
     */
    public function get_queue_status() {
        global $wpdb;

        $stats = $wpdb->get_results( "SELECT status, COUNT(*) as count FROM {$this->table_name} GROUP BY status" );
        
        $result = array(
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0
        );

        foreach ( $stats as $stat ) {
            if ( isset( $result[$stat->status] ) ) {
                $result[$stat->status] = (int) $stat->count;
            }
        }

        return $result;
    }
}
