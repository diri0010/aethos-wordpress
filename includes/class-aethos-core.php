<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Core {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Aethos_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'AETHOS_VERSION' ) ) {
			$this->version = AETHOS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'aethos-chat';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Aethos_Loader. Orchestrates the hooks of the plugin.
	 * - Aethos_Admin. Defines all hooks for the admin area.
	 * - Aethos_Public. Defines all hooks for the public side of the site.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
	        /**
	         * The class responsible for API interactions.
	         */
	        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-api.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-public.php';

        /**
         * Enhanced Admin Functionality
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-api-client.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-vector-storage.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-content-scanner.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-scan-orchestrator.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-admin-enhanced.php';

        /**
         * The class responsible for API client functionality.
         */
        if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-api-client.php' ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-api-client.php';
        }

        /**
         * The class responsible for security.
         */
        if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-security.php' ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-security.php';
        }

	        /**
	         * The class responsible for caching.
	         */
	        if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-cache.php' ) ) {
	            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-cache.php';
	        }

        /**
         * The class responsible for analytics.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-analytics.php';

        /**
         * The enhanced admin class.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-admin-enhanced.php';

        /**
         * The Q&A AJAX handlers.
         */
        if ( is_admin() ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-qna-ajax.php';
        }

        /**
         * The Conversation History class.
         */
        if ( is_admin() ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-conversation-history.php';
        }

        /**
         * The Feedback & Ratings class.
         */
        if ( is_admin() ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-feedback.php';
        }

        /**
         * The Vector Storage class.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-vector-storage.php';

        /**
         * The Content Scanner class.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-content-scanner.php';

        /**
         * The Embeddings API class.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aethos-embeddings.php';

		/**
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 */
		$this->loader = new Aethos_Loader();

		// Register custom cron schedules
		add_filter( 'cron_schedules', array( $this, 'add_custom_cron_schedules' ) );

	}

	/**
	 * Add custom cron schedules
	 *
	 * @since    1.0.0
	 */
	public function add_custom_cron_schedules( $schedules ) {
		$schedules['weekly'] = array(
			'interval' => 604800, // 7 days in seconds
			'display'  => __( 'Once Weekly' )
		);
		$schedules['monthly'] = array(
			'interval' => 2635200, // 30.5 days in seconds
			'display'  => __( 'Once Monthly' )
		);
		return $schedules;
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Aethos_Admin_Enhanced( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
	        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
	        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_enhanced_settings' );
	        $this->loader->add_action( 'wp_ajax_aethos_test_connection', $plugin_admin, 'test_connection' );
	        $this->loader->add_action( 'wp_ajax_aethos_disconnect', $plugin_admin, 'disconnect' );
	        $this->loader->add_action( 'wp_ajax_aethos_get_analytics', $plugin_admin, 'get_analytics_data' );
	        $this->loader->add_action( 'wp_ajax_aethos_export_analytics', $plugin_admin, 'export_analytics' );
	        $this->loader->add_action( 'wp_ajax_aethos_clear_logs', $plugin_admin, 'clear_conversation_logs' );
	        $this->loader->add_action( 'wp_ajax_aethos_regenerate_key', $plugin_admin, 'regenerate_api_key' );
	        $this->loader->add_action( 'wp_ajax_aethos_upload_icon', $plugin_admin, 'upload_chat_icon' );
        $this->loader->add_action( 'wp_ajax_aethos_reset_appearance', $plugin_admin, 'reset_appearance_settings' );
        $this->loader->add_action( 'wp_ajax_aethos_reset_behavior', $plugin_admin, 'reset_behavior_settings' );
        $this->loader->add_action( 'wp_ajax_aethos_search_content', $plugin_admin, 'search_content' );

        // Danger Zone AJAX handlers
        $this->loader->add_action( 'wp_ajax_aethos_clear_cache', $plugin_admin, 'clear_cache' );
        $this->loader->add_action( 'wp_ajax_aethos_delete_all_conversations', $plugin_admin, 'delete_all_conversations' );
        $this->loader->add_action( 'wp_ajax_aethos_reset_settings', $plugin_admin, 'reset_all_settings' );
        
        // Q&A Management AJAX handlers
        $this->loader->add_action( 'wp_ajax_aethos_load_qna', $plugin_admin, 'ajax_load_qna' );
        $this->loader->add_action( 'wp_ajax_aethos_save_qna', $plugin_admin, 'ajax_save_qna' );
        $this->loader->add_action( 'wp_ajax_aethos_delete_qna', $plugin_admin, 'ajax_delete_qna' );
        $this->loader->add_action( 'wp_ajax_aethos_bulk_action_qna', $plugin_admin, 'ajax_bulk_action_qna' );

        // Discovered Content AJAX handlers
        $this->loader->add_action( 'wp_ajax_aethos_scan_now', $plugin_admin, 'scan_now' );
        $this->loader->add_action( 'wp_ajax_aethos_scan_single', $plugin_admin, 'scan_single' );
        $this->loader->add_action( 'wp_ajax_aethos_toggle_exclude', $plugin_admin, 'toggle_exclude' );
        $this->loader->add_action( 'wp_ajax_aethos_exclude_post_from_kb', $plugin_admin, 'exclude_post_from_kb' );
        $this->loader->add_action( 'wp_ajax_aethos_include_post_to_kb', $plugin_admin, 'include_post_to_kb' );
        $this->loader->add_action( 'wp_ajax_aethos_save_scan_schedule', $plugin_admin, 'save_scan_schedule' );
        $this->loader->add_action( 'wp_ajax_aethos_delete_vectors', $plugin_admin, 'delete_vectors' );
        $this->loader->add_action( 'wp_ajax_aethos_remove_all_vectors', $plugin_admin, 'remove_all_vectors' );

        // Automated scan cron hook
        $this->loader->add_action( 'aethos_automated_scan', $plugin_admin, 'run_automated_scan' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Aethos_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'render_widget' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Aethos_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
