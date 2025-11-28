<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/widget.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/widget.js', array(), $this->version, true );
        
        // Get all widget configuration
        $api = new Aethos_API();
        $api_endpoint = $api->get_api_endpoint();
        
        wp_localize_script( $this->plugin_name, 'aethosData', array(
            'apiKey' => get_option('aethos_api_key', ''),
            'apiUrl' => $api_endpoint . '/api/chat',
            
            // Appearance settings
            'primaryColor' => get_option('aethos_primary_color', '#0052CC'),
            'accentColor' => get_option('aethos_accent_color', '#33C2E3'),
            'successColor' => get_option('aethos_success_color', '#59BA47'),
            'errorColor' => get_option('aethos_error_color', '#E5233D'),
            
            // Behavior settings
            'autoOpen' => (bool) get_option('aethos_auto_open', false),
            'autoOpenDelay' => (int) get_option('aethos_auto_open_delay', 3),
            'greetingMessage' => get_option('aethos_greeting_message', 'Hello! How can I help you today?'),
            'offlineMessage' => get_option('aethos_offline_message', 'We\'re currently offline. Please try again later.'),
            
            // Widget position and size
            'position' => get_option('aethos_widget_position', 'bottom-right'),
            'size' => get_option('aethos_widget_size', 'medium'),
            
            // Icons
            'chatIcon' => get_option('aethos_chat_icon', ''),
        ));

	}

    public function render_widget() {
        // Only render if API key is set
        if ( ! get_option('aethos_api_key') ) {
            return;
        }
        ?>
        <div id="aethos-chat-widget" class="aethos-widget-closed">
            <div class="aethos-chat-header">
                <span>Chat with us</span>
                <button id="aethos-close-btn">&times;</button>
            </div>
            <div class="aethos-chat-body">
                <div id="aethos-messages"></div>
                <div class="aethos-input-area">
                    <input type="text" id="aethos-input" placeholder="Type a message...">
                    <button id="aethos-send-btn">Send</button>
                </div>
            </div>
            <button id="aethos-toggle-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill="currentColor"/>
                </svg>
            </button>
        </div>
        <?php
    }

}
