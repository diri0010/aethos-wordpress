<?php
/**
 * Monitoring & Analytics Page
 * Contains: Analytics, Conversation History, User Feedback
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Include header
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-header.php';
?>

<div class="aethos-page-header" style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 600; color: #111827; margin: 0;">Monitoring & Analytics</h1>
    <p style="color: #6b7280; margin-top: 8px;">Track performance, conversations, and user feedback</p>
</div>

<!-- Tab Navigation -->
<div class="aethos-tab-nav" style="border-bottom: 2px solid #e5e7eb; margin-bottom: 32px;">
    <button class="aethos-tab-btn active" data-tab="analytics-tab" style="padding: 12px 24px; border: none; background: none; color: #4f46e5; border-bottom: 2px solid #4f46e5; margin-bottom: -2px; cursor: pointer; font-size: 15px; font-weight: 500;">
        <span class="dashicons dashicons-chart-line" style="margin-right: 6px;"></span>
        Analytics
    </button>
    <button class="aethos-tab-btn" data-tab="conversations-tab" style="padding: 12px 24px; border: none; background: none; color: #6b7280; cursor: pointer; font-size: 15px; font-weight: 500;">
        <span class="dashicons dashicons-format-chat" style="margin-right: 6px;"></span>
        Conversation History
    </button>
</div>

<!-- Analytics Tab -->
<div id="analytics-tab" class="aethos-tab-content" style="display: block;">
    <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/analytics-content.php'; ?>
</div>

<!-- Conversations Tab -->
<div id="conversations-tab" class="aethos-tab-content" style="display: none;">
    <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/conversations-content.php'; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Storage key for this page
    const STORAGE_KEY = 'aethos_active_tab_monitoring';

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
        
        // Load data for the active tab
        if (tabId === 'analytics-tab') {
            // Trigger analytics load if function exists
            if (typeof loadAnalytics === 'function') {
                loadAnalytics();
            }
        } else if (tabId === 'conversations-tab') {
            // Trigger conversations load if function exists
            if (typeof loadConversations === 'function') {
                loadConversations();
            }
        } else if (tabId === 'feedback-tab') {
            // Trigger feedback load if function exists
            if (typeof loadFeedback === 'function') {
                loadFeedback();
            }
        }
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
