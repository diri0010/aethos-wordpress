<?php
/**
 * Connection Content Section
 */

// Get connection status
$api_key = get_option('aethos_api_key', '');
$connection_status = !empty($api_key) ? 'connected' : 'disconnected';
?>

<h1 style="margin: 0 0 24px 0; font-size: 32px; font-weight: 700; color: #111827;">Connection</h1>

<?php if ( $connection_status === 'connected' && !empty( $api_key ) ): ?>
    <!-- Connected State -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <span style="display: inline-block; width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                    <span style="font-size: 14px; font-weight: 600; color: #10b981;">Connected</span>
                </div>
                <h2 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 600;">Your site is connected to Aethos AI</h2>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">API Key: <?php echo esc_html( substr($api_key, 0, 20) . '...' ); ?></p>
            </div>
            <button type="button" id="aethos-disconnect-btn" class="button" style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                Disconnect
            </button>
        </div>
    </div>
<?php else: ?>
    <!-- Not Connected State -->
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
        <h2 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 600;">Connect to Aethos AI</h2>
        
        <div style="margin-bottom: 20px;">
            <label for="aethos_api_key" style="display: block; font-weight: 500; margin-bottom: 8px;">API Key</label>
            <input type="text" name="aethos_api_key" id="aethos_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px;" placeholder="Enter your Aethos API key">
            <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 13px;">Don't have an API key? <a href="https://aethoslogic.com" target="_blank" style="color: #4f46e5;">Get one here</a></p>
        </div>
        
        <button type="button" id="aethos-test-connection-btn" class="button button-primary">
            Test Connection
        </button>
        
        <div id="aethos-connection-message" style="margin-top: 16px; padding: 12px; border-radius: 6px; display: none;"></div>
    </div>
<?php endif; ?>

<script>
jQuery(document).ready(function($) {
    // Test connection
    $('#aethos-test-connection-btn').on('click', function() {
        const apiKey = $('#aethos_api_key').val();
        const $btn = $(this);
        const $msg = $('#aethos-connection-message');
        
        if (!apiKey) {
            $msg.removeClass('success error').addClass('error').text('Please enter an API key').show();
            return;
        }
        
        $btn.prop('disabled', true).text('Testing...');
        $msg.hide();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_test_connection',
                nonce: '<?php echo wp_create_nonce('aethos_admin_nonce'); ?>',
                api_key: apiKey
            },
            success: function(response) {
                if (response.success) {
                    $msg.removeClass('error').addClass('success').css({background: '#d1fae5', color: '#065f46', border: '1px solid #10b981'}).text('Connection successful! Reloading...').show();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $msg.removeClass('success').addClass('error').css({background: '#fee2e2', color: '#991b1b', border: '1px solid #ef4444'}).text(response.data.message || 'Connection failed').show();
                }
            },
            error: function() {
                $msg.removeClass('success').addClass('error').css({background: '#fee2e2', color: '#991b1b', border: '1px solid #ef4444'}).text('Connection failed').show();
            },
            complete: function() {
                $btn.prop('disabled', false).text('Test Connection');
            }
        });
    });
    
    // Disconnect
    $('#aethos-disconnect-btn').on('click', function() {
        if (!confirm('Are you sure you want to disconnect?')) return;
        
        const $btn = $(this);
        $btn.prop('disabled', true).text('Disconnecting...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_disconnect',
                nonce: '<?php echo wp_create_nonce('aethos_admin_nonce'); ?>'
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Disconnection failed');
                $btn.prop('disabled', false).text('Disconnect');
            }
        });
    });
});
</script>
