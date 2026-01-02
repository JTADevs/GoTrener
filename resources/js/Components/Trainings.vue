<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    mentees: Array,
    trainings: Array,
});

// Mentee selection state
const searchQuery = ref('');
const selectedMentee = ref(null);

// Form state
const trainingTitle = ref('');
const trainingDate = ref('');
const trainingStartTime = ref('');
const trainingEndTime = ref('');
const trainingDescription = ref('');

const today = new Date().toISOString().split('T')[0];

const filteredMentees = computed(() => {
    if (!props.mentees) return [];
    return props.mentees.filter(mentee => {
        return mentee.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

const selectMentee = (mentee) => {
    selectedMentee.value = mentee;
};

const submitTraining = () => {
    if (!selectedMentee.value) {
        alert('Proszę wybrać podopiecznego.');
        return;
    }

    if (trainingDate.value < today) {
        alert('Nie można dodać treningu dla daty przeszłej.');
        return;
    }
    
    const trainingData = {
        uid: selectedMentee.value.uid,
        trainerUid: props.user.uid,
        title: trainingTitle.value,
        date: trainingDate.value,
        startTime: trainingStartTime.value,
        endTime: trainingEndTime.value,
        description: trainingDescription.value
    };

    router.post('/addTraining', trainingData, {
        onSuccess: () => {
            searchQuery.value = '';
            selectedMentee.value = null;
            trainingTitle.value = '';
            trainingDate.value = '';
            trainingStartTime.value = '';
            trainingEndTime.value = '';
            trainingDescription.value = '';
        }
    });
};

const cancelTraining = (id, uid) => {
    if (confirm('Czy na pewno chcesz anulować ten trening?')) {
        router.post('/cancelTraining', { id: id, uid: uid }, {
            preserveScroll: true,
        });
    }
};

const getTrainingStatus = (training) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const trainingDate = new Date(training.date);

    if (training.status === 'Planowany' && trainingDate < today) {
        return 'Ukończony';
    }
    return training.status;
};

</script>

<template>
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto" v-if="props.user.role === 'trainer'">
            <h2 class="text-2xl font-semibold mb-5 text-gray-700 border-b pb-4">Dodaj nowy trening</h2>
            <form @submit.prevent="submitTraining">
                <!-- Mentee Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">1. Wybierz podopiecznego</label>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Wpisz imię, aby wyszukać..."
                        class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 mb-3"
                    />
                    <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-md bg-gray-50">
                        <ul v-if="filteredMentees.length > 0">
                            <li
                                v-for="mentee in filteredMentees"
                                :key="mentee.uid"
                                @click="selectMentee(mentee)"
                                class="p-3 hover:bg-gray-200 cursor-pointer flex items-center transition-colors duration-150"
                                :class="{ 'bg-yellow-200 hover:bg-yellow-300': selectedMentee && selectedMentee.uid === mentee.uid }"
                            >
                                <img :src="mentee.imageUrl" alt="Avatar" class="w-10 h-10 rounded-full mr-4">
                                <span class="text-gray-800 font-medium">{{ mentee.name }}</span>
                            </li>
                        </ul>
                        <p v-else class="p-3 text-gray-500">Brak podopiecznych lub nie znaleziono.</p>
                    </div>

                    <div v-if="selectedMentee" class="mt-4 p-3 bg-green-100 border border-green-200 rounded-md">
                        <p class="font-semibold text-green-800">Wybrany podopieczny: <span class="font-bold">{{ selectedMentee.name }}</span></p>
                    </div>
                </div>

                <!-- Training Details -->
                <div class="mb-4 border-t pt-6">
                     <label class="block text-gray-700 text-sm font-bold mb-2">2. Wprowadź szczegóły treningu</label>
                    <div class="mb-5">
                        <label for="training-title" class="block text-gray-600 text-xs font-bold mb-1">Tytuł treningu</label>
                        <input v-model="trainingTitle" id="training-title" type="text" placeholder="Np. Trening siłowy" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" required/>
                    </div>
                    <div class="mb-5">
                        <label for="training-date" class="block text-gray-600 text-xs font-bold mb-1">Data treningu</label>
                        <input v-model="trainingDate" id="training-date" type="date" :min="today" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" required/>
                    </div>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                         <div>
                            <label for="training-start-time" class="block text-gray-600 text-xs font-bold mb-1">Godzina rozpoczęcia</label>
                            <input v-model="trainingStartTime" id="training-start-time" type="time" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" required/>
                        </div>
                        <div>
                            <label for="training-end-time" class="block text-gray-600 text-xs font-bold mb-1">Godzina zakończenia</label>
                            <input v-model="trainingEndTime" id="training-end-time" type="time" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" required/>
                        </div>
                    </div>
                     <div class="mb-6">
                        <label for="training-description" class="block text-gray-600 text-xs font-bold mb-1">Opis i ćwiczenia</label>
                        <textarea v-model="trainingDescription" id="training-description" placeholder="Wprowadź ćwiczenia, serie, powtórzenia itd." class="w-full p-3 border border-gray-300 rounded-md h-36 resize-y focus:outline-none focus:ring-2 focus:ring-yellow-500" required></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end border-t pt-6">
                    <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-[#241F20] font-bold py-3 px-8 rounded-lg transition-all duration-200 transform flex items-center justify-center cursor-pointer">
                        Dodaj trening
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto mt-8">
            <h2 class="text-2xl font-semibold mb-5 text-gray-700 border-b pb-4">Lista treningów</h2>
            <div v-if="trainings && trainings.length > 0" class="space-y-4">
                <div v-for="training in trainings" :key="training.id" class="p-4 border rounded-lg bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">{{ training.title }}</h3>
                            <p class="text-sm text-gray-600">Data: {{ training.date }}</p>
                            <p class="text-sm text-gray-600">Godzina: {{ training.startTime }} - {{ training.endTime }}</p>
                            <p v-if="training.description" class="mt-2 text-sm text-gray-600 whitespace-pre-wrap">{{ training.description }}</p>
                             <p class="text-sm text-gray-600 mt-2">Status: <span class="font-medium" :class="{
                                'text-green-600': getTrainingStatus(training) === 'Ukończony',
                                'text-blue-600': getTrainingStatus(training) === 'Planowany',
                                'text-red-600': getTrainingStatus(training) === 'Anulowany'
                            }">{{ getTrainingStatus(training) }}</span></p>
                            <button v-if="getTrainingStatus(training) === 'Planowany'" @click="cancelTraining(training.id, training.uid)" class="mt-2 text-sm text-red-500 hover:text-red-700 font-bold focus:outline-none transition-colors duration-200 cursor-pointer">
                                <i class="fa-solid fa-ban mr-1"></i> Anuluj
                            </button>
                        </div>
                        <div v-if="training.otherUser" class="text-right">
                            <img :src="training.otherUser.imageURL || '/images/no_user.png'" alt="Avatar" class="w-12 h-12 rounded-full ml-auto mb-2">
                            <span class="text-sm font-medium text-gray-700">{{ training.otherUser.name }}</span>
                            <p class="text-xs text-gray-500">{{ user.role === 'trainer' ? 'Podopieczny' : 'Trener' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <p class="text-gray-500">Brak zaplanowanych treningów.</p>
            </div>
        </div>
    </div>
</template>