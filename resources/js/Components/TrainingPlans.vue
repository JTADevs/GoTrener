<script setup>
    import { ref, computed } from 'vue';
    import { router } from '@inertiajs/vue3';

    const props = defineProps({
        user: Object,
        mentees: Array,
        trainingPlans: Array,
    });

    const searchQuery = ref('');
    const selectedMentee = ref(null);
    const planTitle = ref('');
    const planDescription = ref('');
    const selectedPlanForDetails = ref(null);
    
    // Data structure for days
    const daysOfWeek = ['Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota', 'Niedziela'];
    const trainingDays = ref([
        { dayName: 'Poniedziałek', exercises: [{ exercise: '', details: '' }] }
    ]);

    const filteredMentees = computed(() => {
        if (!props.mentees) return [];
        return props.mentees.filter(mentee => {
            return mentee.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        });
    });

    const addTrainingDay = () => {
        if (trainingDays.value.length >= 7) {
            alert('Plan może obejmować maksymalnie 7 dni (jeden tydzień).');
            return;
        }

        // Find the first day of week that is not yet used
        let nextDay = 'Poniedziałek';
        const usedDays = trainingDays.value.map(d => d.dayName);
        const availableDay = daysOfWeek.find(d => !usedDays.includes(d));
        
        if (availableDay) {
            nextDay = availableDay;
        } else {
             // Fallback logic if all names matched (shouldn't happen given length < 7 check, but safety)
             // Or if user used duplicates. Just take next in sequence from last one.
            const lastDayName = trainingDays.value[trainingDays.value.length - 1].dayName;
            const index = daysOfWeek.indexOf(lastDayName);
             if (index !== -1 && index < daysOfWeek.length - 1) {
                nextDay = daysOfWeek[index + 1];
            }
        }

        trainingDays.value.push({ dayName: nextDay, exercises: [{ exercise: '', details: '' }] });
    };

    const removeTrainingDay = (index) => {
        if (trainingDays.value.length > 1) {
            trainingDays.value.splice(index, 1);
        } else {
             alert('Plan musi zawierać co najmniej jeden dzień treningowy.');
        }
    };

    const addExercise = (dayIndex) => {
        if (trainingDays.value[dayIndex].exercises.length < 20) {
            trainingDays.value[dayIndex].exercises.push({ exercise: '', details: '' });
        } else {
            alert('Możesz dodać maksymalnie 20 ćwiczeń na dzień.');
        }
    };

    const removeExercise = (dayIndex, exerciseIndex) => {
        if (trainingDays.value[dayIndex].exercises.length > 1) {
            trainingDays.value[dayIndex].exercises.splice(exerciseIndex, 1);
        }
    };

    const selectMentee = (mentee) => {
        selectedMentee.value = mentee;
    };

    const submitPlan = () => {
        if (!selectedMentee.value) {
            alert('Proszę wybrać podopiecznego.');
            return;
        }
        
        // Format the plan as a string:
        // PONIEDZIAŁEK:
        // - Exercise: Details
        // ...
        
        let formattedPlan = '';
        trainingDays.value.forEach(day => {
            formattedPlan += `${day.dayName.toUpperCase()}:\n`;
            day.exercises.forEach(ex => {
                 formattedPlan += `- ${ex.exercise}: ${ex.details}\n`;
            });
            formattedPlan += '\n';
        });

        const planData = {
            menteeUid: selectedMentee.value.uid,
            trainerUid: props.user.uid,
            trainerName: props.user.name,
            menteeName: selectedMentee.value.name,
            title: planTitle.value,
            description: planDescription.value,
            plan: formattedPlan.trim()
        };
        
        console.log('Submitting formatted plan:', planData);
        
        router.post('/addTrainingPlan', planData, {
            onSuccess: () => {
                planTitle.value = '';   
                planDescription.value = '';
                selectedMentee.value = null;
                searchQuery.value = '';
                trainingDays.value = [{ dayName: 'Poniedziałek', exercises: [{ exercise: '', details: '' }] }];
                alert('Plan treningowy został dodany pomyślnie.');
            },
            onError: () => {
                alert('Wystąpił błąd podczas dodawania planu treningowego.');
            }
        });
    };

    const openDetails = (plan) => {
        selectedPlanForDetails.value = plan;
    };

    const closeDetails = () => {
        selectedPlanForDetails.value = null;
    };

    const deletePlan = (planId) => {
        if (confirm('Czy na pewno chcesz usunąć ten plan treningowy?')) {
            router.delete(`/deleteTrainingPlan/${planId}`, {
                preserveScroll: true,
                onSuccess: () => {

                },
                onError: (errors) => {
                    alert('Wystąpił błąd podczas usuwania planu.');
                }
            });
        }
    };

    const downloadPDF = (id) => {
        window.location.href = `/training-plan-downloadPDF/${id}`;
    };
</script>   

