/**
 * St. Lawrence School AI Assistant - Frontend JavaScript
 * Handles chat widget interactions and API communication
 */

class StLawrenceChatbot {
    constructor() {
        this.apiUrl = '../backend/api/chatbot/chat.php';
        this.isOpen = false;
        this.isTyping = false;
        this.currentTab = 'chat';
        
        // Voice properties
        this.recognition = null;
        this.synthesis = window.speechSynthesis;
        this.isListening = false;
        this.isSpeaking = false;
        this.isMuted = false;
        this.callStartTime = null;
        this.timerInterval = null;
        this.selectedVoice = null;
        this.lastTranscript = ''; // Move to instance variable to persist across restarts
        this.lastProcessedTime = 0; // Track when we last processed input
        this.shouldStopSpeaking = false; // Flag to stop chunk processing immediately
        
        this.init();
    }
    
    init() {
        this.createChatWidget();
        this.attachEventListeners();
        this.initializeChat();
        this.initializeVoice();
    }
    
    createChatWidget() {
        const chatHTML = `
            <!-- Chat Button -->
            <div class="chat-button" id="chatButton">
                <img src="../img/5.jpg" alt="St. Lawrence Assistant">
            </div>
            
            <!-- Chat Widget -->
            <div class="chat-widget" id="chatWidget">
                <!-- Header -->
                <div class="chat-header">
                    <div class="chat-header-left">
                        <div class="chat-logo">
                            <img src="../img/5.jpg" alt="St. Lawrence">
                        </div>
                        <div class="chat-title">
                            <h3>St Lawrence Assistant</h3>
                            <span>Online • Ready to help</span>
                        </div>
                    </div>
                    <button class="chat-close" id="chatClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Tabs -->
                <div class="chat-tabs">
                    <button class="chat-tab active" data-tab="chat" id="chatTab" onclick="if(window.chatbot) window.chatbot.switchTab('chat')">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                    </button>
                    <button class="chat-tab" data-tab="voice" id="voiceTab" onclick="if(window.chatbot) window.chatbot.switchTab('voice')">
                        <i class="fas fa-microphone"></i>
                        <span>Voice</span>
                    </button>
                </div>
                
                <!-- Chat Tab Content -->
                <div class="tab-content active" id="chatContent">
                    <div class="chat-body" id="chatBody">
                        <!-- Messages will be inserted here -->
                    </div>
                    
                    <div class="chat-footer">
                        <input 
                            type="text" 
                            class="chat-input" 
                            id="chatInput" 
                            placeholder="Type your message..."
                            autocomplete="off"
                        >
                        <button class="chat-send" id="chatSend">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Voice Tab Content -->
                <div class="tab-content" id="voiceContent">
                    <div class="voice-call-container" id="voiceCallContainer">
                        <div class="voice-avatar-container">
                            <div class="voice-avatar-ring ring-3"></div>
                            <div class="voice-avatar-ring ring-2"></div>
                            <div class="voice-avatar-ring"></div>
                            <div class="voice-avatar" id="voiceAvatar">
                                <img src="../img/5.jpg" alt="St. Lawrence AI">
                            </div>
                        </div>
                        
                        <div class="voice-ai-name">
                            <h2>St. Lawrence AI <span class="ai-badge">AI</span></h2>
                        </div>
                        <p class="voice-ai-title">Education Advisor</p>
                        
                        <div class="voice-status">
                            <div class="voice-status-item">
                                <div class="voice-status-icon" id="statusIconContainer">
                                    <i class="fas fa-microphone" id="statusIcon"></i>
                                </div>
                                <div class="audio-bars" id="audioBars" style="display: none;">
                                    <span class="audio-bar"></span>
                                    <span class="audio-bar"></span>
                                    <span class="audio-bar"></span>
                                    <span class="audio-bar"></span>
                                    <span class="audio-bar"></span>
                                </div>
                                <span class="voice-status-text" id="voiceStatus">Ready</span>
                            </div>
                            <div class="voice-status-item">
                                <span class="voice-timer" id="voiceTimer">00:00</span>
                            </div>
                        </div>
                        
                        <div class="voice-controls">
                            <div style="text-align: center;">
                                <button class="voice-control-btn mute-btn" id="muteBtn">
                                    <i class="fas fa-microphone"></i>
                                </button>
                                <div class="voice-control-label">Mute</div>
                            </div>
                            <div style="text-align: center;">
                                <button class="voice-control-btn end-call-btn" id="endCallBtn">
                                    <i class="fas fa-phone-slash"></i>
                                </button>
                                <div class="voice-control-label">End Call</div>
                            </div>
                        </div>
                        
                        <div class="voice-transcript">
                            <div class="transcript-label">Transcript</div>
                            <div class="transcript-text" id="transcriptText">Say something to start...</div>
                        </div>
                    </div>
                </div>
                
                <!-- Listening Indicator -->
                <div class="listening-indicator" id="listeningIndicator">
                    <span class="listening-pulse"></span>
                    <span>Listening...</span>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', chatHTML);
    }
    
    attachEventListeners() {
        // Use setTimeout to ensure DOM elements are ready
        setTimeout(() => {
            const chatButton = document.getElementById('chatButton');
            const chatClose = document.getElementById('chatClose');
            const chatSend = document.getElementById('chatSend');
            const chatInput = document.getElementById('chatInput');
            
            // Tab switching
            const chatTab = document.getElementById('chatTab');
            const voiceTab = document.getElementById('voiceTab');
            
            // Voice controls
            const muteBtn = document.getElementById('muteBtn');
            const endCallBtn = document.getElementById('endCallBtn');
            
            if (chatButton) chatButton.addEventListener('click', () => this.toggleChat());
            if (chatClose) chatClose.addEventListener('click', () => this.closeChat());
            if (chatSend) chatSend.addEventListener('click', () => this.sendMessage());
            
            if (chatInput) {
                chatInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.sendMessage();
                    }
                });
            }
            
            if (chatTab) chatTab.addEventListener('click', () => this.switchTab('chat'));
            if (voiceTab) voiceTab.addEventListener('click', () => this.switchTab('voice'));
            
            if (muteBtn) muteBtn.addEventListener('click', () => this.toggleMute());
            if (endCallBtn) endCallBtn.addEventListener('click', () => this.endVoiceCall());
        }, 100);
    }
    
    toggleChat() {
        const chatWidget = document.getElementById('chatWidget');
        const chatButton = document.getElementById('chatButton');
        
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            chatWidget.classList.add('active');
            chatButton.classList.add('active');
            document.getElementById('chatInput').focus();
        } else {
            chatWidget.classList.remove('active');
            chatButton.classList.remove('active');
        }
    }
    
    closeChat() {
        const chatWidget = document.getElementById('chatWidget');
        const chatButton = document.getElementById('chatButton');
        
        this.isOpen = false;
        chatWidget.classList.remove('active');
        chatButton.classList.remove('active');
    }
    
    async initializeChat() {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'init' })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.addBotMessage(data.message);
                this.addQuickActions(data.quickActions);
            }
        } catch (error) {
            console.error('Error initializing chat:', error);
            this.addBotMessage("Hello! 👋 I'm your St. Lawrence Junior School assistant. How can I help you today?");
            this.addDefaultQuickActions();
        }
    }
    
    addBotMessage(message, showAvatar = true) {
        const chatBody = document.getElementById('chatBody');
        const timestamp = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        
        const messageHTML = `
            <div class="chat-message bot">
                ${showAvatar ? `
                <div class="message-avatar">
                    <img src="../img/5.jpg" alt="Bot">
                </div>
                ` : '<div style="width: 35px;"></div>'}
                <div>
                    <div class="message-content">${this.formatMessage(message)}</div>
                    <div class="message-time">${timestamp}</div>
                </div>
            </div>
        `;
        
        chatBody.insertAdjacentHTML('beforeend', messageHTML);
        this.scrollToBottom();
    }
    
    addUserMessage(message) {
        const chatBody = document.getElementById('chatBody');
        const timestamp = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        
        const messageHTML = `
            <div class="chat-message user">
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="message-content">${this.escapeHtml(message)}</div>
                    <div class="message-time">${timestamp}</div>
                </div>
            </div>
        `;
        
        chatBody.insertAdjacentHTML('beforeend', messageHTML);
        this.scrollToBottom();
    }
    
    addQuickActions(actions) {
        const chatBody = document.getElementById('chatBody');
        
        let actionsHTML = '<div class="quick-actions">';
        actions.forEach(action => {
            actionsHTML += `
                <button class="quick-action-btn" onclick="chatbot.handleQuickAction('${this.escapeHtml(action)}')">
                    ${this.escapeHtml(action)}
                </button>
            `;
        });
        actionsHTML += '</div>';
        
        chatBody.insertAdjacentHTML('beforeend', actionsHTML);
        this.scrollToBottom();
    }
    
    addDefaultQuickActions() {
        const defaultActions = [
            "What are your school hours?",
            "How do I apply for admission?",
            "What extracurricular activities do you offer?",
            "What is the school's contact information?",
            "What are the school fees?",
            "Do you offer boarding?"
        ];
        this.addQuickActions(defaultActions);
    }
    
    showTypingIndicator() {
        const chatBody = document.getElementById('chatBody');
        
        const typingHTML = `
            <div class="chat-message bot typing-message">
                <div class="message-avatar">
                    <img src="../img/5.jpg" alt="Bot">
                </div>
                <div class="typing-indicator active">
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        
        chatBody.insertAdjacentHTML('beforeend', typingHTML);
        this.scrollToBottom();
        this.isTyping = true;
    }
    
