<script setup>
import { ref, watch, nextTick, computed, onMounted, onUnmounted } from 'vue';
import { db } from '../../firebase';
import { collection, query, orderBy, onSnapshot, limit, doc, updateDoc } from 'firebase/firestore';
import axios from 'axios';
import ProfileLayout from '../../Layouts/ProfileLayout.vue';
import SidebarNav from '../../Components/SidebarNav.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    currentUser: {
        type: Object,
        required: true,
    },
    conversations: {
        type: Array,
        default: () => [],
    },
});

const localConversations = ref(props.conversations || []);
const loading = ref(false);
const messages = ref([]);
const newMessage = ref('');
const messagesContainer = ref(null);
const selectedConversation = ref(null);
let unsubscribe = null;
const conversationUnsubscribes = [];

const chatId = computed(() => {
    return selectedConversation.value?.chat_id || null;
});

const isSender = (senderId) => {
    const currentId = props.currentUser?.uid || props.currentUser?.id;
    return senderId == currentId;
};

const setupListener = () => {
    if (unsubscribe) {
        unsubscribe();
    }
    
    if (!chatId.value) {
        messages.value = [];
        return;
    }

    const messagesQuery = query(
        collection(db, 'chats', chatId.value, 'messages'),
        orderBy('timestamp')
    );

    unsubscribe = onSnapshot(messagesQuery, (snapshot) => {
        messages.value = snapshot.docs.map(doc => ({
            id: doc.id,
            ...doc.data(),
            timestamp: doc.data().timestamp?.toDate()
        }));

        nextTick(() => {
            if (messagesContainer.value) {
                messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
            }
        });
    });
};

const sendMessage = async () => {
    if (newMessage.value.trim() === '' || !chatId.value || !selectedConversation.value) return;

    try {
        await axios.post('/chat/send', {
            recipient_id: selectedConversation.value.otherUser.uid,
            message: newMessage.value.trim(),
            sender_id: props.currentUser?.id,
            read: false,
        });
        newMessage.value = '';
    } catch (error) {
        // console.error("Error sending message:", error);
    }
};

const selectConversation = (conversation) => {
    selectedConversation.value = conversation;

    if (conversation.chat_id && conversation.read === false && conversation.last_sender_id !== props.currentUser.uid) {
        updateDoc(doc(db, 'chats', conversation.chat_id), {
            read: true
        }).catch();
    }
};

const setupConversationsListeners = () => {
    conversationUnsubscribes.forEach(unsub => unsub());
    conversationUnsubscribes.length = 0;

    localConversations.value.forEach((conversation) => {
        if (!conversation.chat_id) return;

        const lastMessageQuery = query(
            collection(db, 'chats', conversation.chat_id, 'messages'),
            orderBy('timestamp', 'desc'),
            limit(1)
        );

        const unsub = onSnapshot(lastMessageQuery, (snapshot) => {
            if (!snapshot.empty) {
                const doc = snapshot.docs[0];
                const data = doc.data();
                const conv = localConversations.value.find(c => c.chat_id === conversation.chat_id);
                if (conv) {
                    conv.last_message = {
                        text: data.text,
                        timestamp: data.timestamp
                    };
                }
            }
        }, (error) => {
            // console.error("Błąd nasłuchiwania ostatniej wiadomości:", error);
        });
        conversationUnsubscribes.push(unsub);

        const chatDocRef = doc(db, 'chats', conversation.chat_id);
        const unsubDoc = onSnapshot(chatDocRef, (docSnap) => {
            if (docSnap.exists()) {
                const data = docSnap.data();
                const conv = localConversations.value.find(c => c.chat_id === conversation.chat_id);
                if (conv) {
                    conv.read = data.read;
                    conv.last_sender_id = data.sender_id;

                    if (selectedConversation.value?.chat_id === conv.chat_id && 
                        conv.read === false && 
                        conv.last_sender_id !== props.currentUser.uid) {
                        updateDoc(chatDocRef, { read: true })
                            .catch(error => console.error("Error marking as read:", error));
                    }
                }
            }
        }, (error) => {
            // console.error("Błąd nasłuchiwania dokumentu czatu (sprawdź reguły Firestore):", error);
        });
        conversationUnsubscribes.push(unsubDoc);
    });
};

