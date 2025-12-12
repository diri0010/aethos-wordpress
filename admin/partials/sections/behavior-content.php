<div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
        <h2 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600;">Chatbot Behavior</h2>
        <p style="margin: 0 0 24px 0; color: #6b7280; font-size: 14px;">Configure how your AI chatbot interacts with visitors on your site.</p>
        
        <!-- Persona & Tone -->
        <div style="margin-bottom: 32px;">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600;">Persona & Tone</h3>
            <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px;">Choose the personality for your AI chatbot. This affects the tone and style of its responses.</p>
            
            <div style="margin-bottom: 16px;">
                <label for="aethos_chatbot_persona" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Chatbot Persona</label>
                <select name="aethos_chatbot_persona" id="aethos_chatbot_persona" class="regular-text" style="width: 100%; max-width: 400px;">
                    <option value="friendly" <?php selected( $chatbot_persona, 'friendly' ); ?>>Friendly & Helpful</option>
                    <option value="professional" <?php selected( $chatbot_persona, 'professional' ); ?>>Professional & Formal</option>
                    <option value="casual" <?php selected( $chatbot_persona, 'casual' ); ?>>Casual & Conversational</option>
                    <option value="technical" <?php selected( $chatbot_persona, 'technical' ); ?>>Technical & Precise</option>
                </select>
            </div>
        </div>
        
        <!-- Initial Interaction -->
        <div style="margin-bottom: 32px;">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600;">Initial Interaction</h3>
            
            <div style="margin-bottom: 16px;">
                <label for="aethos_greeting_message" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Initial Greeting Message</label>
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px;">The first message the chatbot displays to a visitor.</p>
                <input type="text" name="aethos_greeting_message" id="aethos_greeting_message" value="<?php echo esc_attr( $greeting_message ); ?>" class="regular-text" style="width: 100%;">
            </div>
            
            <div>
                <label for="aethos_fallback_response" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">
                    Fallback Response
                    <span class="dashicons dashicons-info" style="color: #6b7280; font-size: 16px; vertical-align: middle;" title="Shown when the chatbot can't find an answer"></span>
                </label>
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px;">Message shown when the AI cannot find a suitable answer.</p>
                <input type="text" name="aethos_fallback_response" id="aethos_fallback_response" value="<?php echo esc_attr( $fallback_response ); ?>" class="regular-text" style="width: 100%;">
            </div>
        </div>
    </div>
    
<!-- Reset Button -->
<div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
    <button type="button" id="aethos-reset-behavior" class="button" style="color: #6b7280;">
        <span class="dashicons dashicons-image-rotate" style="vertical-align: middle;"></span> Reset to Defaults
    </button>
    <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 12px;">This will restore all behavior settings to their default values.</p>
</div>

<script>
jQuery(document).ready(function($) {
    // Reset to defaults button
    $('#aethos-reset-behavior').on('click', function() {
        if (!confirm('Are you sure you want to reset all behavior settings to their defaults? This cannot be undone.')) {
            return;
        }
        
        const $button = $(this);
        const originalText = $button.html();
        $button.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite; vertical-align: middle;"></span> Resetting...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_reset_behavior',
                nonce: '<?php echo wp_create_nonce("aethos_reset_behavior"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Behavior settings have been reset to defaults. The page will now reload.');
                    window.location.reload();
                } else {
                    alert('Error: ' + (response.data || 'Failed to reset settings'));
                    $button.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                alert('Network error. Please try again.');
                $button.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Persona preview
    $('#aethos_chatbot_persona').on('change', function() {
        const persona = $(this).val();
        const examples = {
            'friendly': 'Hi there! 😊 I\'m here to help you with anything you need. What can I do for you today?',
            'professional': 'Good day. I am here to assist you with your inquiries. How may I be of service?',
            'casual': 'Hey! What\'s up? Need help with something?',
            'technical': 'System ready. Please provide your query for processing.'
        };
        
        // Show example (optional - you can add a preview area)
        console.log('Persona changed to:', persona, 'Example:', examples[persona]);
    });
});
</script>

<?php
// Include footer
