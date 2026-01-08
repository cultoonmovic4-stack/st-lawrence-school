// AI Assistant for St. Lawrence Junior School
class SchoolAssistant {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.typing = false;
        this.quickQuestions = [
            "What are your school hours?",
            "How do I apply for admission?",
            "What extracurricular activities do you offer?",
            "What is the school's contact information?"
        ];
        
        // API endpoint
        this.apiUrl = 'api/assistant.php';
        
        this.init();
    }
    
    init() {
        this.createAssistantElement();
        this.setupEventListeners();
        this.addWelcomeMessage();
    }
    
    createAssistantElement() {
        // Create main container
        const container = document.createElement('div');
        container.className = 'ai-assistant-container';
        container.id = 'ai-assistant';
        
        // Create toggle button
        const toggleButton = document.createElement('button');
        toggleButton.className = 'assistant-toggle';
        toggleButton.innerHTML = '<i class="fas fa-comment-dots"></i>';
        toggleButton.setAttribute('aria-label', 'Chat with Assistant');
        
        // Create chat window
        const chatWindow = document.createElement('div');
        chatWindow.className = 'assistant-window';
        
        // Create header with school logo
        const header = document.createElement('div');
        header.className = 'assistant-header';
        header.innerHTML = `
            <h3>
                <img src="img/5.jpg" alt="St. Lawrence Junior School Logo" class="school-logo">
                St. Lawrence Assistant
            </h3>
            <button class="close-btn" aria-label="Close chat">&times;</button>
        `;
        
        // Create messages container
        const messagesContainer = document.createElement('div');
        messagesContainer.className = 'assistant-messages';
        messagesContainer.id = 'assistant-messages';
        
        // Create input area
        const inputArea = document.createElement('div');
        inputArea.className = 'assistant-input-area';
        inputArea.innerHTML = `
            <div class="quick-actions" id="quick-actions"></div>
            <div class="assistant-input-wrapper">
                <input type="text" class="assistant-input" id="user-input" placeholder="Type your message..." aria-label="Type your message">
                <button class="send-button" id="send-button" aria-label="Send message">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        `;
        
        // Assemble the chat window
        chatWindow.appendChild(header);
        chatWindow.appendChild(messagesContainer);
        chatWindow.appendChild(inputArea);
        
        // Add toggle button and chat window to container
        container.appendChild(toggleButton);
        container.appendChild(chatWindow);
        
        // Store references to important elements
        this.elements = {
            container,
            toggleButton,
            chatWindow,
            messagesContainer,
            input: inputArea.querySelector('.assistant-input'),
            sendButton: inputArea.querySelector('.send-button'),
            quickActions: inputArea.querySelector('.quick-actions'),
            closeButton: header.querySelector('.close-btn')
        };
        
        document.body.appendChild(container);
    }
    
    setupEventListeners() {
        const { toggleButton, input, sendButton, closeButton } = this.elements;
        
        // Toggle chat window
        toggleButton.addEventListener('click', () => this.toggleChat());
        
        // Send message on button click
        sendButton.addEventListener('click', () => this.handleSendMessage());
        
        // Send message on Enter key
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.handleSendMessage();
            }
        });
        
        // Close button
        closeButton.addEventListener('click', () => this.toggleChat(false));
        
        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && 
                !this.elements.container.contains(e.target) && 
                e.target !== this.elements.toggleButton) {
                this.toggleChat(false);
            }
        });
    }
    
    toggleChat(forceState) {
        this.isOpen = forceState !== undefined ? forceState : !this.isOpen;
        
        if (this.isOpen) {
            this.elements.chatWindow.classList.add('active');
            this.elements.toggleButton.classList.add('active');
            this.elements.input.focus();
            // Add a small delay to ensure the animation plays
            setTimeout(() => {
                this.scrollToBottom();
            }, 100);
        } else {
            this.elements.chatWindow.classList.remove('active');
            this.elements.toggleButton.classList.remove('active');
        }
    }
    
    addWelcomeMessage() {
        const welcomeMessage = `Hello! I'm your St. Lawrence Junior School assistant. How can I help you today?`;
        this.addMessage('assistant', welcomeMessage);
        this.showQuickActions();
    }
    
    showQuickActions() {
        const quickActionsContainer = this.elements.quickActions;
        quickActionsContainer.innerHTML = '';
        
        this.quickQuestions.forEach(question => {
            const button = document.createElement('button');
            button.className = 'quick-action';
            button.textContent = question;
            button.addEventListener('click', () => {
                this.elements.input.value = question;
                this.handleSendMessage();
            });
            quickActionsContainer.appendChild(button);
        });
    }
    
    handleSendMessage() {
        const input = this.elements.input;
        const message = input.value.trim();
        
        if (message === '' || this.typing) return;
        
        // Add user message to chat
        this.addMessage('user', message);
        input.value = '';
        
        // Process the message and get a response - typing indicator will be shown in processMessage
        this.processMessage(message);
    }
    
    showTypingIndicator() {
        this.typing = true;
        const typingId = 'typing-' + Date.now();
        const typingElement = document.createElement('div');
        typingElement.id = typingId;
        typingElement.className = 'message assistant typing';
        typingElement.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        `;
        
        this.elements.messagesContainer.appendChild(typingElement);
        this.scrollToBottom();
        
        return typingId;
    }
    
    removeTypingIndicator(typingId) {
        const element = document.getElementById(typingId);
        if (element) {
            element.remove();
        }
        this.typing = false;
    }
    
    async processMessage(message) {
        const typingId = this.showTypingIndicator();
        
        try {
            const response = await this.sendToAPI(message);
            
            if (response.status === 'success') {
                // Add assistant's response
                this.addMessage('assistant', response.response);
                
                // Update quick actions with suggestions if available
                if (response.suggestions && response.suggestions.length > 0) {
                    this.updateQuickActions(response.suggestions);
                } else {
                    this.showQuickActions(); // Show default quick actions
                }
                
                // Handle end of conversation if needed
                if (response.end_conversation) {
                    setTimeout(() => this.toggleChat(false), 2000);
                }
            } else {
                throw new Error(response.message || 'Unknown error occurred');
            }
        } catch (error) {
            console.error('Error processing message:', error);
            this.addMessage('assistant', 'Sorry, I encountered an error. Please try again later.');
            this.showQuickActions();
        } finally {
            // Always remove the typing indicator, whether there was an error or not
            this.removeTypingIndicator(typingId);
        }
    }
    
    async sendToAPI(message) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }
    
    updateQuickActions(suggestions) {
        const quickActionsContainer = this.elements.quickActions;
        quickActionsContainer.innerHTML = '';
        
        if (!suggestions || suggestions.length === 0) {
            this.showQuickActions(); // Fallback to default quick actions
            return;
        }
        
        suggestions.forEach(suggestion => {
            if (typeof suggestion === 'string') {
                const button = document.createElement('button');
                button.className = 'quick-action';
                button.textContent = suggestion;
                button.addEventListener('click', () => {
                    this.elements.input.value = suggestion;
                    this.handleSendMessage();
                });
                quickActionsContainer.appendChild(button);
            }
        });
        
        // Ensure quick actions are visible
        quickActionsContainer.style.display = 'flex';
    }
    
    addMessage(sender, text) {
        const messageElement = document.createElement('div');
        messageElement.className = `message ${sender}`;
        messageElement.textContent = text;
        
        this.elements.messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
        
        // Add to messages array for history
        this.messages.push({ sender, text });
        
        // Keep only the last 50 messages to prevent performance issues
        if (this.messages.length > 50) {
            this.messages.shift();
        }
    }
    
    scrollToBottom() {
        this.elements.messagesContainer.scrollTop = this.elements.messagesContainer.scrollHeight;
    }
}

// Initialize the assistant when the page loads
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize if not already initialized
    if (!window.schoolAssistant) {
        window.schoolAssistant = new SchoolAssistant();
    }
});
