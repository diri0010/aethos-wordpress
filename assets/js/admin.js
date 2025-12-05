/**
 * Aethos Admin JavaScript
 * Handles navigation, connection testing, and settings management
 */

(function ($) {
    'use strict';

    const AethosAdmin = {
        init: function () {
            this.bindEvents();
            this.initNavigation();
            this.initAccordions();
            this.initColorPickers();
            this.initMediaUploader();
            this.initIconPresets();
            this.initLivePreview();
        },

        bindEvents: function () {
            // Connect button (auto-saves on click)
            $('#aethos-connect').on('click', this.connectAndSave.bind(this));

            // Test connection button
            $('#aethos-test-connection').on('click', this.testConnection.bind(this));

            // Disconnect button
            $('#aethos-disconnect').on('click', this.disconnect.bind(this));

            // Save settings
            $('#aethos-settings-form').on('submit', this.saveSettings.bind(this));

            // Toggle switches
            $('.aethos-toggle input').on('change', this.handleToggleChange.bind(this));

            // Appearance settings changes for live preview
            $('input[name^="aethos_"], select[name^="aethos_"], textarea[name^="aethos_"]').on('change input', this.updateLivePreview.bind(this));
        },

        initNavigation: function () {
            // Sidebar navigation
            $('.aethos-nav-item').on('click', function (e) {
                e.preventDefault();
                const sectionId = $(this).data('section');

                // Update active states
                $('.aethos-nav-item').removeClass('active');
                $(this).addClass('active');

                $('.aethos-content-section').removeClass('active');
                $('#' + sectionId).addClass('active');

                // Save active section to localStorage
                localStorage.setItem('aethos_active_section', sectionId);
            });

            // Restore last active section or show first section
            const lastSection = localStorage.getItem('aethos_active_section');
            if (lastSection && $('#' + lastSection).length) {
                $('.aethos-nav-item[data-section="' + lastSection + '"]').click();
            } else {
                $('.aethos-nav-item').first().click();
            }
        },

        initAccordions: function () {
            $('.aethos-accordion-header').on('click', function () {
                const $item = $(this).closest('.aethos-accordion-item');
                const $content = $item.find('.aethos-accordion-content');

                // Toggle open state
                $item.toggleClass('open');

                // Animate content
                if ($item.hasClass('open')) {
                    $content.css('max-height', $content[0].scrollHeight + 'px');
                } else {
                    $content.css('max-height', '0');
                }
            });
        },

        initColorPickers: function () {
            $('.aethos-color-picker').wpColorPicker({
                change: function (event, ui) {
                    $(this).trigger('change'); // Trigger change for live preview

                    // Update text input if it exists
                    const $textInput = $(this).closest('.aethos-form-group').find('.aethos-color-input');
                    if ($textInput.length) {
                        $textInput.val(ui.color.toString());
                    }
                }
            });

            // Bidirectional sync
            $('.aethos-color-input').on('change input', function () {
                const val = $(this).val();
                const $picker = $(this).closest('.aethos-form-group').find('.aethos-color-picker');
                if ($picker.length) {
                    $picker.wpColorPicker('color', val);
                }
            });
        },

        initMediaUploader: function () {
            $('#aethos_upload_icon_btn').on('click', function (e) {
                e.preventDefault();

                const image_frame = wp.media({
                    title: 'Select or Upload Media',
                    multiple: false,
                    library: {
                        type: 'image',
                    },
                    button: {
                        text: 'Use this icon',
                    },
                });

                image_frame.on('select', function () {
                    const selection = image_frame.state().get('selection').first().toJSON();
                    $('#aethos_chat_icon').val(selection.url);
                    $('#aethos_chat_icon_preview').attr('src', selection.url);
                    AethosAdmin.updateLivePreview();
                });

                image_frame.open();
            });
        },

        initIconPresets: function () {
            $('.aethos-icon-preset').on('click', function () {
                const iconUrl = $(this).data('icon');

                // Update hidden input
                $('#aethos_chat_icon').val(iconUrl);

                // Update preview
                $('#aethos_chat_icon_preview').attr('src', iconUrl);

                // Update selection state
                $('.aethos-icon-preset').removeClass('selected');
                $(this).addClass('selected');

                // Trigger live preview update
                AethosAdmin.updateLivePreview();
            });
        },

        initLivePreview: function () {
            // Initial preview update
            this.updateLivePreview();
        },

        updateLivePreview: function () {
            // Get values
            const primaryColor = $('#aethos_primary_color').val() || '#4F46E5';
            const userTextColor = $('#aethos_user_text_color').val() || '#FFFFFF';
            const aiBubbleColor = $('#aethos_ai_bubble_color').val() || '#F3F4F6';
            const headerTitle = $('#aethos_header_title').val() || 'Aethos AI Assistant';
            const greetingMessage = $('#aethos_greeting_message').val() || 'Welcome! How can I help you today?';
            const chatIcon = $('#aethos_chat_icon').val();
            const fontFamily = $('#aethos_font_family').val();
            const fontSize = $('#aethos_font_size').val();

            // Update styles
            $('.aethos-preview-header').css('background-color', primaryColor);
            $('.aethos-preview-title-wrapper h3').text(headerTitle);

            // Bot Message
            $('.aethos-preview-message.bot .aethos-preview-message-content').first().text(greetingMessage);
            $('.aethos-preview-message.bot .aethos-preview-message-content').css('background-color', aiBubbleColor);

            // User Message
            $('.aethos-preview-message.user .aethos-preview-message-content').css('background-color', primaryColor);
            $('.aethos-preview-message.user .aethos-preview-message-content').css('color', userTextColor);

            // Icon
            if (chatIcon) {
                $('.aethos-preview-avatar img, .aethos-preview-message-avatar img').attr('src', chatIcon);
            }

            // Typography
            $('.aethos-preview-widget').css({
                'font-family': fontFamily,
                'font-size': fontSize + 'px'
            });

            // Update loading spinner color if present
            const styleSheet = document.createElement("style");
            styleSheet.innerText = `
                .aethos-loading::after { border-color: ${primaryColor}; border-top-color: transparent; }
                .aethos-preview-bubble:hover { transform: scale(1.1); }
                .aethos-preview-bubble { background-color: ${primaryColor}; }
            `;
            document.head.appendChild(styleSheet);
        },

        testConnection: function (e) {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const apiKey = $('#aethos_api_key').val();

            if (!apiKey) {
                this.showAlert('error', 'Please enter an API key first.');
                return;
            }

            const originalText = $button.html();
            $button.prop('disabled', true);
            $button.addClass('aethos-loading');
            $button.html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Testing...');

            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_test_connection',
                    nonce: aethosAdmin.nonce,
                    api_key: apiKey
                },
                success: (response) => {
                    if (response.success) {
                        this.showAlert('success', 'Connection successful! Your API key is valid.');
                    } else {
                        this.showAlert('error', response.data.message || 'Connection failed. Please check your API key.');
                    }
                },
                error: () => {
                    this.showAlert('error', 'Network error. Please try again.');
                },
                complete: () => {
                    $button.prop('disabled', false);
                    $button.removeClass('aethos-loading');
                    $button.html(originalText);
                }
            });
        },

        connectAndSave: function (e) {
            e.preventDefault();

            const $button = $(e.currentTarget);
            const apiKey = $('#aethos_api_key').val().trim();

            if (!apiKey || apiKey.trim() === '') {
                this.showAlert('error', 'Please enter your API Key first.');
                return;
            }

            // Show loading state with spinner
            const originalText = $button.html();
            $button.prop('disabled', true);
            $button.addClass('aethos-loading');
            $button.html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Connecting...');

            // Test connection - this will also save the settings if successful
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_test_connection',
                    nonce: aethosAdmin.nonce,
                    api_key: apiKey
                },
                success: (response) => {
                    if (response.success) {
                        this.showAlert('success', 'Connection successful! Reloading...');
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        $button.prop('disabled', false);
                        $button.removeClass('aethos-loading');
                        $button.html(originalText);
                        this.showAlert('error', response.data.message || 'The API Key provided is invalid. Please check and try again.');
                    }
                },
                error: () => {
                    $button.prop('disabled', false);
                    $button.removeClass('aethos-loading');
                    $button.html(originalText);
                    this.showAlert('error', 'Network error. Please try again.');
                }
            });
        },

        disconnect: function (e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to disconnect? This will deactivate Aethos AI Chat.')) {
                return;
            }

            const $button = $(e.currentTarget);
            $button.prop('disabled', true);
            $button.text('Disconnecting...');

            // Call WordPress backend to handle disconnect
            $.ajax({
                url: aethosAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_disconnect',
                    nonce: aethosAdmin.nonce
                },
                success: (response) => {
                    if (response.success) {
                        window.location.reload(true);
                    } else {
                        alert('Failed to disconnect: ' + (response.data.message || 'Unknown error'));
                        $button.prop('disabled', false);
                        $button.text('Disconnect');
                    }
                },
                error: () => {
                    alert('Network error. Please try again.');
                    $button.prop('disabled', false);
                    $button.text('Disconnect');
                }
            });
        },

        saveSettings: function (e) {
            // Let WordPress handle the form submission
            const $submitButton = $(e.target).find('input[type="submit"]');
            $submitButton.val('Saving...');
        },

        handleToggleChange: function (e) {
            const $toggle = $(e.currentTarget);
            const isChecked = $toggle.is(':checked');
            const settingName = $toggle.attr('name');

            // Update live preview
            this.updateLivePreview();
        },

        showAlert: function (type, message) {
            const alertClass = 'aethos-alert-' + type;
            const iconClass = type === 'success' ? 'dashicons-yes-alt' : 'dashicons-warning';

            const $alert = $(`
                <div class="aethos-alert ${alertClass}">
                    <span class="aethos-alert-icon"><span class="dashicons ${iconClass}"></span></span>
                    <div class="aethos-alert-content">
                        <p class="aethos-alert-message">${message}</p>
                    </div>
                </div>
            `);

            // Remove existing alerts
            $('.aethos-alert').remove();

            // Add new alert at the top of the active section
            $('.aethos-content-section.active').prepend($alert);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                $alert.fadeOut(() => $alert.remove());
            }, 5000);
        }
    };

    // Initialize when document is ready
    $(document).ready(function () {
        AethosAdmin.init();
    });

})(jQuery);
