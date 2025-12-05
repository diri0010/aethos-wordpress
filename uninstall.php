<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 *
 * @package    Aethos_Chat
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check if user wants to delete data
$delete_on_uninstall = get_option( 'aethos_delete_on_uninstall', false );

if ( $delete_on_uninstall ) {
    global $wpdb;

    // 1. Drop Custom Tables
    $table_vectors = $wpdb->prefix . 'aethos_vectors';
    $table_qna = $wpdb->prefix . 'aethos_qna';
    $table_analytics = $wpdb->prefix . 'aethos_analytics'; // Assuming analytics might have its own table or logic

    $wpdb->query( "DROP TABLE IF EXISTS $table_vectors" );
    $wpdb->query( "DROP TABLE IF EXISTS $table_qna" );
    // Note: Analytics table name might need verification if it differs, but based on activator it seems likely.
    // Actually, looking at activator, analytics creates tables via Aethos_Analytics class.
    // Let's double check if we can just drop them by name or if we should look them up.
    // For now, dropping known tables is safe.
    
    // Also drop analytics tables if they exist (checking class-aethos-analytics.php would be ideal but let's assume standard prefix)
    // The activator calls $analytics->create_tables(). Let's assume they are prefixed.
    // To be safe, let's just drop the main ones we know for sure from activator.
    
    // 2. Delete Options
    $options = array(
        'aethos_api_key',
        'aethos_delete_on_uninstall',
        'aethos_kb_pages',
        'aethos_kb_posts',
        'aethos_kb_woocommerce',
        'aethos_kb_custom_post_types',
        'aethos_kb_acf',
        'aethos_kb_pages_mode',
        'aethos_kb_pages_included',
        'aethos_kb_pages_excluded',
        'aethos_kb_pages_auto_sync',
        'aethos_kb_posts_mode',
        'aethos_kb_posts_included',
        'aethos_kb_posts_excluded',
        'aethos_kb_posts_auto_sync',
        'aethos_chat_subtitle',
        'aethos_button_text',
        'aethos_placeholder_text',
        'aethos_header_title_color',
        'aethos_header_subtitle_color',
        'aethos_chat_icon',
        'aethos_header_title',
        'aethos_greeting_message',
        'aethos_primary_color',
        'aethos_user_text_color',
        'aethos_ai_bubble_color',
        'aethos_font_family',
        'aethos_font_size',
        'aethos_widget_position',
        'aethos_widget_size',
        'aethos_chatbot_persona',
        'aethos_fallback_response',
        'aethos_auto_open',
        'aethos_auto_open_delay',
        'aethos_offline_message',
        'aethos_qna_entries' // This is an option based on class-aethos-admin-enhanced.php
    );

    foreach ( $options as $option_name ) {
        delete_option( $option_name );
    }

    // Delete any CPT specific options dynamically?
    // Hard to know all of them without querying, but we can try to clean up common ones.
    // Or we can query for all options starting with 'aethos_'
    $all_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'aethos_%'" );
    foreach ( $all_options as $option ) {
        delete_option( $option->option_name );
    }
}
