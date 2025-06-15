<div x-cloak x-data="chatPanel" x-show="isOpen" x-transition class="fixed bottom-20 right-4 w-96 h-[500px] bg-white border border-gray-300 rounded-lg shadow-xl z-50">
    <!-- Chat Header -->
    <div class="flex items-center justify-between p-3 bg-primary text-white rounded-t-lg">
        <h3 class="font-semibold">Chat</h3>
        <button @click="isOpen = false" class="text-white hover:text-gray-200 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Chat Content -->
    <div class="flex h-[440px]">
        <!-- Conversations List -->
        <div x-show="!activeConversation" class="w-full p-3 overflow-y-auto">
            <div class="mb-3">
                <button @click="showSellersList = true" class="btn btn-primary btn-sm w-full">
                    Start New Chat
                </button>
            </div>

            <div x-show="conversations.length === 0" class="text-center text-gray-500 mt-8">
                No conversations yet
            </div>

            <template x-for="conversation in conversations" :key="conversation.id">
                <div @click="selectConversation(conversation)"
                     class="p-3 border-b cursor-pointer hover:bg-gray-50 rounded-md transition-colors">
                    <div class="font-medium text-sm mb-1" x-text="getConversationName(conversation)"></div>
                    <div class="text-xs text-gray-500 truncate" x-text="conversation.latest_message?.content || 'No messages'"></div>
                </div>
            </template>
        </div>

        <!-- Sellers List -->
        <div x-show="showSellersList" class="w-full p-3 overflow-y-auto">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium">Select Seller</h4>
                <button @click="showSellersList = false" class="text-gray-500 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <template x-for="seller in sellers" :key="seller.id">
                <div @click="startConversation(seller.id)"
                     class="p-3 border-b cursor-pointer hover:bg-gray-50 flex items-center rounded-md transition-colors">
                    <img :src="seller.logo_url || '/imgs/user-icon.png'" :alt="seller.business_name" class="w-10 h-10 rounded-full mr-3 object-cover">
                    <span class="text-sm font-medium" x-text="seller.business_name"></span>
                </div>
            </template>
        </div>

        <!-- Chat Messages -->
        <div x-show="activeConversation" class="w-full flex flex-col">
            <!-- Chat Header -->
            <div class="p-2 border-b bg-gray-50 flex items-center">
                <button @click="backToConversations()" class="mr-2 text-gray-500 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <span class="font-medium text-sm" x-text="activeConversation?.seller?.business_name || activeConversation?.user?.name"></span>
            </div>

            <!-- Messages -->
            <div class="flex-1 p-3 overflow-y-auto bg-gray-50" x-ref="messagesContainer">
                <template x-for="message in messages" :key="message.id">
                    <div class="mb-3" :class="message.is_own ? 'text-right' : 'text-left'">
                        <div class="inline-block max-w-[75%] p-3 rounded-lg text-sm shadow-sm"
                             :class="message.is_own ? 'bg-primary text-white rounded-br-sm' : 'bg-white text-gray-800 rounded-bl-sm border'">
                            <div x-text="message.content" class="break-words"></div>
                            <div class="text-xs opacity-75 mt-1" x-text="formatTime(message.created_at)"></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Message Input -->
            <div class="p-3 border-t bg-white">
                <form @submit.prevent="sendMessage()" class="flex gap-2">
                    <input x-model="newMessage"
                           type="text"
                           placeholder="Type a message..."
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <button type="submit"
                            :disabled="!newMessage.trim()"
                            class="px-4 py-2 bg-primary text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-primary-focus transition-colors">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
