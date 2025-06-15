import './bootstrap';

import Alpine from 'alpinejs';

window.cleanNumberString = function (input) {
    // Regular expression to match a valid number string (including leading zeros)
    const numberRegex = /^0*[0-9]+$/;

    // Check if input is a valid number string
    if (typeof input === 'string' && numberRegex.test(input)) {
        // Remove leading zeros using replace with regex
        const cleaned = input.replace(/^0+/, '');
        // Handle case where input is all zeros (e.g., "0000")
        return cleaned === '' ? '0' : cleaned;
    } else {
        return null; // or return "Invalid input" if you prefer
    }
}

// Global Chat Controller
window.ChatController = {
    component: null,

    openChat() {
        if (this.component) {
            this.component.openChat();
        }
    },

    openChatWithSeller(sellerId) {
        console.log('ChatController.openChatWithSeller called with:', sellerId);
        if (this.component) {
            this.component.openChatWithSeller(sellerId);
        } else {
            console.error('Chat component not initialized');
        }
    }
};

// Register Alpine data for chat panel
Alpine.data('chatPanel', () => ({
    isOpen: false,
    conversations: [],
    sellers: [],
    activeConversation: null,
    messages: [],
    newMessage: '',
    showSellersList: false,
    pollingInterval: null,

    init() {
        // Register this component with the global controller
        window.ChatController.component = this;
        console.log('Chat panel component initialized');

        // Listen for direct chat requests from product pages
        window.addEventListener('open-chat-with-seller', (event) => {
            console.log('Received open-chat-with-seller event:', event.detail);
            this.openChatWithSeller(event.detail.sellerId);
        });
    },

    openChat() {
        this.isOpen = true;
        this.loadConversations();
        this.loadSellers();
        this.startPolling();
    },

    async openChatWithSeller(sellerId) {
        console.log('openChatWithSeller called with sellerId:', sellerId);
        this.isOpen = true;
        console.log('Chat panel opened, isOpen:', this.isOpen);
        this.loadConversations();
        this.loadSellers();
        this.startPolling();

        // Directly start conversation with the specified seller
        console.log('Starting conversation with seller:', sellerId);
        await this.startConversation(sellerId);
    },

    async loadConversations() {
        try {
            const response = await fetch('/chat/conversations', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            this.conversations = await response.json();
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    },

    async loadSellers() {
        try {
            const response = await fetch('/chat/sellers', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            this.sellers = await response.json();
        } catch (error) {
            console.error('Error loading sellers:', error);
        }
    },

    async startConversation(sellerId) {
        try {
            const response = await fetch('/chat/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ seller_id: sellerId })
            });

            const data = await response.json();
            this.activeConversation = { id: data.conversation_id, seller: data.seller };
            this.showSellersList = false;
            this.loadMessages(data.conversation_id);
        } catch (error) {
            console.error('Error starting conversation:', error);
        }
    },

    selectConversation(conversation) {
        this.activeConversation = conversation;
        this.loadMessages(conversation.id);
    },

    async loadMessages(conversationId) {
        try {
            const response = await fetch(`/chat/messages/${conversationId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            this.messages = await response.json();
            this.$nextTick(() => {
                this.scrollToBottom();
            });
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    },

    async sendMessage() {
        if (!this.newMessage.trim() || !this.activeConversation) return;

        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    conversation_id: this.activeConversation.id,
                    content: this.newMessage
                })
            });

            const message = await response.json();
            this.messages.push(message);
            this.newMessage = '';
            this.$nextTick(() => {
                this.scrollToBottom();
            });
        } catch (error) {
            console.error('Error sending message:', error);
        }
    },

    backToConversations() {
        this.activeConversation = null;
        this.messages = [];
        this.loadConversations();
    },

    getConversationName(conversation) {
        return conversation.seller?.business_name || conversation.user?.name || 'Unknown';
    },

    formatTime(timestamp) {
        return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    },

    scrollToBottom() {
        if (this.$refs.messagesContainer) {
            this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
        }
    },

    startPolling() {
        if (this.pollingInterval) return;

        this.pollingInterval = setInterval(() => {
            if (this.activeConversation) {
                this.loadMessages(this.activeConversation.id);
            }
        }, 5000);
    },

    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }
}));

window.Alpine = Alpine;
Alpine.start();
