<?php

/**
 * Conversation History Management
 *
 * @since      1.4.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Conversation_History {

    /**
     * Get conversations list with filters
     */
    public function get_conversations( $args = array() ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_conversations';

        $defaults = array(
            'search' => '',
            'date_from' => '',
            'date_to' => '',
            'tags' => '',
            'rating' => '',
            'page' => 1,
            'per_page' => 10
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( '1=1' );
        $where_values = array();

        // Search filter
        if ( ! empty( $args['search'] ) ) {
            $where[] = '(user_id LIKE %s OR messages LIKE %s)';
            $search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }

        // Date range filter
        if ( ! empty( $args['date_from'] ) ) {
            $where[] = 'created_at >= %s';
            $where_values[] = $args['date_from'];
        }

        if ( ! empty( $args['date_to'] ) ) {
            $where[] = 'created_at <= %s';
            $where_values[] = $args['date_to'];
        }

        // Rating filter
        if ( ! empty( $args['rating'] ) ) {
            $where[] = 'rating = %d';
            $where_values[] = intval( $args['rating'] );
        }

        $where_clause = implode( ' AND ', $where );

        // Count total
        if ( ! empty( $where_values ) ) {
            $count_query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE {$where_clause}",
                $where_values
            );
        } else {
            $count_query = "SELECT COUNT(*) FROM {$table_name} WHERE {$where_clause}";
        }
        
        $total = $wpdb->get_var( $count_query );

        // Get conversations
        $offset = ( $args['page'] - 1 ) * $args['per_page'];
        
        if ( ! empty( $where_values ) ) {
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE {$where_clause} ORDER BY created_at DESC LIMIT %d OFFSET %d",
                array_merge( $where_values, array( $args['per_page'], $offset ) )
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE {$where_clause} ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $args['per_page'],
                $offset
            );
        }

        $conversations = $wpdb->get_results( $query, ARRAY_A );

        // Process conversations
        foreach ( $conversations as &$conv ) {
            // Add time_ago field
            if ( ! empty( $conv['created_at'] ) ) {
                $conv['time_ago'] = human_time_diff( strtotime( $conv['created_at'] ), current_time( 'timestamp' ) ) . ' ago';
            } else {
                $conv['time_ago'] = 'Unknown';
            }
            
            // Add snippet field (alias for first_message)
            $conv['snippet'] = ! empty( $conv['first_message'] ) ? wp_trim_words( $conv['first_message'], 10 ) : 'No message';
            
            if ( ! empty( $conv['messages'] ) ) {
                $messages = maybe_unserialize( $conv['messages'] );
                $conv['messages_array'] = $messages;
                $conv['first_message'] = isset( $messages[0]['message'] ) ? $messages[0]['message'] : '';
                $conv['message_count'] = count( $messages );
            } else {
                $conv['messages_array'] = array();
                // first_message already exists from database
                $conv['message_count'] = isset($conv['message_count']) ? $conv['message_count'] : 0;
            }
        }

        return array(
            'conversations' => $conversations,
            'total' => $total,
            'total_pages' => ceil( $total / $args['per_page'] ),
            'pages' => ceil( $total / $args['per_page'] ), // Keep for backward compat
            'current_page' => $args['page']
        );
    }

    /**
     * Get single conversation details
     */
    public function get_conversation( $id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_conversations';

        $conversation = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $id ),
            ARRAY_A
        );

        if ( $conversation && ! empty( $conversation['messages'] ) ) {
            $conversation['messages_array'] = maybe_unserialize( $conversation['messages'] );
        }

        return $conversation;
    }

    /**
     * Export conversations to CSV
     */
    public function export_conversations( $args = array() ) {
        $result = $this->get_conversations( array_merge( $args, array( 'per_page' => 1000 ) ) );
        $conversations = $result['conversations'];

        $csv_data = array();
        $csv_data[] = array( 'ID', 'Session ID', 'User ID', 'Started', 'Duration', 'Message Count', 'Upvotes', 'Downvotes', 'Conversation Transcript' );

        foreach ( $conversations as $conv ) {
            // Count upvotes and downvotes from messages
            $upvotes = 0;
            $downvotes = 0;
            $transcript = '';
            
            // Use messages_array already populated by get_conversations
            $messages = isset( $conv['messages_array'] ) ? $conv['messages_array'] : array();
            
            if ( is_array( $messages ) && ! empty( $messages ) ) {
                foreach ( $messages as $msg ) {
                    $role = isset( $msg['role'] ) ? ucfirst( $msg['role'] ) : 'Unknown';
                    $content = isset( $msg['content'] ) ? $msg['content'] : '';
                    
                    // Build transcript
                    if ( ! empty( $transcript ) ) {
                        $transcript .= " | ";
                    }
                    $transcript .= $role . ': ' . $content;
                    
                    // Count feedback
                    if ( isset( $msg['feedback'] ) ) {
                        if ( $msg['feedback'] === 'upvote' ) {
                            $upvotes++;
                        } elseif ( $msg['feedback'] === 'downvote' ) {
                            $downvotes++;
                        }
                    }
                }
            }
            
            $csv_data[] = array(
                $conv['id'],
                isset( $conv['session_id'] ) ? $conv['session_id'] : '',
                $conv['user_id'],
                $conv['created_at'],
                isset( $conv['duration'] ) ? $conv['duration'] . 's' : '0s',
                isset( $conv['message_count'] ) ? $conv['message_count'] : 0,
                $upvotes,
                $downvotes,
                $transcript
            );
        }

        return $csv_data;
    }

    /**
     * Register AJAX handlers
     */
    public function register_ajax_handlers() {
        add_action( 'wp_ajax_aethos_get_conversations', array( $this, 'ajax_get_conversations' ) );
        add_action( 'wp_ajax_aethos_get_conversation_details', array( $this, 'ajax_get_conversation_details' ) );
        add_action( 'wp_ajax_aethos_export_conversations', array( $this, 'ajax_export_conversations' ) );
    }

    /**
     * AJAX: Get conversations list
     */
    public function ajax_get_conversations() {
        check_ajax_referer( 'aethos_get_conversations', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        // Handle days parameter - convert to date_from
        $date_from = '';
        if ( ! empty( $_POST['days'] ) && is_numeric( $_POST['days'] ) ) {
            $days = intval( $_POST['days'] );
            $date_from = date( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );
        }

        $args = array(
            'search' => isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '',
            'date_from' => $date_from,
            'date_to' => isset( $_POST['date_to'] ) ? sanitize_text_field( $_POST['date_to'] ) : '',
            'rating' => isset( $_POST['rating'] ) ? sanitize_text_field( $_POST['rating'] ) : '',
            'page' => isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
            'per_page' => 10
        );

        $result = $this->get_conversations( $args );

        wp_send_json_success( $result );
    }

    /**
     * AJAX: Get conversation details
     */
    public function ajax_get_conversation_details() {
        check_ajax_referer( 'aethos_get_conversation_details', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        // Accept both 'id' and 'conversation_id' for compatibility
        $id = isset( $_POST['conversation_id'] ) ? absint( $_POST['conversation_id'] ) : 0;
        if ( ! $id ) {
            $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
        }

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid conversation ID' ) );
        }

        $conversation = $this->get_conversation( $id );

        if ( ! $conversation ) {
            wp_send_json_error( array( 'message' => 'Conversation not found' ) );
        }
        
        // Format for UI
        $formatted = array(
            'user_id' => $conversation['user_id'],
            'started_at' => date( 'M j, Y g:i A', strtotime( $conversation['created_at'] ) ),
            'duration' => $this->format_duration( isset($conversation['duration']) ? $conversation['duration'] : 0 ),
            'messages' => array()
        );
        
        // Parse messages
        $messages_data = isset($conversation['messages_array']) ? $conversation['messages_array'] : maybe_unserialize($conversation['messages'] ?? '');
        if ( is_array( $messages_data ) ) {
            foreach ( $messages_data as $msg ) {
                $formatted['messages'][] = array(
                    'role' => isset($msg['role']) ? $msg['role'] : 'user',
                    'content' => isset($msg['content']) ? $msg['content'] : (isset($msg['message']) ? $msg['message'] : ''),
                    'timestamp' => isset($msg['timestamp']) ? date( 'g:i A', $msg['timestamp'] / 1000 ) : '',
                    'feedback' => isset($msg['feedback']) ? $msg['feedback'] : null,
                    'sources' => isset($msg['sources']) ? $msg['sources'] : []
                );
            }
        }

        wp_send_json_success( $formatted );
    }
    
    /**
     * Format duration in seconds to human readable
     */
    private function format_duration( $seconds ) {
        if ( $seconds < 60 ) {
            return $seconds . 's';
        } elseif ( $seconds < 3600 ) {
            return floor( $seconds / 60 ) . 'm ' . ( $seconds % 60 ) . 's';
        } else {
            return floor( $seconds / 3600 ) . 'h ' . floor( ( $seconds % 3600 ) / 60 ) . 'm';
        }
    }

    /**
     * AJAX: Export conversations
     */
    public function ajax_export_conversations() {
        check_ajax_referer( 'aethos_export_conversations', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized', 403 );
        }

        $args = array(
            'search' => isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '',
            'date_from' => isset( $_GET['date_from'] ) ? sanitize_text_field( $_GET['date_from'] ) : '',
            'date_to' => isset( $_GET['date_to'] ) ? sanitize_text_field( $_GET['date_to'] ) : '',
            'rating' => isset( $_GET['rating'] ) ? sanitize_text_field( $_GET['rating'] ) : ''
        );

        $csv_data = $this->export_conversations( $args );

        // Output as direct CSV download
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=aethos-conversations-' . date( 'Y-m-d' ) . '.csv' );
        
        $output = fopen( 'php://output', 'w' );
        foreach ( $csv_data as $row ) {
            fputcsv( $output, $row );
        }
        fclose( $output );
        exit;
    }
}

// Initialize and register AJAX handlers
$aethos_conversation_history = new Aethos_Conversation_History();
$aethos_conversation_history->register_ajax_handlers();

