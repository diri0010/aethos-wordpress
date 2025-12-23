<?php
/**
 * Dashboard Page Template
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get current settings
$api_key = get_option('aethos_api_key', '');
$connection_status = get_option('aethos_connection_status', 'unknown');

// Include header
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-header.php';
?>

<div style="margin-bottom: 32px;">
    <h1 style="margin: 0; font-size: 32px; font-weight: 700; color: #111827;">Dashboard</h1>
</div>

<!-- Not Connected State -->
<div id="dashboard-not-connected" style="display: <?php echo ($connection_status !== 'connected' || empty($api_key)) ? 'block' : 'none'; ?>;">
    <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 12px; padding: 24px; margin-bottom: 24px;">
        <div style="display: flex; align-items: start; gap: 16px;">
            <span class="dashicons dashicons-warning" style="color: #f59e0b; font-size: 24px; margin-top: 2px;"></span>
            <div style="flex: 1;">
                <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600; color: #92400e;">Not Connected</h3>
                <p style="margin: 0 0 16px 0; color: #92400e; line-height: 1.6;">Your chatbot is not connected to the Aethos AI platform. Please connect your site to start using the AI chatbot.</p>
                <a href="<?php echo admin_url('admin.php?page=aethos-chat-setup'); ?>" style="display: inline-flex; align-items: center; gap: 8px; background: #4f46e5; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 14px;">
                    <span class="dashicons dashicons-admin-links" style="font-size: 18px;"></span>
                    Go to Connection Settings
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Connected State -->
<div id="dashboard-connected" style="display: <?php echo ($connection_status === 'connected' && !empty($api_key)) ? 'block' : 'none'; ?>;">
    <!-- API Status Card -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <span style="display: inline-block; width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
            <span style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">API STATUS</span>
        </div>
        <h2 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 700; color: #111827;">Connected</h2>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">Your chatbot is live and responding to users.</p>
    </div>

    <!-- Latest Conversations -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">Latest Conversations</h3>
            <a href="<?php echo admin_url('admin.php?page=aethos-chat-monitoring'); ?>" style="color: #4f46e5; text-decoration: none; font-weight: 600; font-size: 14px;">
                View All
            </a>
        </div>
        <div id="dashboard-latest-conversations">
            <div style="color: #9ca3af; text-align: center; padding: 20px;">Loading...</div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load dashboard data if connected
    <?php if ($connection_status === 'connected' && !empty($api_key)): ?>
    loadDashboardData();
    <?php endif; ?>
    
    function loadDashboardData() {
        // Load latest conversations
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_conversations',
                nonce: '<?php echo wp_create_nonce('aethos_get_conversations'); ?>',
                page: 1
            },
            success: function(response) {
                if (response.success && response.data && response.data.conversations) {
                    var conversations = response.data.conversations.slice(0, 3);
                    var html = '';
                    
                    if (conversations.length === 0) {
                        html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                    } else {
                        conversations.forEach(function(conv) {
                            var snippet = conv.snippet || (conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message');
                            html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                            html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                            html += '<div style="font-size: 12px; color: #9ca3af;">' + (conv.time_ago || 'Recently') + '</div>';
                            html += '</div>';
                        });
                    }
                    
                    $('#dashboard-latest-conversations').html(html);
                } else {
                    $('#dashboard-latest-conversations').html('<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>');
                }
            },
            error: function() {
                $('#dashboard-latest-conversations').html('<div style="color: #9ca3af; text-align: center; padding: 20px;">Unable to load conversations</div>');
            }
        });
    }
    
    function escapeHtml(text) {
        var map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>

<?php
// Include footer
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-footer.php';
?>
