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

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
    <h1 style="margin: 0; font-size: 32px; font-weight: 700; color: #111827;">Dashboard</h1>
    <a href="<?php echo admin_url('admin.php?page=aethos-chat-appearance'); ?>" style="display: inline-flex; align-items: center; gap: 8px; background: #4f46e5; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 14px;">
        <span class="dashicons dashicons-visibility" style="font-size: 18px;"></span>
        View Chatbot on Site
    </a>
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

    <!-- Performance Overview -->
    <h2 style="margin: 0 0 20px 0; font-size: 24px; font-weight: 700; color: #111827;">Performance Overview</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
        <!-- Total Conversations -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <div style="font-size: 13px; font-weight: 600; color: #6b7280; margin-bottom: 12px;">Total Conversations (7d)</div>
            <div style="font-size: 36px; font-weight: 700; color: #111827; margin-bottom: 8px;" id="dashboard-total-conversations">0</div>
            <div style="display: flex; align-items: center; gap: 4px; font-size: 14px;">
                <span class="dashicons dashicons-arrow-up-alt" style="color: #10b981; font-size: 16px;"></span>
                <span style="color: #10b981; font-weight: 600;" id="dashboard-total-conversations-change">+0%</span>
            </div>
        </div>

        <!-- New Questions -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <div style="font-size: 13px; font-weight: 600; color: #6b7280; margin-bottom: 12px;">New Questions</div>
            <div style="font-size: 36px; font-weight: 700; color: #111827; margin-bottom: 8px;" id="dashboard-new-questions">0</div>
            <div style="display: flex; align-items: center; gap: 4px; font-size: 14px;">
                <span class="dashicons dashicons-arrow-up-alt" style="color: #10b981; font-size: 16px;"></span>
                <span style="color: #10b981; font-weight: 600;" id="dashboard-new-questions-change">+0%</span>
            </div>
        </div>

        <!-- User Feedback Score -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <div style="font-size: 13px; font-weight: 600; color: #6b7280; margin-bottom: 12px;">User Feedback Score</div>
            <div style="font-size: 36px; font-weight: 700; color: #111827; margin-bottom: 8px;" id="dashboard-feedback-score">0/5</div>
            <div style="display: flex; align-items: center; gap: 4px; font-size: 14px;">
                <span class="dashicons dashicons-arrow-down-alt" style="color: #ef4444; font-size: 16px;"></span>
                <span style="color: #ef4444; font-weight: 600;" id="dashboard-feedback-change">-0%</span>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Pending Actions -->
        <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 12px; padding: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span style="font-size: 24px;">💡</span>
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">Pending Actions</h3>
            </div>
            <p style="margin: 0 0 16px 0; color: #6b7280; font-size: 14px;" id="dashboard-pending-text">You have <strong id="dashboard-pending-count">0</strong> new Q&A suggestions to review from user conversations.</p>
            <a href="<?php echo admin_url('admin.php?page=aethos-chat-qna'); ?>" style="display: inline-block; color: #4f46e5; text-decoration: none; font-weight: 600; font-size: 14px;">
                Review Suggestions →
            </a>
        </div>

        <!-- Latest Conversations -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">Latest Conversations</h3>
                <a href="<?php echo admin_url('admin.php?page=aethos-chat-conversations'); ?>" style="color: #4f46e5; text-decoration: none; font-weight: 600; font-size: 14px;">
                    View All
                </a>
            </div>
            <div id="dashboard-latest-conversations">
                <div style="color: #9ca3af; text-align: center; padding: 20px;">Loading...</div>
            </div>
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
        // Load analytics data
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_analytics',
                nonce: '<?php echo wp_create_nonce('aethos_admin_nonce'); ?>',
                period: 'last_7_days'
            },
            success: function(response) {
                if (response.success && response.data) {
                    var data = response.data;
                    $('#dashboard-total-conversations').text(data.total_conversations || 0);
                    $('#dashboard-new-questions').text(data.new_questions || 0);
                    $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                    $('#dashboard-pending-count').text(data.pending_qna || 0);
                }
            }
        });
        
        // Load latest conversations
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_conversations',
                nonce: '<?php echo wp_create_nonce('aethos_admin_nonce'); ?>',
                limit: 3
            },
            success: function(response) {
                if (response.success && response.data && response.data.conversations) {
                    var conversations = response.data.conversations;
                    var html = '';
                    
                    if (conversations.length === 0) {
                        html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                    } else {
                        conversations.forEach(function(conv) {
                            var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                            html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                            html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                            html += '<div style="font-size: 12px; color: #9ca3af;">Just now</div>';
                            html += '</div>';
                        });
                    }
                    
                    $('#dashboard-latest-conversations').html(html);
                }
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
