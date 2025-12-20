<?php
/**
 * Discovered Content Section
 * Shows scanned content, scan history, and scheduling options
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get current options
$scan_schedule = get_option('aethos_scan_schedule', 'daily');
$last_scan = get_option('aethos_last_full_scan', 0);
$api_key = get_option('aethos_api_key', '');
$connection_status = get_option('aethos_connection_status', 'unknown');
$is_connected = !empty($api_key) && $connection_status === 'connected';
// Get exclusion lists
$excluded_pages = get_option('aethos_kb_excluded_pages', array());
if (!is_array($excluded_pages)) $excluded_pages = array();

$excluded_posts = get_option('aethos_kb_excluded_posts', array());
if (!is_array($excluded_posts)) $excluded_posts = array();

$excluded_products = get_option('aethos_kb_excluded_woo_products', array());
if (!is_array($excluded_products)) $excluded_products = array();

// Get statistics
$storage = new Aethos_Vector_Storage();
$stats = $storage->get_stats();

// Get recent scans from sync log
global $wpdb;
$sync_log_table = $wpdb->prefix . 'aethos_sync_log';
$recent_scans = $wpdb->get_results(
    "SELECT * FROM $sync_log_table ORDER BY started_at DESC LIMIT 5"
);

// Get last scan info
$last_scan_info = !empty($recent_scans) ? $recent_scans[0] : null;
?>

<style>
.discovered-content-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.scan-now-btn {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
}

.scan-now-btn:hover {
    background: #2563eb;
}

.scan-now-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.scan-now-btn .dashicons {
    font-size: 18px;
    width: 18px;
    height: 18px;
}

.last-scan-status {
    color: #6b7280;
    font-size: 14px;
    margin-top: 8px;
}

.last-scan-status .status-completed {
    color: #10b981;
    font-weight: 500;
}

.last-scan-status .status-failed {
    color: #ef4444;
    font-weight: 500;
}

.content-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 24px;
}

.content-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 8px 0;
}

.content-card p {
    color: #6b7280;
    font-size: 14px;
    margin: 0 0 16px 0;
}

.schedule-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.schedule-option {
    display: flex;
    align-items: center;
    gap: 8px;
}

.schedule-option input[type="radio"] {
    margin: 0;
}

.schedule-option label {
    font-size: 14px;
    color: #374151;
    margin: 0;
}

.recent-scans-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.scan-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 6px;
}

.scan-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.scan-icon.success {
    background: #d1fae5;
    color: #065f46;
}

.scan-icon.failed {
    background: #fee2e2;
    color: #991b1b;
}

.scan-info {
    flex: 1;
}

.scan-date {
    font-size: 14px;
    font-weight: 500;
    color: #111827;
}

.scan-details {
    font-size: 13px;
    color: #6b7280;
    margin-top: 2px;
}

.content-table {
    width: 100%;
    border-collapse: collapse;
}

.content-table thead {
    background: #f9fafb;
}

.content-table th {
    text-align: left;
    padding: 12px;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #e5e7eb;
}

.content-table td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 14px;
    color: #374151;
}

.content-table tbody tr:hover {
    background: #f9fafb;
}

.content-type-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.content-type-badge.post {
    background: #dbeafe;
    color: #1e40af;
}

.content-type-badge.page {
    background: #e0e7ff;
    color: #4338ca;
}

.content-type-badge.product {
    background: #fef3c7;
    color: #92400e;
}

.action-btn {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 13px;
    border: 1px solid #d1d5db;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #f3f4f6;
}

.action-btn.scan {
    color: #3b82f6;
    border-color: #3b82f6;
}

.action-btn.exclude {
    color: #6b7280;
}

.action-btn.delete {
    color: #ef4444;
    border-color: #ef4444;
}

.action-btn.delete:hover {
    background: #fef2f2;
}

.remove-all-btn {
    background: #ef4444;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
    margin-top: 24px;
}

.remove-all-btn:hover {
    background: #dc2626;
}

.remove-all-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.no-content-message {
    text-align: center;
    padding: 48px 24px;
    color: #6b7280;
}

.no-content-message .dashicons {
    font-size: 48px;
    width: 48px;
    height: 48px;
    color: #d1d5db;
    margin-bottom: 16px;
}

#scan-progress {
    display: none;
    margin-top: 16px;
    padding: 12px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    color: #1e40af;
    font-size: 14px;
}
</style>

<div class="discovered-content-header">
    <div>
        <h2 style="font-size: 20px; font-weight: 600; color: #111827; margin: 0;">Discovered Content</h2>
        <p style="color: #6b7280; margin-top: 4px; font-size: 14px;">Manage the content that the AI uses for its knowledge base.</p>
        <?php if ($last_scan_info): ?>
            <div class="last-scan-status">
                Last Scan: <?php echo date('F j, Y', strtotime($last_scan_info->started_at)); ?> - 
                <span class="status-<?php echo $last_scan_info->status; ?>">
                    <?php echo ucfirst($last_scan_info->status); ?>
                </span>
                <?php if ($last_scan_info->status === 'completed'): ?>
                    - <?php echo $last_scan_info->vectors_created + $last_scan_info->vectors_updated; ?> items discovered.
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <div style="text-align: right;">
        <button type="button" class="scan-now-btn" id="scan-now-btn" <?php echo !$is_connected ? 'disabled' : ''; ?>>
            <span class="dashicons dashicons-update"></span>
            Scan Now
        </button>
        <?php if (!$is_connected): ?>
            <p style="color: #ef4444; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Connection required</p>
        <?php endif; ?>
    </div>
</div>

<div id="scan-progress"></div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
    <!-- Automated Scan Schedule -->
    <div class="content-card">
        <h3>Automated Scan Schedule</h3>
        <p>Set how often Aethos AI automatically scans your website. Regular scans ensure your chatbot always has the latest information.</p>
        
        <div class="schedule-options">
            <div class="schedule-option">
                <input type="radio" id="schedule-daily" name="aethos_scan_schedule" value="daily" <?php checked($scan_schedule, 'daily'); ?>>
                <label for="schedule-daily">Daily</label>
            </div>
            <div class="schedule-option">
                <input type="radio" id="schedule-weekly" name="aethos_scan_schedule" value="weekly" <?php checked($scan_schedule, 'weekly'); ?>>
                <label for="schedule-weekly">Weekly</label>
            </div>
            <div class="schedule-option">
                <input type="radio" id="schedule-monthly" name="aethos_scan_schedule" value="monthly" <?php checked($scan_schedule, 'monthly'); ?>>
                <label for="schedule-monthly">Monthly</label>
            </div>
        </div>
    </div>

    <!-- Recent Scans -->
    <div class="content-card">
        <h3>Recent Scans</h3>
        <div class="recent-scans-list">
            <?php if (!empty($recent_scans)): ?>
                <?php foreach ($recent_scans as $scan): ?>
                    <div class="scan-item">
                        <div class="scan-icon <?php echo $scan->status === 'completed' ? 'success' : 'failed'; ?>">
                            <span class="dashicons dashicons-<?php echo $scan->status === 'completed' ? 'yes' : 'no'; ?>"></span>
                        </div>
                        <div class="scan-info">
                            <div class="scan-date">
                                <?php 
                                $timestamp = strtotime($scan->started_at);
                                echo gmdate('M j, Y \a\t H:i', $timestamp) . ' UTC';
                                ?>
                            </div>
                            <div class="scan-details">
                                <?php if ($scan->status === 'completed'): ?>
                                    <?php echo $scan->vectors_created + $scan->vectors_updated; ?> items found
                                <?php else: ?>
                                    <span style="color: #ef4444;">Failed</span> - <?php echo esc_html($scan->error_message ?: 'Scan interrupted'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 24px; color: #9ca3af;">
                    No scans yet. Click "Scan Now" to start.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Discovered Content Table -->
<div class="content-card">
    <h3>Discovered Content</h3>
    
    <?php
    // Get all discovered content (grouped by post_id)
    $vectors_table = $wpdb->prefix . 'aethos_vectors';
    $discovered_content = $wpdb->get_results("
        SELECT 
            post_id,
            post_type,
            post_url,
            MAX(created_at) as last_scanned,
            COUNT(*) as chunk_count
        FROM $vectors_table
        GROUP BY post_id
        ORDER BY last_scanned DESC
        LIMIT 50
    ");
    ?>
    
    <?php if (!empty($discovered_content)): ?>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Content</th>
                    <th>Type</th>
                    <th>URL</th>
                    <th>Last Scanned</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($discovered_content as $item): ?>
                    <?php 
                    $post = get_post($item->post_id);
                    if (!$post) continue;
                    $is_excluded = false;
                    if ($post->post_type === 'page') {
                        $is_excluded = in_array($item->post_id, $excluded_pages);
                    } elseif ($post->post_type === 'post') {
                        $is_excluded = in_array($item->post_id, $excluded_posts);
                    } elseif ($post->post_type === 'product') {
                        $is_excluded = in_array($item->post_id, $excluded_products);
                    } else {
                        $cpt_excluded = get_option("aethos_kb_excluded_{$post->post_type}", array());
                        if (!is_array($cpt_excluded)) $cpt_excluded = array();
                        $is_excluded = in_array($item->post_id, $cpt_excluded);
                    }
                    ?>
                    <tr data-post-id="<?php echo $item->post_id; ?>">
                        <td>
                            <strong><?php echo esc_html($post->post_title); ?></strong>
                            <?php if ($is_excluded): ?>
                                <span style="color: #9ca3af; font-size: 12px; margin-left: 8px;">(Excluded)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="content-type-badge <?php echo $item->post_type; ?>">
                                <?php echo ucfirst($item->post_type); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo esc_url($item->post_url); ?>" target="_blank" style="color: #3b82f6; text-decoration: none;">
                                <?php echo parse_url($item->post_url, PHP_URL_PATH); ?>
                            </a>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($item->last_scanned)); ?></td>
                        <td>
                            <button type="button" class="action-btn scan scan-single-btn" data-post-id="<?php echo $item->post_id; ?>">
                                <span class="dashicons dashicons-update" style="font-size: 14px; width: 14px; height: 14px;"></span>
                                Scan Now
                            </button>
                            <button type="button" class="action-btn exclude exclude-btn" data-post-id="<?php echo $item->post_id; ?>">
                                <span class="dashicons dashicons-<?php echo $is_excluded ? 'visibility' : 'hidden'; ?>" style="font-size: 14px; width: 14px; height: 14px;"></span>
                                <?php echo $is_excluded ? 'Include' : 'Exclude'; ?>
                            </button>
                            <button type="button" class="action-btn delete delete-btn" data-post-id="<?php echo $item->post_id; ?>">
                                <span class="dashicons dashicons-trash" style="font-size: 14px; width: 14px; height: 14px;"></span>
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-content-message">
            <div class="dashicons dashicons-search"></div>
            <p style="font-size: 16px; font-weight: 500; color: #374151; margin-bottom: 8px;">No content discovered yet</p>
            <p style="font-size: 14px;">Click "Scan Now" to start discovering your website content.</p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($discovered_content)): ?>
        <button type="button" class="remove-all-btn" id="remove-all-btn">
            <span class="dashicons dashicons-trash"></span>
            Remove All Discovered Content
        </button>
    <?php endif; ?>
</div>

<!-- Modal removed - exclusion now happens directly without confirmation -->

<style>
.aethos-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.aethos-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
}

.aethos-modal-content {
    position: relative;
    background: white;
    border-radius: 8px;
    padding: 0;
    max-width: 480px;
    width: 90%;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    z-index: 100001;
}

.modal-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.modal-body {
    padding: 20px 24px;
}

.modal-message {
    margin: 0 0 16px 0;
    color: #374151;
    font-size: 14px;
}

.modal-list {
    margin: 0 0 16px 0;
    padding-left: 24px;
    color: #6b7280;
    font-size: 14px;
}

.modal-list li {
    margin-bottom: 8px;
}

.modal-warning {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 6px;
    padding: 12px;
    color: #991b1b;
    font-size: 13px;
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 0 0 8px 8px;
}

.button-danger {
    background: #dc2626 !important;
    border-color: #dc2626 !important;
}

.button-danger:hover {
    background: #b91c1c !important;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Scan Now button - Batch Processing
    $('#scan-now-btn').on('click', function() {
        var $btn = $(this);
        var $progress = $('#scan-progress');
        var totalItemsFound = 0;
        var batchSize = 5; // Process 5 posts per batch
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Scanning...');
        $progress.show().text('Starting full site scan...');
        
        // Recursive function to process batches
        function processBatch(offset) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_scan_now',
                    nonce: '<?php echo wp_create_nonce('aethos_scan_now'); ?>',
                    offset: offset,
                    limit: batchSize
                },
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        totalItemsFound += data.items_found;
                        
                        // Calculate progress
                        var processed = offset + data.processed_count;
                        var total = data.total_posts;
                        var percentage = Math.round((processed / total) * 100);
                        
                        $progress.text('Scanning... ' + processed + ' of ' + total + ' posts (' + percentage + '%)');
                        
                        // Continue if there are more posts
                        if (data.has_more) {
                            // Add 2-second delay between batches to avoid rate limiting
                            setTimeout(function() {
                                processBatch(offset + batchSize);
                            }, 2000);
                        } else {
                            // Scan complete
                            $progress.css({background: '#f0fdf4', borderColor: '#86efac', color: '#166534'})
                                    .text('Scan completed! Found ' + totalItemsFound + ' items. Refreshing page...');
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    } else {
                        $progress.css({background: '#fef2f2', borderColor: '#fecaca', color: '#991b1b'})
                                .text('Error: ' + response.data.message);
                        $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Scan Now');
                    }
                },
                error: function() {
                    $progress.css({background: '#fef2f2', borderColor: '#fecaca', color: '#991b1b'})
                            .text('Error: Failed to connect to server');
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Scan Now');
                }
            });
        }
        
        // Start processing from offset 0
        processBatch(0);
    });
    
    // Scan single post
    $('.scan-single-btn').on('click', function() {
        var $btn = $(this);
        var postId = $btn.data('post-id');
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Scanning...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_scan_single',
                post_id: postId,
                nonce: '<?php echo wp_create_nonce('aethos_scan_single'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $btn.html('<span class="dashicons dashicons-yes"></span> Done');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + response.data.message);
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Scan Now');
                }
            }
        });
    });
    
    // Exclude button - Direct toggle (no confirmation modal needed)
    $('.exclude-btn').on('click', function() {
        var $btn = $(this);
        var postId = $btn.data('post-id');
        var $row = $btn.closest('tr');
        var isExcluded = $btn.text().trim() === 'Include';
        
        $btn.prop('disabled', true);
        
        // Toggle between exclude and include
        var action = isExcluded ? 'aethos_include_post_to_kb' : 'aethos_exclude_post_from_kb';
        var nonce = isExcluded ? '<?php echo wp_create_nonce('aethos_toggle_exclude'); ?>' : '<?php echo wp_create_nonce('aethos_exclude_post'); ?>';
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: action,
                post_id: postId,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                alert('Error: Failed to connect to server');
                $btn.prop('disabled', false);
            }
        });
    });
    
    // Delete button
    $('.delete-btn').on('click', function() {
        var $btn = $(this);
        var postId = $btn.data('post-id');
        var $row = $btn.closest('tr');
        var postTitle = $row.find('strong').text();
        
        if (!confirm('Are you sure you want to delete all vectors for "' + postTitle + '"? This will remove it from the knowledge base.')) {
            return;
        }
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Deleting...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_delete_vectors',
                post_id: postId,
                nonce: '<?php echo wp_create_nonce('aethos_delete_vectors'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(function() {
                        $(this).remove();
                        // Check if table is now empty
                        if ($('.content-table tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    alert('Error: ' + response.data.message);
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-trash"></span> Delete');
                }
            }
        });
    });
    
    // Remove All button
    $('#remove-all-btn').on('click', function() {
        var $btn = $(this);
        var count = $('.content-table tbody tr').length;
        
        if (!confirm('Are you sure you want to remove ALL ' + count + ' discovered content items from the knowledge base? This action cannot be undone.')) {
            return;
        }
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Removing...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_remove_all_vectors',
                nonce: '<?php echo wp_create_nonce('aethos_remove_all_vectors'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Successfully removed ' + response.data.deleted_count + ' vectors from the knowledge base.');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-trash"></span> Remove All Discovered Content');
                }
            }
        });
    });
    
    // Save scan schedule
    $('input[name="aethos_scan_schedule"]').on('change', function() {
        var schedule = $(this).val();
        var $scheduleCard = $('.schedule-options').closest('.content-card');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_save_scan_schedule',
                schedule: schedule,
                nonce: '<?php echo wp_create_nonce('aethos_save_scan_schedule'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    var $msg = $('<div style="color: #10b981; font-size: 14px; margin-top: 12px; padding: 8px 12px; background: #d1fae5; border-radius: 4px; display: flex; align-items: center; gap: 8px;"><span class="dashicons dashicons-yes-alt" style="font-size: 16px; width: 16px; height: 16px;"></span> Schedule saved successfully!</div>');
                    $scheduleCard.find('.schedule-options').after($msg);
                    setTimeout(function() { 
                        $msg.fadeOut(function() { $(this).remove(); }); 
                    }, 3000);
                }
            }
        });
    });
});
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.dashicons.spin {
    animation: spin 1s linear infinite;
}
</style>
