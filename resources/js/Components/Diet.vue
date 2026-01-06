<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

    const props = defineProps({
        user: Object,
        mentees: Array,
        diets: Array,
    });

    const searchQuery = ref('');
    const selectedMentee = ref(null);
    const selectedDietForDetails = ref(null);

    // Diet form fields
    const dietDescription = ref('');
    const caloricValue = ref('');
    const mondayBreakfast = ref('');
    const mondaySecondBreakfast = ref('');
    const mondayLunch = ref('');
    const mondayAfternoonSnack = ref('');
    const mondayDinner = ref('');
    const mondayMacro = ref('');
    const tuesdayBreakfast = ref('');
    const tuesdaySecondBreakfast = ref('');
    const tuesdayLunch = ref('');
    const tuesdayAfternoonSnack = ref('');
    const tuesdayDinner = ref('');
    const tuesdayMacro = ref('');
    const wednesdayBreakfast = ref('');
    const wednesdaySecondBreakfast = ref('');
    const wednesdayLunch = ref('');
    const wednesdayAfternoonSnack = ref('');
    const wednesdayDinner = ref('');
    const wednesdayMacro = ref('');
    const thursdayBreakfast = ref('');
    const thursdaySecondBreakfast = ref('');
    const thursdayLunch = ref('');
    const thursdayAfternoonSnack = ref('');
    const thursdayDinner = ref('');
    const thursdayMacro = ref('');
    const fridayBreakfast = ref('');
    const fridaySecondBreakfast = ref('');
    const fridayLunch = ref('');
    const fridayAfternoonSnack = ref('');
    const fridayDinner = ref('');
    const fridayMacro = ref('');
    const saturdayBreakfast = ref('');
    const saturdaySecondBreakfast = ref('');
    const saturdayLunch = ref('');
    const saturdayAfternoonSnack = ref('');
    const saturdayDinner = ref('');
    const saturdayMacro = ref('');
    const sundayBreakfast = ref('');
    const sundaySecondBreakfast = ref('');
    const sundayLunch = ref('');
    const sundayAfternoonSnack = ref('');
    const sundayDinner = ref('');
    const sundayMacro = ref('');

    const filteredMentees = computed(() => {
        if (!props.mentees) return [];
        return props.mentees.filter(mentee => {
            return mentee.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        });
    });

    const selectMentee = (mentee) => {
        selectedMentee.value = mentee;
    };

    const openDetails = (diet) => {
        selectedDietForDetails.value = diet;
    };

    const closeDetails = () => {
        selectedDietForDetails.value = null;
    };

    const getDayName = (day) => {
        const dayNames = {
            monday: 'Poniedziałek',
            tuesday: 'Wtorek',
            wednesday: 'Środa',
            thursday: 'Czwartek',
            friday: 'Piątek',
            saturday: 'Sobota',
            sunday: 'Niedziela'
        };
        return dayNames[day] || day;
    };

    const submitDiet = () => {
        if (!selectedMentee.value) {
            alert('Proszę wybrać podopiecznego.');
            return;
        }

        const dietData = {
            menteeUid: selectedMentee.value.uid,
            trainerUid: props.user.uid,
            trainerName: props.user.name,
            menteeName: selectedMentee.value.name,
            dietDescription: dietDescription.value,
            caloricValue: caloricValue.value,
            monday: {
                breakfast: mondayBreakfast.value,
                secondBreakfast: mondaySecondBreakfast.value,
                lunch: mondayLunch.value,
                afternoonSnack: mondayAfternoonSnack.value,
                dinner: mondayDinner.value,
                macro: mondayMacro.value,
            },
            tuesday: {
                breakfast: tuesdayBreakfast.value,
                secondBreakfast: tuesdaySecondBreakfast.value,
                lunch: tuesdayLunch.value,
                afternoonSnack: tuesdayAfternoonSnack.value,
                dinner: tuesdayDinner.value,
                macro: tuesdayMacro.value,
            },
            wednesday: {
                breakfast: wednesdayBreakfast.value,
                secondBreakfast: wednesdaySecondBreakfast.value,
                lunch: wednesdayLunch.value,
                afternoonSnack: wednesdayAfternoonSnack.value,
                dinner: wednesdayDinner.value,
                macro: wednesdayMacro.value,
            },
            thursday: {
                breakfast: thursdayBreakfast.value,
                secondBreakfast: thursdaySecondBreakfast.value,
                lunch: thursdayLunch.value,
                afternoonSnack: thursdayAfternoonSnack.value,
                dinner: thursdayDinner.value,
                macro: thursdayMacro.value,
            },
            friday: {
                breakfast: fridayBreakfast.value,
                secondBreakfast: fridaySecondBreakfast.value,
                lunch: fridayLunch.value,
                afternoonSnack: fridayAfternoonSnack.value,
                dinner: fridayDinner.value,
                macro: fridayMacro.value,
            },
            saturday: {
                breakfast: saturdayBreakfast.value,
                secondBreakfast: saturdaySecondBreakfast.value,
                lunch: saturdayLunch.value,
                afternoonSnack: saturdayAfternoonSnack.value,
                dinner: saturdayDinner.value,
                macro: saturdayMacro.value,
            },
            sunday: {
                breakfast: sundayBreakfast.value,
                secondBreakfast: sundaySecondBreakfast.value,
                lunch: sundayLunch.value,
                afternoonSnack: sundayAfternoonSnack.value,
                dinner: sundayDinner.value,
                macro: sundayMacro.value,
            },
        };

        router.post('/addDiet', dietData, {
            onSuccess: () => {
                dietDescription.value = '';
                caloricValue.value = '';
                mondayBreakfast.value = '';
                mondaySecondBreakfast.value = '';
                mondayLunch.value = '';
                mondayAfternoonSnack.value = '';
                mondayDinner.value = '';
                tuesdayBreakfast.value = '';
                tuesdaySecondBreakfast.value = '';
                tuesdayLunch.value = '';
                tuesdayAfternoonSnack.value = '';
                tuesdayDinner.value = '';
                wednesdayBreakfast.value = '';
                wednesdaySecondBreakfast.value = '';
                wednesdayLunch.value = '';
                wednesdayAfternoonSnack.value = '';
                wednesdayDinner.value = '';
                thursdayBreakfast.value = '';
                thursdaySecondBreakfast.value = '';
                thursdayLunch.value = '';
                thursdayAfternoonSnack.value = '';
                thursdayDinner.value = '';
                fridayBreakfast.value = '';
                fridaySecondBreakfast.value = '';
                fridayLunch.value = '';
                fridayAfternoonSnack.value = '';
                fridayDinner.value = '';
                saturdayBreakfast.value = '';
                saturdaySecondBreakfast.value = '';
                saturdayLunch.value = '';
                saturdayAfternoonSnack.value = '';
                saturdayDinner.value = '';
                sundayBreakfast.value = '';
                sundaySecondBreakfast.value = '';
                sundayLunch.value = '';
                sundayAfternoonSnack.value = '';
                sundayDinner.value = '';
                searchQuery.value = '';
                selectedMentee.value = null;
            },
            onError: () => {
                alert('Wystąpił błąd podczas dodawania diety.');
            }
        });
    }
    const deleteDiet = (dietId) => {
        if (confirm('Czy na pewno chcesz usunąć tę dietę?')) {
            router.delete(`/deleteDiet/${dietId}`, {
                preserveScroll: true,
                onSuccess: () => {

                },
                onError: (errors) => {
                    alert('Wystąpił błąd podczas usuwania diety.');
                }
            });
        }
    };

    const downloadPDF = (id) => {
        window.location.href = `/diet-downloadPDF/${id}`;
    }
