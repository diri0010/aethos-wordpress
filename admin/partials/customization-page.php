<?php
/**
 * Look & Feel Page
 * Contains: Appearance, Behavior
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Include header
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-header.php';

// Get behavior settings for the form
$chatbot_persona = get_option( 'aethos_chatbot_persona', 'friendly' );
$greeting_message = get_option( 'aethos_greeting_message', 'Welcome! How can I help you today?' );
$fallback_response = get_option( 'aethos_fallback_response', 'I apologize, but I don\'t have enough information to answer that question accurately. Could you try rephrasing it or asking something else?' );
?>

<div class="aethos-page-header" style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 600; color: #111827; margin: 0;">Look & Feel</h1>
    <p style="color: #6b7280; margin-top: 8px;">Customize your chatbot's appearance and behavior</p>
</div>

<!-- Tab Navigation -->
<div class="aethos-tab-nav" style="border-bottom: 2px solid #e5e7eb; margin-bottom: 32px;">
    <button class="aethos-tab-btn active" data-tab="appearance-tab" style="padding: 12px 24px; border: none; background: none; color: #4f46e5; border-bottom: 2px solid #4f46e5; margin-bottom: -2px; cursor: pointer; font-size: 15px; font-weight: 500;">
        <span class="dashicons dashicons-art" style="margin-right: 6px;"></span>
        Appearance
    </button>
    <button class="aethos-tab-btn" data-tab="behavior-tab" style="padding: 12px 24px; border: none; background: none; color: #6b7280; cursor: pointer; font-size: 15px; font-weight: 500;">
        <span class="dashicons dashicons-admin-generic" style="margin-right: 6px;"></span>
        Behavior
    </button>
</div>

<!-- Appearance Tab Form -->
<div id="appearance-tab" class="aethos-tab-content" style="display: block;">
    <form method="post" action="options.php">
        <?php settings_fields( 'aethos_appearance' ); ?>
        <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/appearance-content.php'; ?>
        <?php submit_button( 'Save Changes', 'primary', 'submit', true, array( 'style' => 'margin-top: 24px;' ) ); ?>
    </form>
</div>

<!-- Behavior Tab Form -->
<div id="behavior-tab" class="aethos-tab-content" style="display: none;">
    <form method="post" action="options.php">
        <?php settings_fields( 'aethos_behavior' ); ?>
        <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/behavior-content.php'; ?>
        <?php submit_button( 'Save Changes', 'primary', 'submit', true, array( 'style' => 'margin-top: 24px;' ) ); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Storage key for this page
    const STORAGE_KEY = 'aethos_active_tab_customization';

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
