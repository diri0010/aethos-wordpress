/**
 * Aethos Chat Widget - Enhanced Version
 * Phase 3: Modern features with typing indicators, message history, and animations
 */

(function () {
    'use strict';

    class AethosWidget {
        constructor() {
            this.widget = document.getElementById('aethos-chat-widget');
            this.toggleBtn = document.getElementById('aethos-toggle-btn');
            this.closeBtn = document.getElementById('aethos-close-btn');
            this.sendBtn = document.getElementById('aethos-send-btn');
            this.input = document.getElementById('aethos-input');
            this.messagesContainer = document.getElementById('aethos-messages');

            this.isOpen = false;
            this.isTyping = false;
            this.messageHistory = [];

            this.init();
        }

        init() {
            this.loadHistory();
            this.bindEvents();
            this.applyCustomColors();
            this.showGreeting();
            this.checkAutoOpen();
        }

        bindEvents() {
            this.toggleBtn.addEventListener('click', () => this.toggle());
            this.closeBtn.addEventListener('click', () => this.close());
            this.sendBtn.addEventListener('click', () => this.sendMessage());
            this.input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            // Escape key to close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                }
            });
        }

        toggle() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        }

        open() {
            this.widget.classList.remove('aethos-widget-closed');
            this.widget.classList.add('aethos-widget-open');
            this.isOpen = true;
            this.input.focus();
            this.scrollToBottom();
        }

        close() {
            this.widget.classList.remove('aethos-widget-open');
            this.widget.classList.add('aethos-widget-closed');
            this.isOpen = false;
        }

        applyCustomColors() {
            const primaryColor = aethosData.primaryColor || '#0052CC';
            const accentColor = aethosData.accentColor || '#33C2E3';

            document.documentElement.style.setProperty('--aethos-primary', primaryColor);
            document.documentElement.style.setProperty('--aethos-accent', accentColor);
        }

        showGreeting() {
            if (this.messageHistory.length === 0) {
                const greeting = aethosData.greetingMessage || 'Hello! How can I help you today?';
                this.addMessage(greeting, 'bot', false);
            }
        }

        checkAutoOpen() {
            if (aethosData.autoOpen) {
                const delay = (aethosData.autoOpenDelay || 3) * 1000;
                setTimeout(() => this.open(), delay);
            }
        }

        async sendMessage() {
            const text = this.input.value.trim();
            if (!text || this.isTyping) return;

            // Add user message
            this.addMessage(text, 'user');
            this.input.value = '';

            // Show typing indicator
            this.showTypingIndicator();

            try {
                const response = await fetch(aethosData.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        message: text,
                        apiKey: aethosData.apiKey
                    })
                });

                if (!response.ok) {
                    throw new Error('API request failed');
                }

                const data = await response.json();

                // Remove typing indicator
                this.hideTypingIndicator();

                // Add bot response
                const botMessage = data.message || "Sorry, I couldn't understand that.";
                this.addMessage(botMessage, 'bot');

            } catch (error) {
                console.error('Aethos Chat Error:', error);
                this.hideTypingIndicator();

                const errorMessage = aethosData.offlineMessage ||
                    "I'm having trouble connecting right now. Please try again later.";
                this.addMessage(errorMessage, 'bot error');
            }
        }

        addMessage(text, sender, save = true) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `aethos-message aethos-message-${sender}`;

            // Avatar
            const avatarDiv = document.createElement('div');
            avatarDiv.className = 'aethos-message-avatar';

            if (sender === 'user') {
                // Default user icon (SVG)
                avatarDiv.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="12" fill="#E5E7EB"/>
                        <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="#9CA3AF"/>
                        <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="#9CA3AF"/>
                    </svg>
                `;
            } else {
                // Bot icon
                if (aethosData.chatIcon) {
                    const img = document.createElement('img');
                    img.src = aethosData.chatIcon;
                    img.alt = 'Bot';
                    avatarDiv.appendChild(img);
                } else {
                    // Default bot icon (SVG)
                    avatarDiv.innerHTML = `
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="12" fill="var(--aethos-primary)"/>
                            <path d="M12 6C13.1 6 14 6.9 14 8V14C14 15.1 13.1 16 12 16C10.9 16 10 15.1 10 14V8C10 6.9 10.9 6 12 6ZM7 9C7.55 9 8 9.45 8 10V13C8 13.55 7.55 14 7 14C6.45 14 6 13.55 6 13V10C6 9.45 6.45 9 7 9ZM17 9C17.55 9 18 9.45 18 10V13C18 13.55 17.55 14 17 14C16.45 14 16 13.55 16 13V10C16 9.45 16.45 9 17 9Z" fill="white"/>
                        </svg>
                    `;
                }
            }

            const contentWrapper = document.createElement('div');
            contentWrapper.className = 'aethos-message-wrapper';

            const contentDiv = document.createElement('div');
            contentDiv.className = 'aethos-message-content';
            contentDiv.textContent = text;

            const timeDiv = document.createElement('div');
            timeDiv.className = 'aethos-message-time';
            timeDiv.textContent = this.formatTime(new Date());

            contentWrapper.appendChild(contentDiv);
            contentWrapper.appendChild(timeDiv);

            if (sender === 'user') {
                messageDiv.appendChild(contentWrapper);
                messageDiv.appendChild(avatarDiv);
            } else {
                messageDiv.appendChild(avatarDiv);
                messageDiv.appendChild(contentWrapper);
            }

            // Add animation
            messageDiv.style.opacity = '0';
            messageDiv.style.transform = 'translateY(10px)';

            this.messagesContainer.appendChild(messageDiv);

            // Trigger animation
            requestAnimationFrame(() => {
                messageDiv.style.transition = 'all 0.3s ease';
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateY(0)';
            });

            this.scrollToBottom();

            // Save to history
            if (save) {
                this.messageHistory.push({
                    text,
                    sender,
                    timestamp: Date.now()
                });
                this.saveHistory();
            }
        }

        showTypingIndicator() {
            this.isTyping = true;

            const typingDiv = document.createElement('div');
            typingDiv.className = 'aethos-typing-indicator';
            typingDiv.id = 'aethos-typing';
            typingDiv.innerHTML = `
                <span></span>
                <span></span>
                <span></span>
            `;

            this.messagesContainer.appendChild(typingDiv);
            this.scrollToBottom();
        }

        hideTypingIndicator() {
            this.isTyping = false;
            const typingDiv = document.getElementById('aethos-typing');
            if (typingDiv) {
                typingDiv.remove();
            }
        }

        scrollToBottom() {
            setTimeout(() => {
                this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
            }, 100);
        }

        loadHistory() {
            try {
                const saved = localStorage.getItem('aethos_chat_history');
                if (saved) {
                    this.messageHistory = JSON.parse(saved);

                    // Only show last 10 messages
                    const recentMessages = this.messageHistory.slice(-10);
                    recentMessages.forEach(msg => {
                        this.addMessage(msg.text, msg.sender, false);
                    });
                }
            } catch (error) {
                console.error('Error loading chat history:', error);
            }
        }

        saveHistory() {
            try {
                // Keep only last 50 messages
                if (this.messageHistory.length > 50) {
                    this.messageHistory = this.messageHistory.slice(-50);
                }
                localStorage.setItem('aethos_chat_history', JSON.stringify(this.messageHistory));
            } catch (error) {
                console.error('Error saving chat history:', error);
            }
        }

        clearHistory() {
            this.messageHistory = [];
            localStorage.removeItem('aethos_chat_history');
            this.messagesContainer.innerHTML = '';
            this.showGreeting();
        }

        formatTime(date) {
            const hours = date.getHours();
            const minutes = date.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = hours % 12 || 12;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
            return `${formattedHours}:${formattedMinutes} ${ampm}`;
        }
    }

    // Initialize widget when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.aethosWidget = new AethosWidget();
        });
    } else {
        window.aethosWidget = new AethosWidget();
    }

})();
