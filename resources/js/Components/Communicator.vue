<script setup>
import { ref, watch, nextTick, computed, onMounted, onUnmounted } from 'vue';
import { db } from '../firebase';
import { collection, query, orderBy, onSnapshot, limit, doc, updateDoc } from 'firebase/firestore';
import axios from 'axios';

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
    axios.get('/profile/conversations')
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
</script>

<template>
    <div class="flex h-[600px] bg-gray-100 border rounded-lg overflow-hidden">
        <!-- Conversation List -->
        <div class="w-1/3 bg-white border-r overflow-y-auto">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold">Wiadomości</h2>
            </div>
            <div v-if="loading" class="p-4 text-center text-gray-500">
                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Ładowanie...
            </div>
            <ul>
                <li v-for="conversation in localConversations" :key="conversation.chat_id"
                    @click="selectConversation(conversation)"
                    class="flex items-center p-4 cursor-pointer hover:bg-yellow-100 transition-colors duration-200"
                    :class="{ 
                        'bg-white': selectedConversation && selectedConversation.chat_id === conversation.chat_id,
                        'blinking-unread': conversation.read === false && conversation.last_sender_id !== currentUser.uid && selectedConversation?.chat_id !== conversation.chat_id
                    }">
                    <img :src="conversation.otherUser.imageURL || '/images/no_user.png'" alt="User avatar" class="w-12 h-12 rounded-full mr-4 object-cover">
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between">
                            <span class="font-semibold truncate">{{ conversation.otherUser.name }}</span>
                            <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                {{ conversation.last_message?.timestamp ? new Date(conversation.last_message.timestamp.seconds * 1000).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 truncate">
                            {{ conversation.last_message?.text }}
                        </p>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Chat Window -->
        <div class="w-2/3 flex flex-col bg-white">
            <div v-if="selectedConversation" class="flex flex-col h-full">
                 <div class="flex items-center p-4 bg-white border-b shadow-sm z-10">
                    <img :src="selectedConversation.otherUser.imageURL || '/images/no_user.png'" alt="User avatar" class="w-10 h-10 rounded-full mr-3 object-cover">
                    <h2 class="text-lg font-semibold">{{ selectedConversation.otherUser.name }}</h2>
                </div>
                <div class="flex-1 p-4 overflow-y-auto bg-gray-100" ref="messagesContainer">
                    <div v-if="messages.length === 0" class="text-center text-gray-500 mt-10">
                        Brak wiadomości. Zacznij rozmowę!
                    </div>
                    <div v-for="message in messages" :key="message.id" class="mb-4">
                        <div :class="['flex', isSender(message.sender_id) ? 'justify-end' : 'justify-start']">
                            <div :class="[
                                'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
                                isSender(message.sender_id) ? 'bg-yellow-500 text-[#241F20]' : 'bg-white text-gray-800'
                            ]">
                                <p class="text-sm">{{ message.text }}</p>
                                <div class="text-xs text-right mt-1 font-semibold" :class="[isSender(message.sender_id) ? 'text-black' : 'text-black']">
                                    {{ message.timestamp ? new Date(message.timestamp).toLocaleTimeString() : '...' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-white border-t">
                    <form @submit.prevent="sendMessage" class="flex items-center">
                        <input type="text" v-model="newMessage" placeholder="Wpisz wiadomość..."
                            class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                        <button type="submit"
                            class="ml-3 px-6 py-2 bg-yellow-500 text-[#241F20] font-bold rounded-full hover:bg-yellow-600 focus:outline-none cursor-pointer transition-colors duration-200">
                            Wyślij
                        </button>
                    </form>
                </div>
            </div>
             <div v-else class="flex items-center justify-center h-full text-gray-500">
                Wybierz konwersację, aby rozpocząć czat.
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blink {
  0% { background-color: #fef9c3; }
  50% { background-color: #fef08a; }
  100% { background-color: #fef9c3; }
}

.blinking-unread {
  animation: blink 2s infinite;
}
</style>