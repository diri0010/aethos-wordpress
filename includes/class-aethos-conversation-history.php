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
            if ( ! empty( $conv['messages'] ) ) {
                $messages = maybe_unserialize( $conv['messages'] );
                $conv['messages_array'] = $messages;
                $conv['first_message'] = isset( $messages[0]['message'] ) ? $messages[0]['message'] : '';
                $conv['message_count'] = count( $messages );
            } else {
                $conv['messages_array'] = array();
                $conv['first_message'] = '';
                $conv['message_count'] = 0;
            }
        }

        return array(
            'conversations' => $conversations,
            'total' => $total,
            'pages' => ceil( $total / $args['per_page'] ),
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
        $csv_data[] = array( 'ID', 'User ID', 'Started', 'Duration', 'Messages', 'Rating', 'First Message' );

        foreach ( $conversations as $conv ) {
            $csv_data[] = array(
                $conv['id'],
                $conv['user_id'],
                $conv['created_at'],
                $conv['duration'] . 's',
                $conv['message_count'],
                $conv['rating'] ?: 'N/A',
                substr( $conv['first_message'], 0, 100 )
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
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $args = array(
            'search' => isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '',
            'date_from' => isset( $_POST['date_from'] ) ? sanitize_text_field( $_POST['date_from'] ) : '',
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
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid conversation ID' ) );
        }

        $conversation = $this->get_conversation( $id );

        if ( ! $conversation ) {
            wp_send_json_error( array( 'message' => 'Conversation not found' ) );
        }

        wp_send_json_success( array( 'conversation' => $conversation ) );
    }

    /**
     * AJAX: Export conversations
     */
    public function ajax_export_conversations() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $args = array(
            'search' => isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '',
            'date_from' => isset( $_POST['date_from'] ) ? sanitize_text_field( $_POST['date_from'] ) : '',
            'date_to' => isset( $_POST['date_to'] ) ? sanitize_text_field( $_POST['date_to'] ) : '',
            'rating' => isset( $_POST['rating'] ) ? sanitize_text_field( $_POST['rating'] ) : ''
        );

        $csv_data = $this->export_conversations( $args );

        // Generate CSV content
        ob_start();
        $output = fopen( 'php://output', 'w' );
        
        foreach ( $csv_data as $row ) {
            fputcsv( $output, $row );
        }
        
        fclose( $output );
        $csv_content = ob_get_clean();

        wp_send_json_success( array(
            'csv' => $csv_content,
            'filename' => 'aethos-conversations-' . date( 'Y-m-d' ) . '.csv'
        ) );
    }
}

// Initialize and register AJAX handlers
$aethos_conversation_history = new Aethos_Conversation_History();
$aethos_conversation_history->register_ajax_handlers();

