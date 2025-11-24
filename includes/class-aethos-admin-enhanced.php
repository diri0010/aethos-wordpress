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
        
        // New granular KB settings - Pages
        register_setting( 'aethos_options', 'aethos_kb_pages_mode', array(
            'type' => 'string',
            'default' => 'include_all'
        ));
        register_setting( 'aethos_options', 'aethos_kb_pages_included', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_pages_excluded', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_pages_auto_sync', array(
            'type' => 'boolean',
            'default' => true
        ));
        
        // New granular KB settings - Posts
        register_setting( 'aethos_options', 'aethos_kb_posts_mode', array(
            'type' => 'string',
            'default' => 'include_all'
        ));
        register_setting( 'aethos_options', 'aethos_kb_posts_included', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_posts_excluded', array(
            'type' => 'array',
            'default' => array()
        ));
        register_setting( 'aethos_options', 'aethos_kb_posts_auto_sync', array(
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
            register_setting( 'aethos_options', "aethos_kb_cpt_{$cpt->name}_mode", array(
                'type' => 'string',
                'default' => 'none'
            ));
            register_setting( 'aethos_options', "aethos_kb_cpt_{$cpt->name}_included", array(
                'type' => 'array',
                'default' => array()
            ));
            register_setting( 'aethos_options', "aethos_kb_cpt_{$cpt->name}_excluded", array(
                'type' => 'array',
                'default' => array()
            ));
            register_setting( 'aethos_options', "aethos_kb_cpt_{$cpt->name}_auto_sync", array(
                'type' => 'boolean',
                'default' => false
            ));
        }
        
        // Additional appearance settings
        register_setting( 'aethos_options', 'aethos_chat_subtitle', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Typically replies instantly'
        ));
        
        // Additional appearance settings for revamped UI
        register_setting( 'aethos_options', 'aethos_button_text', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Send'
        ));
        
        register_setting( 'aethos_options', 'aethos_placeholder_text', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Type your message...'
        ));
        
        register_setting( 'aethos_options', 'aethos_header_title_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#FFFFFF'
        ));
        
        register_setting( 'aethos_options', 'aethos_header_subtitle_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#FFFFFF'
        ));
    }

    /**
     * AJAX handler for getting analytics data.
     *
     * @since    1.0.0
     */
    public function get_analytics_data() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        
        $date_range = isset( $_POST['date_range'] ) ? sanitize_text_field( wp_unslash( $_POST['date_range'] ) ) : 'last_7_days';
        $date_from = isset( $_POST['date_from'] ) ? sanitize_text_field( wp_unslash( $_POST['date_from'] ) ) : null;
        $date_to = isset( $_POST['date_to'] ) ? sanitize_text_field( wp_unslash( $_POST['date_to'] ) ) : null;
        
        // Calculate date range
        list( $date_from, $date_to ) = $this->calculate_date_range( $date_range, $date_from, $date_to );
        
        // Get statistics
        $stats = $this->analytics->get_statistics( $date_from, $date_to );
        
        // Get volume data
        $volume = $this->analytics->get_volume_by_day( $date_from, $date_to );
        
        // Get top topics
        $topics = $this->analytics->get_top_topics( 5, $date_from, $date_to );
        
        // Get recent conversations
        $conversations = $this->analytics->get_conversations( array(
            'limit' => 10,
            'date_from' => $date_from,
            'date_to' => $date_to
        ) );
        
        wp_send_json_success( array(
            'stats' => $stats,
            'volume' => $volume,
            'topics' => $topics,
            'conversations' => $conversations
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
            'aethos_greeting_message' => 'Welcome! How can I help you today?',
            'aethos_primary_color' => '#1400CC',
            'aethos_user_text_color' => '#FFFFFF',
            'aethos_ai_bubble_color' => '#F3F4F6',
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
            'aethos_fallback_response' => 'I\'m sorry, I couldn\'t find an answer to that. Please try rephrasing your question.',
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
        
        $qna_entries = get_option( 'aethos_qna_entries', array() );
        
        // Ensure it's an array
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
        $qna_entries = get_option( 'aethos_qna_entries', array() );
        
        if ( ! is_array( $qna_entries ) ) {
            $qna_entries = array();
        }
        
        // Validate required fields
        if ( empty( $data['question'] ) || empty( $data['answer'] ) ) {
            wp_send_json_error( array( 'message' => 'Question and answer are required' ) );
        }
        
        // Sanitize entry data
        $entry = array(
            'id' => isset( $data['id'] ) ? intval( $data['id'] ) : 0,
            'question' => sanitize_text_field( $data['question'] ),
            'answer' => sanitize_textarea_field( $data['answer'] ),
            'category' => sanitize_text_field( $data['category'] ),
            'priority' => sanitize_text_field( $data['priority'] ),
            'status' => sanitize_text_field( $data['status'] ),
            'updated_at' => current_time( 'mysql' )
        );
        
        if ( $entry['id'] > 0 ) {
            // Update existing entry
            $found = false;
            foreach ( $qna_entries as $index => $existing ) {
                if ( isset( $existing['id'] ) && $existing['id'] === $entry['id'] ) {
                    $entry['created_at'] = isset( $existing['created_at'] ) ? $existing['created_at'] : current_time( 'mysql' );
                    $qna_entries[$index] = $entry;
                    $found = true;
                    break;
                }
            }
            
            if ( ! $found ) {
                wp_send_json_error( array( 'message' => 'Entry not found' ) );
            }
        } else {
            // Add new entry
            $entry['id'] = $this->get_next_qna_id( $qna_entries );
            $entry['created_at'] = current_time( 'mysql' );
            $qna_entries[] = $entry;
        }
        
        update_option( 'aethos_qna_entries', $qna_entries );
        
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
        $qna_entries = get_option( 'aethos_qna_entries', array() );
        
        if ( ! is_array( $qna_entries ) ) {
            $qna_entries = array();
        }
        
        // Filter out the entry with matching ID
        $qna_entries = array_filter( $qna_entries, function( $entry ) use ( $id ) {
            return ! isset( $entry['id'] ) || $entry['id'] !== $id;
        });
        
        // Re-index array
        $qna_entries = array_values( $qna_entries );
        
        update_option( 'aethos_qna_entries', $qna_entries );
        
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
        
        $qna_entries = get_option( 'aethos_qna_entries', array() );
        
        if ( ! is_array( $qna_entries ) ) {
            $qna_entries = array();
        }
        
        if ( $action_type === 'delete' ) {
            // Delete selected entries
            $qna_entries = array_filter( $qna_entries, function( $entry ) use ( $ids ) {
                return ! isset( $entry['id'] ) || ! in_array( $entry['id'], $ids );
            });
            $qna_entries = array_values( $qna_entries );
        } else {
            // Update status for selected entries
            $new_status = ( $action_type === 'publish' ) ? 'published' : 'draft';
            
            foreach ( $qna_entries as $index => $entry ) {
                if ( isset( $entry['id'] ) && in_array( $entry['id'], $ids ) ) {
                    $qna_entries[$index]['status'] = $new_status;
                    $qna_entries[$index]['updated_at'] = current_time( 'mysql' );
                }
            }
        }
        
        update_option( 'aethos_qna_entries', $qna_entries );
        
        wp_send_json_success( array( 'message' => 'Bulk action completed' ) );
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
}
