<?php

/**
 * API Client for Aethos SaaS Platform
 *
 * Handles all HTTP communication with the Aethos API
 *
 * @since      1.0.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_API_Client {

    /**
     * API endpoint base URL
     *
     * @var string
     */
    private $api_endpoint;

    /**
     * API key for authentication
     *
     * @var string
     */
    private $api_key;

    /**
     * Debug mode flag
     *
     * @var bool
     */
    private $debug_mode;

    /**
     * Initialize the API client
     *
     * @since    1.0.0
     */
    public function __construct() {
        $api = new Aethos_API();
        $this->api_endpoint = $api->get_api_endpoint();
        $this->api_key = get_option( 'aethos_api_key', '' );
        $this->debug_mode = get_option( 'aethos_debug_mode', false );
    }

    /**
     * Get API endpoint base URL
     *
     * @return string API endpoint
     */
    public function get_api_endpoint() {
        return $this->api_endpoint;
    }

    /**
     * Send a chat message to the API
     *
     * @param string $message The user's message
     * @param array  $context Additional context (optional)
     * @return array|WP_Error Response data or error
     */
    public function send_message( $message, $context = array() ) {
        if ( empty( $this->api_key ) ) {
            return new WP_Error( 'no_api_key', 'API key is not configured' );
        }

        $endpoint = $this->api_endpoint . '/api/chat';
        
        $body = array(
            'apiKey' => $this->api_key,
            'message' => $message
        );

        // Add context if provided
        if ( ! empty( $context ) ) {
            $body['context'] = $context;
        }

        $this->log( 'Sending message to API', array(
            'endpoint' => $endpoint,
            'message_length' => strlen( $message )
        ));

        $response = $this->make_request( $endpoint, $body );

        if ( is_wp_error( $response ) ) {
            $this->log( 'API request failed', array(
                'error' => $response->get_error_message()
            ), 'error' );
            return $response;
        }

        $this->log( 'API response received', array(
            'status' => 'success'
        ));

        return $response;
    }

    /**
     * Verify API key validity
     *
     * @param string $api_key Optional API key to test (uses stored key if not provided)
     * @return bool|WP_Error True if valid, WP_Error if invalid
     */
    public function verify_api_key( $api_key = null ) {
        $key_to_test = $api_key ? $api_key : $this->api_key;

        if ( empty( $key_to_test ) ) {
            return new WP_Error( 'no_api_key', 'No API key provided' );
        }

        $endpoint = $this->api_endpoint . '/api/chat';
        
        $body = array(
            'apiKey' => $key_to_test,
            'message' => 'Connection test'
        );

        $response = $this->make_request( $endpoint, $body, array( 'timeout' => 10 ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return true;
    }

    /**
     * Get site configuration from API
     *
     * @return array|WP_Error Configuration data or error
     */
    public function get_site_config() {
        // Check cache first
        $cache_key = 'aethos_site_config_' . md5( $this->api_key );
        $cached = get_transient( $cache_key );

        if ( $cached !== false && get_option( 'aethos_cache_enabled', true ) ) {
            $this->log( 'Using cached site config' );
            return $cached;
        }

        // Fetch from API
        $endpoint = $this->api_endpoint . '/api/site/config';
        
        $response = $this->make_request( $endpoint, array(
            'apiKey' => $this->api_key
        ));

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        // Cache for 1 hour
        set_transient( $cache_key, $response, HOUR_IN_SECONDS );

        return $response;
    }

    /**
     * Log analytics event
     *
     * @param array $event_data Event data to log
     * @return bool Success status
     */
    public function log_analytics( $event_data ) {
        if ( ! get_option( 'aethos_enable_analytics', false ) ) {
            return false;
        }

        $endpoint = $this->api_endpoint . '/api/analytics';
        
        $body = array_merge(
            array( 'apiKey' => $this->api_key ),
            $event_data
        );

        // Fire and forget - don't wait for response
        wp_remote_post( $endpoint, array(
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body' => json_encode( $body ),
            'timeout' => 1,
            'blocking' => false
        ));

        return true;
    }

    /**
     * Make HTTP request to API
     *
     * @param string $endpoint API endpoint URL
     * @param array  $body Request body data
     * @param array  $args Additional wp_remote_post arguments
     * @return array|WP_Error Response data or error
     */
    public function make_request( $endpoint, $body, $args = array() ) {
        $default_args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode( $body ),
            'timeout' => 15,
            'sslverify' => true
        );

        // Add API key to headers if available
        if ( ! empty( $this->api_key ) ) {
            $default_args['headers']['x-api-key'] = $this->api_key;
        }

        $args = wp_parse_args( $args, $default_args );

        // Apply rate limiting
        if ( ! $this->check_rate_limit() ) {
            return new WP_Error( 'rate_limit_exceeded', 'Rate limit exceeded. Please try again later.' );
        }

        $response = wp_remote_post( $endpoint, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( $status_code !== 200 ) {
            $error_message = isset( $data['error'] ) ? $data['error'] : 'API request failed';
            return new WP_Error( 'api_error', $error_message, array( 'status' => $status_code ) );
        }

        return $data;
    }

    /**
     * Check rate limiting
     *
     * @return bool True if request is allowed, false if rate limited
     */
    private function check_rate_limit() {
        // Skip rate limiting for admin users and WP Cron
        if ( current_user_can( 'manage_options' ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
            return true;
        }

        $rate_limit = get_option( 'aethos_rate_limit', 60 );
        $user_id = get_current_user_id();
        $ip_address = $this->get_client_ip();
        
        // Use user ID if logged in, otherwise use IP
        $identifier = $user_id ? 'user_' . $user_id : 'ip_' . md5( $ip_address );
        $transient_key = 'aethos_rate_limit_' . $identifier;

        $request_count = get_transient( $transient_key );

        if ( $request_count === false ) {
            // First request in this minute
            set_transient( $transient_key, 1, MINUTE_IN_SECONDS );
            return true;
        }

        if ( $request_count >= $rate_limit ) {
            $this->log( 'Rate limit exceeded', array(
                'identifier' => $identifier,
                'count' => $request_count,
                'limit' => $rate_limit
            ), 'warning' );
            return false;
        }

        // Increment counter
        set_transient( $transient_key, $request_count + 1, MINUTE_IN_SECONDS );
        return true;
    }

    /**
     * Get client IP address
     *
     * @return string Client IP address
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );

        foreach ( $ip_keys as $key ) {
            if ( array_key_exists( $key, $_SERVER ) === true ) {
                foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
                    $ip = trim( $ip );
                    if ( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) {
                        return $ip;
                    }
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Log debug information
     *
     * @param string $message Log message
     * @param array  $context Additional context
     * @param string $level Log level (info, warning, error)
     */
    private function log( $message, $context = array(), $level = 'info' ) {
        if ( ! $this->debug_mode ) {
            return;
        }

        $log_entry = array(
            'timestamp' => current_time( 'mysql' ),
            'level' => $level,
            'message' => $message,
            'context' => $context
        );

        error_log( '[Aethos API] ' . json_encode( $log_entry ) );
    }

    /**
     * Clear all caches
     *
     * @return void
     */
    public function clear_cache() {
        global $wpdb;
        
        // Delete all Aethos transients
        $wpdb->query( 
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_aethos_%' 
            OR option_name LIKE '_transient_timeout_aethos_%'"
        );

        $this->log( 'Cache cleared' );
    }
}