    hideTypingIndicator() {
        const typingMessage = document.querySelector('.typing-message');
        if (typingMessage) {
            typingMessage.remove();
        }
        this.isTyping = false;
    }
    
    async sendMessage() {
        const chatInput = document.getElementById('chatInput');
        const message = chatInput.value.trim();
        
        if (!message || this.isTyping) return;
        
        // Add user message
        this.addUserMessage(message);
        chatInput.value = '';
        
        // Show typing indicator
        this.showTypingIndicator();
        
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'chat',
                    message: message
                })
            });
            
            const data = await response.json();
            
            // Simulate typing delay
            setTimeout(() => {
                this.hideTypingIndicator();
                
                if (data.success) {
                    this.addBotMessage(data.response);
                } else {
                    this.addBotMessage("I'm sorry, I encountered an error. Please try again or contact us directly at +256 701 420 506.");
                }
            }, 1000);
            
        } catch (error) {
            console.error('Error sending message:', error);
            this.hideTypingIndicator();
            this.addBotMessage("I'm sorry, I'm having trouble connecting. Please try again or contact us directly at +256 701 420 506.");
        }
    }
    
    async handleQuickAction(question) {
        // Add user message
        this.addUserMessage(question);
        
        // Show typing indicator
        this.showTypingIndicator();
        
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'quickAction',
                    question: question
                })
            });
            
            const data = await response.json();
            
            // Simulate typing delay
            setTimeout(() => {
                this.hideTypingIndicator();
                
                if (data.success) {
                    this.addBotMessage(data.response);
                } else {
                    this.addBotMessage("I'm sorry, I encountered an error. Please try again.");
                }
            }, 1000);
            
        } catch (error) {
            console.error('Error handling quick action:', error);
            this.hideTypingIndicator();
            this.addBotMessage("I'm sorry, I'm having trouble connecting. Please try again.");
        }
    }
    
    formatMessage(message) {
        // Convert line breaks to <br>
        message = message.replace(/\n/g, '<br>');
        
        // Make bold text
        message = message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Make links clickable
        message = message.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
        
        return message;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    scrollToBottom() {
        const chatBody = document.getElementById('chatBody');
        setTimeout(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 100);
    }
    
    // ==================== VOICE METHODS ====================
    
    switchTab(tab) {
        console.log('Switching to tab:', tab); // Debug log
        this.currentTab = tab;
        
        // Update tab buttons
        document.querySelectorAll('.chat-tab').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeTab = document.querySelector(`[data-tab="${tab}"]`);
        if (activeTab) {
            activeTab.classList.add('active');
        }
        
        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        if (tab === 'chat') {
            const chatContent = document.getElementById('chatContent');
            if (chatContent) chatContent.classList.add('active');
            this.stopVoiceCall();
        } else if (tab === 'voice') {
            const voiceContent = document.getElementById('voiceContent');
            if (voiceContent) voiceContent.classList.add('active');
            this.startVoiceCall();
        }
    }
    
    initializeVoice() {
        // Check if browser supports speech recognition
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            
            this.recognition.continuous = true;
            this.recognition.interimResults = true; // Changed to true for better responsiveness
            this.recognition.lang = 'en-US';
            this.recognition.maxAlternatives = 1;
            
            // Add timeout handling
            let silenceTimer = null;
            
            this.recognition.onstart = () => {
                this.isListening = true;
                this.updateVoiceStatus('Listening...', 'listening');
                console.log('Voice recognition started');
            };
            
            this.recognition.onresult = (event) => {
                // CRITICAL: Ignore recognition results if AI is speaking
                if (this.isSpeaking) {
                    console.log('AI is speaking, ignoring recognition result');
                    return;
                }
                
                // Clear any existing silence timer
                if (silenceTimer) {
                    clearTimeout(silenceTimer);
                }
                
                // Get the latest transcript
                const current = event.resultIndex;
                const transcript = event.results[current][0].transcript;
                const isFinal = event.results[current].isFinal;
                
                console.log('Transcript:', transcript, 'Final:', isFinal);
                
                if (isFinal) {
                    // Only process if transcript is different and not empty
                    // Also check if enough time has passed since last processing (prevent duplicates)
                    const now = Date.now();
                    if (transcript.trim() && 
                        transcript !== this.lastTranscript && 
                        (now - this.lastProcessedTime) > 3000) { // 3 second cooldown
                        this.lastTranscript = transcript;
                        this.lastProcessedTime = now;
                        this.handleVoiceInput(transcript);
                    }
                } else {
                    // Show interim results
                    this.updateTranscript(`You: ${transcript}...`);
                    
                    // Set a timer to process if user stops speaking
                    silenceTimer = setTimeout(() => {
                        const now = Date.now();
                        if (transcript.trim() && 
                            transcript !== this.lastTranscript && 
                            !this.isSpeaking &&
                            (now - this.lastProcessedTime) > 3000) { // 3 second cooldown
                            this.lastTranscript = transcript;
                            this.lastProcessedTime = now;
                            this.handleVoiceInput(transcript);
                        }
                    }, 2000); // Wait 2 seconds of silence
                }
            };
            
            this.recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                
                if (event.error === 'no-speech') {
                    // Don't show error for no-speech, just continue listening
                    console.log('No speech detected, continuing...');
                } else if (event.error === 'aborted') {
                    console.log('Recognition aborted');
                } else if (event.error === 'network') {
                    this.updateVoiceStatus('Network error', 'error');
                    setTimeout(() => {
                        if (this.currentTab === 'voice' && !this.isMuted) {
                            this.updateVoiceStatus('Listening...', 'listening');
                        }
                    }, 2000);
                } else {
                    this.updateVoiceStatus('Error: ' + event.error, 'error');
                    setTimeout(() => {
                        if (this.currentTab === 'voice' && !this.isMuted) {
                            this.updateVoiceStatus('Listening...', 'listening');
                        }
                    }, 2000);
                }
            };
            
            this.recognition.onend = () => {
                console.log('Recognition ended');
                this.isListening = false;
                
                // ONLY restart if still in voice mode, not muted, and AI is NOT speaking
                // This prevents recognition from restarting while AI is speaking long responses
                if (this.currentTab === 'voice' && !this.isMuted && !this.isSpeaking) {
                    setTimeout(() => {
                        // Double-check AI is still not speaking before restarting
                        if (!this.isSpeaking) {
                            try {
                                this.recognition.start();
                                console.log('Recognition restarted');
                            } catch (e) {
                                console.log('Recognition restart failed:', e);
                                // Try again after a longer delay
                                setTimeout(() => {
                                    if (!this.isSpeaking) {
                                        try {
                                            this.recognition.start();
                                        } catch (e2) {
                                            console.log('Recognition restart failed again:', e2);
                                        }
                                    }
                                }, 1000);
                            }
                        }
                    }, 500);
                }
            };
        } else {
            console.warn('Speech recognition not supported in this browser');
        }
        
        // Load available voices
        if (this.synthesis) {
            this.synthesis.onvoiceschanged = () => {
                const voices = this.synthesis.getVoices();
                // Prefer female English voice
                this.selectedVoice = voices.find(voice => 
                    voice.lang.startsWith('en') && voice.name.includes('Female')
                ) || voices.find(voice => voice.lang.startsWith('en')) || voices[0];
                
                console.log('Selected voice:', this.selectedVoice ? this.selectedVoice.name : 'Default');
            };
        }
    }
    
    startVoiceCall() {
        if (!this.recognition) {
            alert('Voice recognition is not supported in your browser. Please use Chrome, Edge, or Safari.');
            this.switchTab('chat');
            return;
        }
        
        // Reset transcript tracking
        this.lastTranscript = '';
        this.lastProcessedTime = 0;
        
        // Reset stop flag when starting new call
        this.shouldStopSpeaking = false;
        
        // Start timer
        this.callStartTime = Date.now();
        this.timerInterval = setInterval(() => {
            this.updateTimer();
            
            // Check if call has exceeded 3 hours (10800 seconds)
            const elapsed = Math.floor((Date.now() - this.callStartTime) / 1000);
            if (elapsed >= 10800) { // 3 hours = 10800 seconds
                this.speak("Your call has reached the 3-hour limit. Thank you for using St. Lawrence Assistant. Goodbye!");
                setTimeout(() => {
                    this.stopVoiceCall();
                    this.switchTab('chat');
                }, 3000);
            }
        }, 1000);
        
        // Start listening
        this.isMuted = false;
        this.updateMuteButton();
        
        try {
            this.recognition.start();
            this.updateVoiceStatus('Listening...', 'listening');
            this.updateTranscript('I\'m ready to help! Ask me anything about St. Lawrence Junior School. For example: "What are your school fees?" or "Tell me about admission"');
        } catch (e) {
            console.error('Failed to start recognition:', e);
        }
        
        // Welcome message - REMOVED automatic speaking
        // User can start speaking immediately without waiting
        console.log('Voice call started - ready for user input');
    }
    
    stopVoiceCall() {
        console.log('STOPPING VOICE CALL');
        
        // CRITICAL: Set flag to stop chunk processing
        this.shouldStopSpeaking = true;
        this.isSpeaking = false;
        
        // Stop recognition
        if (this.recognition && this.isListening) {
            try {
                this.recognition.stop();
                this.recognition.abort(); // Force abort
            } catch (e) {
                console.log('Could not stop recognition:', e);
            }
        }
        
        // NUCLEAR OPTION: Multiple strategies to kill speech
        if (this.synthesis) {
            // Strategy 1: Pause and cancel
            try {
                this.synthesis.pause();
                this.synthesis.cancel();
            } catch (e) {
                console.log('Pause/cancel error:', e);
            }
            
            // Strategy 2: Cancel in loop
            for (let i = 0; i < 20; i++) {
                try {
                    this.synthesis.cancel();
                } catch (e) {}
            }
            
            // Strategy 3: Resume and cancel (clears paused queue)
            try {
                this.synthesis.resume();
                this.synthesis.cancel();
            } catch (e) {}
            
            // Strategy 4: Delayed cancels to catch stragglers
            const cancelDelays = [0, 5, 10, 20, 50, 100, 150, 200];
            cancelDelays.forEach(delay => {
                setTimeout(() => {
                    if (this.synthesis && this.shouldStopSpeaking) {
                        try {
                            this.synthesis.pause();
                            this.synthesis.cancel();
                            this.synthesis.cancel();
                        } catch (e) {}
                    }
                }, delay);
            });
        }
        
        // Clear timer
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
        
        // Reset all flags and tracking
        this.callStartTime = null;
        this.isListening = false;
        this.isSpeaking = false;
        this.lastTranscript = '';
        this.lastProcessedTime = 0;
        this.updateVoiceStatus('Ready', 'ready');
        this.updateTimer(true);
    }
    
    endVoiceCall() {
        console.log('END CALL CLICKED - STOPPING EVERYTHING');
        
        // CRITICAL: Set flag to stop all chunk processing
        this.shouldStopSpeaking = true;
        this.isSpeaking = false;
        
        // NUCLEAR OPTION: Pause and cancel multiple times
        if (this.synthesis) {
            // Pause first to stop current utterance
            this.synthesis.pause();
            this.synthesis.cancel();
            
            // Cancel multiple times immediately
            for (let i = 0; i < 10; i++) {
                this.synthesis.cancel();
            }
            
            // Resume and cancel again (clears paused queue)
            this.synthesis.resume();
            this.synthesis.cancel();
            
            // Final cancels with delays
            setTimeout(() => {
                if (this.synthesis) {
                    this.synthesis.pause();
                    this.synthesis.cancel();
                    this.synthesis.cancel();
                }
            }, 0);
            
            setTimeout(() => {
                if (this.synthesis) {
                    this.synthesis.cancel();
                }
            }, 10);
            
            setTimeout(() => {
                if (this.synthesis) {
                    this.synthesis.cancel();
                }
            }, 50);
        }
        
        // End call immediately without speaking
        this.stopVoiceCall();
        
        // Force switch to chat tab
        this.currentTab = 'chat';
        this.switchTab('chat');
    }
    
    toggleMute() {
        this.isMuted = !this.isMuted;
        this.updateMuteButton();
        
        if (this.isMuted) {
            if (this.recognition && this.isListening) {
                this.recognition.stop();
            }
            this.updateVoiceStatus('Muted', 'muted');
        } else {
            try {
                this.recognition.start();
                this.updateVoiceStatus('Listening...', 'listening');
            } catch (e) {
                console.log('Recognition restart failed:', e);
            }
        }
    }
    
    updateMuteButton() {
        const muteBtn = document.getElementById('muteBtn');
        const icon = muteBtn.querySelector('i');
        
        if (this.isMuted) {
            icon.className = 'fas fa-microphone-slash';
            muteBtn.classList.add('muted');
        } else {
            icon.className = 'fas fa-microphone';
            muteBtn.classList.remove('muted');
        }
    }
    
    updateVoiceStatus(text, status) {
        const statusText = document.getElementById('voiceStatus');
        const statusIcon = document.getElementById('statusIcon');
        const statusIconContainer = document.getElementById('statusIconContainer');
        const audioBars = document.getElementById('audioBars');
        
        statusText.textContent = text;
        
        // Update icon based on status
        if (status === 'listening') {
            statusIconContainer.style.display = 'flex';
            statusIcon.className = 'fas fa-microphone';
            statusIcon.style.color = '#0066cc';
            statusIcon.style.display = 'block';
            audioBars.style.display = 'none';
        } else if (status === 'speaking') {
            statusIconContainer.style.display = 'none';
            statusIcon.style.display = 'none';
            audioBars.style.display = 'flex';
        } else if (status === 'muted') {
            statusIconContainer.style.display = 'flex';
            statusIcon.className = 'fas fa-microphone-slash';
            statusIcon.style.color = '#dc3545';
            statusIcon.style.display = 'block';
            audioBars.style.display = 'none';
        } else if (status === 'error') {
            statusIconContainer.style.display = 'flex';
            statusIcon.className = 'fas fa-exclamation-circle';
            statusIcon.style.color = '#dc3545';
            statusIcon.style.display = 'block';
            audioBars.style.display = 'none';
        } else {
            statusIconContainer.style.display = 'flex';
            statusIcon.className = 'fas fa-check-circle';
            statusIcon.style.color = '#28a745';
            statusIcon.style.display = 'block';
            audioBars.style.display = 'none';
        }
    }
    
    updateTimer(reset = false) {
        const timerElement = document.getElementById('voiceTimer');
        
        if (reset) {
            timerElement.textContent = '00:00';
            return;
        }
        
        if (!this.callStartTime) return;
        
        const elapsed = Math.floor((Date.now() - this.callStartTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        
        timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }
    
    updateTranscript(text) {
        const transcriptText = document.getElementById('transcriptText');
        transcriptText.textContent = text;
    }
    
    async handleVoiceInput(transcript) {
        console.log('Voice input:', transcript);
        
        // CRITICAL: Ignore input if AI is currently speaking
        if (this.isSpeaking) {
            console.log('AI is speaking, ignoring voice input');
            return;
        }
        
        // Update transcript
        this.updateTranscript(`You: ${transcript}`);
        this.updateVoiceStatus('Processing...', 'processing');
        
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'chat',
                    message: transcript
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Clean response for speech (remove special characters and formatting)
                const cleanResponse = this.cleanTextForSpeech(data.response);
                
                // Update transcript
                this.updateTranscript(`AI: ${cleanResponse}`);
                
                // Speak the response
                this.speak(cleanResponse);
            } else {
                this.speak("I'm sorry, I encountered an error. Please try again.");
                this.updateVoiceStatus('Error', 'error');
            }
            
        } catch (error) {
            console.error('Error processing voice input:', error);
            this.speak("I'm sorry, I'm having trouble connecting. Please try again.");
            this.updateVoiceStatus('Error', 'error');
        }
    }
    
    speak(text) {
        // CRITICAL: Check if we should stop before even starting
        if (this.shouldStopSpeaking) {
            console.log('STOP FLAG ACTIVE - Not starting speech');
            return;
        }
        
        if (!this.synthesis) {
            console.error('Speech synthesis not supported');
            return;
        }
        
        // DON'T reset the stop flag here - only reset when call starts
        // this.shouldStopSpeaking = false; // REMOVED - was causing the bug!
        
        // Stop listening while AI speaks to prevent echo/feedback
        if (this.recognition && this.isListening) {
            try {
                this.recognition.stop();
                console.log('Stopped listening while AI speaks');
            } catch (e) {
                console.log('Could not stop recognition:', e);
            }
        }
        
        // IMPORTANT: Only cancel if we're starting a NEW conversation
        // Don't cancel if we're already speaking (prevents interruption)
        if (!this.isSpeaking) {
            this.synthesis.cancel();
        }
        
        // For very long text, split into chunks to prevent browser timeout
        const maxLength = 200; // Characters per chunk
        const chunks = [];
        
        if (text.length > maxLength) {
            // Split by sentences first
            const sentences = text.match(/[^.!?]+[.!?]+/g) || [text];
            let currentChunk = '';
            
            for (const sentence of sentences) {
                if ((currentChunk + sentence).length > maxLength && currentChunk.length > 0) {
                    chunks.push(currentChunk.trim());
                    currentChunk = sentence;
                } else {
                    currentChunk += sentence;
                }
            }
            if (currentChunk.trim()) {
                chunks.push(currentChunk.trim());
            }
        } else {
            chunks.push(text);
        }
        
        console.log(`Speaking ${chunks.length} chunk(s)`);
        
        // Speak all chunks sequentially
        let chunkIndex = 0;
        
        const speakChunk = () => {
            // CRITICAL: Check if we should stop speaking
            if (this.shouldStopSpeaking) {
                console.log('STOP FLAG DETECTED - Aborting chunk processing');
                this.isSpeaking = false;
                this.synthesis.cancel();
                return;
            }
            
            if (chunkIndex >= chunks.length) {
                // All chunks spoken
                this.isSpeaking = false;
                console.log('AI finished speaking all chunks');
                
                // Resume listening after AI finishes speaking
                if (!this.isMuted && this.currentTab === 'voice' && !this.shouldStopSpeaking) {
                    setTimeout(() => {
                        if (!this.isMuted && this.currentTab === 'voice' && !this.isSpeaking && !this.shouldStopSpeaking) {
                            try {
                                this.recognition.start();
                                this.updateVoiceStatus('Listening...', 'listening');
                                console.log('Resumed listening after AI spoke');
                            } catch (e) {
                                console.log('Could not restart recognition:', e);
                            }
                        }
                    }, 1500);
                }
                return;
            }
            
            const utterance = new SpeechSynthesisUtterance(chunks[chunkIndex]);
            
            // Set voice properties
            if (this.selectedVoice) {
                utterance.voice = this.selectedVoice;
            }
            utterance.rate = 0.95;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;
            
            utterance.onstart = () => {
                // Check stop flag even at utterance start
                if (this.shouldStopSpeaking) {
                    console.log('STOP FLAG - Cancelling at utterance start');
                    this.synthesis.cancel();
                    this.isSpeaking = false;
                    return;
                }
                
                if (chunkIndex === 0) {
                    this.isSpeaking = true;
                    this.updateVoiceStatus('Speaking...', 'speaking');
                    console.log('AI started speaking');
                }
                console.log(`Speaking chunk ${chunkIndex + 1}/${chunks.length}`);
            };
            
            utterance.onend = () => {
                // Check stop flag before continuing
                if (this.shouldStopSpeaking) {
                    console.log('STOP FLAG - Not continuing to next chunk');
                    this.isSpeaking = false;
                    return;
                }
                
                console.log(`Finished chunk ${chunkIndex + 1}/${chunks.length}`);
                chunkIndex++;
                
                // Small delay between chunks for natural flow
                setTimeout(() => {
                    speakChunk();
                }, 100);
            };
            
            utterance.onerror = (event) => {
                console.error('Speech synthesis error:', event);
                this.isSpeaking = false;
                this.updateVoiceStatus('Error', 'error');
                
                // Try to resume listening even after error
                if (!this.isMuted && this.currentTab === 'voice' && !this.shouldStopSpeaking) {
                    setTimeout(() => {
                        try {
                            this.recognition.start();
                            this.updateVoiceStatus('Listening...', 'listening');
                        } catch (e) {
                            console.log('Could not restart recognition after error:', e);
                        }
                    }, 1000);
                }
            };
            
            // Speak this chunk
            this.synthesis.speak(utterance);
        };
        
        // Start speaking first chunk
        speakChunk();
    }
    
    cleanTextForSpeech(text) {
        // Remove markdown formatting
        text = text.replace(/\*\*(.*?)\*\*/g, '$1'); // Remove bold
        text = text.replace(/\*(.*?)\*/g, '$1'); // Remove italic
        text = text.replace(/\[(.*?)\]\(.*?\)/g, '$1'); // Remove links
        
        // Remove emojis and special characters
        text = text.replace(/[📍📧📞📮🏫⏰📝🎒📚🎓💰🏠👔⚽🎨🔬🏃🍽️🚌🔒👨‍👩‍👧‍👦📅🏆♿✅❌]/g, '');
        
        // Replace bullet points
        text = text.replace(/•/g, '');
        text = text.replace(/✓/g, '');
        text = text.replace(/✔/g, '');
        
        // Clean up multiple spaces and newlines
        text = text.replace(/\n+/g, '. ');
        text = text.replace(/\s+/g, ' ');
        
        // Replace "UGX" with "Uganda Shillings"
        text = text.replace(/UGX/g, 'Uganda Shillings');
        
        // Replace numbers with commas for better pronunciation
        text = text.replace(/(\d),(\d)/g, '$1$2');
        
        return text.trim();
    }
}

// Initialize chatbot when DOM is ready
let chatbot;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        chatbot = new StLawrenceChatbot();
        window.chatbot = chatbot; // Make globally accessible
    });
} else {
    chatbot = new StLawrenceChatbot();
    window.chatbot = chatbot; // Make globally accessible
}
