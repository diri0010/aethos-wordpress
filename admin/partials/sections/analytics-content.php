
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 600;">Analytics</h2>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">Track chatbot performance and user interactions. For detailed usage analytics (conversations used, limits), visit your <a href="https://app.aethoslogic.com" target="_blank" style="color: #4f46e5;">AethosLogic Dashboard</a>.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <select id="aethos-analytics-timerange" class="regular-text">
            <option value="last_7_days">Last 7 Days</option>
            <option value="last_30_days">Last 30 Days</option>
            <option value="last_90_days">Last 90 Days</option>
            <option value="all_time">All Time</option>
        </select>
    </div>
</div>

<!-- Key Metrics -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    <!-- Total Conversations -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Conversations Saved</div>
        <div style="font-size: 28px; font-weight: 600; margin-bottom: 8px;" id="metric-total-conversations">0</div>
        <div style="display: flex; align-items: center; gap: 4px; font-size: 13px;">
            <span class="dashicons dashicons-arrow-up-alt" style="color: #10b981; font-size: 16px;"></span>
            <span style="color: #10b981;" id="metric-conversations-change">+0%</span>
        </div>
    </div>
    
    <!-- Average Duration -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Avg. Duration</div>
        <div style="font-size: 28px; font-weight: 600; margin-bottom: 8px;" id="metric-avg-duration">0s</div>
        <div style="display: flex; align-items: center; gap: 4px; font-size: 13px;">
            <span class="dashicons dashicons-arrow-down-alt" style="color: #ef4444; font-size: 16px;"></span>
            <span style="color: #ef4444;" id="metric-duration-change">-0%</span>
        </div>
    </div>
    
    <!-- Unique Users -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Unique Users</div>
        <div style="font-size: 28px; font-weight: 600; margin-bottom: 8px;" id="metric-unique-users">0</div>
        <div style="display: flex; align-items: center; gap: 4px; font-size: 13px;">
            <span class="dashicons dashicons-arrow-up-alt" style="color: #10b981; font-size: 16px;"></span>
            <span style="color: #10b981;" id="metric-users-change">+0%</span>
        </div>
    </div>
    
    <!-- Feedback Score -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Feedback Score</div>
        <div style="font-size: 28px; font-weight: 600; margin-bottom: 8px;" id="metric-feedback-score">0/5</div>
        <div style="display: flex; align-items: center; gap: 4px; font-size: 13px;">
            <span class="dashicons dashicons-arrow-up-alt" style="color: #10b981; font-size: 16px;"></span>
            <span style="color: #10b981;" id="metric-feedback-change">+0%</span>
        </div>
    </div>
</div>

<!-- Feedback Summary -->
<div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
    <h3 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600;">Feedback Summary</h3>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; align-items: center;">
        <!-- Upvotes -->
        <div style="text-align: center; padding: 20px; background: #f0fdf4; border-radius: 8px;">
            <div style="font-size: 32px; margin-bottom: 8px;">👍</div>
            <div style="font-size: 28px; font-weight: 600; color: #10b981;" id="total-upvotes">0</div>
            <div style="color: #6b7280; font-size: 14px;">Upvotes</div>
        </div>
        
        <!-- Downvotes -->
        <div style="text-align: center; padding: 20px; background: #fef2f2; border-radius: 8px;">
            <div style="font-size: 32px; margin-bottom: 8px;">👎</div>
            <div style="font-size: 28px; font-weight: 600; color: #ef4444;" id="total-downvotes">0</div>
            <div style="color: #6b7280; font-size: 14px;">Downvotes</div>
        </div>
        
        <!-- Positive Sentiment -->
        <div style="text-align: center; padding: 20px; background: #f9fafb; border-radius: 8px;">
            <div style="font-size: 32px; margin-bottom: 8px;">📊</div>
            <div style="font-size: 28px; font-weight: 600; color: #4f46e5;" id="positive-sentiment">0%</div>
            <div style="color: #6b7280; font-size: 14px;">Positive Sentiment</div>
        </div>
    </div>
    
    <!-- Ratio Bar -->
    <div style="margin-top: 24px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: #6b7280;">
            <span>Feedback Ratio</span>
            <span id="feedback-total">0 total responses</span>
        </div>
        <div style="display: flex; height: 12px; border-radius: 6px; overflow: hidden; background: #e5e7eb;">
            <div id="upvote-bar" style="background: #10b981; width: 0%; transition: width 0.3s;"></div>
            <div id="downvote-bar" style="background: #ef4444; width: 0%; transition: width 0.3s;"></div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load analytics data
    function loadAnalyticsData() {
        const timerange = $('#aethos-analytics-timerange').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_analytics',
                nonce: '<?php echo wp_create_nonce("aethos_get_analytics"); ?>',
                date_range: timerange
            },
            success: function(response) {
                if (response.success) {
                    updateMetrics(response.data.metrics);
                    updateFeedbackSummary(response.data.feedback_summary);
                }
            }
        });
    }
    
    function updateMetrics(metrics) {
        $('#metric-total-conversations').text(metrics.total_conversations || 0);
        $('#metric-conversations-change').text(metrics.conversations_change || '+0%');
        
        $('#metric-avg-duration').text((metrics.avg_duration || 0) + 's');
        $('#metric-duration-change').text(metrics.duration_change || '-0%');
        
        $('#metric-unique-users').text(metrics.unique_users || 0);
        $('#metric-users-change').text(metrics.users_change || '+0%');
        
        // Update feedback score to show as percentage instead of /5
        const score = metrics.feedback_score || 0;
        const percentage = score > 0 ? Math.round((score / 5) * 100) : 0;
        $('#metric-feedback-score').text(percentage + '%');
        $('#metric-feedback-change').text(metrics.feedback_change || '+0%');
    }
    
    function updateFeedbackSummary(data) {
        if (!data) return;
        
        const upvotes = data.upvotes || 0;
        const downvotes = data.downvotes || 0;
        const total = upvotes + downvotes;
        const positivePct = total > 0 ? Math.round((upvotes / total) * 100) : 0;
        
        $('#total-upvotes').text(upvotes);
        $('#total-downvotes').text(downvotes);
        $('#positive-sentiment').text(positivePct + '%');
        $('#feedback-total').text(total + ' total responses');
        
        // Update ratio bar
        if (total > 0) {
            $('#upvote-bar').css('width', positivePct + '%');
            $('#downvote-bar').css('width', (100 - positivePct) + '%');
        } else {
            $('#upvote-bar').css('width', '0%');
            $('#downvote-bar').css('width', '0%');
        }
    }
    
    // Load data on page load
    loadAnalyticsData();
    
    // Reload on timerange change
    $('#aethos-analytics-timerange').on('change', function() {
        loadAnalyticsData();
    });
});
</script>

<?php
// Include footer
