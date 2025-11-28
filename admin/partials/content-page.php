<?php
/**
 * Content Management Page
 * Contains: Knowledge Base, Q&A Management
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Include header
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-header.php';
?>

<div class="aethos-page-header" style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 600; color: #111827; margin: 0;">Content Management</h1>
    <p style="color: #6b7280; margin-top: 8px;">Manage your chatbot's knowledge base and Q&A content</p>
</div>

<!-- Tab Navigation -->
<div class="aethos-tab-nav" style="border-bottom: 2px solid #e5e7eb; margin-bottom: 32px;">
    <button class="aethos-tab-btn active" data-tab="knowledge-base-tab" style="padding: 12px 24px; border: none; background: none; color: #4f46e5; border-bottom: 2px solid #4f46e5; margin-bottom: -2px; cursor: pointer; font-size: 15px; font-weight: 500;">
        <span class="dashicons dashicons-book-alt" style="margin-right: 6px;"></span>
        Knowledge Base
    </button>
    <button class="aethos-tab-btn" data-tab="qna-management-tab" style="padding: 12px 24px; border: none; background: none; color: #6b7280; cursor: pointer; font-size: 15px; font-weight: 500;">
        <span class="dashicons dashicons-format-chat" style="margin-right: 6px;"></span>
        Q&A Management
    </button>
    <button class="aethos-tab-btn" data-tab="discovered-content-tab" style="padding: 12px 24px; border: none; background: none; color: #6b7280; cursor: pointer; font-size: 15px; font-weight: 500;">
        <span class="dashicons dashicons-search" style="margin-right: 6px;"></span>
        Discovered Content
    </button>
</div>

<!-- Knowledge Base Tab -->
<div id="knowledge-base-tab" class="aethos-tab-content" style="display: block;">
    <form method="post" action="options.php">
        <?php settings_fields( 'aethos_options' ); ?>
        <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/kb-sources-component.php'; ?>
        <?php submit_button( 'Save Changes', 'primary', 'submit', true, array( 'style' => 'margin-top: 24px;' ) ); ?>
    </form>
</div>

<!-- Q&A Management Tab -->
<div id="qna-management-tab" class="aethos-tab-content" style="display: none;">
    <form method="post" action="options.php">
        <?php settings_fields( 'aethos_options' ); ?>
        <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/qna-content.php'; ?>
        <?php submit_button( 'Save Changes', 'primary', 'submit', true, array( 'style' => 'margin-top: 24px;' ) ); ?>
    </form>
</div>

<!-- Discovered Content Tab -->
<div id="discovered-content-tab" class="aethos-tab-content" style="display: none;">
    <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/discovered-content.php'; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Storage key for this page
    const STORAGE_KEY = 'aethos_active_tab_content';

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
