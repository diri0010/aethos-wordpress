<?php

/**
 * The analytics-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Analytics {

    /**
     * The table name for storing conversation logs.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $table_name    The conversation logs table name.
     */
    private $table_name;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'aethos_conversations';
    }

    /**
     * Create the conversations table.
     *
     * @since    1.0.0
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id varchar(100) NOT NULL,
            user_ip varchar(45) DEFAULT NULL,
            message_count int(11) DEFAULT 0,
            duration int(11) DEFAULT 0,
            first_message text,
            messages longtext,
            topics text,
            rating int(11) DEFAULT NULL,
            feedback_score decimal(2,1) DEFAULT NULL,
            feedback_text text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY created_at (created_at),
            KEY rating (rating)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Log a conversation.
     *
     * @since    1.0.0
     * @param    array    $data    The conversation data.
     * @return   int|false         The conversation ID or false on failure.
     */
    public function log_conversation( $data ) {
        global $wpdb;
        
        $defaults = array(
            'user_id' => 'guest_' . uniqid(),
            'user_ip' => $this->get_user_ip(),
            'message_count' => 1,
            'duration' => 0,
            'first_message' => '',
            'topics' => '',
            'feedback_score' => null,
        );
        
        $data = wp_parse_args( $data, $defaults );
        
        // Check if IP logging is disabled
        if ( get_option( 'aethos_disable_ip_logging', false ) ) {
            $data['user_ip'] = null;
        }
        
        $result = $wpdb->insert(
            $this->table_name,
            $data,
            array( '%s', '%s', '%d', '%d', '%s', '%s', '%f' )
        );
        
        if ( $result ) {
            return $wpdb->insert_id;
        }
        
        return false;
    }

    /**
     * Update a conversation.
     *
     * @since    1.0.0
     * @param    int      $id      The conversation ID.
     * @param    array    $data    The conversation data.
     * @return   bool              True on success, false on failure.
     */
    public function update_conversation( $id, $data ) {
        global $wpdb;
        
        $result = $wpdb->update(
            $this->table_name,
            $data,
            array( 'id' => $id ),
            array( '%s', '%s', '%d', '%d', '%s', '%s', '%f' ),
            array( '%d' )
        );
        
        return $result !== false;
    }

    /**
     * Get conversations with filters.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   array             Array of conversation objects.
     */
    public function get_conversations( $args = array() ) {
        global $wpdb;
        
        $defaults = array(
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
            'date_from' => null,
            'date_to' => null,
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        $where = array( '1=1' );
        
        if ( $args['date_from'] ) {
            $where[] = $wpdb->prepare( 'created_at >= %s', $args['date_from'] );
        }
        
        if ( $args['date_to'] ) {
            $where[] = $wpdb->prepare( 'created_at <= %s', $args['date_to'] );
        }
        
        $where_clause = implode( ' AND ', $where );
        
        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE {$where_clause} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d",
            $args['limit'],
            $args['offset']
        );
        
        return $wpdb->get_results( $sql );
    }

    /**
     * Get conversation statistics.
     *
     * @since    1.0.0
     * @param    string   $date_from    Start date.
     * @param    string   $date_to      End date.
     * @return   array                  Statistics array.
     */
    public function get_statistics( $date_from = null, $date_to = null ) {
        global $wpdb;
        
        $where = array( '1=1' );
        
        if ( $date_from ) {
            $where[] = $wpdb->prepare( 'created_at >= %s', $date_from );
        }
        
        if ( $date_to ) {
            $where[] = $wpdb->prepare( 'created_at <= %s', $date_to );
        }
        
        $where_clause = implode( ' AND ', $where );
        
        $stats = $wpdb->get_row( "
            SELECT 
                COUNT(*) as total_conversations,
                COUNT(DISTINCT user_id) as unique_users,
                AVG(duration) as avg_duration,
                AVG(feedback_score) as avg_feedback_score
            FROM {$this->table_name}
            WHERE {$where_clause}
        " );
        
        return array(
            'total_conversations' => (int) $stats->total_conversations,
            'unique_users' => (int) $stats->unique_users,
            'avg_duration' => round( (float) $stats->avg_duration ),
            'avg_feedback_score' => round( (float) $stats->avg_feedback_score, 1 ),
        );
    }

    /**
     * Get conversation statistics from SaaS API.
     * This ensures WordPress shows the same counts as SaaS dashboard.
     *
     * @since    1.7.0
     * @param    string   $period    Period: '7d', '30d', '90d', 'all'.
     * @return   array|WP_Error      Statistics array or error.
     */
    public function get_statistics_from_saas( $period = '30d' ) {
        $api_key = get_option( 'aethos_api_key' );
        $saas_url = get_option( 'aethos_saas_url', 'https://app.aethoslogic.com' );
        
        if ( empty( $api_key ) ) {
            return new WP_Error( 'no_api_key', 'Aethos API key not configured' );
        }
        
        $response = wp_remote_get(
            trailingslashit( $saas_url ) . 'api/sites/' . $api_key . '/analytics?period=' . $period,
            array(
                'headers' => array(
                    'x-api-key' => $api_key,
                    'Content-Type' => 'application/json',
                ),
                'timeout' => 15,
            )
        );
        
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        
        $status_code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        if ( $status_code !== 200 ) {
            return new WP_Error( 
                'saas_error', 
                isset( $body['error'] ) ? $body['error'] : 'Failed to fetch analytics from SaaS' 
            );
        }
        
        if ( isset( $body['success'] ) && $body['success'] && isset( $body['data'] ) ) {
            return array(
                'total_conversations' => $body['data']['total_conversations'] ?? 0,
                'unique_visitors' => $body['data']['unique_visitors'] ?? 0,
                'total_messages' => $body['data']['total_messages'] ?? 0,
                'avg_messages_per_conversation' => $body['data']['avg_messages_per_conversation'] ?? 0,
                'source' => 'saas', // Indicate these are SaaS-sourced stats
            );
        }
        
        return new WP_Error( 'invalid_response', 'Invalid response from SaaS' );
    }

    /**
     * Get conversation volume by day.
     *
     * @since    1.0.0
     * @param    string   $date_from    Start date.
     * @param    string   $date_to      End date.
     * @return   array                  Volume data by day.
     */
    public function get_volume_by_day( $date_from, $date_to ) {
        global $wpdb;
        
        $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
            FROM {$this->table_name}
            WHERE created_at >= %s AND created_at <= %s
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", $date_from, $date_to ) );
        
        $volume = array();
        foreach ( $results as $row ) {
            $volume[ $row->date ] = (int) $row->count;
        }
        
        return $volume;
    }

    /**
     * Get top topics.
     *
     * @since    1.0.0
     * @param    int      $limit        Number of topics to return.
     * @param    string   $date_from    Start date.
     * @param    string   $date_to      End date.
     * @return   array                  Top topics with counts.
     */
    public function get_top_topics( $limit = 5, $date_from = null, $date_to = null ) {
        global $wpdb;
        
        $where = array( "topics != ''" );
        
        if ( $date_from ) {
            $where[] = $wpdb->prepare( 'created_at >= %s', $date_from );
        }
        
        if ( $date_to ) {
            $where[] = $wpdb->prepare( 'created_at <= %s', $date_to );
        }
        
        $where_clause = implode( ' AND ', $where );
        
        $results = $wpdb->get_results( "
            SELECT topics
            FROM {$this->table_name}
            WHERE {$where_clause}
        " );
        
        // Count topics
        $topic_counts = array();
        foreach ( $results as $row ) {
            $topics = explode( ',', $row->topics );
            foreach ( $topics as $topic ) {
                $topic = trim( $topic );
                if ( ! empty( $topic ) ) {
                    if ( ! isset( $topic_counts[ $topic ] ) ) {
                        $topic_counts[ $topic ] = 0;
                    }
                    $topic_counts[ $topic ]++;
                }
            }
        }
        
        // Sort by count
        arsort( $topic_counts );
        
        // Return top N
        return array_slice( $topic_counts, 0, $limit, true );
    }

    /**
     * Clear old conversation logs based on retention settings.
     *
     * @since    1.0.0
     * @return   int    Number of rows deleted.
     */
    public function clear_old_logs() {
        global $wpdb;
        
        $retention_days = get_option( 'aethos_log_retention_days', 30 );
        
        $date_threshold = date( 'Y-m-d H:i:s', strtotime( "-{$retention_days} days" ) );
        
        $deleted = $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$this->table_name} WHERE created_at < %s",
            $date_threshold
        ) );
        
        return $deleted;
    }

    /**
     * Clear all conversation logs.
     *
     * @since    1.0.0
     * @return   bool    True on success, false on failure.
     */
    public function clear_all_logs() {
        global $wpdb;
        
        $result = $wpdb->query( "TRUNCATE TABLE {$this->table_name}" );
        
        return $result !== false;
    }

    /**
     * Get user IP address.
     *
     * @since    1.0.0
     * @return   string    User IP address.
     */
    private function get_user_ip() {
        $ip = '';
        
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }
        
        return $ip;
    }

    /**
     * Export conversations to CSV.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   string            CSV content.
     */
    public function export_to_csv( $args = array() ) {
        $conversations = $this->get_conversations( array_merge( $args, array( 'limit' => 999999 ) ) );
        
        $csv = "Timestamp,User ID,Message Count,Duration (seconds),First Message,Topics,Feedback Score\n";
        
        foreach ( $conversations as $conversation ) {
            $csv .= sprintf(
                '"%s","%s","%d","%d","%s","%s","%s"' . "\n",
                $conversation->created_at,
                $conversation->user_id,
                $conversation->message_count,
                $conversation->duration,
                str_replace( '"', '""', $conversation->first_message ),
                $conversation->topics,
                $conversation->feedback_score ? $conversation->feedback_score : 'N/A'
            );
        }
        
        return $csv;
    }

}
