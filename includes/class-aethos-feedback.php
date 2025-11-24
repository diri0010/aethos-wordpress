<?php

/**
 * User Feedback & Ratings Management
 *
 * @since      1.6.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Feedback {

    /**
     * Get feedback statistics
     */
    public function get_statistics( $days = 30 ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_conversations';

        $date_filter = '';
        if ( $days !== 'all' ) {
            $date_filter = $wpdb->prepare( 
                'AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)', 
                intval( $days ) 
            );
        }

        // Get average rating
        $avg_rating = $wpdb->get_var( 
            "SELECT AVG(rating) FROM {$table_name} WHERE rating IS NOT NULL {$date_filter}"
        );

        // Get total feedback count
        $total_feedback = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} WHERE rating IS NOT NULL {$date_filter}"
        );

        // Get rating distribution
        $distribution = $wpdb->get_results(
            "SELECT rating, COUNT(*) as count 
            FROM {$table_name} 
            WHERE rating IS NOT NULL {$date_filter}
            GROUP BY rating 
            ORDER BY rating DESC",
            ARRAY_A
        );

        // Calculate percentages
        $rating_counts = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0 );
        foreach ( $distribution as $row ) {
            $rating_counts[ $row['rating'] ] = intval( $row['count'] );
        }

        $rating_percentages = array();
        foreach ( $rating_counts as $rating => $count ) {
            $rating_percentages[ $rating ] = $total_feedback > 0 
                ? round( ( $count / $total_feedback ) * 100, 1 ) 
                : 0;
        }

        // Calculate positive sentiment (4 and 5 stars)
        $positive_count = $rating_counts[4] + $rating_counts[5];
        $positive_sentiment = $total_feedback > 0 
            ? round( ( $positive_count / $total_feedback ) * 100 ) 
            : 0;

        // Get previous period stats for comparison
        $prev_date_filter = '';
        if ( $days !== 'all' ) {
            $prev_date_filter = $wpdb->prepare(
                'AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY) AND created_at < DATE_SUB(NOW(), INTERVAL %d DAY)',
                intval( $days ) * 2,
                intval( $days )
            );
        }

        $prev_avg_rating = $wpdb->get_var(
            "SELECT AVG(rating) FROM {$table_name} WHERE rating IS NOT NULL {$prev_date_filter}"
        );

        $prev_total_feedback = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} WHERE rating IS NOT NULL {$prev_date_filter}"
        );

        // Calculate changes
        $rating_change = $prev_avg_rating > 0 
            ? round( ( ( $avg_rating - $prev_avg_rating ) / $prev_avg_rating ) * 100, 1 ) 
            : 0;

        $feedback_change = $prev_total_feedback > 0 
            ? round( ( ( $total_feedback - $prev_total_feedback ) / $prev_total_feedback ) * 100 ) 
            : 0;

        return array(
            'avg_rating' => round( floatval( $avg_rating ), 1 ),
            'total_feedback' => intval( $total_feedback ),
            'positive_sentiment' => $positive_sentiment,
            'rating_distribution' => $rating_counts,
            'rating_percentages' => $rating_percentages,
            'rating_change' => $rating_change,
            'feedback_change' => $feedback_change,
            'review_count' => number_format( $total_feedback )
        );
    }

    /**
     * Get feedback list with filters
     */
    public function get_feedback_list( $args = array() ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_conversations';

        $defaults = array(
            'search' => '',
            'rating' => '',
            'days' => 30,
            'sort' => 'newest',
            'page' => 1,
            'per_page' => 10
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( 'rating IS NOT NULL' );
        $where_values = array();

        // Date filter
        if ( $args['days'] !== 'all' ) {
            $where[] = 'created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)';
            $where_values[] = intval( $args['days'] );
        }

        // Rating filter
        if ( ! empty( $args['rating'] ) ) {
            $where[] = 'rating = %d';
            $where_values[] = intval( $args['rating'] );
        }

        // Search filter
        if ( ! empty( $args['search'] ) ) {
            $where[] = '(user_id LIKE %s OR messages LIKE %s OR feedback_text LIKE %s)';
            $search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }

        $where_clause = implode( ' AND ', $where );

        // Sort order
        $order_by = 'created_at DESC';
        switch ( $args['sort'] ) {
            case 'oldest':
                $order_by = 'created_at ASC';
                break;
            case 'highest':
                $order_by = 'rating DESC, created_at DESC';
                break;
            case 'lowest':
                $order_by = 'rating ASC, created_at DESC';
                break;
        }

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

        // Get feedback
        $offset = ( $args['page'] - 1 ) * $args['per_page'];

        if ( ! empty( $where_values ) ) {
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE {$where_clause} ORDER BY {$order_by} LIMIT %d OFFSET %d",
                array_merge( $where_values, array( $args['per_page'], $offset ) )
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE {$where_clause} ORDER BY {$order_by} LIMIT %d OFFSET %d",
                $args['per_page'],
                $offset
            );
        }

        $feedback_list = $wpdb->get_results( $query, ARRAY_A );

        // Process feedback
        foreach ( $feedback_list as &$feedback ) {
            if ( ! empty( $feedback['messages'] ) ) {
                $messages = maybe_unserialize( $feedback['messages'] );
                $feedback['first_message'] = isset( $messages[0]['message'] ) 
                    ? $messages[0]['message'] 
                    : '';
            } else {
                $feedback['first_message'] = '';
            }

            // Generate feedback text if not exists
            if ( empty( $feedback['feedback_text'] ) ) {
                $feedback['feedback_text'] = $feedback['first_message'];
            }
        }

        return array(
            'feedback' => $feedback_list,
            'total' => $total,
            'pages' => ceil( $total / $args['per_page'] ),
            'current_page' => $args['page']
        );
    }

    /**
     * Register AJAX handlers
     */
    public function register_ajax_handlers() {
        add_action( 'wp_ajax_aethos_get_feedback_stats', array( $this, 'ajax_get_feedback_stats' ) );
        add_action( 'wp_ajax_aethos_get_feedback_list', array( $this, 'ajax_get_feedback_list' ) );
    }

    /**
     * AJAX: Get feedback statistics
     */
    public function ajax_get_feedback_stats() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $days = isset( $_POST['days'] ) ? sanitize_text_field( $_POST['days'] ) : 30;

        $stats = $this->get_statistics( $days );

        wp_send_json_success( $stats );
    }

    /**
     * AJAX: Get feedback list
     */
    public function ajax_get_feedback_list() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $args = array(
            'search' => isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '',
            'rating' => isset( $_POST['rating'] ) ? sanitize_text_field( $_POST['rating'] ) : '',
            'days' => isset( $_POST['days'] ) ? sanitize_text_field( $_POST['days'] ) : 30,
            'sort' => isset( $_POST['sort'] ) ? sanitize_text_field( $_POST['sort'] ) : 'newest',
            'page' => isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
            'per_page' => 10
        );

        $result = $this->get_feedback_list( $args );

        wp_send_json_success( $result );
    }
}

// Initialize and register AJAX handlers
$aethos_feedback = new Aethos_Feedback();
$aethos_feedback->register_ajax_handlers();