<template>
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto" v-if="props.user.role === 'trainer'">
            <h2 class="text-2xl font-semibold mb-5 text-gray-700 border-b pb-4">Dodaj nowy plan treningowy</h2>
            <form @submit.prevent="submitPlan">
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">1. Wybierz podopiecznego</label>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Wpisz imię, aby wyszukać..."
                        class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 mb-3"
                    />
                    <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-md bg-gray-50">
                        <ul v-if="filteredMentees && filteredMentees.length > 0">
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

                <!-- Plan Details -->
                <div class="mb-4 border-t pt-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">2. Wprowadź szczegóły planu</label>
                    <div class="mb-5">
                        <label for="plan-title" class="block text-gray-600 text-xs font-bold mb-1">Nazwa planu</label>
                        <input v-model="planTitle" id="plan-title" type="text" placeholder="Np. Plan na masę" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" required/>
                    </div>
                    <div class="mb-6">
                        <label for="plan-description" class="block text-gray-600 text-xs font-bold mb-1">Opis (opcjonalnie)</label>
                        <textarea v-model="planDescription" id="plan-description" placeholder="Wprowadź opis planu" class="w-full p-3 border border-gray-300 rounded-md h-24 resize-y focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-600 text-xs font-bold mb-2">Harmonogram treningowy</label>
                        
                        <div v-for="(day, dayIndex) in trainingDays" :key="dayIndex" class="mb-6 p-4 bg-gray-100 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-center mb-3 border-b pb-2 border-gray-300">
                                <div class="w-1/2">
                                    <select v-model="day.dayName" class="w-full p-2 border border-gray-300 rounded bg-white text-gray-800 font-semibold focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                        <option v-for="d in daysOfWeek" :key="d" :value="d">{{ d }}</option>
                                    </select>
                                </div>
                                <button 
                                    type="button" 
                                    @click="removeTrainingDay(dayIndex)" 
                                    class="text-red-500 hover:text-red-700 text-sm font-semibold cursor-pointer"
                                    transition-colors
                                >
                                    <i class="fa-solid fa-trash-can mr-1"></i> Usuń dzień
                                </button>
                            </div>

                            <div v-for="(item, exerciseIndex) in day.exercises" :key="exerciseIndex" class="flex flex-col sm:flex-row gap-3 mb-3">
                                <div class="flex-grow">
                                    <input 
                                        v-model="item.exercise" 
                                        placeholder="Nazwa ćwiczenia" 
                                        class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" 
                                        required
                                    />
                                </div>
                                <div class="sm:w-1/3">
                                    <input 
                                        v-model="item.details" 
                                        placeholder="Serie / Powtórzenia / Kg" 
                                        class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" 
                                        required
                                    />
                                </div>
                                <button 
                                    type="button" 
                                    @click="removeExercise(dayIndex, exerciseIndex)" 
                                    class="p-3 text-red-500 hover:text-red-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors flex items-center justify-center shrink-0 cursor-pointer" 
                                    v-if="day.exercises.length > 1"
                                    title="Usuń ćwiczenie"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            <button 
                                type="button" 
                                @click="addExercise(dayIndex)" 
                                class="mt-2 text-sm text-yellow-600 hover:text-yellow-700 font-bold flex items-center gap-2 px-1 py-1 rounded transition-colors cursor-pointer"
                                :class="{ 'opacity-50 cursor-not-allowed': day.exercises.length >= 20 }"
                                :disabled="day.exercises.length >= 20"
                            >
                                <i class="fa-solid fa-plus-circle"></i> Dodaj ćwiczenie
                            </button>
                        </div>

                        <button 
                            type="button" 
                            @click="addTrainingDay" 
                            v-if="trainingDays.length < 7"
                            class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:text-yellow-600 hover:border-yellow-500 font-semibold transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fa-solid fa-calendar-plus"></i> Dodaj kolejny dzień treningowy
                        </button>
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
            <div v-if="props.trainingPlans && props.trainingPlans.length > 0" class="space-y-4">
                <div v-for="plan in props.trainingPlans" :key="plan.id">
                    <div class="p-4 border bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors" @click="openDetails(plan)">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">{{ plan.title }}</h3>
                                <p class="text-sm text-gray-600">Utworzono: {{ plan.created_at.split('T')[0] }}</p>
                                <p class="text-sm text-gray-600" v-if="props.user.role !== 'trainer'">Trener: {{ plan.trainerName }}</p>
                                <p class="text-sm text-gray-600" v-if="props.user.role !== 'client'">Podopieczny: {{ plan.menteeName }}</p>
                            </div>
                            <div class="flex items-center">
                                <button v-if="props.user.role === 'trainer'" class="text-sm text-red-500 hover:text-red-700 mr-4" @click.stop="deletePlan(plan.id)">Usuń</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <p class="text-gray-500">Brak przypisanych planów treningowych.</p>
            </div>
        </div>

        <!-- Details Modal -->
        <div v-if="selectedPlanForDetails" class="fixed inset-0 bg-[rgba(0,0,0,0.85)] flex items-center justify-center p-4 z-50" @click.self="closeDetails">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b flex justify-between items-center sticky top-0 bg-white z-10">
                    <div>
                         <h3 class="text-xl font-bold text-gray-800">{{ selectedPlanForDetails.title }}</h3>
                         <p class="text-sm text-gray-600 mt-1" v-if="selectedPlanForDetails.description">{{ selectedPlanForDetails.description }}</p>
                    </div>
                    <button @click="closeDetails" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                     <!-- User Info -->
                     <div class="flex flex-col sm:flex-row gap-4 text-sm text-gray-600 border-b pb-4">
                        <p v-if="props.user.role !== 'trainer'"><span class="font-semibold">Trener:</span> {{ selectedPlanForDetails.trainerName }}</p>
                        <p v-if="props.user.role !== 'client'"><span class="font-semibold">Podopieczny:</span> {{ selectedPlanForDetails.menteeName }}</p>
                        <p><span class="font-semibold">Utworzono:</span> {{ selectedPlanForDetails.created_at.split('T')[0] }}</p>
                     </div>

                    <!-- Plan Details -->
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 font-mono text-sm whitespace-pre-wrap text-gray-800">
                        {{ selectedPlanForDetails.plan }}
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end sticky bottom-0 z-10">
                    <button 
                        class="bg-yellow-500 hover:bg-yellow-600 text-gray-800 font-semibold py-2 px-4 rounded transition-colors mr-2 cursor-pointer"
                        @click="downloadPDF(selectedPlanForDetails.id)"
                    >
                        Pobierz PDF
                    </button>
                    <button @click="closeDetails" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition-colors cursor-pointer">
                        Zamknij
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
