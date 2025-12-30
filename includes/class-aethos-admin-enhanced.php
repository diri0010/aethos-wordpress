<?php

/**
 * Enhanced admin functionality for the plugin.
 *
 * @since      1.0.0
 *
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Admin_Enhanced extends Aethos_Admin {

    /**
     * The analytics instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      Aethos_Analytics    $analytics    The analytics instance.
     */
    private $analytics;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        parent::__construct( $plugin_name, $version );
        
        require_once plugin_dir_path( __FILE__ ) . 'class-aethos-analytics.php';
        $this->analytics = new Aethos_Analytics();
    }

    /**
     * Register additional settings.
     *
     * @since    1.0.0
     */
    public function register_enhanced_settings() {
        // Knowledge base content sources
        register_setting( 'aethos_options', 'aethos_kb_pages', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        register_setting( 'aethos_options', 'aethos_kb_posts', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        register_setting( 'aethos_options', 'aethos_kb_woocommerce', array(
            'type' => 'boolean',
            'default' => false
        ));
        
        register_setting( 'aethos_options', 'aethos_kb_custom_post_types', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
        
        register_setting( 'aethos_options', 'aethos_kb_acf', array(
            'type' => 'boolean',
            'default' => false
        ));
        
        // Knowledge Base Settings - Pages
        register_setting( 'aethos_options', 'aethos_kb_include_all_pages', array(
            'type' => 'boolean',
            'default' => true
        ));
        register_setting( 'aethos_options', 'aethos_kb_included_pages', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_excluded_pages', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_pages_auto_sync', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        // Knowledge Base Settings - Posts
        register_setting( 'aethos_options', 'aethos_kb_include_all_posts', array(
            'type' => 'boolean',
            'default' => true
        ));
        register_setting( 'aethos_options', 'aethos_kb_included_posts', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_excluded_posts', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_excluded_categories', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_posts_auto_sync', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        // Knowledge Base Settings - WooCommerce Products
        register_setting( 'aethos_options', 'aethos_kb_include_all_woo_products', array(
            'type' => 'boolean',
            'default' => true
        ));
        register_setting( 'aethos_options', 'aethos_kb_included_woo_products', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_excluded_woo_products', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_woo_products_auto_sync', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        // Knowledge Base Settings - WooCommerce Categories
        register_setting( 'aethos_options', 'aethos_kb_include_all_woo_categories', array(
            'type' => 'boolean',
            'default' => true
        ));
        register_setting( 'aethos_options', 'aethos_kb_included_woo_categories', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_excluded_woo_categories', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_woo_categories_auto_sync', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        // Register settings for each custom post type dynamically
        $args = array(
            'public' => true,
            '_builtin' => false
        );
        $custom_post_types = get_post_types( $args, 'objects' );
        
        foreach ( $custom_post_types as $cpt ) {
            // Skip WooCommerce product post type (handled separately)
            if ( $cpt->name === 'product' ) {
                continue;
            }
            
            register_setting( 'aethos_options', "aethos_kb_include_all_{$cpt->name}", array(
                'type' => 'boolean',
                'default' => false
            ));
            register_setting( 'aethos_options', "aethos_kb_included_{$cpt->name}", array(
                'type' => 'array',
                'default' => array()
            ));
            register_setting( 'aethos_options', "aethos_kb_excluded_{$cpt->name}", array(
                'type' => 'array',
                'default' => array()
            ));
            register_setting( 'aethos_options', "aethos_kb_{$cpt->name}_auto_sync", array(
                'type' => 'boolean',
                'default' => false
            ));
        }
        
        // Additional appearance settings
        register_setting( 'aethos_appearance', 'aethos_chat_subtitle', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Typically replies instantly'
        ));
        
        // Additional appearance settings for revamped UI
        register_setting( 'aethos_appearance', 'aethos_button_text', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Send'
        ));
        
        register_setting( 'aethos_appearance', 'aethos_placeholder_text', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Type your message...'
        ));
        
        register_setting( 'aethos_appearance', 'aethos_header_title_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#FFFFFF'
        ));
        
        register_setting( 'aethos_appearance', 'aethos_header_subtitle_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#FFFFFF'
        ));

        // Uninstall settings
        register_setting( 'aethos_options', 'aethos_delete_on_uninstall', array(
            'type' => 'boolean',
            'default' => false
        ));
    }

    /**
     * AJAX handler for getting analytics data.
     *
     * @since    1.0.0
     */
    public function get_analytics_data() {
        check_ajax_referer( 'aethos_get_analytics', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        
        $date_range = isset( $_POST['date_range'] ) ? sanitize_text_field( wp_unslash( $_POST['date_range'] ) ) : 'last_7_days';
        $date_from = isset( $_POST['date_from'] ) ? sanitize_text_field( wp_unslash( $_POST['date_from'] ) ) : null;
        $date_to = isset( $_POST['date_to'] ) ? sanitize_text_field( wp_unslash( $_POST['date_to'] ) ) : null;
        
        // Calculate date range
        list( $date_from, $date_to ) = $this->calculate_date_range( $date_range, $date_from, $date_to );
        
        // Convert date_range to SaaS period format
        $saas_period = '30d';
        switch ( $date_range ) {
            case 'last_7_days':
                $saas_period = '7d';
                break;
            case 'last_30_days':
            case 'this_month':
                $saas_period = '30d';
                break;
            case 'last_90_days':
                $saas_period = '90d';
                break;
            default:
                $saas_period = '30d';
        }
        
        // Try to get stats from SaaS for consistent billing counts
        $saas_stats = $this->analytics->get_statistics_from_saas( $saas_period );
        
        if ( ! is_wp_error( $saas_stats ) ) {
            // SaaS succeeded - use SaaS conversation count, local for details
            $local_stats = $this->analytics->get_statistics( $date_from, $date_to );
            
            // Merge: SaaS for conversation count, local for other details
            $stats = array(
                'total_conversations' => $saas_stats['total_conversations'],
                'unique_users' => $saas_stats['unique_visitors'],
                'avg_duration' => $local_stats['avg_duration'],
                'avg_feedback_score' => $local_stats['avg_feedback_score'],
                'source' => 'saas'
            );
        } else {
            // SaaS failed - use local data
            $stats = $this->analytics->get_statistics( $date_from, $date_to );
            $stats['source'] = 'local';
        }
        
        // Get volume data (always from local)
        $volume = $this->analytics->get_volume_by_day( $date_from, $date_to );
        
        // Get top topics (always from local)
        $topics = $this->analytics->get_top_topics( 5, $date_from, $date_to );
        
        // Get recent conversations (always from local)
        $conversations = $this->analytics->get_conversations( array(
            'limit' => 10,
            'date_from' => $date_from,
            'date_to' => $date_to
        ) );
        
        // Format stats as metrics for UI
        $metrics = array(
            'total_conversations' => isset($stats['total_conversations']) ? $stats['total_conversations'] : 0,
            'unique_users' => isset($stats['unique_users']) ? $stats['unique_users'] : 0,
            'avg_duration' => isset($stats['avg_duration']) ? $stats['avg_duration'] : 0,
            'feedback_score' => isset($stats['avg_feedback_score']) ? $stats['avg_feedback_score'] : 0,
            'conversations_change' => '+0%',
            'duration_change' => '-0%',
            'users_change' => '+0%',
            'feedback_change' => '+0%',
            'source' => isset($stats['source']) ? $stats['source'] : 'local'
        );
        
        // Get feedback summary (upvotes/downvotes)
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_conversations';
        
        // Count upvotes (score=5) and downvotes (score=1)
        $upvotes = 0;
        $downvotes = 0;
        
        $feedback_query = "SELECT feedback_score, COUNT(*) as count FROM $table_name WHERE feedback_score IS NOT NULL";
        if (!empty($date_from)) {
            $feedback_query .= $wpdb->prepare(" AND created_at >= %s", $date_from);
        }
        if (!empty($date_to)) {
            $feedback_query .= $wpdb->prepare(" AND created_at <= %s", $date_to);
        }
        $feedback_query .= " GROUP BY feedback_score";
        
        $results = $wpdb->get_results($feedback_query);
        foreach ($results as $row) {
            $score = intval($row->feedback_score);
            if ($score == 5) {
                $upvotes = intval($row->count);
            } elseif ($score == 1) {
                $downvotes = intval($row->count);
            }
        }
        
        $feedback_summary = array(
            'upvotes' => $upvotes,
            'downvotes' => $downvotes
        );
        
        wp_send_json_success( array(
            'metrics' => $metrics,
            'feedback_summary' => $feedback_summary
        ) );
    }

    /**
     * AJAX handler for exporting analytics data.
     *
     * @since    1.0.0
     */
    public function export_analytics() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        
        $date_range = isset( $_POST['date_range'] ) ? sanitize_text_field( wp_unslash( $_POST['date_range'] ) ) : 'last_7_days';
        $date_from = isset( $_POST['date_from'] ) ? sanitize_text_field( wp_unslash( $_POST['date_from'] ) ) : null;
        $date_to = isset( $_POST['date_to'] ) ? sanitize_text_field( wp_unslash( $_POST['date_to'] ) ) : null;
        
        list( $date_from, $date_to ) = $this->calculate_date_range( $date_range, $date_from, $date_to );
        
        $csv = $this->analytics->export_to_csv( array(
            'date_from' => $date_from,
            'date_to' => $date_to
        ) );
        
        wp_send_json_success( array( 'csv' => $csv ) );
    }

    /**
     * AJAX handler for clearing conversation logs.
     *
     * @since    1.0.0
     */
    public function clear_conversation_logs() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        
        $result = $this->analytics->clear_all_logs();
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => 'All conversation logs have been cleared.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to clear conversation logs.' ) );
        }
    }

    /**
     * AJAX handler for regenerating API key.
     *
     * @since    1.0.0
     */
    public function regenerate_api_key() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        
        // Generate a new API key
        $new_key = $this->generate_api_key();
        
        // Save the new key
        update_option( 'aethos_api_key', $new_key );
        
        wp_send_json_success( array( 
            'message' => 'API key regenerated successfully.',
            'key' => $new_key
        ) );
    }

    /**
     * AJAX handler for uploading chat icon.
     *
     * @since    1.0.0
     */
    public function upload_chat_icon() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        
        if ( ! isset( $_FILES['file'] ) ) {
            wp_send_json_error( array( 'message' => 'No file uploaded.' ) );
        }
        
        $file = $_FILES['file'];
        
        // Validate file type
        $allowed_types = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
        if ( ! in_array( $file['type'], $allowed_types ) ) {
            wp_send_json_error( array( 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.' ) );
        }
        
        // Handle the upload
        $upload = wp_handle_upload( $file, array( 'test_form' => false ) );
        
        if ( isset( $upload['error'] ) ) {
            wp_send_json_error( array( 'message' => $upload['error'] ) );
        }
        
        // Save the URL
        update_option( 'aethos_chat_icon', $upload['url'] );
        
        wp_send_json_success( array( 
            'message' => 'Icon uploaded successfully.',
            'url' => $upload['url']
        ) );
    }

    /**
     * Calculate date range based on preset or custom dates.
     *
     * @since    1.0.0
     * @param    string   $range        Preset range.
     * @param    string   $date_from    Custom start date.
     * @param    string   $date_to      Custom end date.
     * @return   array                  Array with start and end dates.
     */
    private function calculate_date_range( $range, $date_from = null, $date_to = null ) {
        $now = current_time( 'mysql' );
        
        switch ( $range ) {
            case 'last_7_days':
                $date_from = date( 'Y-m-d H:i:s', strtotime( '-7 days', strtotime( $now ) ) );
                $date_to = $now;
                break;
            
            case 'last_30_days':
                $date_from = date( 'Y-m-d H:i:s', strtotime( '-30 days', strtotime( $now ) ) );
                $date_to = $now;
                break;
            
            case 'last_90_days':
                $date_from = date( 'Y-m-d H:i:s', strtotime( '-90 days', strtotime( $now ) ) );
                $date_to = $now;
                break;
            
            case 'all_time':
                $date_from = null; // No date filter - get all data
                $date_to = null;
                break;
            
            case 'this_month':
                $date_from = date( 'Y-m-01 00:00:00', strtotime( $now ) );
                $date_to = $now;
                break;
            
            case 'custom':
                if ( ! $date_from || ! $date_to ) {
                    $date_from = date( 'Y-m-d H:i:s', strtotime( '-7 days', strtotime( $now ) ) );
                    $date_to = $now;
                }
                break;
            
            default:
                $date_from = date( 'Y-m-d H:i:s', strtotime( '-7 days', strtotime( $now ) ) );
                $date_to = $now;
        }
        
        return array( $date_from, $date_to );
    }

    /**
     * Generate a random API key.
     *
     * @since    1.0.0
     * @return   string    The generated API key.
     */
    private function generate_api_key() {
        return 'ak_' . bin2hex( random_bytes( 32 ) );
    }

    /**
     * AJAX handler for resetting appearance settings to defaults.
     *
     * @since    1.0.0
     */
    public function reset_appearance_settings() {
        check_ajax_referer( 'aethos_reset_appearance', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Insufficient permissions' );
        }
        
        // Define appearance defaults
        $appearance_defaults = array(
            'aethos_chat_icon' => AETHOS_PLUGIN_URL . 'assets/images/avatars/Aethos-Bubble-Icon.png',
            'aethos_header_title' => 'Aethos AI Assistant',
            'aethos_chat_subtitle' => 'Typically replies instantly',
            'aethos_primary_color' => '#0052CC',
            'aethos_user_text_color' => '#FFFFFF',
            'aethos_ai_bubble_color' => '#F3F4F6',
            'aethos_input_bg_color' => '#FFFFFF',
            'aethos_header_title_color' => '#FFFFFF',
            'aethos_header_subtitle_color' => '#FFFFFF',
            'aethos_font_family' => 'Roboto',
            'aethos_font_size' => 14,
            'aethos_widget_position' => 'bottom-right',
            'aethos_widget_size' => 'medium',
            'aethos_placeholder_text' => 'Type your message...',
            'aethos_button_text' => 'Send'
        );
        
        // Update each setting
        foreach ( $appearance_defaults as $key => $value ) {
            update_option( $key, $value );
        }
        
        wp_send_json_success( 'Appearance settings reset to defaults' );
    }
    
    /**
     * AJAX handler for resetting behavior settings to defaults.
     *
     * @since    1.0.0
     */
    public function reset_behavior_settings() {
        check_ajax_referer( 'aethos_reset_behavior', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Insufficient permissions' );
        }
        
        // Define behavior defaults
        $behavior_defaults = array(
            'aethos_chatbot_persona' => 'friendly',
            'aethos_fallback_response' => 'I apologize, but I don\'t have enough information to answer that question accurately. Could you try rephrasing it or asking something else?',
            'aethos_greeting_message' => 'Welcome! How can I help you today?',
            'aethos_auto_open' => false,
            'aethos_auto_open_delay' => 3,
            'aethos_offline_message' => 'We\'re currently offline. Please leave a message and we\'ll get back to you.'
        );
        
        // Update each setting
        foreach ( $behavior_defaults as $key => $value ) {
            update_option( $key, $value );
        }
        
         wp_send_json_success( 'Behavior settings reset to defaults' );
    }
    
    /**
     * AJAX handler for resetting Knowledge Base settings to defaults.
     *
     * @since    1.0.0
     */
    public function reset_knowledge_base_settings() {
        check_ajax_referer( 'aethos_reset_knowledge_base', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Insufficient permissions' );
        }
        
        // Define Knowledge Base defaults
        $kb_defaults = array(
            // Pages
            'aethos_kb_include_all_pages' => true,
            'aethos_kb_included_pages' => array(),
            'aethos_kb_excluded_pages' => array(),
            'aethos_kb_pages_auto_sync' => true,
            
            // Posts
            'aethos_kb_include_all_posts' => true,
            'aethos_kb_included_posts' => array(),
            'aethos_kb_excluded_posts' => array(),
            'aethos_kb_excluded_categories' => array(),
            'aethos_kb_posts_auto_sync' => true,
            
            // WooCommerce Products
            'aethos_kb_include_all_woo_products' => true,
            'aethos_kb_included_woo_products' => array(),
            'aethos_kb_excluded_woo_products' => array(),
            'aethos_kb_woo_products_auto_sync' => true,
            
            // WooCommerce Categories
            'aethos_kb_include_all_woo_categories' => true,
            'aethos_kb_included_woo_categories' => array(),
            'aethos_kb_excluded_woo_categories' => array(),
            'aethos_kb_woo_categories_auto_sync' => true
        );
        
        // Update each setting
        foreach ( $kb_defaults as $key => $value ) {
            update_option( $key, $value );
        }
        
        // Reset custom post types
        $args = array(
            'public' => true,
            '_builtin' => false
        );
        $custom_post_types = get_post_types( $args, 'objects' );
        
        foreach ( $custom_post_types as $cpt ) {
            // Skip WooCommerce product post type (handled above)
            if ( $cpt->name === 'product' ) {
                continue;
            }
            
            update_option( "aethos_kb_include_all_{$cpt->name}", true );
            update_option( "aethos_kb_included_{$cpt->name}", array() );
            update_option( "aethos_kb_excluded_{$cpt->name}", array() );
            update_option( "aethos_kb_{$cpt->name}_auto_sync", true );
        }
        
        wp_send_json_success( 'Knowledge Base settings reset to defaults' );
    }
    
    /**
     * AJAX handler for searching content (pages, posts, categories, tags).
     *
     * @since    1.0.0
     */
    public function search_content() {
        check_ajax_referer( 'aethos_search_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Insufficient permissions' );
        }
        
        $query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';
        $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'pages';
        $post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';
        
        $results = array();
        
        if ( $type === 'pages' || $type === 'posts' || $type === 'custom' ) {
            // Determine post type
            if ( ! empty( $post_type ) ) {
                $query_post_type = $post_type;
            } elseif ( $type === 'pages' ) {
                $query_post_type = 'page';
            } elseif ( $type === 'posts' ) {
                $query_post_type = 'post';
            } else {
                $query_post_type = array( 'page', 'post' );
            }
            
            // Build query args
            $args = array(
                'post_type' => $query_post_type,
                'posts_per_page' => empty( $query ) ? 100 : 20,
                'post_status' => 'publish',
                'orderby' => empty( $query ) ? 'title' : 'relevance',
                'order' => 'ASC'
            );
            
            // Add search query if provided
            if ( ! empty( $query ) ) {
                $args['s'] = $query;
            }
            
            $search_query = new WP_Query( $args );
            
            if ( $search_query->have_posts() ) {
                while ( $search_query->have_posts() ) {
                    $search_query->the_post();
                    $results[] = array(
                        'id' => get_the_ID(),
                        'title' => get_the_title(),
                        'type' => get_post_type_object( get_post_type() )->labels->singular_name
                    );
                }
                wp_reset_postdata();
            }
        } elseif ( $type === 'categories' ) {
            // Search for categories and tags
            $terms = get_terms( array(
                'taxonomy' => array( 'category', 'post_tag' ),
                'search' => $query,
                'number' => 20,
                'hide_empty' => false
            ));
            
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    $results[] = array(
                        'id' => $term->term_id,
                        'title' => $term->name,
                        'type' => $term->taxonomy === 'category' ? 'Category' : 'Tag'
                    );
                }
            }
        } elseif ( $type === 'woo-products' ) {
            // Search for WooCommerce products
            if ( class_exists( 'WooCommerce' ) ) {
                $args = array(
                    's' => $query,
                    'post_type' => 'product',
                    'posts_per_page' => 20,
                    'post_status' => 'publish',
                    'orderby' => 'relevance'
                );
                
                $search_query = new WP_Query( $args );
                
                if ( $search_query->have_posts() ) {
                    while ( $search_query->have_posts() ) {
                        $search_query->the_post();
                        $results[] = array(
                            'id' => get_the_ID(),
                            'title' => get_the_title(),
                            'type' => 'Product'
                        );
                    }
                    wp_reset_postdata();
                }
            }
        } elseif ( $type === 'woo-categories' ) {
            // Search for WooCommerce product categories
            if ( class_exists( 'WooCommerce' ) ) {
                $terms = get_terms( array(
                    'taxonomy' => 'product_cat',
                    'search' => $query,
                    'number' => 20,
                    'hide_empty' => false
                ));
                
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $results[] = array(
                            'id' => $term->term_id,
                            'title' => $term->name,
                            'type' => 'Product Category'
                        );
                    }
                }
            }
        }
        
        wp_send_json_success( $results );
    }
    
    /**
     * Load Q&A entries from database
     *
     * @since    1.0.0
     */
    public function ajax_load_qna() {
        check_ajax_referer( 'aethos_qna_nonce', 'nonce' );
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_qna';
        
        $qna_entries = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A );
        
        if ( ! is_array( $qna_entries ) ) {
            $qna_entries = array();
        }
        
        wp_send_json_success( array( 'entries' => $qna_entries ) );
    }
    
    /**
     * Save Q&A entry to database
     *
     * @since    1.0.0
     */
    public function ajax_save_qna() {
        check_ajax_referer( 'aethos_qna_nonce', 'nonce' );
        
        if ( ! isset( $_POST['data'] ) ) {
            wp_send_json_error( array( 'message' => 'No data provided' ) );
        }
        
        $data = $_POST['data'];
        
        // Validate required fields
        if ( empty( $data['question'] ) || empty( $data['answer'] ) ) {
            wp_send_json_error( array( 'message' => 'Question and answer are required' ) );
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_qna';
        
        // Sanitize entry data
        $entry = array(
            'question' => sanitize_text_field( $data['question'] ),
            'answer' => sanitize_textarea_field( $data['answer'] ),
            'category' => sanitize_text_field( $data['category'] ),
            'priority' => sanitize_text_field( $data['priority'] ),
            'status' => sanitize_text_field( $data['status'] ),
            'updated_at' => current_time( 'mysql' )
        );
        
        $id = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
        
        if ( $id > 0 ) {
            // Update existing entry
            $result = $wpdb->update(
                $table_name,
                $entry,
                array( 'id' => $id ),
                array( '%s', '%s', '%s', '%s', '%s', '%s' ),
                array( '%d' )
            );
            
            if ( $result === false ) {
                wp_send_json_error( array( 'message' => 'Failed to update entry' ) );
            }
            
            $entry['id'] = $id;
        } else {
            // Add new entry
            $entry['created_at'] = current_time( 'mysql' );
            
            $result = $wpdb->insert(
                $table_name,
                $entry,
                array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
            );
            
            if ( $result === false ) {
                wp_send_json_error( array( 'message' => 'Failed to create entry' ) );
            }
            
            $entry['id'] = $wpdb->insert_id;
        }
        
        wp_send_json_success( array( 'entry' => $entry ) );
    }
    
    /**
     * Delete Q&A entry from database
     *
     * @since    1.0.0
     */
    public function ajax_delete_qna() {
        check_ajax_referer( 'aethos_qna_nonce', 'nonce' );
        
        if ( ! isset( $_POST['id'] ) ) {
            wp_send_json_error( array( 'message' => 'No ID provided' ) );
        }
        
        $id = intval( $_POST['id'] );
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_qna';
        
        $result = $wpdb->delete(
            $table_name,
            array( 'id' => $id ),
            array( '%d' )
        );
        
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => 'Failed to delete entry' ) );
        }
        
        wp_send_json_success( array( 'message' => 'Entry deleted' ) );
    }
    
    /**
     * Bulk action on Q&A entries
     *
     * @since    1.0.0
     */
    public function ajax_bulk_action_qna() {
        check_ajax_referer( 'aethos_qna_nonce', 'nonce' );
        
        if ( ! isset( $_POST['action_type'] ) || ! isset( $_POST['ids'] ) ) {
            wp_send_json_error( array( 'message' => 'Invalid request' ) );
        }
        
        $action_type = sanitize_text_field( $_POST['action_type'] );
        $ids = array_map( 'intval', $_POST['ids'] );
        
        if ( empty( $ids ) ) {
            wp_send_json_error( array( 'message' => 'No items selected' ) );
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_qna';
        $ids_placeholder = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
        
        if ( $action_type === 'delete' ) {
            // Delete selected entries
            $sql = "DELETE FROM $table_name WHERE id IN ($ids_placeholder)";
            $result = $wpdb->query( $wpdb->prepare( $sql, $ids ) );
        } else {
            // Update status for selected entries
            $new_status = ( $action_type === 'publish' ) ? 'published' : 'draft';
            $updated_at = current_time( 'mysql' );
            
            $sql = "UPDATE $table_name SET status = %s, updated_at = %s WHERE id IN ($ids_placeholder)";
            $params = array_merge( array( $new_status, $updated_at ), $ids );
            $result = $wpdb->query( $wpdb->prepare( $sql, $params ) );
        }
        
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => 'Bulk action failed' ) );
        }
        
        wp_send_json_success( array( 'message' => 'Bulk action completed' ) );
    }
    
    /**
     * AJAX handler for clearing vectors (cache).
     *
     * @since    1.0.0
     */
    public function clear_cache() {
        check_ajax_referer( 'aethos_clear_cache', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_vectors';
        
        // Truncate the vectors table
        $result = $wpdb->query( "TRUNCATE TABLE $table_name" );
        
        if ( $result !== false ) {
            wp_send_json_success( array( 'message' => 'Vector cache cleared successfully.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to clear vector cache.' ) );
        }
    }

    /**
     * AJAX handler for deleting all conversations.
     *
     * @since    1.0.0
     */
    public function delete_all_conversations() {
        check_ajax_referer( 'aethos_delete_all_conversations', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_conversations';
        
        // Truncate the Conversations table
        $result = $wpdb->query( "TRUNCATE TABLE $table_name" );
        
        if ( $result !== false ) {
            wp_send_json_success( array( 'message' => 'All conversations deleted successfully.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to delete conversations.' ) );
        }
    }

    /**
     * Cron job handler for data retention cleanup.
     * Deletes conversations older than the retention period.
     *
     * @since    1.0.0
     */
    public function run_data_retention_cleanup() {
        $retention_days = intval( get_option( 'aethos_data_retention', 30 ) );
        
        // If retention is 0, don't delete anything
        if ( $retention_days <= 0 ) {
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_conversations';
        
        // Calculate cutoff date
        $cutoff_date = date( 'Y-m-d H:i:s', strtotime( "-{$retention_days} days" ) );
        
        // Delete old conversations
        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $table_name WHERE created_at < %s",
                $cutoff_date
            )
        );
        
        // Log the cleanup if debug mode is enabled
        if ( get_option( 'aethos_debug_mode', false ) ) {
            aethos_log("Data Retention: Deleted {$deleted} conversations older than {$retention_days} days");
        }
    }

    /**
     * AJAX handler for resetting all settings.
     *
     * @since    1.0.0
     */
    public function reset_all_settings() {
        check_ajax_referer( 'aethos_reset_settings', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        
        // Reset Options
        $options_to_reset = array(
            'aethos_api_key', 'aethos_api_endpoint', 'aethos_connection_status',
            'aethos_widget_position', 'aethos_widget_size', 'aethos_primary_color',
            'aethos_accent_color', 'aethos_success_color', 'aethos_error_color',
            'aethos_dark_mode', 'aethos_chat_icon', 'aethos_user_text_color',
            'aethos_ai_bubble_color', 'aethos_input_bg_color', 'aethos_font_family', 'aethos_font_size',
            'aethos_auto_open', 'aethos_auto_open_delay', 'aethos_greeting_message',
            'aethos_offline_message', 'aethos_persona', 'aethos_greeting',
            'aethos_log_conversations', 'aethos_log_ip', 'aethos_header_title',
            'aethos_chatbot_persona', 'aethos_fallback_response', 'aethos_data_retention',
            'aethos_disable_ip_logging', 'aethos_enable_minification', 'aethos_request_timeout',
            'aethos_debug_mode', 'aethos_cache_enabled', 'aethos_rate_limit',
            'aethos_enable_analytics', 'aethos_ai_model', 'aethos_global_visibility',
            'aethos_api_key', 'aethos_api_endpoint', 'aethos_connection_status',
            'aethos_include_all_pages', 'aethos_included_pages', 'aethos_excluded_pages',
            'aethos_include_all_categories', 'aethos_included_categories', 'aethos_excluded_categories',
            'aethos_include_all_woo_products', 'aethos_included_woo_products', 'aethos_excluded_woo_products',
            'aethos_include_all_woo_categories', 'aethos_included_woo_categories', 'aethos_excluded_woo_categories',
            'aethos_delete_on_uninstall', 'aethos_qna_entries',
            // Knowledge Base Content Sources - Pages
            'aethos_kb_include_all_pages', 'aethos_kb_included_pages', 'aethos_kb_excluded_pages', 'aethos_kb_pages_auto_sync',
            // Knowledge Base Content Sources - Posts
            'aethos_kb_include_all_posts', 'aethos_kb_included_posts', 'aethos_kb_excluded_posts', 'aethos_kb_excluded_categories', 'aethos_kb_posts_auto_sync',
            // Knowledge Base Content Sources - WooCommerce Products
            'aethos_kb_include_all_woo_products', 'aethos_kb_included_woo_products', 'aethos_kb_excluded_woo_products', 'aethos_kb_woo_products_auto_sync',
            // Knowledge Base Content Sources - WooCommerce Categories
            'aethos_kb_include_all_woo_categories', 'aethos_kb_included_woo_categories', 'aethos_kb_excluded_woo_categories', 'aethos_kb_woo_categories_auto_sync'
        );
        
        // Also reset custom post type KB settings dynamically
        $cpt_args = array(
            'public' => true,
            '_builtin' => false
        );
        $custom_post_types = get_post_types($cpt_args, 'objects');
        
        foreach ($custom_post_types as $cpt) {
            if ($cpt->name === 'product') continue; // Skip WooCommerce (handled above)
            
            $options_to_reset[] = "aethos_kb_include_all_{$cpt->name}";
            $options_to_reset[] = "aethos_kb_included_{$cpt->name}";
            $options_to_reset[] = "aethos_kb_excluded_{$cpt->name}";
            $options_to_reset[] = "aethos_kb_{$cpt->name}_auto_sync";
        }
        
        foreach ( $options_to_reset as $option ) {
            delete_option( $option );
        }
        
        // Truncate Tables
        global $wpdb;
        $qna_table = $wpdb->prefix . 'aethos_qna';
        $conversations_table = $wpdb->prefix . 'aethos_conversations';
        $vectors_table = $wpdb->prefix . 'aethos_vectors';
        $sync_log_table = $wpdb->prefix . 'aethos_sync_log';
        
        $wpdb->query( "TRUNCATE TABLE $qna_table" );
        $wpdb->query( "TRUNCATE TABLE $conversations_table" );
        $wpdb->query( "TRUNCATE TABLE $vectors_table" );
        $wpdb->query( "TRUNCATE TABLE $sync_log_table" );
        
        // Also delete discovered content options
        delete_option( 'aethos_excluded_posts' );
        
        // Set KB include defaults explicitly (prevents empty string issue on form save)
        update_option( 'aethos_kb_include_all_pages', '1' );
        update_option( 'aethos_kb_include_all_posts', '1' );
        update_option( 'aethos_kb_include_all_woo_products', '1' );
        update_option( 'aethos_kb_include_all_woo_categories', '1' );
        
        // Also set for custom post types
        foreach ( $custom_post_types as $cpt ) {
            if ( $cpt->name === 'product' ) continue;
            update_option( "aethos_kb_include_all_{$cpt->name}", '1' );
        }
        
        $orchestrator = new Aethos_Scan_Orchestrator();

        aethos_log('Starting full scan from AJAX');
        $result = $orchestrator->start_full_scan();
        aethos_log('Full scan result: ' . print_r($result, true));
        
        wp_send_json_success( array( 'message' => 'All settings and data have been reset to defaults.' ) );
    }

    /**
     * Get next available Q&A ID
     *
     * @since    1.0.0
     * @param    array    $entries    Existing Q&A entries
     * @return   int                  Next available ID
     */
    private function get_next_qna_id( $entries ) {
        if ( empty( $entries ) ) {
            return 1;
        }
        
        $max_id = 0;
        foreach ( $entries as $entry ) {
            if ( isset( $entry['id'] ) && $entry['id'] > $max_id ) {
                $max_id = $entry['id'];
            }
        }
        
        return $max_id + 1;
    }

    /**
     * AJAX handler for excluding post from knowledge base
     * Marks as excluded (vectors filtered at search time, not deleted)
     *
     * @since    1.0.0
     */
    public function exclude_post_from_kb() {
        check_ajax_referer( 'aethos_exclude_post', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'Invalid post ID' ) );
        }

        $post = get_post( $post_id );
        if ( ! $post ) {
            wp_send_json_error( array( 'message' => 'Post not found' ) );
        }

        // Note: Vectors are NOT deleted - they're filtered at search time
        // This allows easy re-inclusion without needing to re-scan

        // Add to appropriate KB exclusion list based on post type
    $exclusion_added = false;
    $exclusion_list_name = '';

    switch ( $post->post_type ) {
        case 'page':
            $excluded_pages = get_option( 'aethos_kb_excluded_pages', array() );
            if ( ! is_array( $excluded_pages ) ) {
                $excluded_pages = array();
            }
            if ( ! in_array( $post_id, $excluded_pages ) ) {
                $excluded_pages[] = $post_id;
                update_option( 'aethos_kb_excluded_pages', $excluded_pages );
                $exclusion_added = true;
                $exclusion_list_name = 'Pages';
                aethos_log("Added page $post_id to aethos_kb_excluded_pages");
            }
            break;

        case 'post':
            // For posts, add the post itself to excluded posts
            $excluded_posts = get_option( 'aethos_kb_excluded_posts', array() );
            if ( ! is_array( $excluded_posts ) ) {
                $excluded_posts = array();
            }
            if ( ! in_array( $post_id, $excluded_posts ) ) {
                $excluded_posts[] = $post_id;
                update_option( 'aethos_kb_excluded_posts', $excluded_posts );
                $exclusion_added = true;
                $exclusion_list_name = 'Posts';
                aethos_log("Added post $post_id to aethos_kb_excluded_posts");
            }
            break;

        case 'product':
            $excluded_products = get_option( 'aethos_kb_excluded_woo_products', array() );
            if ( ! is_array( $excluded_products ) ) {
                $excluded_products = array();
            }
            if ( ! in_array( $post_id, $excluded_products ) ) {
                $excluded_products[] = $post_id;
                update_option( 'aethos_kb_excluded_woo_products', $excluded_products );
                $exclusion_added = true;
                $exclusion_list_name = 'WooCommerce Products';
                aethos_log("Added product $post_id to aethos_kb_excluded_woo_products");
            }
            break;

        default:
            // Custom post type - use KB naming convention
            $option_name = "aethos_kb_excluded_{$post->post_type}";
            $excluded_items = get_option( $option_name, array() );
            if ( ! is_array( $excluded_items ) ) {
                $excluded_items = array();
            }
            if ( ! in_array( $post_id, $excluded_items ) ) {
                $excluded_items[] = $post_id;
                update_option( $option_name, $excluded_items );
                $exclusion_added = true;
                $exclusion_list_name = ucfirst( $post->post_type );
                aethos_log("Added {$post->post_type} $post_id to $option_name");
            }
            break;
    }

    aethos_log("Exclusion result - added: " . ($exclusion_added ? 'yes' : 'no') . ", list: $exclusion_list_name");

    wp_send_json_success( array(
        'message' => 'Content excluded from knowledge base',
        'post_title' => $post->post_title,
        'post_type' => $post->post_type,
        'exclusion_added' => $exclusion_added,
        'exclusion_list' => $exclusion_list_name
    ));
    }

    /**
     * AJAX handler for including a post back to KB (removing from exclusion list)
     *
     * @since    1.0.0
     */
    public function include_post_to_kb() {
        // Verify nonce
        check_ajax_referer( 'aethos_toggle_exclude', 'nonce' );

        // Check permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'Invalid post ID' ) );
        }

        $post = get_post( $post_id );
        if ( ! $post ) {
            wp_send_json_error( array( 'message' => 'Post not found' ) );
        }

        $inclusion_list_name = '';
        $removed_from_exclusion = false;

        aethos_log("Including post ID $post_id, type: {$post->post_type}");

        switch ( $post->post_type ) {
            case 'page':
                $excluded_pages = get_option( 'aethos_kb_excluded_pages', array() );
                if ( is_array( $excluded_pages ) && in_array( $post_id, $excluded_pages ) ) {
                    $excluded_pages = array_diff( $excluded_pages, array( $post_id ) );
                    update_option( 'aethos_kb_excluded_pages', $excluded_pages );
                    $removed_from_exclusion = true;
                    $inclusion_list_name = 'Pages';
                    aethos_log("Removed page $post_id from aethos_kb_excluded_pages");
                }
                break;

            case 'post':
                $excluded_posts = get_option( 'aethos_kb_excluded_posts', array() );
                if ( is_array( $excluded_posts ) && in_array( $post_id, $excluded_posts ) ) {
                    $excluded_posts = array_diff( $excluded_posts, array( $post_id ) );
                    update_option( 'aethos_kb_excluded_posts', $excluded_posts );
                    $removed_from_exclusion = true;
                    $inclusion_list_name = 'Posts';
                    aethos_log("Removed post $post_id from aethos_kb_excluded_posts");
                }
                break;

            case 'product':
                $excluded_products = get_option( 'aethos_kb_excluded_woo_products', array() );
                if ( is_array( $excluded_products ) && in_array( $post_id, $excluded_products ) ) {
                    $excluded_products = array_diff( $excluded_products, array( $post_id ) );
                    update_option( 'aethos_kb_excluded_woo_products', $excluded_products );
                    $removed_from_exclusion = true;
                    $inclusion_list_name = 'WooCommerce Products';
                    aethos_log("Removed product $post_id from aethos_kb_excluded_woo_products");
                }
                break;

            default:
                // Custom post type
                $option_name = "aethos_kb_excluded_{$post->post_type}";
                $excluded_items = get_option( $option_name, array() );
                if ( is_array( $excluded_items ) && in_array( $post_id, $excluded_items ) ) {
                    $excluded_items = array_diff( $excluded_items, array( $post_id ) );
                    update_option( $option_name, $excluded_items );
                    $removed_from_exclusion = true;
                    $inclusion_list_name = ucfirst( $post->post_type );
                    aethos_log("Removed {$post->post_type} $post_id from $option_name");
                }
                break;
        }

        wp_send_json_success( array(
            'message' => 'Content included in knowledge base',
            'post_title' => $post->post_title,
            'post_type' => $post->post_type,
            'removed_from_exclusion' => $removed_from_exclusion,
            'list_name' => $inclusion_list_name
        ) );
    }
}
