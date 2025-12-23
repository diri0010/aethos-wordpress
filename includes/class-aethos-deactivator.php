<?php

/**
 * Fired during plugin deactivation
 *
 * @since      1.0.0
 *
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        // Clear scheduled data retention cleanup
        $timestamp = wp_next_scheduled( 'aethos_data_retention_cleanup' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'aethos_data_retention_cleanup' );
        }
	}

}
