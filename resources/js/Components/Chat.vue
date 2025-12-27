<script setup>
import { ref, watch, onMounted, nextTick, computed } from 'vue';
import { db } from '../firebase';
import { collection, query, orderBy, onSnapshot } from 'firebase/firestore';
import axios from 'axios';

const props = defineProps({
    currentUser: {
        type: Object,
        required: true,
    },
    recipientUser: {
        type: Object,
        required: true,
    },
});

const messages = ref([]);
const newMessage = ref('');
const messagesContainer = ref(null);
let unsubscribe = null;

const getChatId = (id1, id2) => {
    if (!id1 || !id2) return null;
    const ids = [id1, id2];
    ids.sort();
    return ids.join('_');
};

const chatId = computed(() => getChatId(props.currentUser?.id, props.recipientUser?.id));

const isSender = (senderId) => {
    const currentId = props.currentUser?.uid || props.currentUser?.id;
    return senderId == currentId;
};

const setupListener = () => {
    if (unsubscribe) {
        unsubscribe(); // Stop any previous listener
    }
    
    if (!chatId.value) {
        messages.value = []; // Clear messages if there's no chat ID
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

        // Auto-scroll to bottom
        nextTick(() => {
            if (messagesContainer.value) {
                messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
            }
        });
    }, (error) => {
        // console.error("Firestore snapshot error:", error);
    });
};

const sendMessage = async () => {
    if (newMessage.value.trim() === '' || !chatId.value) return;

    try {
        await axios.post('/chat/send', {
            recipient_id: props.recipientUser.id,
            message: newMessage.value.trim(),
            sender_id: props.currentUser?.id,
            read: false,
        });
        newMessage.value = '';
    } catch (error) {
        // console.error("Error sending message:", error);
    }
};

watch(chatId, (currentChatId) => {
    if (currentChatId) {
        setupListener();
    } else {
        if (unsubscribe) {
            unsubscribe();
        }
        messages.value = [];
    }
}, { immediate: true });

</script>

<template>
    <div class="flex flex-col h-96 bg-gray-100 rounded-lg">
        <div class="flex-1 p-4 overflow-y-auto" ref="messagesContainer">
            <div v-if="messages.length === 0" class="text-center text-gray-500">
                Brak wiadomości. Zacznij rozmowę!
            </div>
            <div v-for="message in messages" :key="message.id" class="mb-4">
                <div :class="[
                    'flex',
                    isSender(message.sender_id) ? 'justify-end' : 'justify-start'
                ]">
                    <div :class="[
                        'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
                        isSender(message.sender_id)
                            ? 'bg-yellow-500 text-[#241F20]'
                            : 'bg-white text-gray-800'
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
                <input
                    type="text"
                    v-model="newMessage"
                    placeholder="Wpisz wiadomość..."
                    class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-500"
                />
                <button
                    type="submit"
                    class="ml-3 px-6 py-2 bg-yellow-300 text-[#241F20] font-semibold rounded-full hover:bg-yellow-400 focus:outline-none cursor-pointer"
                >
                    Wyślij
                </button>
            </form>
        </div>
    </div>
</template>