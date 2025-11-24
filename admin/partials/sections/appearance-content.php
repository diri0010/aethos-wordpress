<?php
/**
 * Appearance Content Section
 */

// Get all appearance settings from database
$chat_icon = get_option('aethos_chat_icon', AETHOS_PLUGIN_URL . 'assets/images/avatars/Aethos-Bubble-Icon.png');
$header_title = get_option('aethos_header_title', 'Aethos AI Assistant');
$chat_subtitle = get_option('aethos_chat_subtitle', 'Typically replies instantly');
$primary_color = get_option('aethos_primary_color', '#0052CC');
$user_text_color = get_option('aethos_user_text_color', '#FFFFFF');
$ai_bubble_color = get_option('aethos_ai_bubble_color', '#F3F4F6');
$header_title_color = get_option('aethos_header_title_color', '#FFFFFF');
$header_subtitle_color = get_option('aethos_header_subtitle_color', '#FFFFFF');
$font_family = get_option('aethos_font_family', 'Roboto');
$font_size = get_option('aethos_font_size', 16);
$widget_position = get_option('aethos_widget_position', 'bottom-right');
$widget_size = get_option('aethos_widget_size', 'medium');
$greeting_message = get_option('aethos_greeting_message', 'Welcome! How can I help you today?');
$button_text = get_option('aethos_button_text', 'Send');
$placeholder_text = get_option('aethos_placeholder_text', 'Type your message...');

// Popular Google Fonts
$google_fonts = array(
    'Roboto' => 'Roboto',
    'Open Sans' => 'Open+Sans',
    'Lato' => 'Lato',
    'Montserrat' => 'Montserrat',
    'Poppins' => 'Poppins',
    'Inter' => 'Inter',
    'Raleway' => 'Raleway',
    'Nunito' => 'Nunito',
    'Ubuntu' => 'Ubuntu',
    'Playfair Display' => 'Playfair+Display'
);
?>

