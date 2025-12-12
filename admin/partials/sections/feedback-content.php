
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 600;">User Feedback & Ratings</h2>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">Monitor user satisfaction and feedback.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Average Rating</div>
        <div style="font-size: 32px; font-weight: 600; margin-bottom: 8px;" id="stat-avg-rating">0.0</div>
        <div style="display: flex; align-items: center; gap: 4px; font-size: 13px;">
            <span class="dashicons dashicons-arrow-up-alt" style="color: #10b981; font-size: 16px;"></span>
            <span style="color: #10b981;" id="stat-rating-change">+0%</span>
            <span style="color: #6b7280;">vs last period</span>
        </div>
    </div>
    
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Total Feedback</div>
        <div style="font-size: 32px; font-weight: 600; margin-bottom: 8px;" id="stat-total-feedback">0</div>
        <div style="display: flex; align-items: center; gap: 4px; font-size: 13px;">
            <span class="dashicons dashicons-arrow-up-alt" style="color: #10b981; font-size: 16px;"></span>
            <span style="color: #10b981;" id="stat-feedback-change">+0%</span>
            <span style="color: #6b7280;">vs last period</span>
        </div>
    </div>
    
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Positive Sentiment</div>
        <div style="font-size: 32px; font-weight: 600; margin-bottom: 8px;" id="stat-positive-sentiment">0%</div>
        <div style="display: flex; align-items: center; gap: 4px; font-size: 13px;">
            <span class="dashicons dashicons-arrow-up-alt" style="color: #10b981; font-size: 16px;"></span>
            <span style="color: #10b981;" id="stat-sentiment-change">+0%</span>
            <span style="color: #6b7280;">vs last period</span>
        </div>
    </div>
</div>

<!-- Rating Distribution -->
<div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
    <h3 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600;">Rating Distribution</h3>
    
    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 32px; align-items: center;">
        <!-- Overall Rating -->
        <div style="text-align: center;">
            <div style="font-size: 48px; font-weight: 600; margin-bottom: 8px;" id="overall-rating">0.0</div>
            <div style="color: #f59e0b; font-size: 24px; margin-bottom: 8px;">★★★★★</div>
            <div style="color: #6b7280; font-size: 14px;"><span id="total-reviews">0</span> reviews</div>
        </div>
        
        <!-- Rating Bars -->
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <?php for ($i = 5; $i >= 1; $i--): ?>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="width: 60px; font-size: 14px;"><?php echo $i; ?> stars</span>
                <div style="flex: 1; background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                    <div id="rating-bar-<?php echo $i; ?>" style="background: #f59e0b; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <span id="rating-count-<?php echo $i; ?>" style="width: 50px; text-align: right; font-size: 14px; color: #6b7280;">0%</span>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Filters and Feedback List -->
