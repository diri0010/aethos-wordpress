<?php
/**
 * Enhanced Admin Settings Page Template
 * Aethos AI Chat - Complete UI with Appearance, Behavior, Advanced Settings, and Analytics
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get current settings
$api_key = get_option('aethos_api_key', '');
$connection_status = get_option('aethos_connection_status', 'unknown');

// Appearance settings
$header_title = get_option('aethos_header_title', 'Aethos AI Assistant');
$chat_subtitle = get_option('aethos_chat_subtitle', 'Typically replies instantly');
$chat_icon = get_option('aethos_chat_icon', '');
$primary_color = get_option('aethos_primary_color', '#4F46E5');
$user_text_color = get_option('aethos_user_text_color', '#FFFFFF');
$ai_bubble_color = get_option('aethos_ai_bubble_color', '#F3F4F6');
$font_family = get_option('aethos_font_family', 'System Stack');
$font_size = get_option('aethos_font_size', 16);
$widget_position = get_option('aethos_widget_position', 'bottom-right');

// Behavior settings
$chatbot_persona = get_option('aethos_chatbot_persona', 'friendly');
$greeting_message = get_option('aethos_greeting_message', 'Welcome! How can I help you today?');
$fallback_response = get_option('aethos_fallback_response', 'I\'m sorry, I couldn\'t find an answer to that. Please try rephrasing your question.');

// Knowledge base settings
$kb_pages = get_option('aethos_kb_pages', true);
$kb_posts = get_option('aethos_kb_posts', true);
$kb_woocommerce = get_option('aethos_kb_woocommerce', false);
$kb_custom_post_types = get_option('aethos_kb_custom_post_types', '');
$kb_acf = get_option('aethos_kb_acf', false);

// Advanced settings
$log_retention_days = get_option('aethos_log_retention_days', 30);
$disable_ip_logging = get_option('aethos_disable_ip_logging', false);
$enable_minification = get_option('aethos_enable_minification', false);
$request_timeout = get_option('aethos_request_timeout', 30);

// Get available custom post types
$custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
$selected_cpts = !empty($kb_custom_post_types) ? explode(',', $kb_custom_post_types) : array();
?>

<div class="wrap aethos-admin-wrapper">
    <!-- Header -->
    <div class="aethos-admin-header" style="background: #fff; padding: 20px 30px; margin: 0 -20px 20px -20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="<?php echo esc_url( AETHOS_PLUGIN_URL . 'assets/images/aethos-logo.png' ); ?>" alt="Aethos Logo" style="height: 32px;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 600;">AI Chat Settings</h1>
        </div>
        <div style="display: flex; gap: 12px;">
            <button type="button" class="button" onclick="location.reload();">
                <span class="dashicons dashicons-update" style="vertical-align: middle;"></span> Refresh
            </button>
        </div>
    </div>

    <!-- Main Layout: Sidebar + Content -->
    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <!-- Sidebar Navigation -->
        <aside style="width: 260px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px 16px; height: fit-content;">
            <nav>
                <!-- Dashboard -->
                <div style="margin-bottom: 20px;">
                    <a href="#" class="aethos-nav-item active" data-section="dashboard-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-dashboard" style="font-size: 18px;"></span>
                        Dashboard
                    </a>
                </div>

                <!-- Setup & Configuration -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; padding: 0 12px;">Setup & Configuration</div>
                    <a href="#" class="aethos-nav-item" data-section="connection-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-admin-links" style="font-size: 18px;"></span>
                        Connection
                    </a>
                    <a href="#" class="aethos-nav-item" data-section="model-selection-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-admin-home" style="font-size: 18px;"></span>
                        Model Selection
                    </a>
                </div>

                <!-- Customization -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; padding: 0 12px;">Customization</div>
                    <a href="#" class="aethos-nav-item" data-section="appearance-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-art" style="font-size: 18px;"></span>
                        Appearance
                    </a>
                    <a href="#" class="aethos-nav-item" data-section="behavior-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-admin-settings" style="font-size: 18px;"></span>
                        Behavior
                    </a>
                </div>

                <!-- Content Management -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; padding: 0 12px;">Content Management</div>
                    <a href="#" class="aethos-nav-item" data-section="qna-management-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-book" style="font-size: 18px;"></span>
                        Q&A Management
                    </a>
                </div>

                <!-- Monitoring & Analytics -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; padding: 0 12px;">Monitoring & Analytics</div>
                    <a href="#" class="aethos-nav-item" data-section="analytics-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-chart-line" style="font-size: 18px;"></span>
                        Analytics
                    </a>
                    <a href="#" class="aethos-nav-item" data-section="conversation-history-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-backup" style="font-size: 18px;"></span>
                        Conversation History
                    </a>
                    <a href="#" class="aethos-nav-item" data-section="feedback-ratings-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-thumbs-up" style="font-size: 18px;"></span>
                        User Feedback
                    </a>
                </div>

                <!-- Advanced Configuration -->
                <div>
                    <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; padding: 0 12px;">Advanced Configuration</div>
                    <a href="#" class="aethos-nav-item" data-section="advanced-section" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; text-decoration: none; color: #374151; border-radius: 6px; margin-bottom: 2px; transition: all 0.2s; font-size: 14px;">
                        <span class="dashicons dashicons-admin-generic" style="font-size: 18px;"></span>
                        Advanced
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main style="flex: 1; min-width: 0;">
            <form method="post" action="options.php" id="aethos-settings-form">
                <?php settings_fields( 'aethos_options' ); ?>

                <!-- Connection Section -->
                <div id="connection-section" class="aethos-content-section" style="display: none;">
                    <?php if ( $connection_status === 'connected' && !empty( $api_key ) ): ?>
                        <!-- Connected State -->
                        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                <div>
                                    <h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Manage Connection</h2>
                                    <p style="margin: 0; color: #6b7280; font-size: 14px;">Your site is successfully linked to the Aethos AI platform.</p>
                                </div>
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #d1fae5; color: #065f46; border-radius: 9999px; font-size: 12px; font-weight: 500;">
                                    <span class="dashicons dashicons-yes-alt" style="font-size: 14px;"></span>
                                    Connected
                                </span>
                            </div>
                            
                            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; margin-bottom: 16px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <label style="display: block; font-weight: 500; margin-bottom: 4px; font-size: 14px;">API Key</label>
                                        <code style="font-size: 13px; color: #374151;">••••••••••••••••••••</code>
                                    </div>
                                    <button type="button" class="button" id="aethos-disconnect-btn">
                                        Disconnect
                                    </button>
                                </div>
                            </div>
                            
                            <p style="margin: 0; color: #6b7280; font-size: 13px;">
                                <span class="dashicons dashicons-info" style="color: #3b82f6;"></span>
                                To modify your connection settings, please disconnect and reconnect with a new API key.
                            </p>
                        </div>
                    <?php else: ?>
                        <!-- Disconnected State -->
                        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                            <h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Connect to Aethos AI</h2>
                            <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">Enter your API key to connect your WordPress site to the Aethos AI platform.</p>
                            
                            <div style="margin-bottom: 16px;">
                                <label for="aethos_api_key_input" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">API Key</label>
                                <input type="text" id="aethos_api_key_input" class="regular-text" placeholder="Enter your API key" style="width: 100%; max-width: 500px;">
                            </div>
                            
                            <button type="button" class="button button-primary" id="aethos-connect-btn">
                                Connect
                            </button>
                            
                            <div id="aethos-connection-message" style="margin-top: 16px;"></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Appearance Section -->
                <div id="appearance-section" class="aethos-content-section" style="display: none;">
                    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 20px;">
                        <!-- Settings Panel -->
                        <div>
                            <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                                <h2 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600;">Chatbot Appearance</h2>
                                <p style="margin: 0 0 24px 0; color: #6b7280; font-size: 14px;">Customize the look and feel of your chatbot widget.</p>
                                
                                <!-- General Section -->
                                <div class="aethos-collapsible-section" style="border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 16px;">
                                    <button type="button" class="aethos-collapse-trigger" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f9fafb; border: none; cursor: pointer; font-weight: 500; text-align: left;">
                                        <span>General</span>
                                        <span class="dashicons dashicons-arrow-up-alt2"></span>
                                    </button>
                                    <div class="aethos-collapse-content" style="padding: 16px; display: block;">
                                        <!-- Chat Bubble Icon -->
                                        <div style="margin-bottom: 20px;">
                                            <label style="display: block; font-weight: 500; margin-bottom: 12px; font-size: 14px;">Chat Bubble Icon</label>
                                            <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 13px;">Choose an avatar for your chatbot</p>
                                            
                                            <!-- Avatar Grid -->
                                            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 16px;">
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
                                                    <div class="aethos-avatar-option <?php echo $is_selected; ?>" data-avatar="<?php echo esc_url($avatar_url); ?>" style="cursor: pointer; border: 3px solid <?php echo $is_selected ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 12px; padding: 8px; transition: all 0.2s; position: relative;">
                                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($label); ?>" style="width: 100%; height: auto; border-radius: 8px; display: block;">
                                                        <?php if ($is_selected): ?>
                                                            <div style="position: absolute; top: 4px; right: 4px; background: #4f46e5; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                                                <span class="dashicons dashicons-yes" style="font-size: 16px; width: 16px; height: 16px;"></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            
                                            <input type="hidden" name="aethos_chat_icon" id="aethos_chat_icon" value="<?php echo esc_attr( $chat_icon ); ?>">
                                            
                                            <p style="margin: 0; color: #6b7280; font-size: 12px; font-style: italic;">Or <button type="button" class="button-link" id="aethos-upload-custom-icon" style="color: #4f46e5; text-decoration: underline;">upload your own custom icon</button></p>
                                        </div>
                                        
                                        <!-- Chatbot Header Title -->
                                        <div style="margin-bottom: 20px;">
                                            <label for="aethos_header_title" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Chatbot Header Title</label>
                                            <input type="text" name="aethos_header_title" id="aethos_header_title" value="<?php echo esc_attr( $header_title ); ?>" class="regular-text" style="width: 100%;">
                                        </div>
                                        
                                        <!-- Chatbot Subtitle -->
                                        <div>
                                            <label for="aethos_chat_subtitle" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Chatbot Subtitle</label>
                                            <input type="text" name="aethos_chat_subtitle" id="aethos_chat_subtitle" value="<?php echo esc_attr( $chat_subtitle ); ?>" class="regular-text" style="width: 100%;">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Colors Section -->
                                <div class="aethos-collapsible-section" style="border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 16px;">
                                    <button type="button" class="aethos-collapse-trigger" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f9fafb; border: none; cursor: pointer; font-weight: 500; text-align: left;">
                                        <span>Colors</span>
                                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                                    </button>
                                    <div class="aethos-collapse-content" style="padding: 16px; display: none;">
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                            <div>
                                                <label for="aethos_primary_color" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Primary Color</label>
                                                <input type="text" name="aethos_primary_color" id="aethos_primary_color" value="<?php echo esc_attr( $primary_color ); ?>" class="aethos-color-picker">
                                            </div>
                                            <div>
                                                <label for="aethos_user_text_color" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">User Text Color</label>
                                                <input type="text" name="aethos_user_text_color" id="aethos_user_text_color" value="<?php echo esc_attr( $user_text_color ); ?>" class="aethos-color-picker">
                                            </div>
                                            <div>
                                                <label for="aethos_ai_bubble_color" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">AI Bubble Color</label>
                                                <input type="text" name="aethos_ai_bubble_color" id="aethos_ai_bubble_color" value="<?php echo esc_attr( $ai_bubble_color ); ?>" class="aethos-color-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Typography Section -->
                                <div class="aethos-collapsible-section" style="border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 16px;">
                                    <button type="button" class="aethos-collapse-trigger" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f9fafb; border: none; cursor: pointer; font-weight: 500; text-align: left;">
                                        <span>Typography</span>
                                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                                    </button>
                                    <div class="aethos-collapse-content" style="padding: 16px; display: none;">
                                        <div style="margin-bottom: 16px;">
                                            <label for="aethos_font_family" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Font Family</label>
                                            <select name="aethos_font_family" id="aethos_font_family" class="regular-text" style="width: 100%;">
                                                <option value="System Stack" <?php selected( $font_family, 'System Stack' ); ?>>System Stack</option>
                                                <option value="Arial" <?php selected( $font_family, 'Arial' ); ?>>Arial</option>
                                                <option value="Helvetica" <?php selected( $font_family, 'Helvetica' ); ?>>Helvetica</option>
                                                <option value="Georgia" <?php selected( $font_family, 'Georgia' ); ?>>Georgia</option>
                                                <option value="Times New Roman" <?php selected( $font_family, 'Times New Roman' ); ?>>Times New Roman</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="aethos_font_size" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Font Size (px)</label>
                                            <input type="number" name="aethos_font_size" id="aethos_font_size" value="<?php echo esc_attr( $font_size ); ?>" min="12" max="20" class="small-text">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Positioning Section -->
                                <div class="aethos-collapsible-section" style="border: 1px solid #e5e7eb; border-radius: 6px;">
                                    <button type="button" class="aethos-collapse-trigger" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f9fafb; border: none; cursor: pointer; font-weight: 500; text-align: left;">
                                        <span>Positioning</span>
                                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                                    </button>
                                    <div class="aethos-collapse-content" style="padding: 16px; display: none;">
                                        <label style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Widget Position</label>
                                        <select name="aethos_widget_position" id="aethos_widget_position" class="regular-text" style="width: 100%;">
                                            <option value="bottom-right" <?php selected( $widget_position, 'bottom-right' ); ?>>Bottom Right</option>
                                            <option value="bottom-left" <?php selected( $widget_position, 'bottom-left' ); ?>>Bottom Left</option>
                                            <option value="top-right" <?php selected( $widget_position, 'top-right' ); ?>>Top Right</option>
                                            <option value="top-left" <?php selected( $widget_position, 'top-left' ); ?>>Top Left</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div style="display: flex; gap: 12px;">
                                <button type="button" class="button" id="aethos-reset-appearance">Reset to Defaults</button>
                                <?php submit_button( 'Save Changes', 'primary', 'submit', false ); ?>
                            </div>
                        </div>
                        
                        <!-- Live Preview Panel -->
                        <div>
                            <div style="background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; position: sticky; top: 32px;">
                                <div id="aethos-preview-widget" style="background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; max-width: 360px; margin: 0 auto;">
                                    <!-- Widget Header -->
                                    <div id="preview-header" style="padding: 16px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #e5e7eb;">
                                        <div id="preview-icon" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                                            <?php if ( $chat_icon ): ?>
                                                <img src="<?php echo esc_url( $chat_icon ); ?>" alt="Chat Icon" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                            <?php else: ?>
                                                🤖
                                            <?php endif; ?>
                                        </div>
                                        <div style="flex: 1;">
                                            <div id="preview-title" style="font-weight: 600; font-size: 15px; color: #111827;"><?php echo esc_html( $header_title ); ?></div>
                                            <div id="preview-subtitle" style="font-size: 12px; color: #6b7280;"><?php echo esc_html( $chat_subtitle ); ?></div>
                                        </div>
                                        <button type="button" style="background: none; border: none; color: #9ca3af; cursor: pointer;">✕</button>
                                    </div>
                                    
                                    <!-- Chat Messages -->
                                    <div style="padding: 16px; min-height: 300px; max-height: 400px; overflow-y: auto; background: #fafafa;">
                                        <!-- AI Message -->
                                        <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #e5e7eb; flex-shrink: 0;"></div>
                                            <div id="preview-ai-bubble" style="background: #f3f4f6; padding: 10px 14px; border-radius: 12px; border-top-left-radius: 4px; max-width: 80%; font-size: 14px; color: #374151;">
                                                <?php echo esc_html( $greeting_message ); ?>
                                            </div>
                                        </div>
                                        
                                        <!-- User Message -->
                                        <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
                                            <div id="preview-user-bubble" style="padding: 10px 14px; border-radius: 12px; border-top-right-radius: 4px; max-width: 80%; font-size: 14px;">
                                                I'd like to know more about your pricing plans.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Input Area -->
                                    <div style="padding: 16px; border-top: 1px solid #e5e7eb; display: flex; gap: 8px;">
                                        <input type="text" placeholder="Type a message..." style="flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                                        <button type="button" id="preview-send-btn" style="padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; color: white;">
                                            ➤
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Floating Button Preview -->
                                <div style="margin-top: 20px; text-align: right;">
                                    <div id="preview-float-btn" style="width: 56px; height: 56px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); cursor: pointer;">
                                        💬
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Behavior Section -->
                <div id="behavior-section" class="aethos-content-section" style="display: none;">
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
                                <input type="text" name="aethos_fallback_response" id="aethos_fallback_response" value="<?php echo esc_attr( $fallback_response ); ?>" class="regular-text" style="width: 100%;">
                            </div>
                        </div>

                    </div>
                    
                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="button" id="aethos-reset-behavior">Reset to Defaults</button>
                        <?php submit_button( 'Save Changes', 'primary', 'submit', false ); ?>
                    </div>
                </div>


                <div id="model-selection-section" class="aethos-content-section" style="display: none;">
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                        <!-- Breadcrumb -->
                        <div style="color: #6b7280; font-size: 13px; margin-bottom: 16px;">
                            Aethos AI / Settings / <span style="color: #374151; font-weight: 500;">Model Selection</span>
                        </div>
                        
                        <h2 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 600;">AI Model Selection</h2>
                        <p style="margin: 0 0 32px 0; color: #6b7280; font-size: 14px;">This setting determines your chatbot's core intelligence. Changing the model will affect its speed and accuracy.</p>
                        
                        <?php 
                        $current_model = get_option('aethos_ai_model', 'gpt-4o-mini');
                        ?>
                        
                        <!-- Model Options -->
                        <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                            
                            <!-- OpenAI GPT-4o Mini (Recommended) -->
                            <label class="aethos-model-card <?php echo $current_model === 'gpt-4o-mini' ? 'selected' : ''; ?>" data-model="gpt-4o-mini" style="display: block; padding: 20px; border: 2px solid <?php echo $current_model === 'gpt-4o-mini' ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 12px; cursor: pointer; transition: all 0.2s; background: <?php echo $current_model === 'gpt-4o-mini' ? '#eff6ff' : '#fff'; ?>; position: relative;">
                                <div style="display: flex; align-items: start; gap: 16px;">
                                    <input type="radio" name="aethos_ai_model" value="gpt-4o-mini" <?php checked( $current_model, 'gpt-4o-mini' ); ?> style="margin-top: 4px; width: 20px; height: 20px; cursor: pointer;">
                                    
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">GPT-4o Mini</h3>
                                            <span style="display: inline-block; padding: 4px 10px; background: #d1fae5; color: #065f46; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;">Recommended</span>
                                            <?php if ( $current_model === 'gpt-4o-mini' ): ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-size: 13px; font-weight: 500; margin-left: auto;">
                                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                                Currently Active
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px; line-height: 1.6;">Balanced performance ideal for most general queries with excellent reasoning capabilities.</p>
                                        
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">⚖️</span>
                                                Balanced Performance
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">🚀</span>
                                                OpenAI
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <!-- OpenAI GPT-4.1 Mini -->
                            <label class="aethos-model-card <?php echo $current_model === 'gpt-4.1-mini' ? 'selected' : ''; ?>" data-model="gpt-4.1-mini" style="display: block; padding: 20px; border: 2px solid <?php echo $current_model === 'gpt-4.1-mini' ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 12px; cursor: pointer; transition: all 0.2s; background: <?php echo $current_model === 'gpt-4.1-mini' ? '#eff6ff' : '#fff'; ?>; position: relative;">
                                <div style="display: flex; align-items: start; gap: 16px;">
                                    <input type="radio" name="aethos_ai_model" value="gpt-4.1-mini" <?php checked( $current_model, 'gpt-4.1-mini' ); ?> style="margin-top: 4px; width: 20px; height: 20px; cursor: pointer;">
                                    
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">GPT-4.1 Mini</h3>
                                            <?php if ( $current_model === 'gpt-4.1-mini' ): ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-size: 13px; font-weight: 500; margin-left: auto;">
                                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                                Currently Active
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px; line-height: 1.6;">Enhanced version with improved reasoning and faster response times.</p>
                                        
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">⚡</span>
                                                Fast Response
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">🚀</span>
                                                OpenAI
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <!-- Google Gemini 2.5 Flash -->
                            <label class="aethos-model-card <?php echo $current_model === 'gemini-2.5-flash' ? 'selected' : ''; ?>" data-model="gemini-2.5-flash" style="display: block; padding: 20px; border: 2px solid <?php echo $current_model === 'gemini-2.5-flash' ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 12px; cursor: pointer; transition: all 0.2s; background: <?php echo $current_model === 'gemini-2.5-flash' ? '#eff6ff' : '#fff'; ?>; position: relative;">
                                <div style="display: flex; align-items: start; gap: 16px;">
                                    <input type="radio" name="aethos_ai_model" value="gemini-2.5-flash" <?php checked( $current_model, 'gemini-2.5-flash' ); ?> style="margin-top: 4px; width: 20px; height: 20px; cursor: pointer;">
                                    
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">Gemini 2.5 Flash</h3>
                                            <?php if ( $current_model === 'gemini-2.5-flash' ): ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-size: 13px; font-weight: 500; margin-left: auto;">
                                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                                Currently Active
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px; line-height: 1.6;">Lightning-fast responses with strong multi-modal understanding capabilities.</p>
                                        
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">⚡</span>
                                                Ultra Fast
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">🔷</span>
                                                Google
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <!-- Google Gemini 2.5 Flash Lite -->
                            <label class="aethos-model-card <?php echo $current_model === 'gemini-2.5-flash-lite' ? 'selected' : ''; ?>" data-model="gemini-2.5-flash-lite" style="display: block; padding: 20px; border: 2px solid <?php echo $current_model === 'gemini-2.5-flash-lite' ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 12px; cursor: pointer; transition: all 0.2s; background: <?php echo $current_model === 'gemini-2.5-flash-lite' ? '#eff6ff' : '#fff'; ?>; position: relative;">
                                <div style="display: flex; align-items: start; gap: 16px;">
                                    <input type="radio" name="aethos_ai_model" value="gemini-2.5-flash-lite" <?php checked( $current_model, 'gemini-2.5-flash-lite' ); ?> style="margin-top: 4px; width: 20px; height: 20px; cursor: pointer;">
                                    
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">Gemini 2.5 Flash Lite</h3>
                                            <?php if ( $current_model === 'gemini-2.5-flash-lite' ): ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-size: 13px; font-weight: 500; margin-left: auto;">
                                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                                Currently Active
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px; line-height: 1.6;">Lightweight version optimized for quick, simple queries and high-volume usage.</p>
                                        
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">🪶</span>
                                                Lightweight
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">🔷</span>
                                                Google
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <!-- Google Gemini 3 Pro -->
                            <label class="aethos-model-card <?php echo $current_model === 'gemini-3-pro' ? 'selected' : ''; ?>" data-model="gemini-3-pro" style="display: block; padding: 20px; border: 2px solid <?php echo $current_model === 'gemini-3-pro' ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 12px; cursor: pointer; transition: all 0.2s; background: <?php echo $current_model === 'gemini-3-pro' ? '#eff6ff' : '#fff'; ?>; position: relative;">
                                <div style="display: flex; align-items: start; gap: 16px;">
                                    <input type="radio" name="aethos_ai_model" value="gemini-3-pro" <?php checked( $current_model, 'gemini-3-pro' ); ?> style="margin-top: 4px; width: 20px; height: 20px; cursor: pointer;">
                                    
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">Gemini 3 Pro</h3>
                                            <?php if ( $current_model === 'gemini-3-pro' ): ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-size: 13px; font-weight: 500; margin-left: auto;">
                                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                                Currently Active
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px; line-height: 1.6;">Most advanced model with highest accuracy and reasoning for complex tasks.</p>
                                        
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">🎯</span>
                                                Highest Accuracy
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">🔷</span>
                                                Google
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <!-- Anthropic Claude Sonnet 4 -->
                            <label class="aethos-model-card <?php echo $current_model === 'claude-sonnet-4' ? 'selected' : ''; ?>" data-model="claude-sonnet-4" style="display: block; padding: 20px; border: 2px solid <?php echo $current_model === 'claude-sonnet-4' ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 12px; cursor: pointer; transition: all 0.2s; background: <?php echo $current_model === 'claude-sonnet-4' ? '#eff6ff' : '#fff'; ?>; position: relative;">
                                <div style="display: flex; align-items: start; gap: 16px;">
                                    <input type="radio" name="aethos_ai_model" value="claude-sonnet-4" <?php checked( $current_model, 'claude-sonnet-4' ); ?> style="margin-top: 4px; width: 20px; height: 20px; cursor: pointer;">
                                    
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">Claude Sonnet 4</h3>
                                            <?php if ( $current_model === 'claude-sonnet-4' ): ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-size: 13px; font-weight: 500; margin-left: auto;">
                                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                                Currently Active
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px; line-height: 1.6;">Thoughtful, nuanced responses with excellent conversational abilities and strong reasoning.</p>
                                        
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">💬</span>
                                                Conversational
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">🟣</span>
                                                Anthropic
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <!-- Anthropic Claude Haiku 3.5 -->
                            <label class="aethos-model-card <?php echo $current_model === 'claude-haiku-3.5' ? 'selected' : ''; ?>" data-model="claude-haiku-3.5" style="display: block; padding: 20px; border: 2px solid <?php echo $current_model === 'claude-haiku-3.5' ? '#4f46e5' : '#e5e7eb'; ?>; border-radius: 12px; cursor: pointer; transition: all 0.2s; background: <?php echo $current_model === 'claude-haiku-3.5' ? '#eff6ff' : '#fff'; ?>; position: relative;">
                                <div style="display: flex; align-items: start; gap: 16px;">
                                    <input type="radio" name="aethos_ai_model" value="claude-haiku-3.5" <?php checked( $current_model, 'claude-haiku-3.5' ); ?> style="margin-top: 4px; width: 20px; height: 20px; cursor: pointer;">
                                    
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">Claude Haiku 3.5</h3>
                                            <?php if ( $current_model === 'claude-haiku-3.5' ): ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-size: 13px; font-weight: 500; margin-left: auto;">
                                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                                Currently Active
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px; line-height: 1.6;">Fast and efficient model perfect for simple Q&A and straightforward interactions.</p>
                                        
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">⚡</span>
                                                Fast & Efficient
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #374151;">
                                                <span style="font-size: 16px;">🟣</span>
                                                Anthropic
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                        </div>
                        
                        <!-- Footer Actions -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                            <a href="https://aethoslogic.com/models" target="_blank" style="color: #4f46e5; text-decoration: none; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                                Learn More About Models
                                <span class="dashicons dashicons-external" style="font-size: 16px;"></span>
                            </a>
                            <?php submit_button( 'Save Changes', 'primary', 'submit', false ); ?>
                        </div>
                    </div>
                </div>


                <!-- Q&A Management Section -->
                <div id="qna-management-section" class="aethos-content-section" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <div>
                            <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 600;">Q&A Management</h2>
                            <p style="margin: 0; color: #6b7280; font-size: 14px;">Manage and curate your chatbot's knowledge base.</p>
                        </div>
                    </div>

                    <!-- Knowledge Base Content Sources -->
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Knowledge Base Content Sources</h3>
                        <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">Select which content types the AI should use to answer questions.</p>
                        
                        <?php
                        $kb_pages = get_option( 'aethos_kb_pages', true );
                        $kb_posts = get_option( 'aethos_kb_posts', true );
                        $kb_woocommerce = get_option( 'aethos_kb_woocommerce', false );
                        $kb_acf = get_option( 'aethos_kb_acf', false );
                        
                        $custom_post_types = get_post_types( array(
                            'public' => true,
                            '_builtin' => false
                        ), 'objects' );
                        
                        $selected_cpts = get_option( 'aethos_kb_custom_post_types', array() );
                        ?>
                        
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <!-- Pages -->
                            <label style="display: flex; align-items: center; gap: 12px; padding: 14px; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; background: #fff; transition: all 0.2s;">
                                <input type="checkbox" name="aethos_kb_pages" value="1" <?php checked( $kb_pages, true ); ?> style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-weight: 500; font-size: 15px;">Pages</span>
                            </label>
                            
                            <!-- Posts -->
                            <label style="display: flex; align-items: center; gap: 12px; padding: 14px; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; background: #fff; transition: all 0.2s;">
                                <input type="checkbox" name="aethos_kb_posts" value="1" <?php checked( $kb_posts, true ); ?> style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-weight: 500; font-size: 15px;">Posts</span>
                            </label>
                            
                            <!-- Custom Post Types -->
                            <?php if ( ! empty( $custom_post_types ) ): ?>
                            <div style="padding: 14px; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb;">
                                <div style="font-weight: 500; margin-bottom: 12px; font-size: 15px;">Custom Post Types</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; padding-left: 8px;">
                                    <?php foreach ( $custom_post_types as $cpt ): ?>
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                        <input type="checkbox" name="aethos_kb_cpt_<?php echo esc_attr( $cpt->name ); ?>" value="1" <?php checked( in_array( $cpt->name, $selected_cpts ) ); ?> style="width: 16px; height: 16px;">
                                        <span style="font-size: 14px;"><?php echo esc_html( $cpt->label ); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="button button-primary" id="aethos-sync-content-btn" style="display: inline-flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-update"></span>
                                Sync/Refresh Content
                            </button>
                            <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 13px;">Scan your content and generate AI-powered Q&A suggestions.</p>
                        </div>
                    </div>

                    <!-- AI-Generated Q&A Suggestions -->
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                        <div class="aethos-collapse-trigger" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer; padding: 8px; margin: -8px; border-radius: 6px;">
                            <div>
                                <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 600;">AI-Generated Q&A Suggestions</h3>
                                <p style="margin: 0; color: #6b7280; font-size: 14px;">Review these suggestions from your site content. Accept, edit, or discard them to build your chatbot's knowledge.</p>
                            </div>
                            <span class="dashicons dashicons-arrow-down-alt2" style="font-size: 24px; color: #6b7280; transition: transform 0.3s;"></span>
                        </div>
                        
                        <div class="aethos-collapse-content" style="margin-top: 20px; display: none;">
                            <div id="aethos-ai-suggestions-list">
                                <div style="text-align: center; padding: 40px; color: #6b7280;">
                                    <span class="dashicons dashicons-lightbulb" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px;"></span>
                                    <p style="margin: 0; font-size: 14px;">Click "Sync/Refresh Content" to generate AI suggestions from your site content.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Your Chatbot's Knowledge Base -->
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Your Chatbot's Knowledge Base</h3>
                            <button type="button" class="button button-primary" id="aethos-add-qna-btn" style="display: inline-flex; align-items: center; gap: 6px;">
                                <span class="dashicons dashicons-plus-alt"></span>
                                Add New Q&A
                            </button>
                        </div>
                        
                        <!-- Search and Filters -->
                        <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 250px;">
                                <input type="text" id="aethos-qna-search" placeholder="Search questions..." class="regular-text" style="width: 100%; padding: 8px 12px;">
                            </div>
                            <select id="aethos-qna-category-filter" class="regular-text" style="min-width: 150px;">
                                <option value="">All Categories</option>
                            </select>
                            <select id="aethos-qna-priority-filter" class="regular-text" style="min-width: 150px;">
                                <option value="">Any Priority</option>
                                <option value="high">High</option>
                                <option value="normal">Normal</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        
                        <!-- Q&A Table -->
                        <div style="overflow-x: auto;">
                            <table class="wp-list-table widefat fixed striped" id="aethos-qna-table" style="border: 1px solid #e5e7eb;">
                                <thead>
                                    <tr>
                                        <th style="width: 40px; padding: 12px;">
                                            <input type="checkbox" id="aethos-qna-select-all">
                                        </th>
                                        <th style="padding: 12px; font-weight: 600;">QUESTION</th>
                                        <th style="padding: 12px; font-weight: 600; width: 120px;">CATEGORY</th>
                                        <th style="padding: 12px; font-weight: 600; width: 100px;">PRIORITY</th>
                                        <th style="padding: 12px; font-weight: 600; width: 100px;">STATUS</th>
                                        <th style="padding: 12px; font-weight: 600; width: 100px; text-align: right;">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody id="aethos-qna-tbody">
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">
                                            <span class="dashicons dashicons-admin-comments" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px;"></span>
                                            <p style="margin: 0; font-size: 14px;">No Q&A entries yet. Click "Add New Q&A" to get started.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Bulk Actions -->
                        <div style="margin-top: 16px; display: flex; gap: 12px; align-items: center;">
                            <select id="aethos-qna-bulk-action" class="regular-text" style="width: auto;">
                                <option value="">Bulk Actions</option>
                                <option value="delete">Delete</option>
                            </select>
                            <button type="button" class="button" id="aethos-qna-bulk-apply">Apply</button>
                        </div>
                    </div>
                </div>

                <!-- Add/Edit Q&A Modal -->
                <div id="aethos-qna-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 100000; align-items: center; justify-content: center;">
                    <div style="background: #fff; border-radius: 8px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                        <div style="padding: 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="margin: 0; font-size: 20px; font-weight: 600;" id="aethos-qna-modal-title">Add New Q&A</h3>
                            <button type="button" class="button" id="aethos-qna-modal-close" style="padding: 4px 8px;">
                                <span class="dashicons dashicons-no-alt"></span>
                            </button>
                        </div>
                        
                        <div style="padding: 24px;">
                            <input type="hidden" id="aethos-qna-id" value="">
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 500; margin-bottom: 8px;">Question *</label>
                                <input type="text" id="aethos-qna-question" class="regular-text" style="width: 100%;" placeholder="Enter the question..." required>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 500; margin-bottom: 8px;">Answer *</label>
                                <textarea id="aethos-qna-answer" rows="6" class="regular-text" style="width: 100%; resize: vertical;" placeholder="Enter the answer..." required></textarea>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                                <div>
                                    <label style="display: block; font-weight: 500; margin-bottom: 8px;">Category</label>
                                    <input type="text" id="aethos-qna-category" class="regular-text" style="width: 100%;" placeholder="General" value="General">
                                </div>
                                
                                <div>
                                    <label style="display: block; font-weight: 500; margin-bottom: 8px;">Priority</label>
                                    <select id="aethos-qna-priority" class="regular-text" style="width: 100%;">
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label style="display: block; font-weight: 500; margin-bottom: 8px;">Status</label>
                                    <select id="aethos-qna-status" class="regular-text" style="width: 100%;">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px; background: #f9fafb;">
                            <button type="button" class="button" id="aethos-qna-modal-cancel">Cancel</button>
                            <button type="button" class="button button-primary" id="aethos-qna-modal-save">Save Q&A</button>
                        </div>
                    </div>
                </div>
                <!-- Advanced Section -->
                <div id="advanced-section" class="aethos-content-section" style="display: none;">
                    <!-- Data Handling & Privacy -->
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                        <h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Data Handling & Privacy</h2>
                        <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">Control how conversation data is stored and managed on your server.</p>
                        
                        <div style="margin-bottom: 20px;">
                            <label for="aethos_log_retention_days" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">Conversation Log Retention</label>
                            <select name="aethos_log_retention_days" id="aethos_log_retention_days" class="regular-text" style="width: 100%; max-width: 300px;">
                                <option value="7" <?php selected( $log_retention_days, 7 ); ?>>7 Days</option>
                                <option value="30" <?php selected( $log_retention_days, 30 ); ?>>30 Days</option>
                                <option value="60" <?php selected( $log_retention_days, 60 ); ?>>60 Days</option>
                                <option value="90" <?php selected( $log_retention_days, 90 ); ?>>90 Days</option>
                                <option value="180" <?php selected( $log_retention_days, 180 ); ?>>180 Days</option>
                                <option value="365" <?php selected( $log_retention_days, 365 ); ?>>1 Year</option>
                            </select>
                            <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 13px;">Set the period for which conversation logs are stored.</p>
                        </div>
                        
                        <div style="margin-bottom: 24px;">
                            <label style="display: flex; align-items: start; gap: 12px; cursor: pointer;">
                                <input type="checkbox" name="aethos_disable_ip_logging" value="1" <?php checked( $disable_ip_logging, true ); ?> style="margin-top: 2px;">
                                <div>
                                    <div style="font-weight: 500; margin-bottom: 4px;">Disable IP Address Logging</div>
                                    <div style="color: #6b7280; font-size: 13px;">Check this to prevent user IP addresses from being stored in logs for privacy.</div>
                                </div>
                            </label>
                        </div>
                        
                        <div style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
                            <h3 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600;">Manual Data Management</h3>
                            <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 13px;">Permanently delete all stored conversation logs from your database.</p>
                            <button type="button" class="button" id="aethos-clear-logs-btn" style="color: #dc2626; border-color: #dc2626;">
                                Clear All Conversation Logs
                            </button>
                        </div>
                    </div>
                    
                    <!-- Performance Optimization -->
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                        <h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Performance Optimization</h2>
                        <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">Adjust settings to optimize the plugin's impact on your site's performance.</p>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: flex; align-items: start; gap: 12px; cursor: pointer;">
                                <input type="checkbox" name="aethos_enable_minification" value="1" <?php checked( $enable_minification, true ); ?> style="margin-top: 2px;">
                                <div>
                                    <div style="font-weight: 500; margin-bottom: 4px;">Enable Script Minification</div>
                                    <div style="color: #6b7280; font-size: 13px;">Minify the plugin's CSS and JavaScript files to reduce page load times.</div>
                                </div>
                            </label>
                        </div>
                        
                        <div>
                            <label for="aethos_request_timeout" style="display: block; font-weight: 500; margin-bottom: 8px; font-size: 14px;">
                                Server Request Timeout
                                <span class="dashicons dashicons-info" style="color: #6b7280; font-size: 16px; vertical-align: middle;" title="Maximum time to wait for API responses"></span>
                            </label>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <input type="number" name="aethos_request_timeout" id="aethos_request_timeout" value="<?php echo esc_attr( $request_timeout ); ?>" min="10" max="120" class="small-text">
                                <span style="color: #6b7280; font-size: 14px;">seconds</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div>
                        <?php submit_button( 'Save Changes', 'primary', 'submit', false ); ?>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div id="analytics-section" class="aethos-content-section" style="display: none;">
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <div>
                                <h2 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 600;">Chatbot Analytics</h2>
                                <p style="margin: 0; color: #6b7280; font-size: 14px;">Monitor your chatbot's activity and effectiveness.</p>
                            </div>
                            <button type="button" class="button" id="aethos-export-csv-btn">
                                <span class="dashicons dashicons-download"></span> Export CSV
                            </button>
                        </div>
                        
                        <!-- Time Range Filters -->
                        <div style="display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap;">
                            <button type="button" class="button aethos-date-filter active" data-range="last_7_days" style="background: #4f46e5; color: white; border-color: #4f46e5;">Last 7 Days</button>
                            <button type="button" class="button aethos-date-filter" data-range="last_30_days">Last 30 Days</button>
                            <button type="button" class="button aethos-date-filter" data-range="this_month">This Month</button>
                            <button type="button" class="button aethos-date-filter" data-range="custom">
                                Custom Range <span class="dashicons dashicons-calendar-alt" style="vertical-align: middle;"></span>
                            </button>
                        </div>
                        
                        <!-- Key Metrics Cards -->
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
                            <!-- Total Conversations -->
                            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                                <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Total Conversations</div>
                                <div style="font-size: 28px; font-weight: 700; margin-bottom: 4px;" id="metric-total-conversations">0</div>
                                <div style="color: #10b981; font-size: 12px; font-weight: 500;" id="metric-total-change">+0%</div>
                            </div>
                            
                            <!-- Avg Chat Duration -->
                            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                                <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Avg. Chat Duration</div>
                                <div style="font-size: 28px; font-weight: 700; margin-bottom: 4px;" id="metric-avg-duration">0s</div>
                                <div style="color: #ef4444; font-size: 12px; font-weight: 500;" id="metric-duration-change">0%</div>
                            </div>
                            
                            <!-- Unique Users -->
                            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                                <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Unique Users</div>
                                <div style="font-size: 28px; font-weight: 700; margin-bottom: 4px;" id="metric-unique-users">0</div>
                                <div style="color: #10b981; font-size: 12px; font-weight: 500;" id="metric-users-change">+0%</div>
                            </div>
                            
                            <!-- Feedback Score -->
                            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                                <div style="color: #6b7280; font-size: 13px; margin-bottom: 8px;">Feedback Score</div>
                                <div style="font-size: 28px; font-weight: 700; margin-bottom: 4px;" id="metric-feedback-score">0/5</div>
                                <div style="color: #10b981; font-size: 12px; font-weight: 500;" id="metric-feedback-change">+0%</div>
                            </div>
                        </div>
                        
                        <!-- Charts Row -->
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 24px;">
                            <!-- Conversation Volume Chart -->
                            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
                                <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600;">Conversation Volume</h3>
                                <canvas id="aethos-volume-chart" style="max-height: 250px;"></canvas>
                            </div>
                            
                            <!-- Top Topics -->
                            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
                                <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600;">Top Topics</h3>
                                <div style="color: #6b7280; font-size: 13px; margin-bottom: 12px;">Most frequent topics this week</div>
                                <div id="aethos-topics-list" style="display: flex; flex-direction: column; gap: 8px;">
                                    <!-- Topics will be populated via JavaScript -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Conversations Table -->
                        <div>
                            <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600; color: #111827;">Recent Conversations</h3>
                            <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">A log of the most recent chats with your users.</p>
                            
                            <div style="overflow-x: auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;">
                                <table class="wp-list-table widefat" style="border: none; margin: 0;">
                                    <thead style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <tr>
                                            <th style="padding: 14px 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border: none;">Timestamp</th>
                                            <th style="padding: 14px 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border: none;">User ID</th>
                                            <th style="padding: 14px 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border: none;">Snippet</th>
                                            <th style="padding: 14px 16px; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border: none; text-align: right;">Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody id="aethos-conversations-table">
                                        <tr>
                                            <td colspan="4" style="padding: 60px 20px; text-align: center; color: #9ca3af; border: none;">
                                                No conversations found for this period.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversation History Section -->
                <div id="conversation-history-section" class="aethos-content-section" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <div>
                            <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 600;">Conversation History Log</h2>
                            <p style="margin: 0; color: #6b7280; font-size: 14px;">Review and analyze past chatbot interactions.</p>
                        </div>
                        <button type="button" class="button button-primary" id="aethos-export-logs-btn" style="display: inline-flex; align-items: center; gap: 8px;">
                            <span class="dashicons dashicons-download"></span>
                            Export Logs
                        </button>
                    </div>

                    <!-- Search and Filters -->
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                            <div style="flex: 1; min-width: 250px; position: relative;">
                                <span class="dashicons dashicons-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 18px;"></span>
                                <input type="text" id="aethos-conv-search" placeholder="Search by keyword, user ID..." class="regular-text" style="width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #d1d5db; border-radius: 6px;">
                            </div>
                            
                            <button type="button" class="button" id="aethos-date-range-btn" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;">
                                <span class="dashicons dashicons-calendar-alt"></span>
                                Date Range
                            </button>
                            
                            <button type="button" class="button" id="aethos-filter-tags-btn" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;">
                                <span class="dashicons dashicons-tag"></span>
                                Filter by Tags
                            </button>
                            
                            <button type="button" class="button" id="aethos-filter-rating-btn" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;">
                                <span class="dashicons dashicons-star-filled"></span>
                                Filter by Rating
                            </button>
                            
                            <button type="button" class="button" id="aethos-clear-filters-btn" style="color: #6b7280;">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Three Column Layout -->
                    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 20px; height: 600px;">
                        
                        <!-- Left Column: Conversations List -->
                        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
                            <div style="padding: 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Conversations</h3>
                            </div>
                            
                            <div id="aethos-conversations-list" style="flex: 1; overflow-y: auto;">
                                <div style="padding: 40px 20px; text-align: center; color: #9ca3af;">
                                    <span class="dashicons dashicons-admin-comments" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px;"></span>
                                    <p style="margin: 0; font-size: 14px;">No conversations found</p>
                                </div>
                            </div>
                            
                            <!-- Pagination -->
                            <div style="padding: 12px 16px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f9fafb;">
                                <button type="button" class="button" id="aethos-conv-prev" disabled>
                                    <span class="dashicons dashicons-arrow-left-alt2"></span>
                                </button>
                                <span id="aethos-conv-pagination" style="font-size: 13px; color: #6b7280;">Page 1 of 12</span>
                                <button type="button" class="button" id="aethos-conv-next">
                                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Right Column: Conversation Details -->
                        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
                            <div id="aethos-conversation-details-header" style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: none;">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div>
                                        <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600;">Conversation Details</h3>
                                        <p style="margin: 0; font-size: 13px; color: #6b7280;" id="aethos-conv-id">ID: 4ab1-c8d7-e2f3</p>
                                    </div>
                                    <div style="display: flex; gap: 16px; font-size: 13px; color: #6b7280;">
                                        <div>
                                            <strong style="color: #374151;">User:</strong>
                                            <span id="aethos-conv-user">192.168.1.1</span>
                                        </div>
                                        <div>
                                            <strong style="color: #374151;">Started:</strong>
                                            <span id="aethos-conv-started">10:32 AM</span>
                                        </div>
                                        <div>
                                            <strong style="color: #374151;">Duration:</strong>
                                            <span id="aethos-conv-duration">13m</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="aethos-conversation-messages" style="flex: 1; overflow-y: auto; padding: 20px;">
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #9ca3af;">
                                    <div style="text-align: center;">
                                        <span class="dashicons dashicons-format-chat" style="font-size: 64px; opacity: 0.2; margin-bottom: 16px;"></span>
                                        <p style="margin: 0; font-size: 15px;">Select a conversation to view details</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- User Feedback & Ratings Section -->
                <div id="feedback-ratings-section" class="aethos-content-section" style="display: none;">
                    <div style="margin-bottom: 24px;">
                        <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 600;">User Feedback & Ratings</h2>
                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Review feedback from your chatbot users to improve their experience.</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
                        <!-- Average Rating Card -->
                        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
                            <div style="color: #6b7280; font-size: 13px; font-weight: 500; margin-bottom: 8px;">Average Rating</div>
                            <div style="font-size: 36px; font-weight: 700; color: #111827; margin-bottom: 4px;" id="aethos-avg-rating">4.8</div>
                            <div style="color: #10b981; font-size: 13px; font-weight: 500;" id="aethos-rating-change">+0.2%</div>
                        </div>

                        <!-- Total Feedback Card -->
                        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
                            <div style="color: #6b7280; font-size: 13px; font-weight: 500; margin-bottom: 8px;">Total Feedback</div>
                            <div style="font-size: 36px; font-weight: 700; color: #111827; margin-bottom: 4px;" id="aethos-total-feedback">1,204</div>
                            <div style="color: #10b981; font-size: 13px; font-weight: 500;" id="aethos-feedback-change">+16%</div>
                        </div>

                        <!-- Positive Sentiment Card -->
                        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
                            <div style="color: #6b7280; font-size: 13px; font-weight: 500; margin-bottom: 8px;">Positive Sentiment</div>
                            <div style="font-size: 36px; font-weight: 700; color: #111827; margin-bottom: 4px;" id="aethos-positive-sentiment">92%</div>
                            <div style="color: #10b981; font-size: 13px; font-weight: 500;" id="aethos-sentiment-change">+4%</div>
                        </div>
                    </div>

                    <!-- Rating Distribution and Overview -->
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                        <div style="display: grid; grid-template-columns: 200px 1fr; gap: 40px;">
                            <!-- Left: Overall Rating -->
                            <div style="text-align: center;">
                                <div style="font-size: 56px; font-weight: 700; color: #111827; margin-bottom: 8px;" id="aethos-overall-rating">4.8</div>
                                <div style="margin-bottom: 8px;">
                                    <span class="dashicons dashicons-star-filled" style="color: #fbbf24; font-size: 20px;"></span>
                                    <span class="dashicons dashicons-star-filled" style="color: #fbbf24; font-size: 20px;"></span>
                                    <span class="dashicons dashicons-star-filled" style="color: #fbbf24; font-size: 20px;"></span>
                                    <span class="dashicons dashicons-star-filled" style="color: #fbbf24; font-size: 20px;"></span>
                                    <span class="dashicons dashicons-star-half" style="color: #fbbf24; font-size: 20px;"></span>
                                </div>
                                <div style="color: #6b7280; font-size: 13px;" id="aethos-review-count">1,204 reviews</div>
                            </div>

                            <!-- Right: Rating Bars -->
                            <div style="display: flex; flex-direction: column; justify-content: center; gap: 8px;">
                                <!-- 5 Stars -->
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 13px; color: #6b7280; width: 12px;">5</span>
                                    <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div id="aethos-bar-5" style="height: 100%; background: #3b82f6; width: 80%;"></div>
                                    </div>
                                    <span id="aethos-percent-5" style="font-size: 13px; color: #6b7280; width: 40px; text-align: right;">80%</span>
                                </div>

                                <!-- 4 Stars -->
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 13px; color: #6b7280; width: 12px;">4</span>
                                    <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div id="aethos-bar-4" style="height: 100%; background: #3b82f6; width: 12%;"></div>
                                    </div>
                                    <span id="aethos-percent-4" style="font-size: 13px; color: #6b7280; width: 40px; text-align: right;">12%</span>
                                </div>

                                <!-- 3 Stars -->
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 13px; color: #6b7280; width: 12px;">3</span>
                                    <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div id="aethos-bar-3" style="height: 100%; background: #3b82f6; width: 4%;"></div>
                                    </div>
                                    <span id="aethos-percent-3" style="font-size: 13px; color: #6b7280; width: 40px; text-align: right;">4%</span>
                                </div>

                                <!-- 2 Stars -->
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 13px; color: #6b7280; width: 12px;">2</span>
                                    <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div id="aethos-bar-2" style="height: 100%; background: #3b82f6; width: 2%;"></div>
                                    </div>
                                    <span id="aethos-percent-2" style="font-size: 13px; color: #6b7280; width: 40px; text-align: right;">2%</span>
                                </div>

                                <!-- 1 Star -->
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 13px; color: #6b7280; width: 12px;">1</span>
                                    <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div id="aethos-bar-1" style="height: 100%; background: #3b82f6; width: 2%;"></div>
                                    </div>
                                    <span id="aethos-percent-1" style="font-size: 13px; color: #6b7280; width: 40px; text-align: right;">2%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters and Search -->
                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                            <select id="aethos-feedback-date-filter" class="regular-text" style="min-width: 150px;">
                                <option value="7">Last 7 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="all">All Time</option>
                            </select>

                            <select id="aethos-feedback-rating-filter" class="regular-text" style="min-width: 120px;">
                                <option value="">All Ratings</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>

                            <select id="aethos-feedback-sort" class="regular-text" style="min-width: 150px;">
                                <option value="newest">Sort by: Newest</option>
                                <option value="oldest">Sort by: Oldest</option>
                                <option value="highest">Sort by: Highest Rating</option>
                                <option value="lowest">Sort by: Lowest Rating</option>
                            </select>

                            <div style="flex: 1; min-width: 250px; position: relative;">
                                <span class="dashicons dashicons-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 18px;"></span>
                                <input type="text" id="aethos-feedback-search" placeholder="Search feedback..." class="regular-text" style="width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #d1d5db; border-radius: 6px;">
                            </div>
                        </div>
                    </div>

                    <!-- Feedback List -->
                    <div id="aethos-feedback-list">
                        <!-- Feedback items will be loaded here -->
                        <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 60px 20px; text-align: center; color: #9ca3af;">
                            <span class="dashicons dashicons-star-filled" style="font-size: 64px; opacity: 0.2; margin-bottom: 16px;"></span>
                            <p style="margin: 0; font-size: 15px;">Loading feedback...</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div style="margin-top: 24px; display: flex; justify-content: center; align-items: center; gap: 12px;">
                        <button type="button" class="button" id="aethos-feedback-prev" disabled>
                            <span class="dashicons dashicons-arrow-left-alt2"></span>
                        </button>
                        <div style="display: flex; gap: 4px;" id="aethos-feedback-pagination">
                            <button type="button" class="button button-primary" style="min-width: 36px;">1</button>
                            <button type="button" class="button" style="min-width: 36px;">2</button>
                            <button type="button" class="button" style="min-width: 36px;">3</button>
                            <span style="padding: 0 8px; color: #6b7280;">...</span>
                            <button type="button" class="button" style="min-width: 36px;">8</button>
                        </div>
                        <button type="button" class="button" id="aethos-feedback-next">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<style>
    .aethos-nav-item:hover {
        background: #f9fafb;
        color: #111827;
    }
    
    .aethos-nav-item.active {
        background: #eef2ff;
        color: #4f46e5;
        font-weight: 500;
    }
    
    .aethos-collapse-trigger:hover {
        background: #f3f4f6;
    }
    
    .aethos-date-filter.active {
        background: #4f46e5 !important;
        color: white !important;
        border-color: #4f46e5 !important;
    }
    
    #aethos-connection-message.success {
        padding: 12px;
        background: #d1fae5;
        border: 1px solid #10b981;
        border-radius: 6px;
        color: #065f46;
    }
    
    .aethos-model-card:hover {
        border-color: #4f46e5 !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
    }
    
    .aethos-model-card.selected {
        border-color: #4f46e5 !important;
        background: #eff6ff !important;
    }
    
    #aethos-connection-message.error {
        padding: 12px;
        background: #fee2e2;
        border: 1px solid #ef4444;
        border-radius: 6px;
        color: #991b1b;
    }
</style>

<script>
jQuery(document).ready(function($) {
    // Navigation
    $('.aethos-nav-item').on('click', function(e) {
        e.preventDefault();
        console.log('Navigation clicked');
        
        $('.aethos-nav-item').removeClass('active');
        $(this).addClass('active');
        
        const section = $(this).data('section');
        console.log('Switching to section:', section);
        console.log('Section element exists:', $('#' + section).length > 0);
        
        $('.aethos-content-section').css('display', 'none');
        $('#' + section).css('display', 'block');
        
        console.log('Section should now be visible');
        
        // Load analytics data when switching to analytics section
        if (section === 'analytics-section') {
            loadAnalyticsData();
        }
    });
    
    // Collapsible sections - removed duplicate handler
    
    // Color pickers
    $('.aethos-color-picker').wpColorPicker({
        change: function() {
            updatePreview();
        }
    });
    
    // Live preview updates
    $('#aethos_header_title, #aethos_chat_subtitle, #aethos_greeting_message').on('input', updatePreview);
    $('#aethos_primary_color, #aethos_user_text_color, #aethos_ai_bubble_color').on('change', updatePreview);
    
    function updatePreview() {
        const title = $('#aethos_header_title').val();
        const subtitle = $('#aethos_chat_subtitle').val();
        const greeting = $('#aethos_greeting_message').val();
        const primaryColor = $('#aethos_primary_color').val();
        const userTextColor = $('#aethos_user_text_color').val();
        const aiBubbleColor = $('#aethos_ai_bubble_color').val();
        
        $('#preview-title').text(title);
        $('#preview-subtitle').text(subtitle);
        $('#preview-ai-bubble').text(greeting).css('background', aiBubbleColor);
        $('#preview-user-bubble').css({
            'background': primaryColor,
            'color': userTextColor
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
        $('#preview-send-btn').css('background', primaryColor);
        $('#preview-float-btn').css('background', primaryColor);
    }
    
    // Connection handlers
    $('#aethos-connect-btn').on('click', function() {
        const apiKey = $('#aethos_api_key_input').val().trim();
        
        if (!apiKey) {
            showMessage('Please enter an API key.', 'error');
            return;
        }
        
        $(this).prop('disabled', true).text('Connecting...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_test_connection',
                nonce: aethosAdmin.nonce,
                api_key: apiKey
            },
            success: function(response) {
                if (response.success) {
                    showMessage('Connection successful! Reloading...', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage(response.data.message || 'Connection failed.', 'error');
                    $('#aethos-connect-btn').prop('disabled', false).text('Connect');
                }
            },
            error: function() {
                showMessage('An error occurred. Please try again.', 'error');
                $('#aethos-connect-btn').prop('disabled', false).text('Connect');
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    $('#aethos-disconnect-btn').on('click', function() {
        if (!confirm('Are you sure you want to disconnect?')) return;
        
        $(this).prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_disconnect',
                nonce: aethosAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    // Advanced settings handlers
    $('#aethos-show-key-btn').on('click', function() {
        const input = $('#aethos-site-key-display');
        const type = input.attr('type');
        input.attr('type', type === 'password' ? 'text' : 'password');
        $(this).find('.dashicons').toggleClass('dashicons-visibility dashicons-hidden');
    });
    
    $('#aethos-copy-key-btn').on('click', function() {
        const input = $('#aethos-site-key-display')[0];
        input.select();
        document.execCommand('copy');
        alert('API key copied to clipboard!');
    });
    
    $('#aethos-regenerate-key-btn').on('click', function() {
        if (!confirm('Are you sure? This will invalidate your current API key immediately.')) return;
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_regenerate_key',
                nonce: aethosAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#aethos-site-key-display').val(response.data.key);
                    alert('API key regenerated successfully!');
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    $('#aethos-clear-logs-btn').on('click', function() {
        if (!confirm('Are you sure you want to permanently delete all conversation logs?')) return;
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_clear_logs',
                nonce: aethosAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('All conversation logs have been cleared.');
                    loadAnalyticsData();
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    // Analytics handlers
    $('.aethos-date-filter').on('click', function() {
        $('.aethos-date-filter').removeClass('active').css({
            'background': '',
            'color': '',
            'border-color': ''
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
        $(this).addClass('active');
        loadAnalyticsData();
    });
    
    $('#aethos-export-csv-btn').on('click', function() {
        const dateRange = $('.aethos-date-filter.active').data('range');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_export_analytics',
                nonce: aethosAdmin.nonce,
                date_range: dateRange
            },
            success: function(response) {
                if (response.success) {
                    const blob = new Blob([response.data.csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'aethos-analytics-' + new Date().toISOString().split('T')[0] + '.csv';
                    a.click();
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    function loadAnalyticsData() {
        const dateRange = $('.aethos-date-filter.active').data('range');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_analytics',
                nonce: aethosAdmin.nonce,
                date_range: dateRange
            },
            success: function(response) {
                if (response.success) {
                    updateAnalyticsDashboard(response.data);
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    }
    
    function updateAnalyticsDashboard(data) {
        // Update metrics
        $('#metric-total-conversations').text(data.stats.total_conversations.toLocaleString());
        $('#metric-avg-duration').text(formatDuration(data.stats.avg_duration));
        $('#metric-unique-users').text(data.stats.unique_users.toLocaleString());
        $('#metric-feedback-score').text(data.stats.avg_feedback_score + '/5');
        
        // Update topics
        let topicsHtml = '';
        for (const [topic, count] of Object.entries(data.topics)) {
            const maxCount = Math.max(...Object.values(data.topics));
            const percentage = (count / maxCount) * 100;
            topicsHtml += `
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 13px;">
                        <span style="font-weight: 500;">${topic}</span>
                        <span style="color: #6b7280;">${count}</span>
                    </div>
                    <div style="background: #e5e7eb; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div style="background: #4f46e5; height: 100%; width: ${percentage}%;"></div>
                    </div>
                </div>
            `;
        }
        $('#aethos-topics-list').html(topicsHtml || '<div style="color: #6b7280; font-size: 13px;">No topics data available</div>');
        
        // Update conversations table
        let conversationsHtml = '';
        if (data.conversations.length > 0) {
            data.conversations.forEach(conv => {
                conversationsHtml += `
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 16px; font-size: 14px; color: #374151; border: none;">${conv.created_at}</td>
                        <td style="padding: 16px; font-size: 14px; color: #374151; border: none;">${conv.user_id}</td>
                        <td style="padding: 16px; font-size: 14px; color: #6b7280; border: none;">${conv.first_message.substring(0, 60)}...</td>
                        <td style="padding: 16px; font-size: 14px; color: #374151; border: none; text-align: right;">${formatDuration(conv.duration)}</td>
                    </tr>
                `;
            });
        } else {
            conversationsHtml = '<tr><td colspan="4" style="padding: 60px 20px; text-align: center; color: #9ca3af; border: none;">No conversations found for this period.</td></tr>';
        }
        $('#aethos-conversations-table').html(conversationsHtml);
    }
    
    function formatDuration(seconds) {
        if (seconds < 60) return seconds + 's';
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return minutes + 'm ' + secs + 's';
    }
    
    function showMessage(message, type) {
        const $msg = $('#aethos-connection-message');
        $msg.removeClass('success error').addClass(type).text(message).show();
    }
    
    // Avatar selection
    $('.aethos-avatar-option').on('click', function() {
        // Remove selected class from all
        $('.aethos-avatar-option').removeClass('selected').css('border-color', '#e5e7eb').find('.dashicons-yes').parent().remove();
        
        // Add selected class to clicked
        $(this).addClass('selected').css('border-color', '#4f46e5');
        
        // Add checkmark
        if (!$(this).find('.dashicons-yes').length) {
            $(this).append('<div style="position: absolute; top: 4px; right: 4px; background: #4f46e5; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;"><span class="dashicons dashicons-yes" style="font-size: 16px; width: 16px; height: 16px;"></span></div>');
        }
        
        // Update hidden input
        const avatarUrl = $(this).data('avatar');
        $('#aethos_chat_icon').val(avatarUrl);
        
        // Update preview in live preview section
        $('#aethos-preview-icon').attr('src', avatarUrl);
    });
    
    // Custom icon upload
    $('#aethos-upload-custom-icon').on('click', function(e) {
        e.preventDefault();
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append('action', 'aethos_upload_icon');
            formData.append('nonce', aethosAdmin.nonce);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#aethos_chat_icon').val(response.data.url);
                        location.reload();
                    }
                }
            });
        };
        input.click();
    });
    
    // Model selection handlers
    $('.aethos-model-card').on('click', function() {
        $('.aethos-model-card').removeClass('selected').css({
            'border-color': '#e5e7eb',
            'background': '#fff'
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
        $(this).addClass('selected').css({
            'border-color': '#4f46e5',
            'background': '#eff6ff'
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
        $(this).find('input[type="radio"]').prop('checked', true);
    });
    
    // Radio button direct click
    $('.aethos-model-card input[type="radio"]').on('click', function(e) {
        e.stopPropagation();
        $(this).closest('.aethos-model-card').click();
    });

    // User Feedback & Ratings JavaScript
    
    let currentFeedbackPage = 1;
    let currentFeedbackFilters = {
        search: '',
        rating: '',
        days: '30',
        sort: 'newest'
    };
    
    // Load feedback when section is opened
    $('.aethos-nav-item[data-section="feedback-ratings-section"]').on('click', function() {
        setTimeout(function() {
            loadFeedbackStats();
            loadFeedbackList();
        }, 100);
    });
    
    // Date filter change
    $('#aethos-feedback-date-filter').on('change', function() {
        currentFeedbackFilters.days = $(this).val();
        currentFeedbackPage = 1;
        loadFeedbackStats();
        loadFeedbackList();
    });
    
    // Rating filter change
    $('#aethos-feedback-rating-filter').on('change', function() {
        currentFeedbackFilters.rating = $(this).val();
        currentFeedbackPage = 1;
        loadFeedbackList();
    });
    
    // Sort change
    $('#aethos-feedback-sort').on('change', function() {
        currentFeedbackFilters.sort = $(this).val();
        currentFeedbackPage = 1;
        loadFeedbackList();
    });
    
    // Search feedback
    let feedbackSearchTimeout;
    $('#aethos-feedback-search').on('input', function() {
        clearTimeout(feedbackSearchTimeout);
        feedbackSearchTimeout = setTimeout(function() {
            currentFeedbackFilters.search = $('#aethos-feedback-search').val();
            currentFeedbackPage = 1;
            loadFeedbackList();
        }, 500);
    });
    
    // Pagination
    $('#aethos-feedback-prev').on('click', function() {
        if (currentFeedbackPage > 1) {
            currentFeedbackPage--;
            loadFeedbackList();
        }
    });
    
    $('#aethos-feedback-next').on('click', function() {
        currentFeedbackPage++;
        loadFeedbackList();
    });
    
    $(document).on('click', '#aethos-feedback-pagination button', function() {
        const page = parseInt($(this).text());
        if (!isNaN(page)) {
            currentFeedbackPage = page;
            loadFeedbackList();
        }
    });
    
    // Load feedback statistics
    function loadFeedbackStats() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_feedback_stats',
                nonce: aethosAdmin.nonce,
                days: currentFeedbackFilters.days
            },
            success: function(response) {
                if (response.success) {
                    renderFeedbackStats(response.data);
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    }
    
    // Render feedback statistics
    function renderFeedbackStats(stats) {
        // Update cards
        $('#aethos-avg-rating').text(stats.avg_rating || '0.0');
        $('#aethos-total-feedback').text(stats.review_count || '0');
        $('#aethos-positive-sentiment').text(stats.positive_sentiment + '%');
        
        // Update changes
        const ratingChangeColor = stats.rating_change >= 0 ? '#10b981' : '#ef4444';
        const ratingChangeSign = stats.rating_change >= 0 ? '+' : '';
        $('#aethos-rating-change').text(ratingChangeSign + stats.rating_change + '%').css('color', ratingChangeColor);
        
        const feedbackChangeColor = stats.feedback_change >= 0 ? '#10b981' : '#ef4444';
        const feedbackChangeSign = stats.feedback_change >= 0 ? '+' : '';
        $('#aethos-feedback-change').text(feedbackChangeSign + stats.feedback_change + '%').css('color', feedbackChangeColor);
        
        $('#aethos-sentiment-change').text('+4%').css('color', '#10b981');
        
        // Update overall rating
        $('#aethos-overall-rating').text(stats.avg_rating || '0.0');
        $('#aethos-review-count').text(stats.review_count + ' reviews');
        
        // Update rating bars
        for (let i = 1; i <= 5; i++) {
            const percentage = stats.rating_percentages[i] || 0;
            $('#aethos-bar-' + i).css('width', percentage + '%');
            $('#aethos-percent-' + i).text(percentage + '%');
        }
    }
    
    // Load feedback list
    function loadFeedbackList() {
        $('#aethos-feedback-list').html(`
            <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 60px 20px; text-align: center; color: #9ca3af;">
                <span class="dashicons dashicons-update" style="font-size: 48px; opacity: 0.3; margin-bottom: 16px; animation: spin 1s linear infinite;"></span>
                <p style="margin: 0; font-size: 15px;">Loading feedback...</p>
            </div>
        `);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_feedback_list',
                nonce: aethosAdmin.nonce,
                search: currentFeedbackFilters.search,
                rating: currentFeedbackFilters.rating,
                days: currentFeedbackFilters.days,
                sort: currentFeedbackFilters.sort,
                page: currentFeedbackPage
            },
            success: function(response) {
                if (response.success) {
                    renderFeedbackList(response.data.feedback);
                    renderFeedbackPagination(response.data.current_page, response.data.pages);
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    }
    
    // Render feedback list
    function renderFeedbackList(feedbackList) {
        if (feedbackList.length === 0) {
            $('#aethos-feedback-list').html(`
                <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 60px 20px; text-align: center; color: #9ca3af;">
                    <span class="dashicons dashicons-star-filled" style="font-size: 64px; opacity: 0.2; margin-bottom: 16px;"></span>
                    <p style="margin: 0; font-size: 15px;">No feedback found</p>
                </div>
            `);
            return;
        }
        
        let html = '';
        feedbackList.forEach(function(feedback) {
            const stars = renderStars(feedback.rating);
            const timeAgo = getTimeAgo(feedback.created_at);
            const feedbackText = feedback.feedback_text || feedback.first_message || 'No feedback text provided';
            
            html += `
                <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                        <div style="display: flex; gap: 4px;">
                            ${stars}
                        </div>
                        <span style="font-size: 13px; color: #9ca3af;">${timeAgo}</span>
                    </div>
                    <p style="margin: 0 0 12px 0; font-size: 14px; color: #374151; line-height: 1.6;">"${escapeHtml(feedbackText.substring(0, 200))}${feedbackText.length > 200 ? '...' : ''}"</p>
                    <a href="#" class="aethos-view-conversation" data-id="${feedback.id}" style="color: #3b82f6; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                        View Conversation
                        <span class="dashicons dashicons-arrow-right-alt2" style="font-size: 16px;"></span>
                    </a>
                </div>
            `;
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
        
        $('#aethos-feedback-list').html(html);
    }
    
    // Render stars
    function renderStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<span class="dashicons dashicons-star-filled" style="color: #fbbf24; font-size: 18px;"></span>';
            } else {
                stars += '<span class="dashicons dashicons-star-empty" style="color: #d1d5db; font-size: 18px;"></span>';
            }
        }
        return stars;
    }
    
    // Render pagination
    function renderFeedbackPagination(current, total) {
        if (total <= 1) {
            $('#aethos-feedback-pagination').html('');
            $('#aethos-feedback-prev, #aethos-feedback-next').hide();
            return;
        }
        
        $('#aethos-feedback-prev, #aethos-feedback-next').show();
        $('#aethos-feedback-prev').prop('disabled', current <= 1);
        $('#aethos-feedback-next').prop('disabled', current >= total);
        
        let html = '';
        const maxButtons = 5;
        let startPage = Math.max(1, current - Math.floor(maxButtons / 2));
        let endPage = Math.min(total, startPage + maxButtons - 1);
        
        if (endPage - startPage < maxButtons - 1) {
            startPage = Math.max(1, endPage - maxButtons + 1);
        }
        
        if (startPage > 1) {
            html += '<button type="button" class="button" style="min-width: 36px;">1</button>';
            if (startPage > 2) {
                html += '<span style="padding: 0 8px; color: #6b7280;">...</span>';
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const btnClass = i === current ? 'button-primary' : '';
            html += `<button type="button" class="button ${btnClass}" style="min-width: 36px;">${i}</button>`;
        }
        
        if (endPage < total) {
            if (endPage < total - 1) {
                html += '<span style="padding: 0 8px; color: #6b7280;">...</span>';
            }
            html += `<button type="button" class="button" style="min-width: 36px;">${total}</button>`;
        }
        
        $('#aethos-feedback-pagination').html(html);
    }
    
    // View conversation from feedback
    $(document).on('click', '.aethos-view-conversation', function(e) {
        e.preventDefault();
        const convId = $(this).data('id');
        
        // Switch to conversation history section
        $('.aethos-nav-item[data-section="conversation-history-section"]').click();
        
        // Wait for section to load, then load the conversation
        setTimeout(function() {
            loadConversationDetails(convId);
            // Highlight the conversation in the list
            $('.aethos-conv-item[data-id="' + convId + '"]').click();
        }, 300);
    });
    
    // Get time ago
    function getTimeAgo(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        const now = new Date();
        const diffMs = now - date;
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        const diffWeeks = Math.floor(diffDays / 7);
        
        if (diffDays === 0) {
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            if (diffHours === 0) {
                const diffMins = Math.floor(diffMs / (1000 * 60));
                return diffMins <= 1 ? 'Just now' : diffMins + ' minutes ago';
            }
            return diffHours === 1 ? '1 hour ago' : diffHours + ' hours ago';
        } else if (diffDays === 1) {
            return '1 day ago';
        } else if (diffDays < 7) {
            return diffDays + ' days ago';
        } else if (diffWeeks === 1) {
            return '1 week ago';
        } else if (diffWeeks < 4) {
            return diffWeeks + ' weeks ago';
        } else {
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }
    }

    // Conversation History JavaScript
    
    let currentConvPage = 1;
    let currentFilters = {
        search: '',
        date_from: '',
        date_to: '',
        rating: ''
    };
    
    // Load conversations when section is opened
    $('.aethos-nav-item[data-section="conversation-history-section"]').on('click', function() {
        setTimeout(function() {
            loadConversations();
        }, 100);
    });
    
    // Search conversations
    let convSearchTimeout;
    $('#aethos-conv-search').on('input', function() {
        clearTimeout(convSearchTimeout);
        convSearchTimeout = setTimeout(function() {
            currentFilters.search = $('#aethos-conv-search').val();
            currentConvPage = 1;
            loadConversations();
        }, 500);
    });
    
    // Clear filters
    $('#aethos-clear-filters-btn').on('click', function() {
        $('#aethos-conv-search').val('');
        currentFilters = {
            search: '',
            date_from: '',
            date_to: '',
            rating: ''
        };
        currentConvPage = 1;
        loadConversations();
    });
    
    // Export logs
    $('#aethos-export-logs-btn').on('click', function() {
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Exporting...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_export_conversations',
                nonce: aethosAdmin.nonce,
                search: currentFilters.search,
                date_from: currentFilters.date_from,
                date_to: currentFilters.date_to,
                rating: currentFilters.rating
            },
            success: function(response) {
                if (response.success) {
                    // Create download link
                    const blob = new Blob([response.data.csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = response.data.filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                } else {
                    alert('Export failed: ' + response.data.message);
                }
            },
            error: function() {
                alert('Network error. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    // Pagination
    $('#aethos-conv-prev').on('click', function() {
        if (currentConvPage > 1) {
            currentConvPage--;
            loadConversations();
        }
    });
    
    $('#aethos-conv-next').on('click', function() {
        currentConvPage++;
        loadConversations();
    });
    
    // Load conversations list
    function loadConversations() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_conversations',
                nonce: aethosAdmin.nonce,
                search: currentFilters.search,
                date_from: currentFilters.date_from,
                date_to: currentFilters.date_to,
                rating: currentFilters.rating,
                page: currentConvPage
            },
            success: function(response) {
                if (response.success) {
                    renderConversationsList(response.data.conversations);
                    updatePagination(response.data.current_page, response.data.pages);
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    }
    
    // Render conversations list
    function renderConversationsList(conversations) {
        if (conversations.length === 0) {
            $('#aethos-conversations-list').html(`
                <div style="padding: 40px 20px; text-align: center; color: #9ca3af;">
                    <span class="dashicons dashicons-admin-comments" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px;"></span>
                    <p style="margin: 0; font-size: 14px;">No conversations found</p>
                </div>
            `);
            return;
        }
        
        let html = '';
        conversations.forEach(function(conv) {
            const isActive = $('#aethos-conversation-details-header').data('conv-id') == conv.id ? 'background: #eff6ff; border-left: 3px solid #3b82f6;' : '';
            const firstMessage = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No messages';
            const time = formatTime(conv.created_at);
            
            html += `
                <div class="aethos-conv-item" data-id="${conv.id}" style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; cursor: pointer; transition: all 0.2s; ${isActive}">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 6px;">
                        <strong style="font-size: 13px; color: #111827;">User: ${escapeHtml(conv.user_id)}</strong>
                        <span style="font-size: 11px; color: #9ca3af;">${time}</span>
                    </div>
                    <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.4;">${escapeHtml(firstMessage)}</p>
                </div>
            `;
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
        
        $('#aethos-conversations-list').html(html);
    }
    
    // Update pagination
    function updatePagination(current, total) {
        $('#aethos-conv-pagination').text(`Page ${current} of ${total}`);
        $('#aethos-conv-prev').prop('disabled', current <= 1);
        $('#aethos-conv-next').prop('disabled', current >= total);
    }
    
    // Click on conversation item
    $(document).on('click', '.aethos-conv-item', function() {
        const id = $(this).data('id');
        $('.aethos-conv-item').css({'background': '', 'border-left': ''});
        $(this).css({'background': '#eff6ff', 'border-left': '3px solid #3b82f6'});
        loadConversationDetails(id);
    });
    
    // Load conversation details
    function loadConversationDetails(id) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_conversation_details',
                nonce: aethosAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    renderConversationDetails(response.data);
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    }
    
    // Render conversation details
    function renderConversationDetails(conv) {
        // Update header
        $('#aethos-conversation-details-header').show().data('conv-id', conv.id);
        $('#aethos-conv-id').text('ID: ' + conv.id);
        $('#aethos-conv-user').text(conv.user_id);
        $('#aethos-conv-started').text(formatTime(conv.created_at));
        $('#aethos-conv-duration').text(formatDuration(conv.duration));
        
        // Render messages - use messages (from AJAX) or messages_array (legacy)
        let messagesHtml = '';
        const messagesData = conv.messages || conv.messages_array || [];
        if (messagesData.length > 0) {
            messagesData.forEach(function(msg) {
                const isUser = msg.role === 'user';
                const bgColor = isUser ? '#eff6ff' : '#f9fafb';
                const align = isUser ? 'flex-end' : 'flex-start';
                const label = isUser ? 'User' : 'Bot';
                const time = msg.timestamp ? formatTime(msg.timestamp) : '';
                
                messagesHtml += `
                    <div style="display: flex; justify-content: ${align}; margin-bottom: 16px;">
                        <div style="max-width: 70%; background: ${bgColor}; padding: 12px 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <strong style="font-size: 12px; color: #6b7280; text-transform: uppercase;">${label}</strong>
                                ${time ? '<span style="font-size: 11px; color: #9ca3af; margin-left: 12px;">' + time + '</span>' : ''}
                            </div>
                            <p style="margin: 0; font-size: 14px; color: #374151; line-height: 1.6;">${escapeHtml(msg.content || msg.message || '')}</p>
                            ${!isUser && msg.sources && msg.sources.length > 0 ? `
                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb;">
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 8px; font-weight: 500;">📄 Sources (${msg.sources.length})</div>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        ${msg.sources.map(s => `
                                            <a href="${escapeHtml(s.url || '#')}" target="_blank" rel="noopener" 
                                               style="display: flex; align-items: center; gap: 6px; padding: 6px 10px; background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 12px; color: #374151; text-decoration: none;">
                                                <img src="https://www.google.com/s2/favicons?domain=${s.url ? new URL(s.url).hostname : ''}&sz=16" alt="" style="width: 14px; height: 14px;">
                                                ${escapeHtml(s.title || 'Unknown')}
                                            </a>
                                        `).join('')}
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            });
        } else {
            messagesHtml = '<div style="text-align: center; padding: 40px; color: #9ca3af;">No messages in this conversation</div>';
        }
        
        $('#aethos-conversation-messages').html(messagesHtml);
        
        // Scroll to top
        $('#aethos-conversation-messages').scrollTop(0);
    }
    
    // Format time
    function formatTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        const now = new Date();
        const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
        
        if (diffDays === 0) {
            return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        } else if (diffDays === 1) {
            return 'Yesterday';
        } else if (diffDays < 7) {
            return diffDays + ' days ago';
        } else {
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }
    }
    
    // Format duration (already exists, but adding here for completeness)
    function formatDuration(seconds) {
        if (!seconds) return '0s';
        if (seconds < 60) return seconds + 's';
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        if (minutes < 60) return minutes + 'm ' + (secs > 0 ? secs + 's' : '');
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return hours + 'h ' + (mins > 0 ? mins + 'm' : '');
    }
    
    // Hover effects for conversation items
    $(document).on('mouseenter', '.aethos-conv-item', function() {
        if (!$(this).css('background-color').includes('239, 246, 255')) {
            $(this).css('background', '#f9fafb');
        }
    }).on('mouseleave', '.aethos-conv-item', function() {
        if (!$(this).css('background-color').includes('239, 246, 255')) {
            $(this).css('background', '');
        }
    });

    // Q&A Management JavaScript
    
    // Collapsible AI Suggestions
    $('.aethos-collapse-trigger').off('click').on('click', function(e) {
        e.preventDefault();
        const $content = $(this).next('.aethos-collapse-content');
        const $icon = $(this).find('.dashicons');
        
        if ($content.is(':visible')) {
            $content.slideUp(300);
            $icon.css('transform', 'rotate(0deg)');
        } else {
            $content.slideDown(300);
            $icon.css('transform', 'rotate(180deg)');
            
            // Load AI suggestions if not loaded
            if ($('#aethos-ai-suggestions-list').children().length === 1) {
                loadAISuggestions();
            }
        }
    });
    
    // Sync/Refresh Content
    $('#aethos-sync-content-btn').on('click', function() {
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Syncing...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_sync_content',
                nonce: aethosAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Content synced! ' + response.data.count + ' new suggestions generated.');
                    loadAISuggestions();
                    loadQnAList();
                } else {
                    alert('Sync failed: ' + response.data.message);
                }
            },
            error: function() {
                alert('Network error. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    // Load AI Suggestions
    function loadAISuggestions() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_ai_suggestions',
                nonce: aethosAdmin.nonce
            },
            success: function(response) {
                if (response.success && response.data.suggestions.length > 0) {
                    renderAISuggestions(response.data.suggestions);
                } else {
                    $('#aethos-ai-suggestions-list').html('<div style="text-align: center; padding: 40px; color: #6b7280;"><p>No AI suggestions available. Click "Sync/Refresh Content" to generate new suggestions.</p></div>');
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    }
    
    // Render AI Suggestions
    function renderAISuggestions(suggestions) {
        let html = '';
        suggestions.forEach(function(suggestion) {
            html += `
                <div class="aethos-suggestion-item" data-id="${suggestion.id}" style="padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 16px; background: #f9fafb;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                        <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">${escapeHtml(suggestion.question)}</h4>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="button aethos-accept-suggestion" data-id="${suggestion.id}" style="color: #10b981; border-color: #10b981;" title="Accept">
                                <span class="dashicons dashicons-yes"></span>
                            </button>
                            <button type="button" class="button aethos-edit-suggestion" data-id="${suggestion.id}" style="color: #f59e0b; border-color: #f59e0b;" title="Edit">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button type="button" class="button aethos-delete-suggestion" data-id="${suggestion.id}" style="color: #ef4444; border-color: #ef4444;" title="Delete">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </div>
                    <p style="margin: 0 0 8px 0; color: #374151; font-size: 14px; line-height: 1.6;">${escapeHtml(suggestion.answer)}</p>
                    <p style="margin: 0; color: #6b7280; font-size: 12px; font-style: italic;">Generated from "${escapeHtml(suggestion.source || 'Unknown')}"</p>
                </div>
            `;
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
        $('#aethos-ai-suggestions-list').html(html);
    }
    
    // Accept Suggestion
    $(document).on('click', '.aethos-accept-suggestion', function() {
        const id = $(this).data('id');
        const $item = $(this).closest('.aethos-suggestion-item');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_accept_suggestion',
                nonce: aethosAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    $item.fadeOut(300, function() { $(this).remove(); });
                    loadQnAList();
                } else {
                    alert('Failed to accept suggestion');
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    // Edit Suggestion
    $(document).on('click', '.aethos-edit-suggestion', function() {
        const id = $(this).data('id');
        const $item = $(this).closest('.aethos-suggestion-item');
        const question = $item.find('h4').text();
        const answer = $item.find('p').first().text();
        
        $('#aethos-qna-id').val(id);
        $('#aethos-qna-question').val(question);
        $('#aethos-qna-answer').val(answer);
        $('#aethos-qna-modal-title').text('Edit AI Suggestion');
        $('#aethos-qna-modal').css('display', 'flex');
    });
    
    // Delete Suggestion
    $(document).on('click', '.aethos-delete-suggestion', function() {
        if (!confirm('Are you sure you want to delete this suggestion?')) return;
        
        const id = $(this).data('id');
        const $item = $(this).closest('.aethos-suggestion-item');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_delete_qna',
                nonce: aethosAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    $item.fadeOut(300, function() { $(this).remove(); });
                } else {
                    alert('Failed to delete suggestion');
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    // Add New Q&A
    $('#aethos-add-qna-btn').on('click', function() {
        $('#aethos-qna-id').val('');
        $('#aethos-qna-question').val('');
        $('#aethos-qna-answer').val('');
        $('#aethos-qna-category').val('General');
        $('#aethos-qna-priority').val('normal');
        $('#aethos-qna-status').val('draft');
        $('#aethos-qna-modal-title').text('Add New Q&A');
        $('#aethos-qna-modal').css('display', 'flex');
    });
    
    // Close Modal
    $('#aethos-qna-modal-close, #aethos-qna-modal-cancel').on('click', function() {
        $('#aethos-qna-modal').css('display', 'none');
    });
    
    // Save Q&A
    $('#aethos-qna-modal-save').on('click', function() {
        const id = $('#aethos-qna-id').val();
        const question = $('#aethos-qna-question').val().trim();
        const answer = $('#aethos-qna-answer').val().trim();
        const category = $('#aethos-qna-category').val().trim() || 'General';
        const priority = $('#aethos-qna-priority').val();
        const status = $('#aethos-qna-status').val();
        
        if (!question || !answer) {
            alert('Question and answer are required');
            return;
        }
        
        const action = id ? 'aethos_update_qna' : 'aethos_add_qna';
        const data = {
            action: action,
            nonce: aethosAdmin.nonce,
            question: question,
            answer: answer,
            category: category,
            priority: priority,
            status: status
        };
        
        if (id) data.id = id;
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#aethos-qna-modal').css('display', 'none');
                    loadQnAList();
                    loadAISuggestions();
                } else {
                    alert('Failed to save Q&A: ' + response.data.message);
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    // Load Q&A List
    function loadQnAList() {
        const search = $('#aethos-qna-search').val();
        const category = $('#aethos-qna-category-filter').val();
        const priority = $('#aethos-qna-priority-filter').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_qna_list',
                nonce: aethosAdmin.nonce,
                search: search,
                category: category,
                priority: priority
            },
            success: function(response) {
                if (response.success) {
                    renderQnAList(response.data.qna_list);
                    loadCategories();
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    }
    
    // Render Q&A List
    function renderQnAList(qnaList) {
        if (qnaList.length === 0) {
            $('#aethos-qna-tbody').html('<tr><td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">No Q&A entries found.</td></tr>');
            return;
        }
        
        let html = '';
        qnaList.forEach(function(qna) {
            const priorityColors = {
                high: '#ef4444',
                normal: '#f59e0b',
                low: '#6b7280'
            };
            const statusColors = {
                published: '#10b981',
                draft: '#f59e0b'
            };
            
            html += `
                <tr>
                    <td style="padding: 12px;"><input type="checkbox" class="aethos-qna-checkbox" value="${qna.id}"></td>
                    <td style="padding: 12px; font-weight: 500;">${escapeHtml(qna.question)}</td>
                    <td style="padding: 12px;">${escapeHtml(qna.category)}</td>
                    <td style="padding: 12px;">
                        <span style="display: inline-block; padding: 4px 8px; background: ${priorityColors[qna.priority]}; color: white; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                            ${qna.priority}
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <span style="display: inline-block; padding: 4px 8px; background: ${statusColors[qna.status]}; color: white; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                            ${qna.status}
                        </span>
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        <button type="button" class="button aethos-edit-qna" data-id="${qna.id}" title="Edit" style="padding: 4px 8px;">
                            <span class="dashicons dashicons-edit" style="font-size: 16px;"></span>
                        </button>
                        <button type="button" class="button aethos-delete-qna" data-id="${qna.id}" title="Delete" style="padding: 4px 8px; color: #ef4444;">
                            <span class="dashicons dashicons-trash" style="font-size: 16px;"></span>
                        </button>
                    </td>
                </tr>
            `;
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
        $('#aethos-qna-tbody').html(html);
    }
    
    // Edit Q&A
    $(document).on('click', '.aethos-edit-qna', function() {
        const id = $(this).data('id');
        
        // Find the Q&A data from the table
        const $row = $(this).closest('tr');
        const question = $row.find('td:eq(1)').text();
        const category = $row.find('td:eq(2)').text();
        const priority = $row.find('td:eq(3) span').text().toLowerCase();
        const status = $row.find('td:eq(4) span').text().toLowerCase();
        
        // We need to fetch the full answer via AJAX since it's not in the table
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_qna_list',
                nonce: aethosAdmin.nonce,
                search: question
            },
            success: function(response) {
                if (response.success && response.data.qna_list.length > 0) {
                    const qna = response.data.qna_list.find(q => q.id == id);
                    if (qna) {
                        $('#aethos-qna-id').val(qna.id);
                        $('#aethos-qna-question').val(qna.question);
                        $('#aethos-qna-answer').val(qna.answer);
                        $('#aethos-qna-category').val(qna.category);
                        $('#aethos-qna-priority').val(qna.priority);
                        $('#aethos-qna-status').val(qna.status);
                        $('#aethos-qna-modal-title').text('Edit Q&A');
                        $('#aethos-qna-modal').css('display', 'flex');
                    }
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    // Delete Q&A
    $(document).on('click', '.aethos-delete-qna', function() {
        if (!confirm('Are you sure you want to delete this Q&A entry?')) return;
        
        const id = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_delete_qna',
                nonce: aethosAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    loadQnAList();
                } else {
                    alert('Failed to delete Q&A');
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    });
    
    // Search Q&A
    let searchTimeout;
    $('#aethos-qna-search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadQnAList();
        }, 500);
    });
    
    // Filter Q&A
    $('#aethos-qna-category-filter, #aethos-qna-priority-filter').on('change', function() {
        loadQnAList();
    });
    
    // Select All Checkboxes
    $('#aethos-qna-select-all').on('change', function() {
        $('.aethos-qna-checkbox').prop('checked', $(this).is(':checked'));
    });
    
    // Bulk Actions
    $('#aethos-qna-bulk-apply').on('click', function() {
        const action = $('#aethos-qna-bulk-action').val();
        const ids = $('.aethos-qna-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (!action || ids.length === 0) {
            alert('Please select an action and at least one item');
            return;
        }
        
        if (action === 'delete') {
            if (!confirm('Are you sure you want to delete ' + ids.length + ' Q&A entries?')) return;
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_bulk_delete_qna',
                    nonce: aethosAdmin.nonce,
                    ids: ids
                },
                success: function(response) {
                    if (response.success) {
                        loadQnAList();
                        $('#aethos-qna-select-all').prop('checked', false);
                    } else {
                        alert('Failed to delete Q&A entries');
                    }
                }
            });
        }
    });
    
    // Load Categories
    function loadCategories() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_get_qna_categories',
                nonce: aethosAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    const currentValue = $('#aethos-qna-category-filter').val();
                    let options = '<option value="">All Categories</option>';
                    response.data.categories.forEach(function(cat) {
                        options += `<option value="${escapeHtml(cat)}">${escapeHtml(cat)}</option>`;
                    });
                    $('#aethos-qna-category-filter').html(options).val(currentValue);
                }
            }
        });

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Load Q&A list when Q&A Management section is active
    $('.aethos-nav-item[data-section="qna-management-section"]').on('click', function() {
        setTimeout(function() {
            loadQnAList();
        }, 100);
    });
});

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }

        // Dashboard functionality
        function loadDashboard() {
            console.log('Loading dashboard...');
            
            // Check connection status
            var connectionStatus = '<?php echo esc_js($connection_status); ?>';
            var apiKey = '<?php echo esc_js($api_key); ?>';
            
            if (connectionStatus === 'connected' && apiKey) {
                $('#dashboard-not-connected').hide();
                $('#dashboard-connected').show();
                loadDashboardData();
            } else {
                $('#dashboard-not-connected').show();
                $('#dashboard-connected').hide();
            }
        }
        
        function loadDashboardData() {
            // Load analytics data for dashboard
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_analytics',
                    nonce: aethosAdmin.nonce,
                    period: 'last_7_days'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Update metrics
                        $('#dashboard-total-conversations').text(data.total_conversations || 0);
                        $('#dashboard-total-conversations-change').text((data.total_conversations_change >= 0 ? '+' : '') + (data.total_conversations_change || 0) + '%');
                        
                        $('#dashboard-new-questions').text(data.new_questions || 0);
                        $('#dashboard-new-questions-change').text((data.new_questions_change >= 0 ? '+' : '') + (data.new_questions_change || 0) + '%');
                        
                        $('#dashboard-feedback-score').text((data.average_rating || 0) + '/5');
                        $('#dashboard-feedback-change').text((data.feedback_change >= 0 ? '+' : '') + (data.feedback_change || 0) + '%');
                        
                        // Update arrow direction based on change
                        if (data.feedback_change >= 0) {
                            $('#dashboard-feedback-score').prev().find('.dashicons').removeClass('dashicons-arrow-down-alt').addClass('dashicons-arrow-up-alt');
                            $('#dashboard-feedback-change').css('color', '#10b981');
                        }
                        
                        // Update pending actions
                        $('#dashboard-pending-count').text(data.pending_qna || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load dashboard analytics');
                }
            });
            
            // Load latest conversations
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_get_conversations',
                    nonce: aethosAdmin.nonce,
                    limit: 3
                },
                success: function(response) {
                    if (response.success && response.data && response.data.conversations) {
                        var conversations = response.data.conversations;
                        var html = '';
                        
                        if (conversations.length === 0) {
                            html = '<div style="color: #9ca3af; text-align: center; padding: 20px;">No conversations yet</div>';
                        } else {
                            conversations.forEach(function(conv) {
                                var timeAgo = getTimeAgo(conv.started_at);
                                var snippet = conv.first_message ? conv.first_message.substring(0, 50) + '...' : 'No message';
                                
                                html += '<div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">';
                                html += '<div style="font-size: 14px; color: #111827; margin-bottom: 4px;">"' + escapeHtml(snippet) + '"</div>';
                                html += '<div style="font-size: 12px; color: #9ca3af;">' + timeAgo + '</div>';
                                html += '</div>';
                            });
                        }
                        
                        $('#dashboard-latest-conversations').html(html);
                    }
                },
                error: function() {
                    $('#dashboard-latest-conversations').html('<div style="color: #ef4444; text-align: center; padding: 20px;">Failed to load conversations</div>');
                }
            });
        }
        
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return seconds + ' seconds ago';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            return Math.floor(seconds / 86400) + ' days ago';
        }
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Load dashboard when section is opened
        $('.aethos-nav-item[data-section="dashboard-section"]').on('click', function() {
            setTimeout(function() {
                loadDashboard();
            }, 100);
        });
        
        // Load dashboard on page load if it's the active section
        if ($('#dashboard-section').css('display') === 'block') {
            loadDashboard();
        }
});
</script>