</script>

<template>
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto" v-if="props.user.role === 'trainer'">
            <h2 class="text-2xl font-semibold mb-5 text-gray-700 border-b pb-4">Dodaj nową dietę</h2>
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

                <div class="my-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">2. Opis diety i makro</label>
                    <textarea class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 mb-4" rows="4" placeholder="Wprowadź opis diety" v-model="dietDescription"></textarea>
                    <input type="text" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 my-2" placeholder="Wprowadź uśrednioną wartość kaloryczną (np. 2000 kcal)" v-model="caloricValue"/>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">3. Wprowadź szczegóły diety</label>
                </div>

                <!-- Diet details -->
                <div class="mb-4 border-t pt-6 space-y-8">
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-yellow-500 text-white">
                                <tr>
                                    <th colspan="2" class="px-4 py-3 text-lg font-bold tracking-wider">
                                        Poniedziałek
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/5">Śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="mondayBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Drugie śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="mondaySecondBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Obiad</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="mondayLunch"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Podwieczorek</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="mondayAfternoonSnack"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kolacja</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="mondayDinner"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Makro</th>
                                    <td class="p-2"><input type="text" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 my-2" placeholder="Wprowadź makra na poniedziałek (np. B: 150g, W: 200g, T: 70g)" v-model="mondayMacro"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-yellow-500 text-white">
                                <tr>
                                    <th colspan="2" class="px-4 py-3 text-lg font-bold tracking-wider">
                                        Wtorek
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/5">Śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="tuesdayBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Drugie śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="tuesdaySecondBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Obiad</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="tuesdayLunch"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Podwieczorek</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="tuesdayAfternoonSnack"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kolacja</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="tuesdayDinner"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Makro</th>
                                    <td class="p-2"><input type="text" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 my-2" placeholder="Wprowadź makra na wtorek (np. B: 150g, W: 200g, T: 70g)" v-model="tuesdayMacro"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-yellow-500 text-white">
                                <tr>
                                    <th colspan="2" class="px-4 py-3 text-lg font-bold tracking-wider">
                                        Środa
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/5">Śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="wednesdayBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Drugie śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="wednesdaySecondBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Obiad</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="wednesdayLunch"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Podwieczorek</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="wednesdayAfternoonSnack"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kolacja</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="wednesdayDinner"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Makro</th>
                                    <td class="p-2"><input type="text" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 my-2" placeholder="Wprowadź makra na środe (np. B: 150g, W: 200g, T: 70g)" v-model="wednesdayMacro"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-yellow-500 text-white">
                                <tr>
                                    <th colspan="2" class="px-4 py-3 text-lg font-bold tracking-wider">
                                        Czwartek
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/5">Śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="thursdayBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Drugie śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="thursdaySecondBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Obiad</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="thursdayLunch"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Podwieczorek</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="thursdayAfternoonSnack"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kolacja</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="thursdayDinner"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Makro</th>
                                    <td class="p-2"><input type="text" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 my-2" placeholder="Wprowadź makra na czwartek (np. B: 150g, W: 200g, T: 70g)" v-model="thursdayMacro"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-yellow-500 text-white">
                                <tr>
                                    <th colspan="2" class="px-4 py-3 text-lg font-bold tracking-wider">
                                        Piątek
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/5">Śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="fridayBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Drugie śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="fridaySecondBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Obiad</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="fridayLunch"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Podwieczorek</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="fridayAfternoonSnack"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kolacja</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="fridayDinner"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Makro</th>
                                    <td class="p-2"><input type="text" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 my-2" placeholder="Wprowadź makra na piątek (np. B: 150g, W: 200g, T: 70g)" v-model="fridayMacro"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-yellow-500 text-white">
                                <tr>
                                    <th colspan="2" class="px-4 py-3 text-lg font-bold tracking-wider">
                                        Sobota
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/5">Śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="saturdayBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Drugie śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="saturdaySecondBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Obiad</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="saturdayLunch"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Podwieczorek</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="saturdayAfternoonSnack"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kolacja</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="saturdayDinner"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Makro</th>
                                    <td class="p-2"><input type="text" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 my-2" placeholder="Wprowadź makra na sobote (np. B: 150g, W: 200g, T: 70g)" v-model="saturdayMacro"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-yellow-500 text-white">
                                <tr>
                                    <th colspan="2" class="px-4 py-3 text-lg font-bold tracking-wider">
                                        Niedziela
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/5">Śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="sundayBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Drugie śniadanie</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="sundaySecondBreakfast"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Obiad</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="sundayLunch"></textarea></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Podwieczorek</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="sundayAfternoonSnack"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Kolacja</th>
                                    <td class="p-2"><textarea class="w-full p-2 border-gray-200 rounded-md focus:ring-yellow-500 focus:border-yellow-500" rows="2" v-model="sundayDinner"></textarea></td>
                                </tr>
                                <tr class="bg-white">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Makro</th>
                                    <td class="p-2"><input type="text" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 my-2" placeholder="Wprowadź makra na niedziele (np. B: 150g, W: 200g, T: 70g)" v-model="sundayMacro"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-end border-t pt-6">
                    <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-[#241F20] font-bold py-3 px-8 rounded-lg transition-all duration-200 transform flex items-center justify-center cursor-pointer">
                        Dodaj dietę
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto mt-8">
            <h2 class="text-2xl font-semibold mb-5 text-gray-700 border-b pb-4">Twoje Diety</h2>
            <div v-if="diets && diets.length > 0" class="space-y-4">
                <div v-for="diet in diets" :key="diet.id">
                    <div class="p-4 border bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors" @click="openDetails(diet)">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">{{ diet.dietDescription }}</h3>
                                <p class="text-sm text-gray-600">Utworzono: {{ diet.created_at.split('T')[0] }}</p>
                                <p class="text-sm text-gray-600" v-if="diet.caloricValue">Kaloryczność: {{ diet.caloricValue }}</p>
                                <p class="text-sm text-gray-600" v-if="props.user.role !== 'trainer'">Trener: {{ diet.trainerName }}</p>
                                <p class="text-sm text-gray-600" v-if="props.user.role !== 'client'">Podopieczny: {{ diet.menteeName }}</p>
                            </div>
                            <div class="flex items-center">
                                <button v-if="props.user.role === 'trainer'" class="text-sm text-red-500 hover:text-red-700 mr-4" @click.stop="deleteDiet(diet.id)">Usuń</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <p class="text-gray-500">Brak przypisanych diet.</p>
            </div>
        </div>

        <!-- Details Modal -->
        <div v-if="selectedDietForDetails" class="fixed inset-0 bg-[rgba(0,0,0,0.85)] flex items-center justify-center p-4 z-50" @click.self="closeDetails">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b flex justify-between items-center sticky top-0 bg-white z-10">
                    <div>
                         <h3 class="text-xl font-bold text-gray-800">{{ selectedDietForDetails.dietDescription }}</h3>
                         <p class="text-sm text-gray-600 mt-1" v-if="selectedDietForDetails.caloricValue">Kaloryczność: {{ selectedDietForDetails.caloricValue }}</p>
                    </div>
                    <button @click="closeDetails" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                     <!-- User Info -->
                     <div class="flex flex-col sm:flex-row gap-4 text-sm text-gray-600 border-b pb-4">
                        <p v-if="props.user.role !== 'trainer'"><span class="font-semibold">Trener:</span> {{ selectedDietForDetails.trainerName }}</p>
                        <p v-if="props.user.role !== 'client'"><span class="font-semibold">Podopieczny:</span> {{ selectedDietForDetails.menteeName }}</p>
                        <p><span class="font-semibold">Utworzono:</span> {{ selectedDietForDetails.created_at.split('T')[0] }}</p>
                     </div>

                    <!-- Days Tables -->
                    <div class="space-y-6">
                         <div v-for="day in ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']" :key="day" class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                            <table class="min-w-full text-sm">
                                <thead class="bg-yellow-400 text-black">
                                    <tr>
                                        <th colspan="2" class="px-4 py-3 text-lg font-bold tracking-wider">
                                            {{ getDayName(day) }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200" v-if="selectedDietForDetails[day]">
                                    <tr class="bg-white">
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/5">Śniadanie</th>
                                        <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ selectedDietForDetails[day].breakfast }}</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Drugie śniadanie</th>
                                        <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ selectedDietForDetails[day].secondBreakfast }}</td>
                                    </tr>
                                    <tr class="bg-white">
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Obiad</th>
                                        <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ selectedDietForDetails[day].lunch }}</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Podwieczorek</th>
                                        <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ selectedDietForDetails[day].afternoonSnack }}</td>
                                    </tr>
                                    <tr class="bg-white">
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Kolacja</th>
                                        <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ selectedDietForDetails[day].dinner }}</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Makro</th>
                                        <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ selectedDietForDetails[day].macro }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="2" class="p-4 text-center text-gray-500">Brak danych dla tego dnia.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end sticky bottom-0 z-10">
                    <button 
                        class="bg-yellow-500 hover:bg-yellow-600 text-gray-800 font-semibold py-2 px-4 rounded transition-colors mr-2 cursor-pointer"
                        @click="downloadPDF(selectedDietForDetails.id)"
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
