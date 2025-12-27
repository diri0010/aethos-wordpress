
<style>
@keyframes aethos-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 600;">Conversation History</h2>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">Browse and search past chatbot conversations.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="button" id="aethos-refresh-conversations" class="button">
            <span class="dashicons dashicons-update" style="vertical-align: middle;"></span> Refresh
        </button>
        <button type="button" id="aethos-export-conversations" class="button">
            <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> Export CSV
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 20px;">
    <!-- Conversations List -->
    <div>
        <!-- Search and Filters -->
        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <input type="text" id="aethos-conv-search" placeholder="Search conversations..." class="regular-text" style="width: 100%; margin-bottom: 12px;">
            
            <select id="aethos-time-filter" class="regular-text" style="width: 100%;">
                <option value="all">All Time</option>
                <option value="7">Last 7 Days</option>
                <option value="30" selected>Last 30 Days</option>
                <option value="90">Last 90 Days</option>
            </select>
        </div>
        
        <!-- Conversations List -->
        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
            <div id="aethos-conversations-list" style="max-height: 600px; overflow-y: auto;">
                <div style="text-align: center; padding: 40px; color: #9ca3af;">
                    <span class="dashicons dashicons-admin-comments" style="font-size: 48px; opacity: 0.5;"></span>
                    <p style="margin: 12px 0 0 0;">No conversations found</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div style="border-top: 1px solid #e5e7eb; padding: 12px 16px; display: flex; justify-content: between; align-items: center; background: #f9fafb;">
                <button type="button" id="aethos-conv-prev" class="button button-small" disabled>Previous</button>
                <span id="aethos-conv-pagination" style="font-size: 13px; color: #6b7280;">Page 1</span>
                <button type="button" id="aethos-conv-next" class="button button-small">Next</button>
            </div>
        </div>
    </div>
    
    <!-- Conversation Details -->
    <div>
        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; min-height: 600px;">
            <div id="aethos-conversation-details">
                <div style="text-align: center; padding: 80px 40px; color: #9ca3af;">
                    <span class="dashicons dashicons-format-chat" style="font-size: 64px; opacity: 0.3;"></span>
                    <p style="margin: 16px 0 0 0; font-size: 16px;">Select a conversation to view details</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentPage = 1;
    let selectedConversationId = null;
    
    // Load conversations list
    function loadConversations() {
        const timeFilter = $('#aethos-time-filter').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_conversations',
                nonce: '<?php echo wp_create_nonce("aethos_get_conversations"); ?>',
                page: currentPage,
                search: $('#aethos-conv-search').val(),
                days: timeFilter !== 'all' ? timeFilter : ''
            },
            success: function(response) {
                if (response.success && response.data.conversations.length > 0) {
                    let html = '';
                    response.data.conversations.forEach(function(conv) {
                        const isSelected = conv.id === selectedConversationId ? 'background: #eff6ff; border-left: 3px solid #4f46e5;' : '';
                        html += '<div class="aethos-conv-item" data-id="' + conv.id + '" style="padding: 16px; border-bottom: 1px solid #e5e7eb; cursor: pointer; ' + isSelected + '">';
                        html += '<div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">';
                        html += '<div style="font-weight: 500; font-size: 14px;">' + conv.user_id + '</div>';
                        html += '<div style="font-size: 12px; color: #6b7280;">' + conv.time_ago + '</div>';
                        html += '</div>';
                        html += '<div style="font-size: 13px; color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + conv.snippet + '</div>';
                        html += '</div>';
                    });
                    $('#aethos-conversations-list').html(html);
                    
                    // Update pagination
                    $('#aethos-conv-pagination').text('Page ' + currentPage + ' of ' + response.data.total_pages);
                    $('#aethos-conv-prev').prop('disabled', currentPage === 1);
                    $('#aethos-conv-next').prop('disabled', currentPage >= response.data.total_pages);
                    
                    // Auto-open latest conversation on initial page load
                    if (!selectedConversationId && response.data.conversations.length > 0) {
                        const firstId = response.data.conversations[0].id;
                        loadConversationDetails(firstId);
                        // Apply highlight to first item
                        setTimeout(function() {
                            $('.aethos-conv-item[data-id="' + firstId + '"]').css({
                                'background': '#eff6ff',
                                'border-left': '3px solid #4f46e5'
                            });
                        }, 50);
                    }
                } else {
                    $('#aethos-conversations-list').html('<div style="text-align: center; padding: 40px; color: #9ca3af;"><p>No conversations found</p></div>');
                }
            }
        });
    }
    
    // Load conversation details
    function loadConversationDetails(conversationId) {
        selectedConversationId = conversationId;
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_conversation_details',
                nonce: '<?php echo wp_create_nonce("aethos_get_conversation_details"); ?>',
                conversation_id: conversationId
            },
            success: function(response) {
                if (response.success) {
                    const conv = response.data;
                    let html = '';
                    
                    // Header
                    html += '<div style="border-bottom: 1px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 20px;">';
                    html += '<div style="display: flex; justify-content: space-between; align-items: start;">';
                    html += '<div><h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Conversation Details</h3>';
                    html += '<div style="color: #6b7280; font-size: 14px;">User: ' + conv.user_id + ' • ' + conv.started_at + '</div></div>';
                    html += '<div style="text-align: right;"><div style="font-size: 13px; color: #6b7280;">Duration</div>';
                    html += '<div style="font-weight: 600;">' + conv.duration + '</div></div>';
                    html += '</div></div>';
                    
                    // Messages
                    html += '<div style="display: flex; flex-direction: column; gap: 16px;">';
                    conv.messages.forEach(function(msg) {
                        if (msg.role === 'user') {
                            html += '<div style="display: flex; justify-content: flex-end;">';
                            html += '<div style="background: #4f46e5; color: white; padding: 12px 16px; border-radius: 16px 16px 4px 16px; max-width: 70%;">';
                            html += '<div style="font-size: 14px;">' + msg.content + '</div>';
                            html += '<div style="font-size: 11px; opacity: 0.8; margin-top: 4px;">' + msg.timestamp + '</div>';
                            html += '</div></div>';
                        } else {
                            // AI response - show feedback and sources if available
                            let feedbackHtml = '';
                            if (msg.feedback) {
                                if (msg.feedback === 'upvote') {
                                    feedbackHtml = '<span style="color: #10b981; margin-left: 8px;" title="Upvoted">👍</span>';
                                } else if (msg.feedback === 'downvote') {
                                    feedbackHtml = '<span style="color: #ef4444; margin-left: 8px;" title="Downvoted">👎</span>';
                                }
                            }
                            
                            // Build sources HTML if sources exist
                            let sourcesHtml = '';
                            if (msg.sources && msg.sources.length > 0) {
                                sourcesHtml = '<div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">';
                                sourcesHtml += '<div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">📚 Sources:</div>';
                                msg.sources.forEach(function(source) {
                                    if (source.url) {
                                        sourcesHtml += '<a href="' + source.url + '" target="_blank" style="display: block; font-size: 12px; color: #4f46e5; text-decoration: none; margin-top: 2px;">';
                                        sourcesHtml += '→ ' + (source.title || 'Link') + '</a>';
                                    } else if (source.title) {
                                        sourcesHtml += '<div style="font-size: 12px; color: #6b7280; margin-top: 2px;">→ ' + source.title + '</div>';
                                    }
                                });
                                sourcesHtml += '</div>';
                            }
                            
                            html += '<div style="display: flex; justify-content: flex-start;">';
                            html += '<div style="background: #f3f4f6; color: #111827; padding: 12px 16px; border-radius: 16px 16px 16px 4px; max-width: 70%;">';
                            html += '<div style="font-size: 14px;">' + msg.content + '</div>';
                            html += sourcesHtml;
                            html += '<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">';
                            html += '<span style="font-size: 11px; color: #6b7280;">' + msg.timestamp + '</span>';
                            html += feedbackHtml;
                            html += '</div>';
                            html += '</div></div>';
                        }
                    });
                    html += '</div>';
                    
                    $('#aethos-conversation-details').html(html);
                }
            }
        });
    }
    
    // Click on conversation
    $(document).on('click', '.aethos-conv-item', function() {
        const conversationId = $(this).data('id');
        
        // Immediately update visual selection
        $('.aethos-conv-item').css({
            'background': '',
            'border-left': ''
        });
        $(this).css({
            'background': '#eff6ff',
            'border-left': '3px solid #4f46e5'
        });
        
        loadConversationDetails(conversationId);
    });
    
    // Search
    let searchTimeout;
    $('#aethos-conv-search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            selectedConversationId = null;
            loadConversations();
        }, 500);
    });
    
    // Time filter change
    $('#aethos-time-filter').on('change', function() {
        currentPage = 1;
        selectedConversationId = null;
        loadConversations();
    });
    
    // Pagination
    $('#aethos-conv-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadConversations();
        }
    });
    
    $('#aethos-conv-next').on('click', function() {
        currentPage++;
        loadConversations();
    });
    
    // Export
    $('#aethos-export-conversations').on('click', function() {
        window.location.href = ajaxurl + '?action=aethos_export_conversations&nonce=<?php echo wp_create_nonce("aethos_export_conversations"); ?>';
    });
    
    // Refresh button - with spinner and success feedback
    $('#aethos-refresh-conversations').on('click', function() {
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        // Show spinning icon
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="vertical-align: middle; animation: aethos-spin 1s linear infinite;"></span> Refreshing...');
        
        // Store selected ID before refresh
        const currentSelectedId = selectedConversationId;
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_conversations',
                nonce: '<?php echo wp_create_nonce("aethos_get_conversations"); ?>',
                page: currentPage,
                search: $('#aethos-conv-search').val(),
                days: $('#aethos-time-filter').val() !== 'all' ? $('#aethos-time-filter').val() : ''
            },
            success: function(response) {
                if (response.success && response.data.conversations.length > 0) {
                    let html = '';
                    response.data.conversations.forEach(function(conv) {
                        const isSelected = conv.id == currentSelectedId;
                        const selectedStyle = isSelected ? 'background: #eff6ff; border-left: 3px solid #4f46e5;' : '';
                        html += '<div class="aethos-conv-item" data-id="' + conv.id + '" style="padding: 16px; border-bottom: 1px solid #e5e7eb; cursor: pointer; ' + selectedStyle + '">';
                        html += '<div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">';
                        html += '<div style="font-weight: 500; font-size: 14px;">' + conv.user_id + '</div>';
                        html += '<div style="font-size: 12px; color: #6b7280;">' + conv.time_ago + '</div>';
                        html += '</div>';
                        html += '<div style="font-size: 13px; color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + conv.snippet + '</div>';
                        html += '</div>';
                    });
                    $('#aethos-conversations-list').html(html);
                    $('#aethos-conv-pagination').text('Page ' + currentPage + ' of ' + response.data.total_pages);
                }
                
                // If a conversation was selected, reload its details
                if (currentSelectedId) {
                    loadConversationDetails(currentSelectedId);
                }
                
                // Show success feedback
                $btn.html('<span class="dashicons dashicons-yes-alt" style="vertical-align: middle; color: #10b981;"></span> Done!');
                setTimeout(function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }, 1000);
            },
            error: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // Load on page load
    loadConversations();
});
</script>

<?php
// Include footer
