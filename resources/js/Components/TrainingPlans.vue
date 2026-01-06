<script setup>
    import { ref, computed } from 'vue';
    import { router } from '@inertiajs/vue3';

    const props = defineProps({
        user: Object,
        mentees: Array,
    });

    const searchQuery = ref('');
    const selectedMentee = ref(null);
    const selectedDietForDetails = ref(null);

    const filteredMentees = computed(() => {
        if (!props.mentees) return [];
        return props.mentees.filter(mentee => {
            return mentee.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        });
    });

    const selectMentee = (mentee) => {
        selectedMentee.value = mentee;
    };
</script>   

<template>
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto" v-if="props.user.role === 'trainer'">
            <h2 class="text-2xl font-semibold mb-5 text-gray-700 border-b pb-4">Dodaj nowy plan treningowy</h2>
            <form @submit.prevent="submitDiet">
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

                <div class="flex justify-end border-t pt-6">
                    <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-[#241F20] font-bold py-3 px-8 rounded-lg transition-all duration-200 transform flex items-center justify-center cursor-pointer">
                        Dodaj plan treningowy
                    </button>
                </div>
            </form>
        </div>
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto mt-8">
            <h2 class="text-2xl font-semibold mb-5 text-gray-700 border-b pb-4">Twoje plany treningowe</h2>
            
        </div>
    </div>
</template>
