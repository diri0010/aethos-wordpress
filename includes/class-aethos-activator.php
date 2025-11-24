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

        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            chunk_text longtext NOT NULL,
            embedding longtext NOT NULL,
            metadata longtext DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id)
        ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	        dbDelta( $sql );

	        // Create analytics table
	        require_once plugin_dir_path( __FILE__ ) . 'class-aethos-analytics.php';
	        $analytics = new Aethos_Analytics();
	        $analytics->create_tables();
	}

}
