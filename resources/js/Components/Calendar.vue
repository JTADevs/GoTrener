<script setup>
import { computed, ref } from 'vue';

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
            date: new Date(year, month, i)
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

const openPopup = (day) => {
    if (!day) return;
    if (isPast(day.date) && !isToday(day.date)) return;
    selectedDate.value = day.date.toISOString().split('T')[0];
    popupOpen.value = true;
};
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
                                (isPast(day.date) && !isToday(day.date)) ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-blue-50'
                            ]">
                            <span class="text-sm sm:text-base"
                                :class="{
                                    'text-gray-400': isPast(day.date) && !isToday(day.date),
                                    'text-gray-900': !isPast(day.date) || isToday(day.date),
                                    'bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center font-bold': isToday(day.date)
                                }">
                                {{ day.date.getDate() }}
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div v-if="popupOpen" class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Umów się na trening</h3>
                <button @click="popupOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="text-2xl font-bold">&times;</span>
                </button>
            </div>
            <div class="py-4">
                <p class="text-base text-gray-700">
                    Wybrana data: <span class="font-semibold">{{ selectedDate }}</span>
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Wybierz godzinę, o której chcesz odbyć trening.
                </p>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button @click="popupOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 cursor-pointer transition-colors mr-2">
                    Anuluj
                </button>
                <button @click="popupOpen = false" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 cursor-pointer transition-colors">
                    Zatwierdź
                </button>
            </div>
        </div>
    </div>
</template>