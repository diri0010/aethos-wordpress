<?php
/**
 * Shared Admin Header Template
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get current page
$current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'aethos-chat';
?>

<div class="wrap">
    <!-- Header -->
    <div class="aethos-admin-header" style="background: #fff; padding: 20px 30px; margin: 0 0 20px 0; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="<?php echo esc_url( AETHOS_PLUGIN_URL . 'assets/images/aethos-logo.png' ); ?>" alt="Aethos Logo" style="height: 32px;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 600;">AI Chat Settings</h1>
        </div>
        <div style="display: flex; gap: 12px;">
            <button type="button" class="button" onclick="location.reload();">
                <span class="dashicons dashicons-update" style="vertical-align: middle;"></span> Refresh
            </button>
        </div>
    </div>

    <!-- Content Area -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 32px;">