<!-- Load Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=<?php echo implode('&family=', array_values($google_fonts)); ?>&display=swap" rel="stylesheet">

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Settings Panel -->
    <div>
        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
            <h2 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 600; color: #111827;">Chat Appearance</h2>
            <p style="margin: 0 0 24px 0; color: #6b7280; font-size: 14px;">Customize the look and feel of your chat widget</p>
            
            <!-- General Section -->
            <div class="aethos-collapsible-section" style="border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 16px; overflow: hidden;">
                <button type="button" class="aethos-collapse-trigger" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: #f9fafb; border: none; cursor: pointer; font-weight: 500; text-align: left; font-size: 15px;">
                    <span>General</span>
                    <span class="dashicons dashicons-arrow-up-alt2"></span>
                </button>
                <div class="aethos-collapse-content" style="padding: 20px; display: block; background: #fff;">
                    <!-- Chat Bubble Icon -->
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Chat Bubble Icon</label>
                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 13px;">Choose an avatar for your chat</p>
                        
                        <!-- Avatar Grid -->
                        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 12px;">
                            <?php 
                            $avatars = array(
                                'Aethos-Bubble-Icon.png' => 'Aethos Default',
                                'man-1a.png' => 'Man 1',
                                'man-2a.png' => 'Man 2',
                                'man-3a.png' => 'Man 3',
                                'man-4a.png' => 'Man 4',
                                'woman-1a.png' => 'Woman 1',
                                'woman-2a.png' => 'Woman 2',
                                'woman-3a.png' => 'Woman 3',
                                'woman-4a.png' => 'Woman 4'
                            );
                            
                            foreach ($avatars as $filename => $label) :
                                $avatar_url = AETHOS_PLUGIN_URL . 'assets/images/avatars/' . $filename;
                                $is_selected = ($chat_icon === $avatar_url) ? 'selected' : '';
                            ?>
                                <div class="aethos-avatar-option <?php echo $is_selected; ?>" data-avatar="<?php echo esc_url($avatar_url); ?>" style="cursor: pointer; border: 3px solid <?php echo $is_selected ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 12px; padding: 6px; transition: all 0.2s; position: relative; background: #fff;">
                                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($label); ?>" style="width: 100%; height: auto; border-radius: 8px; display: block;">
                                    <?php if ($is_selected): ?>
                                        <div class="aethos-avatar-check" style="position: absolute; top: -4px; right: -4px; background: #4f46e5; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <span class="dashicons dashicons-yes" style="font-size: 14px; width: 14px; height: 14px; line-height: 1;"></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <input type="hidden" name="aethos_chat_icon" id="aethos_chat_icon" value="<?php echo esc_attr( $chat_icon ); ?>">
                        
                        <p style="margin: 0; color: #6b7280; font-size: 12px;">
                            <button type="button" class="button-link" id="aethos-upload-custom-icon" style="color: #4f46e5; text-decoration: none; padding: 0; border: none; background: none; cursor: pointer;">
                                <span class="dashicons dashicons-upload" style="font-size: 14px; vertical-align: middle;"></span> Upload custom icon
                            </button>
                        </p>
                    </div>
                    
                    <!-- Chat Header Title -->
                    <div style="margin-bottom: 20px;">
                        <label for="aethos_header_title" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Chat Header Title</label>
                        <input type="text" name="aethos_header_title" id="aethos_header_title" value="<?php echo esc_attr( $header_title ); ?>" class="regular-text aethos-preview-trigger" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    </div>
                    
                    <!-- Chat Subtitle -->
                    <div style="margin-bottom: 20px;">
                        <label for="aethos_chat_subtitle" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Chat Subtitle</label>
                        <input type="text" name="aethos_chat_subtitle" id="aethos_chat_subtitle" value="<?php echo esc_attr( $chat_subtitle ); ?>" class="regular-text aethos-preview-trigger" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    </div>
                    
                    <!-- Welcome Message -->
                    <div>
                        <label for="aethos_greeting_message" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Welcome Message</label>
                        <textarea name="aethos_greeting_message" id="aethos_greeting_message" rows="3" class="regular-text aethos-preview-trigger" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;"><?php echo esc_textarea( $greeting_message ); ?></textarea>
                        <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">The first message visitors see when opening the chat</p>
                    </div>
                </div>
            </div>
            
            <!-- Colors Section -->
            <div class="aethos-collapsible-section" style="border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 16px; overflow: hidden;">
                <button type="button" class="aethos-collapse-trigger" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: #f9fafb; border: none; cursor: pointer; font-weight: 500; text-align: left; font-size: 15px;">
                    <span>Colors</span>
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
                <div class="aethos-collapse-content" style="padding: 20px; display: none; background: #fff;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="aethos_primary_color" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Primary Color</label>
                            <input type="text" name="aethos_primary_color" id="aethos_primary_color" value="<?php echo esc_attr( $primary_color ); ?>" class="aethos-color-picker">
                            <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">Header & user bubbles</p>
                        </div>
                        <div>
                            <label for="aethos_user_text_color" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">User Text Color</label>
                            <input type="text" name="aethos_user_text_color" id="aethos_user_text_color" value="<?php echo esc_attr( $user_text_color ); ?>" class="aethos-color-picker">
                            <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">Text in user bubbles</p>
                        </div>
                        <div>
                            <label for="aethos_ai_bubble_color" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">AI Bubble Color</label>
                            <input type="text" name="aethos_ai_bubble_color" id="aethos_ai_bubble_color" value="<?php echo esc_attr( $ai_bubble_color ); ?>" class="aethos-color-picker">
                            <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">Background for AI messages</p>
                        </div>
                        <div>
                            <label for="aethos_header_title_color" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Header Title Color</label>
                            <input type="text" name="aethos_header_title_color" id="aethos_header_title_color" value="<?php echo esc_attr( $header_title_color ); ?>" class="aethos-color-picker">
                            <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">Color for chat title text</p>
                        </div>
                        <div>
                            <label for="aethos_header_subtitle_color" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Header Subtitle Color</label>
                            <input type="text" name="aethos_header_subtitle_color" id="aethos_header_subtitle_color" value="<?php echo esc_attr( $header_subtitle_color ); ?>" class="aethos-color-picker">
                            <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">Color for chat subtitle text</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Typography Section -->
            <div class="aethos-collapsible-section" style="border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 16px; overflow: hidden;">
                <button type="button" class="aethos-collapse-trigger" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: #f9fafb; border: none; cursor: pointer; font-weight: 500; text-align: left; font-size: 15px;">
                    <span>Typography</span>
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
                <div class="aethos-collapse-content" style="padding: 20px; display: none; background: #fff;">
                    <div style="margin-bottom: 24px;">
                        <label for="aethos_font_family" style="display: block; font-weight: 500; margin-bottom: 12px; font-size: 14px; color: #374151;">Font Family</label>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                            <?php foreach ($google_fonts as $font_name => $font_url): ?>
                                <label style="display: flex; align-items: center; padding: 12px 14px; border: 2px solid <?php echo $font_family === $font_name ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 8px; cursor: pointer; transition: all 0.2s; background: <?php echo $font_family === $font_name ? '#eff6ff' : '#fff'; ?>;">
                                    <input type="radio" name="aethos_font_family" value="<?php echo esc_attr($font_name); ?>" <?php checked($font_family, $font_name); ?> class="aethos-preview-trigger" style="margin: 0 10px 0 0;">
                                    <span style="font-family: '<?php echo esc_attr($font_name); ?>', sans-serif; font-size: 14px; font-weight: 500;"><?php echo esc_html($font_name); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p style="margin: 12px 0 0 0; color: #6b7280; font-size: 12px;">
                            <span class="dashicons dashicons-info" style="font-size: 14px; vertical-align: middle;"></span>
                            Powered by Google Fonts
                        </p>
                    </div>
                    <div>
                        <label for="aethos_font_size" style="display: block; font-weight: 500; margin-bottom: 12px; font-size: 14px; color: #374151;">Font Size</label>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <input type="range" name="aethos_font_size" id="aethos_font_size" value="<?php echo esc_attr( $font_size ); ?>" min="12" max="20" step="1" class="aethos-preview-trigger" style="flex: 1; height: 6px; border-radius: 3px; background: #e5e7eb; outline: none; -webkit-appearance: none;">
                            <div style="display: flex; align-items: center; gap: 6px; min-width: 60px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px;">
                                <span id="aethos_font_size_display" style="font-weight: 600; color: #4f46e5; font-size: 15px;"><?php echo esc_html( $font_size ); ?></span>
                                <span style="color: #6b7280; font-size: 13px;">px</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Positioning & Size Section -->
            <div class="aethos-collapsible-section" style="border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 16px; overflow: hidden;">
                <button type="button" class="aethos-collapse-trigger" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: #f9fafb; border: none; cursor: pointer; font-weight: 500; text-align: left; font-size: 15px;">
                    <span>Positioning & Size</span>
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
                <div class="aethos-collapse-content" style="padding: 20px; display: none; background: #fff;">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Widget Position</label>
                        <select name="aethos_widget_position" id="aethos_widget_position" class="regular-text aethos-preview-trigger" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                            <option value="bottom-right" <?php selected( $widget_position, 'bottom-right' ); ?>>Bottom Right</option>
                            <option value="bottom-left" <?php selected( $widget_position, 'bottom-left' ); ?>>Bottom Left</option>
                            <option value="floating-right" <?php selected( $widget_position, 'floating-right' ); ?>>Floating Right</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Chat Window Size</label>
                        <select name="aethos_widget_size" id="aethos_widget_size" class="regular-text aethos-preview-trigger" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                            <option value="small" <?php selected( $widget_size, 'small' ); ?>>Small (320px)</option>
                            <option value="medium" <?php selected( $widget_size, 'medium' ); ?>>Medium (380px)</option>
                            <option value="large" <?php selected( $widget_size, 'large' ); ?>>Large (440px)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Input Customization Section -->
            <div class="aethos-collapsible-section" style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                <button type="button" class="aethos-collapse-trigger" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: #f9fafb; border: none; cursor: pointer; font-weight: 500; text-align: left; font-size: 15px;">
                    <span>Input Customization</span>
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
                <div class="aethos-collapse-content" style="padding: 20px; display: none; background: #fff;">
                    <div style="margin-bottom: 20px;">
                        <label for="aethos_placeholder_text" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Placeholder Text</label>
                        <input type="text" name="aethos_placeholder_text" id="aethos_placeholder_text" value="<?php echo esc_attr( $placeholder_text ); ?>" class="regular-text aethos-preview-trigger" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">Text shown in the message input field</p>
                    </div>
                    <div>
                        <label for="aethos_button_text" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px; color: #374151;">Send Button Text</label>
                        <input type="text" name="aethos_button_text" id="aethos_button_text" value="<?php echo esc_attr( $button_text ); ?>" class="regular-text aethos-preview-trigger" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 12px;">Text displayed on the send button</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Live Preview Panel -->
    <div>
        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; position: sticky; top: 32px;">
            <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #111827;">Live Preview</h3>
            <p style="margin: 0 0 16px 0; color: #6b7280; font-size: 12px;">Updates in real-time as you make changes</p>
            
            <div id="aethos-preview-container" style="background: #dcdcde; border-radius: 12px; padding: 60px 24px 24px 24px; min-height: 560px; position: relative; overflow: hidden;">
                
                <!-- Chat Widget Preview -->
                <div id="aethos-preview-widget" class="aethos-widget-bottom-right aethos-widget-medium" style="position: absolute; width: 380px; background: white; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2), 0 10px 10px -5px rgba(0,0,0,0.1); overflow: hidden; transition: all 0.3s ease; display: none; opacity: 0; transform: scale(0.9);">
                    <!-- Header -->
                    <div id="aethos-preview-header" style="padding: 16px 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                        <img id="aethos-preview-icon" src="<?php echo esc_url($chat_icon); ?>" alt="Chat Icon" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.9); box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div style="flex: 1;">
                            <div id="aethos-preview-title" style="font-weight: 600; font-size: 15px; line-height: 1.3; margin-bottom: 2px; color: <?php echo esc_attr($header_title_color); ?>;"><?php echo esc_html($header_title); ?></div>
                            <div id="aethos-preview-subtitle" style="font-size: 12px; color: <?php echo esc_attr($header_subtitle_color); ?>; opacity: 0.9;"><?php echo esc_html($chat_subtitle); ?></div>
                        </div>
                        <button style="width: 32px; height: 32px; border-radius: 50%; border: none; background: rgba(0,0,0,0.05); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <span class="dashicons dashicons-no-alt" style="font-size: 16px; width: 16px; height: 16px; color: #FFFFFF;"></span>
                        </button>
                    </div>
                    
                    <!-- Chat Messages Area -->
                    <div id="aethos-preview-messages" style="padding: 20px; min-height: 280px; max-height: 280px; overflow-y: auto; background: #fafafa;">
                        <!-- AI Welcome Message -->
                        <div style="display: flex; gap: 10px; margin-bottom: 16px;">
                            <img src="<?php echo esc_url($chat_icon); ?>" alt="" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                            <div id="aethos-preview-ai-bubble" style="padding: 10px 14px; border-radius: 12px 12px 12px 4px; max-width: 75%; line-height: 1.5;">
                                <div id="aethos-preview-greeting"><?php echo esc_html($greeting_message); ?></div>
                            </div>
                        </div>
                        
                        <!-- Sample User Message -->
                        <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
                            <div id="aethos-preview-user-bubble" style="padding: 10px 14px; border-radius: 12px 12px 4px 12px; max-width: 75%; line-height: 1.5;">
                                <div id="aethos-preview-user-text">Can you help me?</div>
                            </div>
                        </div>
                        
                        <!-- Sample AI Response -->
                        <div style="display: flex; gap: 10px;">
                            <img src="<?php echo esc_url($chat_icon); ?>" alt="" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                            <div id="aethos-preview-ai-bubble-2" style="padding: 10px 14px; border-radius: 12px 12px 12px 4px; max-width: 75%; line-height: 1.5;">
                                Of course! I'm here to assist you. What would you like to know?
                            </div>
                        </div>
                    </div>
                    
                    <!-- Input Area -->
                    <div style="padding: 16px 20px; border-top: 1px solid #e5e7eb; background: white;">
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="text" id="aethos-preview-input" readonly style="flex: 1; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 20px; font-size: 14px; background: #f9fafb;">
                            <button id="aethos-preview-button" style="padding: 10px 20px; border: none; border-radius: 20px; font-weight: 500; cursor: pointer; font-size: 14px; white-space: nowrap; transition: all 0.2s;"></button>
                        </div>
                    </div>
                </div>
                
                <!-- Chat Bubble Button -->
                <div id="aethos-preview-bubble" class="aethos-bubble-bottom-right" style="position: absolute; width: 60px; height: 60px; border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.15); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                    <img src="<?php echo esc_url($chat_icon); ?>" alt="Chat" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Button -->
