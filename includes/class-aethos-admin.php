<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * The API client.
     *
     * @since    1.0.0
     * @access   private
     * @var      Aethos_API    $api    The API client.
     */
    private $api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->api = new Aethos_API();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 
			$this->plugin_name . '-admin', 
			AETHOS_PLUGIN_URL . 'assets/css/admin.css', 
			array(), 
			$this->version, 
			'all' 
		);
		
		wp_enqueue_style( 
			$this->plugin_name . '-admin-modern', 
			AETHOS_PLUGIN_URL . 'assets/css/admin-modern.css', 
			array(), 
			time(), 
			'all' 
		);
		
		// Enqueue WordPress color picker
		wp_enqueue_style( 'wp-color-picker' );
	} 

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 
			$this->plugin_name . '-admin', 
			AETHOS_PLUGIN_URL . 'assets/js/admin.js', 
			array( 'jquery', 'wp-color-picker' ), 
			$this->version, 
			false 
		);

		// Localize script with data
		wp_localize_script( 
			$this->plugin_name . '-admin', 
			'aethosAdmin', 
			array(
				'nonce' => wp_create_nonce( 'aethos_admin_nonce' ),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'apiEndpoint' => $this->api->get_api_endpoint()
			)
		);
	}

    /**
     * Add plugin admin menu
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        // Main menu page (Dashboard)
        add_menu_page(
            'Aethos Chat', 
            'Aethos Chat', 
            'manage_options', 
            'aethos-chat', 
            array($this, 'display_dashboard_page'),
            'dashicons-format-chat',
            90
        );
        
        // Dashboard submenu (same as main)
        add_submenu_page(
            'aethos-chat',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'aethos-chat',
            array($this, 'display_dashboard_page')
        );
        
        // Setup & Configuration
        add_submenu_page(
            'aethos-chat',
            'Setup & Configuration',
            'Setup & Configuration',
            'manage_options',
            'aethos-chat-setup',
            array($this, 'display_setup_page')
        );
        
        /*
        // Look & Feel - Removed as it is now handled by SaaS
        add_submenu_page(
            'aethos-chat',
            'Look & Feel',
            'Look & Feel',
            'manage_options',
            'aethos-chat-customization',
            array($this, 'display_customization_page')
        );
        */
        
        // Content Management
        add_submenu_page(
            'aethos-chat',
            'Content Management',
            'Content Management',
            'manage_options',
            'aethos-chat-content',
            array($this, 'display_content_page')
        );
        
        // Monitoring & Analytics
        add_submenu_page(
            'aethos-chat',
            'Monitoring & Analytics',
            'Monitoring & Analytics',
            'manage_options',
            'aethos-chat-monitoring',
            array($this, 'display_monitoring_page')
        );
        
        // Advanced Configuration
        add_submenu_page(
            'aethos-chat',
            'Advanced Configuration',
            'Advanced Configuration',
            'manage_options',
            'aethos-chat-advanced-config',
            array($this, 'display_advanced_config_page')
        );
    }

    /**
     * Register all settings
     *
     * @since    1.0.0
     */
    public function register_settings() {
        // Connection settings
        register_setting( 'aethos_connection', 'aethos_api_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
        
        register_setting( 'aethos_connection', 'aethos_api_endpoint', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => $this->api->get_api_endpoint()
        ));
        
        register_setting( 'aethos_connection', 'aethos_connection_status', array(
            'type' => 'string',
            'default' => 'unknown'
        ));

        // Appearance settings
        register_setting( 'aethos_appearance', 'aethos_widget_position', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'bottom-right'
        ));
        
        register_setting( 'aethos_appearance', 'aethos_widget_size', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'medium'
        ));
        
        register_setting( 'aethos_appearance', 'aethos_primary_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#1400CC'
        ));
        
        register_setting( 'aethos_appearance', 'aethos_accent_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#33C2E3'
        ));
        
        register_setting( 'aethos_appearance', 'aethos_success_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#59BA47'
        ));
        
        register_setting( 'aethos_appearance', 'aethos_error_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#E5233D'
        ));
        
        register_setting( 'aethos_appearance', 'aethos_dark_mode', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'auto'
        ));

        // New Appearance Settings (Overhaul)
        register_setting( 'aethos_appearance', 'aethos_chat_icon', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => ''
        ));

        register_setting( 'aethos_appearance', 'aethos_user_text_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#FFFFFF'
        ));

        register_setting( 'aethos_appearance', 'aethos_ai_bubble_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#F3F4F6'
        ));

        register_setting( 'aethos_appearance', 'aethos_input_bg_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#FFFFFF'
        ));

        register_setting( 'aethos_appearance', 'aethos_font_family', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Roboto'
        ));

        register_setting( 'aethos_appearance', 'aethos_font_size', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 16
        ));

        // Behavior settings
        register_setting( 'aethos_behavior', 'aethos_auto_open', array(
            'type' => 'boolean',
            'default' => false
        ));
        
        register_setting( 'aethos_behavior', 'aethos_auto_open_delay', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 3
        ));
        
        register_setting( 'aethos_behavior', 'aethos_greeting_message', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'Welcome! How can I help you today?'
        ));
        
        register_setting( 'aethos_behavior', 'aethos_offline_message', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'We\'re currently offline. Please try again later.'
        ));
        
        register_setting( 'aethos_behavior', 'aethos_persona', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'friendly'
        ));
        
        register_setting( 'aethos_behavior', 'aethos_greeting', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Hi! How can I help you today?'
        ));
        
        // Advanced settings
        register_setting( 'aethos_options', 'aethos_log_conversations', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        register_setting( 'aethos_options', 'aethos_log_ip', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        // Header title for appearance
        register_setting( 'aethos_appearance', 'aethos_header_title', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Aethos AI Assistant'
        ));
        
        // Chatbot persona for behavior
        register_setting( 'aethos_behavior', 'aethos_chatbot_persona', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'friendly'
        ));
        
        // Fallback response for behavior
        register_setting( 'aethos_behavior', 'aethos_fallback_response', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'I apologize, but I don\'t have enough information to answer that question accurately. Could you try rephrasing it or asking something else?'
        ));
        
        register_setting( 'aethos_options', 'aethos_data_retention', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0
        ));
        
        register_setting( 'aethos_options', 'aethos_disable_ip_logging', array(
            'type' => 'boolean',
            'default' => false
        ));
        
        register_setting( 'aethos_options', 'aethos_enable_minification', array(
            'type' => 'boolean',
            'default' => false
        ));
        
        register_setting( 'aethos_options', 'aethos_request_timeout', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 30
        ));

        // Advanced settings
        register_setting( 'aethos_options', 'aethos_debug_mode', array(
            'type' => 'boolean',
            'default' => false
        ));
        
        register_setting( 'aethos_options', 'aethos_cache_enabled', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        register_setting( 'aethos_options', 'aethos_rate_limit', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 60
        ));
        
        register_setting( 'aethos_options', 'aethos_enable_analytics', array(
            'type' => 'boolean',
            'default' => false
        ));
        
        // Visibility settings
        register_setting( 'aethos_visibility', 'aethos_global_visibility', array(
            'type' => 'boolean',
            'sanitize_callback' => 'absint',
            'default' => true
        ));
        
        register_setting( 'aethos_visibility', 'aethos_include_all_pages', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true
        ));
        
        register_setting( 'aethos_visibility', 'aethos_included_pages', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_id_array'),
            'default' => array()
        ));
        
        register_setting( 'aethos_visibility', 'aethos_excluded_pages', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_id_array'),
            'default' => array()
        ));
        
        register_setting( 'aethos_visibility', 'aethos_include_all_categories', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true
        ));
        
        register_setting( 'aethos_visibility', 'aethos_included_categories', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_id_array'),
            'default' => array()
        ));
        
        register_setting( 'aethos_visibility', 'aethos_excluded_categories', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_id_array'),
            'default' => array()
        ));
        
        // WooCommerce visibility settings
        register_setting( 'aethos_visibility', 'aethos_include_all_woo_products', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true
        ));
        
        register_setting( 'aethos_visibility', 'aethos_included_woo_products', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_id_array'),
            'default' => array()
        ));
        
        register_setting( 'aethos_visibility', 'aethos_excluded_woo_products', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_id_array'),
            'default' => array()
        ));
        
        register_setting( 'aethos_visibility', 'aethos_include_all_woo_categories', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true
        ));
        
        register_setting( 'aethos_visibility', 'aethos_included_woo_categories', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_id_array'),
            'default' => array()
        ));
        
        register_setting( 'aethos_visibility', 'aethos_excluded_woo_categories', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_id_array'),
            'default' => array()
        ));
        
        // Custom Post Type visibility settings (dynamic)
        $args = array(
            'public' => true,
            '_builtin' => false
        );
        $custom_post_types = get_post_types($args, 'objects');
        
        foreach ($custom_post_types as $cpt) {
            // Skip WooCommerce product (handled separately above)
            if ($cpt->name === 'product') {
                continue;
            }
            
            register_setting( 'aethos_visibility', "aethos_include_all_{$cpt->name}", array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ));
            
            register_setting( 'aethos_visibility', "aethos_included_{$cpt->name}", array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_id_array'),
                'default' => array()
            ));
            
            register_setting( 'aethos_visibility', "aethos_excluded_{$cpt->name}", array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_id_array'),
                'default' => array()
            ));
        }
    }
    
    /**
     * Sanitize array of IDs
     *
     * @since    1.0.0
     */
    public function sanitize_id_array($value) {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            return array();
        }
        return array_map('absint', array_filter($value));
    }

    /**
     * Display page methods
     */
    public function display_dashboard_page() {
        include_once AETHOS_PLUGIN_DIR . 'admin/partials/dashboard-page.php';
    }
    
    public function display_setup_page() {
        include_once AETHOS_PLUGIN_DIR . 'admin/partials/setup-page.php';
    }
    
    public function display_customization_page() {
        include_once AETHOS_PLUGIN_DIR . 'admin/partials/customization-page.php';
    }
    
    public function display_content_page() {
        include_once AETHOS_PLUGIN_DIR . 'admin/partials/content-page.php';
    }
    
    public function display_monitoring_page() {
        include_once AETHOS_PLUGIN_DIR . 'admin/partials/monitoring-page.php';
    }
    
    public function display_advanced_config_page() {
        include_once AETHOS_PLUGIN_DIR . 'admin/partials/advanced-config-page.php';
    }

    /**
     * AJAX handler for testing API connection
     *
     * @since    1.0.0
     */
    public function test_connection() {
        // Verify nonce
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        // Check user permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '';
        
        if ( empty( $api_key ) ) {
            wp_send_json_error( array( 'message' => 'API key is required' ) );
        }

        // Get the endpoint that will be used
        $endpoint = $this->api->get_api_endpoint();
        $test_url = $endpoint . '/api/sites/verify-key';
        
        // Debug: Log connection attempt
        aethos_log( "Connection Test: Attempting to connect to {$test_url}" );
        aethos_log( "Connection Test: API key prefix: " . substr( $api_key, 0, 15 ) . '...' );
        aethos_log( "Connection Test: WordPress host: " . $_SERVER['HTTP_HOST'] );

        // Test the connection using the verify-key endpoint
        $response = $this->api->post( '/api/sites/verify-key', array(
            'apiKey' => $api_key
        ));

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            $error_code = $response->get_error_code();
            
            // Verbose debug log
            aethos_log( "Connection Test FAILED: WP_Error" );
            aethos_log( "Connection Test: Error code: {$error_code}" );
            aethos_log( "Connection Test: Error message: {$error_message}" );
            aethos_log( "Connection Test: Target endpoint: {$test_url}" );
            
            update_option( 'aethos_connection_status', 'error' );
            wp_send_json_error( array( 'message' => 'Connection failed: ' . $error_message ) );
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        $response_headers = wp_remote_retrieve_headers( $response );

        // Debug: Log response details
        aethos_log( "Connection Test: Response code: {$response_code}" );
        aethos_log( "Connection Test: Response body (first 500 chars): " . substr( $body, 0, 500 ) );

        // verify-key returns {valid: true/false, site: {...}} or {valid: false, message: '...'}
        if ( $response_code === 200 && isset( $data['valid'] ) && $data['valid'] === true ) {
            aethos_log( "Connection Test SUCCESS: Connected to {$endpoint}" );
            aethos_log( "Connection Test: Site name: " . ( $data['site']['name'] ?? 'unknown' ) );
            
            // Save the API key if connection is successful
            $saved_key = update_option( 'aethos_api_key', $api_key );
            $saved_endpoint = update_option( 'aethos_api_endpoint', $this->api->get_api_endpoint() );
            
            // Update the SaaS backend to set status as 'connected' (backend converts to 'active')
            $activation_result = $this->update_saas_connection_status( $api_key, 'connected' );
            
            // Check if activation failed (tier limit, etc.)
            if ( ! $activation_result['success'] ) {
                aethos_log( "Connection Test FAILED: Activation blocked - " . $activation_result['message'] );
                update_option( 'aethos_connection_status', 'error' );
                
                $error_message = $activation_result['message'];
                if ( isset( $activation_result['tier_limit'] ) && $activation_result['tier_limit'] ) {
                    $error_message .= ' Visit your Aethos dashboard to upgrade or deactivate another site.';
                }
                
                wp_send_json_error( array( 'message' => $error_message ) );
                return;
            }
            
            // Activation successful
            update_option( 'aethos_connection_status', 'connected' );
            
            $site_name = isset( $data['site']['name'] ) ? $data['site']['name'] : '';
            wp_send_json_success( array( 
                'message' => 'Connection successful' . ( $site_name ? " - {$site_name}" : '' )
            ) );
        } elseif ( $response_code === 200 && isset( $data['valid'] ) && $data['valid'] === false ) {
            // API responded but key is invalid
            $error_msg = isset( $data['message'] ) ? $data['message'] : 'Invalid API Key';
            $debug_info = "API key validation failed: {$error_msg}";
            
            aethos_log( "Connection Test FAILED: {$debug_info}" );
            
            update_option( 'aethos_connection_status', 'error' );
            
            $response_data = array( 'message' => $error_msg );
            if ( defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) {
                $response_data['debug'] = array(
                    'endpoint' => $endpoint,
                    'test_url' => $test_url,
                    'http_status' => $response_code,
                    'wordpress_site' => site_url(),
                    'wordpress_host' => $_SERVER['HTTP_HOST'],
                    'debug_info' => $debug_info,
                    'response_body' => substr( $body, 0, 1000 ),
                );
            }
            wp_send_json_error( $response_data );
        } else {
            // Determine user-friendly error message
            $error_msg = 'Connection failed';
            $debug_info = '';
            
            if ( isset( $data['error'] ) ) {
                $error_msg = $data['error'];
                $debug_info = $data['error'];
            } elseif ( $response_code === 403 ) {
                $error_msg = 'Access denied - check if the site URL matches your registered site';
                $debug_info = 'CORS/Origin mismatch - WordPress origin may not match site URL in SaaS';
            } elseif ( $response_code === 404 ) {
                $error_msg = 'API endpoint not found';
                $debug_info = "Endpoint {$test_url} returned 404";
            } elseif ( $response_code === 401 ) {
                $error_msg = 'Invalid API key';
                $debug_info = 'API key not recognized by SaaS';
            } elseif ( $response_code === 500 ) {
                $error_msg = 'Server error - please try again later';
                $debug_info = 'SaaS returned 500 Internal Server Error';
            } elseif ( $response_code === 0 || empty( $response_code ) ) {
                $error_msg = 'Could not reach the server - check your internet connection';
                $debug_info = 'No response received from server';
            } else {
                $error_msg = "Connection failed (HTTP {$response_code})";
                $debug_info = "Unexpected HTTP status code: {$response_code}";
            }
            
            // Verbose debug log
            aethos_log( "Connection Test FAILED: {$debug_info}" );
            aethos_log( "Connection Test: HTTP Status: {$response_code}" );
            aethos_log( "Connection Test: Target endpoint: {$endpoint}" );
            aethos_log( "Connection Test: WordPress site: " . site_url() );
            if ( isset( $response_headers['x-request-id'] ) ) {
                aethos_log( "Connection Test: Request ID: " . $response_headers['x-request-id'] );
            }
            
            update_option( 'aethos_connection_status', 'error' );
            
            // Build response array
            $response_data = array( 'message' => $error_msg );
            
            // Include detailed debug info when WP_DEBUG_DISPLAY is enabled
            if ( defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) {
                $response_data['debug'] = array(
                    'endpoint' => $endpoint,
                    'test_url' => $test_url,
                    'http_status' => $response_code,
                    'wordpress_site' => site_url(),
                    'wordpress_host' => $_SERVER['HTTP_HOST'],
                    'debug_info' => $debug_info,
                    'response_body' => substr( $body, 0, 1000 ),
                );
            }
            
            wp_send_json_error( $response_data );
        }
    }

    /**
     * AJAX handler for disconnecting
     *
     * @since    1.0.0
     */
    public function disconnect() {
        // Verify nonce
        if ( ! check_ajax_referer( 'aethos_admin_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed' ) );
            return;
        }

        // Check user permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
            return;
        }

        // Get current settings before clearing them
        $api_key = get_option( 'aethos_api_key' );

        // Update SaaS status to 'active' if we have an API key
        if ( ! empty( $api_key ) ) {
            // We don't check the result here because we want to disconnect locally regardless
            $this->update_saas_connection_status( $api_key, 'active' );
        }

        // Clear connection settings
        update_option( 'aethos_api_key', '' );
        update_option( 'aethos_connection_status', '' );
        
        wp_send_json_success( array( 'message' => 'Disconnected successfully' ) );
    }

    /**
     * Update connection status on SaaS backend
     * Also saves sharedSecret if returned (for JWT token signing)
     *
     * @since    1.0.0
     */
    private function update_saas_connection_status( $api_key, $status ) {
        $response = $this->api->post( '/api/sites/update-connection-status', array(
            'apiKey' => $api_key,
            'status' => $status
        ), array(
            'timeout' => 5, // Short timeout so we don't block the UI
            'blocking' => true, // We want to wait a bit, but not too long
        ));

        if ( is_wp_error( $response ) ) {
            aethos_log('Failed to update SaaS connection status - ' . $response->get_error_message());
            return array(
                'success' => false,
                'message' => 'Could not connect to Aethos server: ' . $response->get_error_message()
            );
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        
        // Parse response body to check for sharedSecret or errors
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        
        // Handle tier limit errors (500 or 400 with error message)
        if ( $response_code >= 400 ) {
            $error_message = isset( $data['error'] ) ? $data['error'] : 'Activation failed';
            aethos_log('SaaS activation error: ' . $error_message);
            return array(
                'success' => false,
                'message' => $error_message,
                'tier_limit' => strpos( $error_message, 'maximum' ) !== false
            );
        }
        
        // Save sharedSecret if returned (used for JWT token signing)
        if ( $response_code === 200 && isset( $data['sharedSecret'] ) && ! empty( $data['sharedSecret'] ) ) {
            update_option( 'aethos_shared_secret', $data['sharedSecret'] );
            aethos_log('Shared secret received and saved for secure widget authentication');
        }
        
        return array(
            'success' => true,
            'message' => 'Status updated successfully'
        );
    }

    /**
     * AJAX handler for full site scan (batch-aware)
     *
     * @since    1.0.0
     */
    public function scan_now() {
        check_ajax_referer( 'aethos_scan_now', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        // Get batch parameters
        $offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
        $limit = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 5;

        require_once AETHOS_PLUGIN_DIR . 'includes/class-aethos-scan-orchestrator.php';
        $orchestrator = new Aethos_Scan_Orchestrator();

        $result = $orchestrator->start_full_scan($offset, $limit);

        if ( $result['success'] ) {
            wp_send_json_success( array(
                'message' => 'Batch processed successfully',
                'total_posts' => $result['total_posts'],
                'processed_count' => $result['processed_count'],
                'offset' => $result['offset'],
                'items_found' => $result['items_found'],
                'has_more' => $result['has_more']
            ));
        } else {
            wp_send_json_error( array( 'message' => $result['error'] ) );
        }
    }

    /**
     * AJAX handler for single post scan
     *
     * @since    1.0.0
     */
    public function scan_single() {
        check_ajax_referer( 'aethos_scan_single', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'Invalid post ID' ) );
        }

        require_once AETHOS_PLUGIN_DIR . 'includes/class-aethos-scan-orchestrator.php';
        $orchestrator = new Aethos_Scan_Orchestrator();

        $result = $orchestrator->scan_single_post( $post_id );

        if ( $result['success'] ) {
            wp_send_json_success( array(
                'message' => 'Post scanned successfully',
                'vectors_created' => $result['vectors_created'],
                'vectors_updated' => $result['vectors_updated']
            ));
        } else {
            wp_send_json_error( array( 'message' => $result['error'] ) );
        }
    }

    /**
     * AJAX handler for toggling post exclusion
     *
     * @since    1.0.0
     */
    public function toggle_exclude() {
        check_ajax_referer( 'aethos_toggle_exclude', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $exclude = isset( $_POST['exclude'] ) ? rest_sanitize_boolean( $_POST['exclude'] ) : false;

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'Invalid post ID' ) );
        }

        $excluded_posts = get_option( 'aethos_excluded_posts', array() );

        if ( $exclude ) {
            if ( ! in_array( $post_id, $excluded_posts ) ) {
                $excluded_posts[] = $post_id;
            }
        } else {
            $excluded_posts = array_diff( $excluded_posts, array( $post_id ) );
        }

        update_option( 'aethos_excluded_posts', $excluded_posts );

        wp_send_json_success( array(
            'message' => $exclude ? 'Post excluded' : 'Post included',
            'excluded' => $exclude
        ));
    }

    /**
     * AJAX handler for saving scan schedule
     *
     * @since    1.0.0
     */
    public function save_scan_schedule() {
        check_ajax_referer( 'aethos_save_scan_schedule', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $schedule = isset( $_POST['schedule'] ) ? sanitize_text_field( $_POST['schedule'] ) : 'daily';

        if ( ! in_array( $schedule, array( 'daily', 'weekly', 'monthly' ) ) ) {
            wp_send_json_error( array( 'message' => 'Invalid schedule' ) );
        }

        update_option( 'aethos_scan_schedule', $schedule );
        
        // Reschedule cron job
        $this->schedule_automated_scan( $schedule );

        wp_send_json_success( array(
            'message' => 'Schedule saved',
            'schedule' => $schedule
        ));
    }

    /**
     * AJAX handler for deleting vectors
     *
     * @since    1.0.0
     */
    public function delete_vectors() {
        check_ajax_referer( 'aethos_delete_vectors', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'Invalid post ID' ) );
        }

        require_once AETHOS_PLUGIN_DIR . 'includes/class-aethos-vector-storage.php';
        $storage = new Aethos_Vector_Storage();

        $result = $storage->delete_post_vectors( $post_id );

        if ( $result !== false ) {
            wp_send_json_success( array(
                'message' => 'Vectors deleted successfully',
                'deleted_count' => $result
            ));
        } else {
            wp_send_json_error( array( 'message' => 'Failed to delete vectors' ) );
        }
    }

    /**
     * Schedule automated scan based on user preference
     *
     * @since    1.0.0
     */
    private function schedule_automated_scan( $schedule ) {
        // Clear existing scheduled event
        $timestamp = wp_next_scheduled( 'aethos_automated_scan' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'aethos_automated_scan' );
        }

        // Schedule new event based on preference
        $interval = 'daily'; // Default
        switch ( $schedule ) {
            case 'weekly':
                $interval = 'weekly';
                break;
            case 'monthly':
                $interval = 'monthly';
                break;
        }

        wp_schedule_event( time(), $interval, 'aethos_automated_scan' );
    }

    /**
     * Handle automated scan cron job
     *
     * @since    1.0.0
     */
    public function run_automated_scan() {
        require_once AETHOS_PLUGIN_DIR . 'includes/class-aethos-scan-orchestrator.php';
        $orchestrator = new Aethos_Scan_Orchestrator();
        $orchestrator->start_full_scan();
    }

    /**
     * AJAX handler for removing all vectors
     *
     * @since    1.0.0
     */
    public function remove_all_vectors() {
        check_ajax_referer( 'aethos_remove_all_vectors', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        require_once AETHOS_PLUGIN_DIR . 'includes/class-aethos-vector-storage.php';
        $storage = new Aethos_Vector_Storage();

        global $wpdb;
        $vectors_table = $wpdb->prefix . 'aethos_vectors';
        
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM $vectors_table" );
        $result = $wpdb->query( "TRUNCATE TABLE $vectors_table" );

        if ( $result !== false ) {
            wp_send_json_success( array(
                'message' => 'All vectors removed successfully',
                'deleted_count' => $count
            ));
        } else {
            wp_send_json_error( array( 'message' => 'Failed to remove vectors' ) );
        }
    }

}