watch(chatId, (newChatId) => {
    if (newChatId) {
        setupListener();
    } else {
        if (unsubscribe) {
            unsubscribe();
        }
        messages.value = [];
    }
}, { immediate: true });

watch(() => props.conversations, (newVal) => {
    localConversations.value = newVal || [];
    setupConversationsListeners();
});

onMounted(() => {
    loading.value = true;
    axios.get('/profile/communicator/conversations')
        .then(response => {
            localConversations.value = response.data;
            setupConversationsListeners();
            if (localConversations.value.length > 0) {
                selectConversation(localConversations.value[0]);
            }
        })
        .catch()
        .finally(() => loading.value = false);
});

onUnmounted(() => {
    if (unsubscribe) unsubscribe();
    conversationUnsubscribes.forEach(unsub => unsub());
});

const changeView = (viewName) => {
    if (viewName === 'komunikator') return;
    if (viewName === 'calendar') {
        router.visit('/profile/calendar');
    } else if (viewName === 'promowanie') {
        router.visit('/profile/promotion');
    } else if (viewName === 'profil') {
        router.visit('/profile');
    }
};

</script>

<template>
    <ProfileLayout>
        <div class="bg-gray-100 flex-1 flex flex-col">
            <div class="flex flex-col lg:flex-row flex-1">
                <SidebarNav :user="currentUser" activeView="komunikator" @change-view="changeView" />

                <main class="flex-1 md:p-4 lg:p-8">
                    <div class="w-full max-w-[1000px] mx-auto pb-12">
                        <!-- Communicator UI Start -->
                        <div class="flex flex-col md:flex-row h-[850px] bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                            <!-- Conversation List -->
                            <div class="w-full md:w-1/3 h-64 md:h-auto border-b md:border-b-0 md:border-r border-gray-100 bg-white flex flex-col shrink-0">
                                <div class="p-4 md:p-6 border-b border-gray-100 bg-white z-10">
                                    <h2 class="text-xl md:text-2xl font-bold text-[#241F20]">Wiadomości</h2>
                                    <p class="text-xs text-gray-400 mt-1 hidden md:block">Twoje ostatnie konwersacje</p>
                                </div>
                                
                                <div v-if="loading" class="flex-1 flex items-center justify-center text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="animate-spin h-6 w-6 text-[#F5F570]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-sm">Ładowanie...</span>
                                    </div>
                                </div>

                                <div v-else class="flex-1 overflow-y-auto custom-scrollbar">
                                    <ul class="space-y-1 p-3">
                                        <li v-for="conversation in localConversations" :key="conversation.chat_id"
                                            @click="selectConversation(conversation)"
                                            class="relative group flex items-center p-3.5 rounded-xl cursor-pointer transition-all duration-200"
                                            :class="[
                                                selectedConversation && selectedConversation.chat_id === conversation.chat_id 
                                                    ? 'bg-gray-50 ring-1 ring-gray-200' 
                                                    : 'hover:bg-gray-50',
                                                conversation.read === false && conversation.last_sender_id !== currentUser.uid && selectedConversation?.chat_id !== conversation.chat_id
                                                    ? 'bg-yellow-50/50'
                                                    : ''
                                            ]">
                                            
                                            <!-- Unread Indicator Dot -->
                                            <div v-if="conversation.read === false && conversation.last_sender_id !== currentUser.uid && selectedConversation?.chat_id !== conversation.chat_id" 
                                                 class="absolute left-1 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-red-500 rounded-full shadow-sm"></div>

                                            <div class="relative flex-shrink-0">
                                                <img :src="conversation.otherUser.imageURL || '/images/no_user.png'" 
                                                     class="w-12 h-12 rounded-full object-cover border border-gray-100 shadow-sm"
                                                     alt="User avatar">
                                            </div>

                                            <div class="ml-4 flex-1 min-w-0">
                                                <div class="flex justify-between items-baseline mb-0.5">
                                                    <span class="font-bold text-gray-800 truncate" :class="{'text-[#241F20]': selectedConversation?.chat_id === conversation.chat_id}">
                                                        {{ conversation.otherUser.name }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400 font-medium whitespace-nowrap ml-2">
                                                        {{ conversation.last_message?.timestamp ? (conversation.last_message.timestamp.seconds ? new Date(conversation.last_message.timestamp.seconds * 1000).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : new Date(conversation.last_message.timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})) : '' }}
                                                    </span>
                                                </div>
                                                <p class="text-sm truncate text-gray-500 group-hover:text-gray-600 transition-colors" 
                                                   :class="{'font-medium text-gray-900': conversation.read === false && conversation.last_sender_id !== currentUser.uid}">
                                                    {{ conversation.last_message?.text || 'Brak wiadomości' }}
                                                </p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Chat Window -->
                            <div class="w-full md:w-2/3 flex flex-col bg-[#FDFBF7] relative flex-1 overflow-hidden">
                                <template v-if="selectedConversation">
                                    <!-- Chat Header -->
                                    <div class="flex items-center px-6 py-4 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm z-20">
                                        <img :src="selectedConversation.otherUser.imageURL || '/images/no_user.png'" 
                                             class="w-11 h-11 rounded-full object-cover shadow-sm border-2 border-white ring-1 ring-gray-100 mr-4">
                                        <div>
                                             <h2 class="text-lg font-bold text-gray-800 leading-tight">
                                                {{ selectedConversation.otherUser.name }}
                                            </h2>
                                        </div>
                                    </div>

                                    <!-- Messages Area -->
                                    <div class="flex-1 p-6 overflow-y-auto custom-scrollbar scroll-smooth space-y-6" ref="messagesContainer">
                                        <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center space-y-4 opacity-60">
                                             <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                             </div>
                                            <p class="text-gray-500">To początek twojej historii z {{ selectedConversation.otherUser.name.split(' ')[0] }}.<br>Przywitaj się!</p>
                                        </div>

                                        <div v-for="(message, index) in messages" :key="message.id" class="group flex flex-col">
                                            <div :class="['flex items-end gap-2', isSender(message.sender_id) ? 'justify-end' : 'justify-start']">
                                                
                                                <!-- Avatar for receiver -->
                                                <img v-if="!isSender(message.sender_id)" 
                                                     :src="selectedConversation.otherUser.imageURL || '/images/no_user.png'" 
                                                     class="w-6 h-6 rounded-full object-cover mb-1 opacity-70">

                                                <!-- Message Bubble -->
                                                <div :class="[
                                                    'max-w-[75%] px-5 py-3 shadow-sm relative text-sm leading-relaxed transition-all duration-200',
                                                    isSender(message.sender_id) 
                                                        ? 'bg-[#241F20] text-white rounded-2xl rounded-tr-sm hover:shadow-md' 
                                                        : 'bg-white text-gray-800 rounded-2xl rounded-tl-sm border border-gray-100 hover:shadow-md'
                                                ]">
                                                    {{ message.text }}
                                                </div>
                                            </div>
                                            
                                            <!-- Timestamp -->
                                            <div :class="['text-[10px] text-gray-400 mt-1', isSender(message.sender_id) ? 'text-right pr-1' : 'text-left pl-9']">
                                                {{ message.timestamp ? new Date(message.timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'Wysyłanie...' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Input Area -->
                                    <div class="p-5 bg-white border-t border-gray-100 z-20">
                                        <form @submit.prevent="sendMessage" class="flex items-center gap-3 bg-gray-50 p-1.5 rounded-full border border-gray-200 focus-within:border-[#F5F570] focus-within:ring-2 focus-within:ring-[#F5F570]/20 transition-all duration-200 shadow-sm">
                                            <input 
                                                type="text" 
                                                v-model="newMessage" 
                                                placeholder="Napisz wiadomość..."
                                                class="flex-1 bg-transparent border-none outline-none focus:outline-none focus:ring-0 px-4 py-2.5 text-gray-700 placeholder-gray-400 text-sm" 
                                            />
                                            <button 
                                                type="submit"
                                                :disabled="!newMessage.trim()"
                                                class="p-2.5 bg-[#F5F570] text-[#241F20] rounded-full hover:bg-[#e4e460] disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow transform active:scale-95 group">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:translate-x-0.5 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </template>

                                <!-- Empty State Selection -->
                                 <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 bg-gray-50/50">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 shadow-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-700 mb-2">Witaj w komunikatorze</h3>
                                    <p class="text-gray-500 text-sm">Wybierz osobę z listy po lewej, aby rozpocząć rozmowę.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Communicator UI End -->
                    </div>
                </main>
            </div>
        </div>
    </ProfileLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 20px;
    border: 2px solid transparent;
    background-clip: content-box;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}
</style>