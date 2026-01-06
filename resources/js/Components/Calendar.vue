<script setup>
import { computed, ref } from 'vue';
import { Form, useForm } from '@inertiajs/vue3';

const { user } = defineProps({
    user: Object,
});

const currentDate = ref(new Date());
currentDate.value.setDate(1);

const monthName = computed(() => {
    return currentDate.value.toLocaleString('pl-PL', { month: 'long' });
});

const year = computed(() => {
    return currentDate.value.getFullYear();
});

const weeks = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();

    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const daysInMonth = lastDayOfMonth.getDate();

    let firstWeekday = firstDayOfMonth.getDay();
    if (firstWeekday === 0) { // Sunday
        firstWeekday = 6;
    } else {
        firstWeekday--;
    }

    const days = [];
    for (let i = 0; i < firstWeekday; i++) {
        days.push(null);
    }

     for (let i = 1; i <= daysInMonth; i++) {
         days.push({
             date: new Date(year, month, i, 12, 0, 0, 0)
         });
     }

    while (days.length % 7 !== 0) {
        days.push(null);
    }

    const result = [];
    for (let i = 0; i < result.length; i += 7) {
        result.push(days.slice(i, i + 7));
    }

    for (let i = 0; i < days.length; i += 7) {
        result.push(days.slice(i, i + 7));
    }

    return result;
});

const isPast = (date) => {
    if (!date) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const dateToCompare = new Date(date);
    dateToCompare.setHours(0, 0, 0, 0);
    return dateToCompare < today;
};

const isToday = (date) => {
    if (!date) return false;
    const today = new Date();
    return date.getDate() === today.getDate() &&
           date.getMonth() === today.getMonth() &&
           date.getFullYear() === today.getFullYear();
};

const canGoToPreviousMonth = computed(() => {
    const today = new Date();
    const current = currentDate.value;
    return current.getFullYear() > today.getFullYear() ||
           (current.getFullYear() === today.getFullYear() && current.getMonth() > today.getMonth());
});

const previousMonth = () => {
    if (canGoToPreviousMonth.value) {
        currentDate.value = new Date(currentDate.value.setMonth(currentDate.value.getMonth() - 1));
    }
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.setMonth(currentDate.value.getMonth() + 1));
};

const popupOpen = ref(false);
const selectedDate = ref(null);
const selectedDateDisplay = ref(null);

const openPopup = (day) => {
    if (!day) return;
    if (isPast(day.date) && !isToday(day.date)) return;
    
    const year = day.date.getFullYear();
    const month = String(day.date.getMonth() + 1).padStart(2, '0');
    const da = String(day.date.getDate()).padStart(2, '0');
    selectedDate.value = `${year}-${month}-${da}`;
    form.selectedDate = selectedDate.value;

    selectedDateDisplay.value = `${da}.${month}.${year}`;
    
    popupOpen.value = true;
};

const hasEvents = (date) => {
    if (!date || !user.personalEvents) return false;
    const dateString = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
    return user.personalEvents.some(event => {
        const eventDate = new Date(event.selectedDate.replace(/-/g, '\/'));
        const eventDateString = eventDate.getFullYear() + '-' + ('0' + (eventDate.getMonth() + 1)).slice(-2) + '-' + ('0' + eventDate.getDate()).slice(-2);
        return eventDateString === dateString;
    });
};

const form = useForm({
    selectedDate: '',
    eventTime: '',
    eventDescription: '',
});

const deleteForm = useForm({
    id: '',
});

const confirmDelete = (eventId) => {
    if (confirm('Czy na pewno chcesz usunąć to wydarzenie?')) {
        deleteForm.delete(`/profil/events/${eventId}/delete`);
    }
};

const filteredEvents = computed(() => {
    if (!user.personalEvents) return [];
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    return user.personalEvents
        .filter(event => {
            const eventDate = new Date(event.selectedDate.replace(/-/g, '\/'));
            return eventDate.getFullYear() === year && eventDate.getMonth() === month;
        })
        .sort((a, b) => {
            const dateA = new Date(`${a.selectedDate.replace(/-/g, '\/')}T${a.eventTime}`);
            const dateB = new Date(`${b.selectedDate.replace(/-/g, '\/')}T${b.eventTime}`);
            return dateA - dateB;
        });
});
</script>

