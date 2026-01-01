<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Activator {

    /**
     * Current schema version for migrations
     */
    const SCHEMA_VERSION = '1.1.0';

    /**
     * Plugin activation handler
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;
        
        // Load dbDelta function
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // Set all default options
        self::set_default_options();

        // Create database tables
        self::create_tables();

        // Run migrations if needed
        self::run_migrations();

        // Schedule daily data retention cleanup
        if ( ! wp_next_scheduled( 'aethos_data_retention_cleanup' ) ) {
            wp_schedule_event( time(), 'daily', 'aethos_data_retention_cleanup' );
        }
    }

    /**
     * Set all default options on activation
     *
     * @since    1.1.0
     */
    private static function set_default_options() {
        // Core options
        if ( false === get_option( 'aethos_api_key' ) ) {
            add_option( 'aethos_api_key', '' );
        }

        // Generate shared secret if it doesn't exist
        if ( false === get_option( 'aethos_shared_secret' ) ) {
            $shared_secret = bin2hex( random_bytes( 32 ) );
            add_option( 'aethos_shared_secret', $shared_secret );
        }

        // Knowledge Base options (REQUIRED for scan to work)
        $kb_defaults = array(
            'aethos_kb_include_all_pages'          => '1',
            'aethos_kb_include_all_posts'          => '1',
            'aethos_kb_include_all_woo_products'   => '1',
            'aethos_kb_include_all_woo_categories' => '1',
            'aethos_kb_include_all_categories'     => '1',
        );

        foreach ( $kb_defaults as $option => $value ) {
            if ( false === get_option( $option ) ) {
                add_option( $option, $value );
            }
        }

        // Widget visibility options (chatbot display)
        $visibility_defaults = array(
            'aethos_global_visibility'   => '1',
            'aethos_include_all_pages'   => '1',
        );

        foreach ( $visibility_defaults as $option => $value ) {
            if ( false === get_option( $option ) ) {
                add_option( $option, $value );
            }
        }

        // Scan schedule
        if ( false === get_option( 'aethos_scan_schedule' ) ) {
            add_option( 'aethos_scan_schedule', 'daily' );
        }
    }

    /**
     * Create all database tables
     *
     * @since    1.0.0
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Q&A table
        $qna_table = $wpdb->prefix . 'aethos_qna';
        $sql_qna = "CREATE TABLE IF NOT EXISTS $qna_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            question text NOT NULL,
            answer longtext NOT NULL,
            category varchar(100) DEFAULT 'General',
            priority enum('high','normal','low') DEFAULT 'normal',
            status enum('published','draft') DEFAULT 'draft',
            source varchar(255) DEFAULT NULL,
            is_ai_generated tinyint(1) DEFAULT 0,
            is_accepted tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY category (category),
            KEY priority (priority)
        ) $charset_collate;";
        
        dbDelta( $sql_qna );

        // Vector storage table
        $table_name = $wpdb->prefix . 'aethos_vectors';
        $sql_vectors = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            post_type varchar(50) NOT NULL,
            post_url varchar(255) DEFAULT NULL,
            chunk_index int NOT NULL DEFAULT 0,
            chunk_text longtext NOT NULL,
            embedding longtext NOT NULL,
            magnitude float DEFAULT NULL,
            token_count int DEFAULT NULL,
            metadata longtext DEFAULT NULL,
            hash varchar(64) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY post_type (post_type),
            KEY post_url (post_url),
            KEY hash (hash),
            UNIQUE KEY unique_chunk (post_id, chunk_index)
        ) $charset_collate;";

        dbDelta( $sql_vectors );

        // Sync log table
        $sync_log_table = $wpdb->prefix . 'aethos_sync_log';
        $sql_sync_log = "CREATE TABLE IF NOT EXISTS $sync_log_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            sync_type enum('full','incremental','single') NOT NULL,
            status enum('pending','processing','completed','failed') NOT NULL,
            posts_scanned int DEFAULT 0,
            vectors_created int DEFAULT 0,
            vectors_updated int DEFAULT 0,
            vectors_deleted int DEFAULT 0,
            error_message text DEFAULT NULL,
            started_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY started_at (started_at)
        ) $charset_collate;";
        
        dbDelta( $sql_sync_log );

        // Background processing table
        $bg_table = $wpdb->prefix . 'aethos_background_processing';
        $sql_bg = "CREATE TABLE IF NOT EXISTS $bg_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            action varchar(20) NOT NULL DEFAULT 'update',
            status varchar(20) NOT NULL DEFAULT 'pending',
            priority int(11) NOT NULL DEFAULT 10,
            attempts int(11) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY post_id (post_id),
            KEY priority (priority)
        ) $charset_collate;";
        
        dbDelta( $sql_bg );

        // Create analytics table
        require_once plugin_dir_path( __FILE__ ) . 'class-aethos-analytics.php';
        $analytics = new Aethos_Analytics();
        $analytics->create_tables();
    }

    /**
     * Run version-based migrations
     *
     * @since    1.1.0
     */
    private static function run_migrations() {
        $current_version = get_option( 'aethos_db_version', '1.0.0' );

        // Migration to 1.1.0: Remove deprecated widget appearance options
        if ( version_compare( $current_version, '1.1.0', '<' ) ) {
            self::migrate_to_1_1_0();
        }

        // Update version
        update_option( 'aethos_db_version', self::SCHEMA_VERSION );
    }

    /**
     * Migration to v1.1.0
     * - Remove deprecated widget appearance options (now controlled by SaaS)
     *
     * @since    1.1.0
     */
    private static function migrate_to_1_1_0() {
        // Widget appearance options are now controlled by SaaS dashboard
        $deprecated_options = array(
            'aethos_theme',
            'aethos_widget_position',
            'aethos_primary_color',
            'aethos_greeting_message',
            'aethos_chat_icon',
            'aethos_placeholder_text',
            'aethos_button_text',
            'aethos_header_title',
            'aethos_chat_subtitle',
            'aethos_auto_open',
            'aethos_auto_open_delay',
        );

        foreach ( $deprecated_options as $option ) {
            delete_option( $option );
        }
    }
}
