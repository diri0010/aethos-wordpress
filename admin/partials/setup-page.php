<?php
/**
 * Setup & Configuration Page
 * Contains: Connection, Model Selection
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get current values
$api_key = get_option('aethos_api_key', '');
$ai_model = get_option('aethos_ai_model', 'gpt-4o-mini');
$is_connected = !empty($api_key);

// Include header
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-header.php';
?>

<div class="aethos-page-header" style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 600; color: #111827; margin: 0;">Setup & Configuration</h1>
    <p style="color: #6b7280; margin-top: 8px;">Configure your API connection and chatbot visibility</p>
</div>

<!-- Tab Navigation -->
<div class="aethos-tab-nav" style="border-bottom: 2px solid #e5e7eb; margin-bottom: 32px;">
    <button class="aethos-tab-btn active" data-tab="connection-tab" style="padding: 12px 24px; border: none; background: none; color: #4f46e5; border-bottom: 2px solid #4f46e5; margin-bottom: -2px; cursor: pointer; font-size: 15px; font-weight: 500;">
        <span class="dashicons dashicons-admin-links" style="margin-right: 6px;"></span>
        Connection
    </button>
    <button class="aethos-tab-btn" data-tab="visibility-tab" style="padding: 12px 24px; border: none; background: none; color: #6b7280; cursor: pointer; font-size: 15px; font-weight: 500;">
        <span class="dashicons dashicons-visibility" style="margin-right: 6px;"></span>
        Chatbot Visibility
    </button>
</div>

<!-- Connection Tab Form -->
<div id="connection-tab" class="aethos-tab-content" style="display: block;">
    <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/connection-content.php'; ?>
</div>

<!-- Visibility Tab Form -->
<div id="visibility-tab" class="aethos-tab-content" style="display: none;">
    <form method="post" action="options.php">
        <?php settings_fields( 'aethos_visibility' ); ?>
        <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/visibility-content.php'; ?>
        <?php submit_button( 'Save Changes', 'primary', 'submit', true, array( 'style' => 'margin-top: 24px;' ) ); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Storage key for this page
    const STORAGE_KEY = 'aethos_active_tab_setup';

    // Tab switching
    $('.aethos-tab-btn').on('click', function() {
        var tabId = $(this).data('tab');
        
        // Update button states
        $('.aethos-tab-btn').css({
            'color': '#6b7280',
            'border-bottom': 'none'
        }).removeClass('active');
        
        $(this).css({
            'color': '#4f46e5',
            'border-bottom': '2px solid #4f46e5'
        }).addClass('active');
        
        // Show/hide content
        $('.aethos-tab-content').hide();
        $('#' + tabId).show();

        // Save to localStorage
        localStorage.setItem(STORAGE_KEY, tabId);
    });

    // Restore active tab on load
    const savedTab = localStorage.getItem(STORAGE_KEY);
    if (savedTab) {
        // Check if the tab exists
        const $tabBtn = $('.aethos-tab-btn[data-tab="' + savedTab + '"]');
        if ($tabBtn.length) {
            $tabBtn.trigger('click');
        }
    }
});
</script>

<?php
// Include footer
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-footer.php';
?>
