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
    // Tab switching
    $('.aethos-tab-btn').on('click', function() {
        var tabId = $(this).data('tab');
        
        // Update button states
        $('.aethos-tab-btn').css({
            'color': '#6b7280',
            'border-bottom': 'none'
        });
        $(this).css({
            'color': '#4f46e5',
            'border-bottom': '2px solid #4f46e5'
        });
        
        // Show/hide content
        $('.aethos-tab-content').hide();
        $('#' + tabId).show();
    });
});
</script>

<?php
// Include footer
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-footer.php';
?>