<div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
    <button type="button" id="aethos-reset-appearance" class="button" style="color: #6b7280;">
        <span class="dashicons dashicons-image-rotate" style="vertical-align: middle;"></span> Reset to Defaults
    </button>
    <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 12px;">This will restore all appearance settings to their default values.</p>
</div>

<style>
/* Widget positioning classes */
.aethos-widget-bottom-right { bottom: 80px; right: 24px; }
.aethos-widget-bottom-left { bottom: 80px; left: 24px; }
.aethos-widget-floating-right { top: 50%; right: 24px; transform: translateY(-50%); }

/* Bubble positioning classes */
.aethos-bubble-bottom-right { bottom: 20px; right: 24px; }
.aethos-bubble-bottom-left { bottom: 20px; left: 24px; }
.aethos-bubble-floating-right { top: 50%; right: 24px; transform: translateY(-50%); }

/* Widget size classes */
.aethos-widget-small { width: 320px !important; }
.aethos-widget-medium { width: 380px !important; }
.aethos-widget-large { width: 440px !important; }

/* Avatar hover effect */
.aethos-avatar-option:hover {
    border-color: #a5b4fc !important;
    transform: scale(1.05);
}

/* Font family radio labels hover */
label:has(input[name="aethos_font_family"]):hover {
    border-color: #a5b4fc !important;
}

