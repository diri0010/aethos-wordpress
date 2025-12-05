<?php

/**
 * The API handler class.
 *
 * This class handles all communication with the Aethos SaaS API.
 *
 * @since      1.0.0
 * @package    Aethos
 * @subpackage Aethos/includes
 * @author     Aethos <support@aethoslogic.com>
 */
class Aethos_API {

    /**
     * The API endpoint URL.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $api_endpoint    The API endpoint URL.
     */
    private $api_endpoint;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->api_endpoint = $this->get_api_endpoint();
    }

    /**
     * Get the API endpoint URL.
     *
     * Checks for a defined constant AETHOS_API_URL first, then the saved option,
     * then falls back to a default based on the environment.
     *
     * @since    1.0.0
     * @return   string    The API endpoint URL.
     */
    public function get_api_endpoint() {
        // 1. Check for constant (allows robust override in wp-config.php)
        if ( defined( 'AETHOS_API_URL' ) && ! empty( AETHOS_API_URL ) ) {
            return untrailingslashit( AETHOS_API_URL );
        }

        // 2. Check for saved option
        $saved_endpoint = get_option( 'aethos_api_endpoint' );
        if ( ! empty( $saved_endpoint ) ) {
            return untrailingslashit( $saved_endpoint );
        }

        // 3. Fallback default
        return $this->get_default_api_endpoint();
    }

    /**
     * Get the default API endpoint based on environment.
     *
     * @since    1.0.0
     * @return   string    The default API endpoint.
     */
    private function get_default_api_endpoint() {
        $host = $_SERVER['HTTP_HOST'];
        
        // Local development
        if ( strpos( $host, 'localhost' ) !== false || strpos( $host, '.test' ) !== false || strpos( $host, '.local' ) !== false ) {
            return 'http://localhost:3000';
        }
        
        // Staging (example)
        if ( strpos( $host, 'staging' ) !== false ) {
            return 'https://staging-api.aethoslogic.com';
        }

        // Production default
        return 'https://aethoslogic.com';
    }

    /**
     * Send a POST request to the API.
     *
     * @since    1.0.0
     * @param    string    $route      The API route (e.g., '/api/sites/update-connection-status').
     * @param    array     $body       The request body data.
     * @param    array     $args       Optional. Additional arguments for wp_remote_post.
     * @return   array|WP_Error        The response or WP_Error on failure.
     */
    public function post( $route, $body = array(), $args = array() ) {
        $url = $this->get_api_endpoint() . $route;
        
        $default_args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body'    => json_encode( $body ),
            'timeout' => 15,
        );

        $request_args = wp_parse_args( $args, $default_args );

        // Log the request for debugging
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'Aethos API POST: ' . $url );
        }

        $response = wp_remote_post( $url, $request_args );

        if ( is_wp_error( $response ) ) {
            error_log( 'Aethos API Error: ' . $response->get_error_message() );
        }

        return $response;
    }

    /**
     * Send a GET request to the API.
     *
     * @since    1.0.0
     * @param    string    $route      The API route.
     * @param    array     $args       Optional. Additional arguments for wp_remote_get.
     * @return   array|WP_Error        The response or WP_Error on failure.
     */
    public function get( $route, $args = array() ) {
        $url = $this->get_api_endpoint() . $route;
        
        $default_args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'timeout' => 15,
        );

        $request_args = wp_parse_args( $args, $default_args );

        $response = wp_remote_get( $url, $request_args );

        if ( is_wp_error( $response ) ) {
            error_log( 'Aethos API Error: ' . $response->get_error_message() );
        }

        return $response;
    }
}