<template>
    <div class="bg-white shadow-sm sm:rounded-lg p-4 md:p-6 border border-gray-200 overflow-x-auto">
        <div class="flex items-center justify-between mb-4">
            <button @click="previousMonth" :disabled="!canGoToPreviousMonth" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer transition-colors">
                &lt;
            </button>
            <h2 class="text-lg font-semibold text-gray-900 capitalize text-center">
                {{ monthName }} {{ year }}
            </h2>
            <button @click="nextMonth" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 cursor-pointer transition-colors">
                &gt;
            </button>
        </div>
        <table class="w-full text-sm text-left text-gray-500 border-collapse">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="py-3 px-2 md:px-4 text-center font-medium">Pon</th>
                    <th scope="col" class="py-3 px-2 md:px-4 text-center font-medium">Wt</th>
                    <th scope="col" class="py-3 px-2 md:px-4 text-center font-medium">Śr</th>
                    <th scope="col" class="py-3 px-2 md:px-4 text-center font-medium">Czw</th>
                    <th scope="col" class="py-3 px-2 md:px-4 text-center font-medium">Pt</th>
                    <th scope="col" class="py-3 px-2 md:px-4 text-center font-medium">Sob</th>
                    <th scope="col" class="py-3 px-2 md:px-4 text-center font-medium">Nd</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr v-for="(week, wi) in weeks" :key="wi" class="bg-white">
                    <td v-for="(day, di) in week" :key="di"
                        class="p-1 h-16 w-16 sm:h-20 sm:w-20 md:h-24 md:w-24 border border-gray-200 text-center align-middle relative"
                        :class="{ 'bg-gray-50': day && isPast(day.date) && !isToday(day.date) }">
                        <div v-if="day"
                            @click="openPopup(day)"
                            :class="[
                                'h-full w-full flex items-center justify-center rounded-lg transition-colors',
                                { 'bg-yellow-100': hasEvents(day.date) && !isToday(day.date) },
                                (isPast(day.date) && !isToday(day.date)) ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-[#F5F570]'
                            ]">
                            <span class="text-sm sm:text-base"
                                :class="{
                                    'text-gray-400': isPast(day.date) && !isToday(day.date),
                                    'text-gray-900': !isPast(day.date) || isToday(day.date),
                                    'bg-[#F5F570] text-[#241F20] rounded-full h-8 w-8 flex items-center justify-center font-bold': isToday(day.date)
                                }">
                                {{ day.date.getDate() }}
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-8 p-6 bg-gray-50 rounded-xl shadow-inner">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-gray-200">Twoje zbliżające się wydarzenia w: <span class="capitalize">{{ monthName }} {{ year }}</span></h2>
        <div v-if="filteredEvents.length > 0" class="space-y-4">
            <div v-for="event in filteredEvents" :key="event.id" class="p-5 border border-gray-200 rounded-lg bg-white shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <p class="font-bold text-lg text-gray-900">{{ new Date(event.selectedDate.replace(/-/g, '\/')).toLocaleDateString('pl-PL', { day: 'numeric', month: 'long' }) }} o {{ event.eventTime }}</p>
                    <span class="px-3 py-1 text-sm font-medium rounded-full"
                        :style="
                            (new Date(event.selectedDate.replace(/-/g, '\/')) > new Date() && new Date(event.selectedDate.replace(/-/g, '\/')).toDateString() !== new Date().toDateString()) || new Date(event.selectedDate.replace(/-/g, '\/')).toDateString() === new Date().toDateString()
                            ? { backgroundColor: '#F5F570', color: '#241F20' }
                            : { backgroundColor: '#38a169', color: '#FFFFFF' }"
                        >
                        {{ new Date(event.selectedDate.replace(/-/g, '\/')) > new Date() ? 'Nadchodzące' : (new Date(event.selectedDate.replace(/-/g, '\/')).toDateString() === new Date().toDateString() ? 'Dziś' : 'Ukończone') }}
                    </span>
                </div>
                <p class="text-gray-700 mt-2">{{ event.eventDescription }}</p>
                <span class="flex justify-end cursor-pointer">
                    <span @click="confirmDelete(event.id)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-red-500 hover:text-red-600 transition-colors">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m15 9-6 6"/>
                        <path d="m9 9 6 6"/>
                        </svg>
                    </span>
                </span>
            </div>
        </div>
        <div v-else class="text-center py-10 px-4 border-2 border-dashed border-gray-300 rounded-lg bg-white">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900">Brak wydarzeń</h3>
            <p class="mt-1 text-sm text-gray-500">Brak zaplanowanych wydarzeń w tym miesiącu.</p>
        </div>
    </div>

    <div v-if="popupOpen" class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Zaznacz wydarzenie</h3>
                <button @click="popupOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="text-2xl font-bold">&times;</span>
                </button>
            </div>
            <Form @submit.prevent="form.post('/profil/events/create', {
                onSuccess: () => {
                    popupOpen = false;
                    form.reset();
                }})" class="mt-4">
                <div class="py-4">
                    <p class="text-base text-gray-700">
                        Wybrana data: <span class="font-semibold">{{ selectedDateDisplay }}</span>
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        Wybierz godzinę, o której chcesz odbyć trening.
                        <input type="time" v-model="form.eventTime" name="eventTime" class="mt-2 border border-gray-300 rounded-lg px-2 py-1 w-full">
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        Krótki opis wydarzenia:
                        <textarea v-model="form.eventDescription" name="eventDescription" rows="3" class="mt-2 border border-gray-300 rounded-lg px-2 py-1 w-full" placeholder="Np. Trening z klientem/trenerem"></textarea>
                    </p>
                    <p class="mt-2">
                        <button type="submit" class="px-4 py-2 bg-[#F5F570] text-[#241F20] rounded-lg hover:bg-[#E6E65C] cursor-pointer transition-colors">
                            Zatwierdź
                        </button>
                    </p>
                </div>
            </Form>
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button @click="popupOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 cursor-pointer transition-colors mr-2">
                    Anuluj
                </button>
            </div>
        </div>
    </div>
</template>