/* Range slider styling */
input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #4f46e5;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

input[type="range"]::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #4f46e5;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Color picker styling */
.wp-picker-container {
    margin-top: 8px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Initialize color pickers
    $('.aethos-color-picker').wpColorPicker({
        change: function(event, ui) {
            setTimeout(updatePreview, 10);
        },
        clear: function() {
            setTimeout(updatePreview, 10);
        }
    });
    
    // Avatar selection
    $('.aethos-avatar-option').on('click', function() {
        // Remove selected from all
        $('.aethos-avatar-option').removeClass('selected').css('border-color', '#e5e7eb');
        $('.aethos-avatar-check').remove();
        
        // Add selected to clicked
        $(this).addClass('selected').css('border-color', '#4f46e5');
        $(this).append('<div class="aethos-avatar-check" style="position: absolute; top: -4px; right: -4px; background: #4f46e5; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"><span class="dashicons dashicons-yes" style="font-size: 14px; width: 14px; height: 14px; line-height: 1;"></span></div>');
        
        // Update hidden input and preview
        const avatarUrl = $(this).data('avatar');
        $('#aethos_chat_icon').val(avatarUrl);
        $('#aethos-preview-icon').attr('src', avatarUrl);
        $('#aethos-preview-messages img').attr('src', avatarUrl);
        $('#aethos-preview-bubble img').attr('src', avatarUrl);
    });
    
    // Custom icon upload
    $('#aethos-upload-custom-icon').on('click', function(e) {
        e.preventDefault();
        
        const frame = wp.media({
            title: 'Select or Upload Chat Icon',
            button: { text: 'Use this icon' },
            multiple: false,
            library: { type: 'image' }
        });
        
        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            $('#aethos_chat_icon').val(attachment.url);
            $('#aethos-preview-icon').attr('src', attachment.url);
            $('#aethos-preview-messages img').attr('src', attachment.url);
            $('#aethos-preview-bubble img').attr('src', attachment.url);
            
            // Remove selected from avatar grid
            $('.aethos-avatar-option').removeClass('selected').css('border-color', '#e5e7eb');
            $('.aethos-avatar-check').remove();
        });
        
        frame.open();
    });
    
    // Collapsible sections
    $('.aethos-collapse-trigger').on('click', function(e) {
        e.preventDefault();
        const $content = $(this).next('.aethos-collapse-content');
        const $icon = $(this).find('.dashicons');
        
        if ($content.is(':visible')) {
            $content.slideUp(250);
            $icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        } else {
            $content.slideDown(250);
            $icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
        }
    });
    
    // Font size slider
    $('#aethos_font_size').on('input', function() {
        $('#aethos_font_size_display').text($(this).val());
        updatePreview();
    });
    
    // Font family radio buttons
    $('input[name="aethos_font_family"]').on('change', function() {
        // Update border colors
        $('label:has(input[name="aethos_font_family"])').css({
            'border-color': '#e5e7eb',
            'background': '#fff'
        });
        $(this).parent().css({
            'border-color': '#4f46e5',
            'background': '#eff6ff'
        });
        updatePreview();
    });
    
    // Live preview updates
    $('.aethos-preview-trigger').on('input change', updatePreview);
    
    // Update preview function
    function updatePreview() {
        // Get all values
        const title = $('#aethos_header_title').val();
        const subtitle = $('#aethos_chat_subtitle').val();
        const greeting = $('#aethos_greeting_message').val();
        const primaryColor = $('#aethos_primary_color').val();
        const userTextColor = $('#aethos_user_text_color').val();
        const aiBubbleColor = $('#aethos_ai_bubble_color').val();
        const headerTitleColor = $('#aethos_header_title_color').val();
        const headerSubtitleColor = $('#aethos_header_subtitle_color').val();
        const fontSize = $('#aethos_font_size').val();
        const fontFamily = $('input[name="aethos_font_family"]:checked').val();
        const position = $('#aethos_widget_position').val();
        const size = $('#aethos_widget_size').val();
        const placeholder = $('#aethos_placeholder_text').val();
        const buttonText = $('#aethos_button_text').val();
        
        // Update text content
        $('#aethos-preview-title').text(title);
        $('#aethos-preview-subtitle').text(subtitle);
        $('#aethos-preview-greeting').text(greeting);
        $('#aethos-preview-input').attr('placeholder', placeholder);
        $('#aethos-preview-button').text(buttonText);
        
        // Update colors
        $('#aethos-preview-header').css('background', primaryColor);
        $('#aethos-preview-title').css('color', headerTitleColor);
        $('#aethos-preview-subtitle').css('color', headerSubtitleColor);
        $('#aethos-preview-button').css({
            'background': primaryColor,
            'color': userTextColor
        });
        $('#aethos-preview-user-bubble').css({
            'background': primaryColor,
            'color': userTextColor
        });
        $('#aethos-preview-ai-bubble, #aethos-preview-ai-bubble-2').css({
            'background': aiBubbleColor,
            'color': '#1f2937'
        });
        
        // Update typography
        const fontValue = "'" + fontFamily + "', sans-serif";
        $('#aethos-preview-widget').css({
            'font-family': fontValue,
            'font-size': fontSize + 'px'
        });
        
        // Update position
        const $widget = $('#aethos-preview-widget');
        const $bubble = $('#aethos-preview-bubble');
        $widget.removeClass('aethos-widget-bottom-right aethos-widget-bottom-left aethos-widget-floating-right');
        $bubble.removeClass('aethos-bubble-bottom-right aethos-bubble-bottom-left aethos-bubble-floating-right');
        $widget.addClass('aethos-widget-' + position);
        $bubble.addClass('aethos-bubble-' + position);
        
        // Update size
        $widget.removeClass('aethos-widget-small aethos-widget-medium aethos-widget-large');
        $widget.addClass('aethos-widget-' + size);
    }
    
    // Chat bubble click animation
    let chatOpen = false;
    $('#aethos-preview-bubble').on('click', function() {
        const $widget = $('#aethos-preview-widget');
        
        if (chatOpen) {
            // Close chat
            $widget.css({
                'opacity': '0',
                'transform': 'scale(0.9)'
            });
            setTimeout(function() {
                $widget.css('display', 'none');
            }, 300);
            chatOpen = false;
        } else {
            // Open chat
            $widget.css('display', 'block');
            setTimeout(function() {
                $widget.css({
                    'opacity': '1',
                    'transform': 'scale(1)'
                });
            }, 10);
            chatOpen = true;
        }
    });
    
    // Reset to defaults button
    $('#aethos-reset-appearance').on('click', function() {
        if (!confirm('Are you sure you want to reset all appearance settings to their defaults? This cannot be undone.')) {
            return;
        }
        
        const $button = $(this);
        const originalText = $button.html();
        $button.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite; vertical-align: middle;"></span> Resetting...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_reset_appearance',
                nonce: '<?php echo wp_create_nonce("aethos_reset_appearance"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Appearance settings have been reset to defaults. The page will now reload.');
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
    
    // Initial preview update
    updatePreview();
});
</script>
