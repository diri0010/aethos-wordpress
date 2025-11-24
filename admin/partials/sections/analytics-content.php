
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 600;">Analytics</h2>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">Track chatbot performance and user interactions.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <select id="aethos-analytics-timerange" class="regular-text">
            <option value="7">Last 7 Days</option>
            <option value="30">Last 30 Days</option>
            <option value="90">Last 90 Days</option>
            <option value="all">All Time</option>
        </select>
        <button type="button" id="aethos-export-analytics" class="button">
            <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> Export CSV
        </button>
    </div>
</div>

<!-- Key Metrics -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    <!-- Total Conversations -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Total Conversations</div>
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

<!-- Charts -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
    <!-- Conversation Volume Chart -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 600;">Conversation Volume</h3>
        <canvas id="aethos-conversation-chart" style="max-height: 300px;"></canvas>
    </div>
    
    <!-- Top Topics -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 600;">Top Topics</h3>
        <div id="aethos-top-topics-list">
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                <span class="dashicons dashicons-chart-bar" style="font-size: 48px; opacity: 0.5;"></span>
                <p style="margin: 12px 0 0 0;">No data available</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Conversations -->
<div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Recent Conversations</h3>
        <a href="<?php echo admin_url('admin.php?page=aethos-chat-conversations'); ?>" class="button">View All</a>
    </div>
    
    <div style="overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 8px;">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr style="background: #f9fafb;">
                    <th style="padding: 16px; font-weight: 600; text-align: left; color: #6b7280; font-size: 13px;">TIMESTAMP</th>
                    <th style="padding: 16px; font-weight: 600; text-align: left; color: #6b7280; font-size: 13px;">USER ID</th>
                    <th style="padding: 16px; font-weight: 600; text-align: left; color: #6b7280; font-size: 13px;">SNIPPET</th>
                    <th style="padding: 16px; font-weight: 600; text-align: right; color: #6b7280; font-size: 13px;">DURATION</th>
                </tr>
            </thead>
            <tbody id="aethos-conversations-table">
                <tr>
                    <td colspan="4" style="text-align: center; padding: 60px; color: #9ca3af;">
                        No conversations yet
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
jQuery(document).ready(function($) {
    let conversationChart = null;
    
    // Load analytics data
    function loadAnalyticsData() {
        const timerange = $('#aethos-analytics-timerange').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_analytics',
                nonce: '<?php echo wp_create_nonce("aethos_get_analytics"); ?>',
                timerange: timerange
            },
            success: function(response) {
                if (response.success) {
                    updateMetrics(response.data.metrics);
                    updateChart(response.data.chart_data);
                    updateTopTopics(response.data.top_topics);
                    updateConversationsTable(response.data.recent_conversations);
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
        
        $('#metric-feedback-score').text((metrics.feedback_score || 0) + '/5');
        $('#metric-feedback-change').text(metrics.feedback_change || '+0%');
    }
    
    function updateChart(chartData) {
        const ctx = document.getElementById('aethos-conversation-chart');
        
        if (conversationChart) {
            conversationChart.destroy();
        }
        
        conversationChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Conversations',
                    data: chartData.data || [],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    function updateTopTopics(topics) {
        if (!topics || topics.length === 0) {
            return;
        }
        
        let html = '';
        topics.forEach(function(topic, index) {
            html += '<div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
            html += '<div style="display: flex; align-items: center; gap: 12px;">';
            html += '<span style="font-weight: 600; color: #6b7280;">' + (index + 1) + '</span>';
            html += '<span>' + topic.name + '</span>';
            html += '</div>';
            html += '<span style="font-weight: 600;">' + topic.count + '</span>';
            html += '</div>';
        });
        
        $('#aethos-top-topics-list').html(html);
    }
    
    function updateConversationsTable(conversations) {
        if (!conversations || conversations.length === 0) {
            return;
        }
        
        let html = '';
        conversations.forEach(function(conv) {
            html += '<tr>';
            html += '<td style="padding: 16px; color: #374151;">' + conv.timestamp + '</td>';
            html += '<td style="padding: 16px; color: #374151;">' + conv.user_id + '</td>';
            html += '<td style="padding: 16px; color: #6b7280;">' + conv.snippet + '</td>';
            html += '<td style="padding: 16px; text-align: right; color: #374151;">' + conv.duration + '</td>';
            html += '</tr>';
        });
        
        $('#aethos-conversations-table').html(html);
    }
    
    // Load data on page load
    loadAnalyticsData();
    
    // Reload on timerange change
    $('#aethos-analytics-timerange').on('change', function() {
        loadAnalyticsData();
    });
    
    // Export CSV
    $('#aethos-export-analytics').on('click', function() {
        window.location.href = ajaxurl + '?action=aethos_export_analytics&nonce=<?php echo wp_create_nonce("aethos_export_analytics"); ?>&timerange=' + $('#aethos-analytics-timerange').val();
    });
});
</script>

<?php
// Include footer
