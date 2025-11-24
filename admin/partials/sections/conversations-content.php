
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 600;">Conversation History</h2>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">Browse and search past chatbot conversations.</p>
    </div>
    <button type="button" id="aethos-export-conversations" class="button">
        <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> Export CSV
    </button>
</div>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 20px;">
    <!-- Conversations List -->
    <div>
        <!-- Search and Filters -->
        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
            <input type="text" id="aethos-conv-search" placeholder="Search conversations..." class="regular-text" style="width: 100%; margin-bottom: 12px;">
            
            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                <button type="button" class="button button-small aethos-filter-btn" data-filter="date">Date Range</button>
                <button type="button" class="button button-small aethos-filter-btn" data-filter="rating">Rating</button>
            </div>
            
            <button type="button" id="aethos-clear-filters" class="button button-small" style="width: 100%;">Clear Filters</button>
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
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_conversations',
                nonce: '<?php echo wp_create_nonce("aethos_get_conversations"); ?>',
                page: currentPage,
                search: $('#aethos-conv-search').val()
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
                            html += '<div style="display: flex; justify-content: flex-start;">';
                            html += '<div style="background: #f3f4f6; color: #111827; padding: 12px 16px; border-radius: 16px 16px 16px 4px; max-width: 70%;">';
                            html += '<div style="font-size: 14px;">' + msg.content + '</div>';
                            html += '<div style="font-size: 11px; color: #6b7280; margin-top: 4px;">' + msg.timestamp + '</div>';
                            html += '</div></div>';
                        }
                    });
                    html += '</div>';
                    
                    $('#aethos-conversation-details').html(html);
                    loadConversations(); // Refresh list to show selection
                }
            }
        });
    }
    
    // Click on conversation
    $(document).on('click', '.aethos-conv-item', function() {
        const conversationId = $(this).data('id');
        loadConversationDetails(conversationId);
    });
    
    // Search
    let searchTimeout;
    $('#aethos-conv-search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            loadConversations();
        }, 500);
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
    
    // Load on page load
    loadConversations();
});
</script>

<?php
// Include footer