<div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Feedback List</h3>
        <div style="display: flex; gap: 12px;">
            <select id="feedback-timerange" class="regular-text">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="90">Last 90 Days</option>
                <option value="all">All Time</option>
            </select>
            <select id="feedback-rating-filter" class="regular-text">
                <option value="">All Ratings</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
            </select>
            <select id="feedback-sort" class="regular-text">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="highest">Highest Rated</option>
                <option value="lowest">Lowest Rated</option>
            </select>
        </div>
    </div>
    
    <input type="text" id="feedback-search" placeholder="Search feedback..." class="regular-text" style="width: 100%; margin-bottom: 20px;">
    
    <!-- Feedback Items -->
    <div id="feedback-list" style="display: flex; flex-direction: column; gap: 16px;">
        <div style="text-align: center; padding: 60px; color: #9ca3af;">
            <span class="dashicons dashicons-star-filled" style="font-size: 64px; opacity: 0.3;"></span>
            <p style="margin: 16px 0 0 0; font-size: 16px;">No feedback yet</p>
        </div>
    </div>
    
    <!-- Pagination -->
    <div style="display: flex; justify-content: center; align-items: center; gap: 12px; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
        <button type="button" id="feedback-prev" class="button" disabled>Previous</button>
        <span id="feedback-pagination" style="font-size: 14px; color: #6b7280;">Page 1</span>
        <button type="button" id="feedback-next" class="button">Next</button>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentPage = 1;
    
    // Load feedback statistics
    function loadStatistics() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_feedback_stats',
                nonce: '<?php echo wp_create_nonce("aethos_get_feedback_stats"); ?>',
                timerange: $('#feedback-timerange').val()
            },
            success: function(response) {
                if (response.success) {
                    const stats = response.data;
                    
                    $('#stat-avg-rating').text(stats.avg_rating.toFixed(1));
                    $('#stat-rating-change').text(stats.rating_change);
                    
                    $('#stat-total-feedback').text(stats.total_feedback);
                    $('#stat-feedback-change').text(stats.feedback_change);
                    
                    $('#stat-positive-sentiment').text(stats.positive_sentiment + '%');
                    $('#stat-sentiment-change').text(stats.sentiment_change);
                    
                    $('#overall-rating').text(stats.avg_rating.toFixed(1));
                    $('#total-reviews').text(stats.total_feedback);
                    
                    // Update rating bars
                    for (let i = 1; i <= 5; i++) {
                        const percentage = stats.rating_distribution[i] || 0;
                        $('#rating-bar-' + i).css('width', percentage + '%');
                        $('#rating-count-' + i).text(percentage + '%');
                    }
                }
            }
        });
    }
    
    // Load feedback list
    function loadFeedbackList() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_feedback_list',
                nonce: '<?php echo wp_create_nonce("aethos_get_feedback_list"); ?>',
                page: currentPage,
                timerange: $('#feedback-timerange').val(),
                rating: $('#feedback-rating-filter').val(),
                sort: $('#feedback-sort').val(),
                search: $('#feedback-search').val()
            },
            success: function(response) {
                if (response.success && response.data.items.length > 0) {
                    let html = '';
                    response.data.items.forEach(function(item) {
                        let stars = '';
                        for (let i = 1; i <= 5; i++) {
                            stars += i <= item.rating ? '★' : '☆';
                        }
                        
                        html += '<div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">';
                        html += '<div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">';
                        html += '<div style="color: #f59e0b; font-size: 20px;">' + stars + '</div>';
                        html += '<div style="font-size: 13px; color: #6b7280;">' + item.time_ago + '</div>';
                        html += '</div>';
                        html += '<div style="font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 12px;">' + item.feedback_text + '</div>';
                        html += '<div style="display: flex; justify-content: space-between; align-items: center;">';
                        html += '<div style="font-size: 13px; color: #6b7280;">User: ' + item.user_id + '</div>';
                        html += '<a href="' + item.conversation_link + '" class="button button-small">View Conversation</a>';
                        html += '</div>';
                        html += '</div>';
                    });
                    $('#feedback-list').html(html);
                    
                    // Update pagination
                    $('#feedback-pagination').text('Page ' + currentPage + ' of ' + response.data.total_pages);
                    $('#feedback-prev').prop('disabled', currentPage === 1);
                    $('#feedback-next').prop('disabled', currentPage >= response.data.total_pages);
                } else {
                    $('#feedback-list').html('<div style="text-align: center; padding: 60px; color: #9ca3af;"><p>No feedback found</p></div>');
                }
            }
        });
    }
    
    // Load data on page load
    loadStatistics();
    loadFeedbackList();
    
    // Reload on filter change
    $('#feedback-timerange, #feedback-rating-filter, #feedback-sort').on('change', function() {
        currentPage = 1;
        loadStatistics();
        loadFeedbackList();
    });
    
    // Search
    let searchTimeout;
    $('#feedback-search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            loadFeedbackList();
        }, 500);
    });
    
    // Pagination
    $('#feedback-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadFeedbackList();
        }
    });
    
    $('#feedback-next').on('click', function() {
        currentPage++;
        loadFeedbackList();
    });
});
</script>

<?php
// Include footer
