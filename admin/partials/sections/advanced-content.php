
<div style="margin-bottom: 24px;">
    <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 600;">Advanced Settings</h2>
    <p style="margin: 0; color: #6b7280; font-size: 14px;">Configure advanced options for privacy, performance, and data management.</p>
</div>

<!-- Data Handling & Privacy -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Data Handling & Privacy</h3>
        <p style="margin: 0 0 24px 0; color: #6b7280; font-size: 14px;">Control how conversation data is collected and stored.</p>
        
        <div style="margin-bottom: 24px;">
            <label style="display: flex; align-items: start; gap: 12px; cursor: pointer;">
                <input type="checkbox" name="aethos_log_conversations" value="1" <?php checked( $log_conversations, true ); ?> style="margin-top: 4px; width: 18px; height: 18px;">
                <div>
                    <div style="font-weight: 500; margin-bottom: 4px;">Log Conversations</div>
                    <div style="color: #6b7280; font-size: 14px;">Store conversation history for analytics and improvement. Disable to enhance privacy.</div>
                </div>
            </label>
        </div>
        
        <div style="margin-bottom: 24px;">
            <label style="display: flex; align-items: start; gap: 12px; cursor: pointer;">
                <input type="checkbox" name="aethos_log_ip" value="1" <?php checked( $log_ip, true ); ?> style="margin-top: 4px; width: 18px; height: 18px;">
                <div>
                    <div style="font-weight: 500; margin-bottom: 4px;">Log IP Addresses</div>
                    <div style="color: #6b7280; font-size: 14px;">Store user IP addresses with conversations. May be required for security or compliance.</div>
                </div>
            </label>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Data Retention Period</label>
            <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 13px;">Automatically delete conversation logs older than this period.</p>
            <select name="aethos_data_retention" class="regular-text" style="max-width: 300px;">
                <option value="30" <?php selected( $data_retention, 30 ); ?>>30 Days</option>
                <option value="60" <?php selected( $data_retention, 60 ); ?>>60 Days</option>
                <option value="90" <?php selected( $data_retention, 90 ); ?>>90 Days</option>
                <option value="180" <?php selected( $data_retention, 180 ); ?>>180 Days</option>
                <option value="365" <?php selected( $data_retention, 365 ); ?>>1 Year</option>
                <option value="0" <?php selected( $data_retention, 0 ); ?>>Never Delete</option>
            </select>
        </div>

        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #f3f4f6;">
            <label style="display: flex; align-items: start; gap: 12px; cursor: pointer;">
                <input type="checkbox" name="aethos_delete_on_uninstall" value="1" <?php checked( $delete_on_uninstall, true ); ?> style="margin-top: 4px; width: 18px; height: 18px;">
                <div>
                    <div style="font-weight: 500; margin-bottom: 4px; color: #ef4444;">Delete Data on Uninstall</div>
                    <div style="color: #6b7280; font-size: 14px;">Permanently delete all plugin data (tables and settings) when the plugin is uninstalled. <strong>This action cannot be undone.</strong></div>
                </div>
            </label>
        </div>
    </div>
    
    <!-- Performance Optimization -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Performance Optimization</h3>
        <p style="margin: 0 0 24px 0; color: #6b7280; font-size: 14px;">Optimize chatbot performance and resource usage.</p>
        
        <div style="margin-bottom: 24px;">
            <label style="display: flex; align-items: start; gap: 12px; cursor: pointer;">
                <input type="checkbox" name="aethos_cache_enabled" value="1" <?php checked( $cache_enabled, true ); ?> style="margin-top: 4px; width: 18px; height: 18px;">
                <div>
                    <div style="font-weight: 500; margin-bottom: 4px;">Enable Response Caching</div>
                    <div style="color: #6b7280; font-size: 14px;">Cache frequently asked questions to reduce API calls and improve response time.</div>
                </div>
            </label>
        </div>
        
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Rate Limit (requests per minute)</label>
            <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 13px;">Maximum number of requests a user can make per minute. Helps prevent abuse.</p>
            <input type="number" name="aethos_rate_limit" value="<?php echo esc_attr( $rate_limit ); ?>" class="small-text">
            <span style="color: #6b7280; font-size: 13px; margin-left: 8px;">requests/minute</span>
        </div>
    </div>
    
    <!-- Danger Zone -->
    <div style="background: #fff; border: 2px solid #ef4444; border-radius: 8px; padding: 24px;">
        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600; color: #ef4444;">Danger Zone</h3>
        <p style="margin: 0 0 24px 0; color: #6b7280; font-size: 14px;">Irreversible actions that affect your chatbot data.</p>
        
        <div style="display: flex; gap: 12px;">
            <button type="button" id="aethos-clear-cache" class="button">
                <span class="dashicons dashicons-trash" style="vertical-align: middle;"></span> Clear Vectors
            </button>
            <button type="button" id="aethos-delete-conversations" class="button" style="color: #ef4444; border-color: #ef4444;">
                <span class="dashicons dashicons-warning" style="vertical-align: middle;"></span> Delete All Conversations
            </button>
            <button type="button" id="aethos-reset-plugin" class="button" style="color: #ef4444; border-color: #ef4444;">
                <span class="dashicons dashicons-update" style="vertical-align: middle;"></span> Reset Plugin Settings
            </button>
        </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Clear Cache
    $('#aethos-clear-cache').on('click', function() {
        if (!confirm('Are you sure you want to clear the response cache? This will temporarily slow down responses until the cache rebuilds.')) {
            return;
        }
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Clearing...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_clear_cache',
                nonce: '<?php echo wp_create_nonce("aethos_clear_cache"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Cache cleared successfully!');
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to clear cache. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // Delete All Conversations
    $('#aethos-delete-conversations').on('click', function() {
        if (!confirm('⚠️ WARNING: This will permanently delete ALL conversation history and cannot be undone!\n\nAre you absolutely sure?')) {
            return;
        }
        
        if (!confirm('This is your last chance. Type "DELETE" in the next prompt to confirm.')) {
            return;
        }
        
        const confirmation = prompt('Type DELETE to confirm:');
        if (confirmation !== 'DELETE') {
            alert('Deletion cancelled.');
            return;
        }
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Deleting...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_delete_all_conversations',
                nonce: '<?php echo wp_create_nonce("aethos_delete_all_conversations"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('All conversations deleted successfully.');
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to delete conversations. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // Reset Plugin Settings
    $('#aethos-reset-plugin').on('click', function() {
        if (!confirm('⚠️ WARNING: This will reset ALL plugin settings to their default values!\n\nYour API keys, appearance settings, and all configurations will be lost.\n\nAre you sure?')) {
            return;
        }
        
        const confirmation = prompt('Type RESET to confirm:');
        if (confirmation !== 'RESET') {
            alert('Reset cancelled.');
            return;
        }
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Resetting...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_reset_settings',
                nonce: '<?php echo wp_create_nonce("aethos_reset_settings"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Plugin settings reset successfully. The page will now reload.');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to reset settings. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
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
</style>

<?php
// Include footer
