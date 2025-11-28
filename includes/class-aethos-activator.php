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
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        global $wpdb;
        
        // Load dbDelta function
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // Create default options if they don't exist
        if ( false === get_option( 'aethos_api_key' ) ) {
            add_option( 'aethos_api_key', '' );
        }

        // Create vector storage table
        $table_name = $wpdb->prefix . 'aethos_vectors';
        $charset_collate = $wpdb->get_charset_collate();

        // Create Q&A table
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

        // Enhanced vector storage table
        $sql_vectors = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            post_type varchar(50) NOT NULL,
            post_url varchar(255) DEFAULT NULL,
            chunk_index int NOT NULL DEFAULT 0,
            chunk_text longtext NOT NULL,
            embedding longtext NOT NULL,
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

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $sql_vectors );

        // Create sync log table
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

	    // Create analytics table
	    require_once plugin_dir_path( __FILE__ ) . 'class-aethos-analytics.php';
	    $analytics = new Aethos_Analytics();
	    $analytics->create_tables();
	}

}
