<script setup>
import { Form, useForm } from '@inertiajs/vue3';
import { dimensions } from '../Data/Dimensions';
import { computed } from 'vue';

    const { user } = defineProps({
        user: Object,
    });

    const statsForm = useForm({
        period: '1_week',
        weight: '',
        height: '',
        neckCircumference: '',
        chestCircumference: '',
        waistCircumference: '',
        abdomenCircumference: '',
        hipCircumference: '',
        bicepsCircumference: '',
        wristCircumference: '',
        thighCircumference: '',
        calfCircumference: '',
        ankleCircumference: '',
    });
    
    const getNextUpdateDate = (period, lastUpdate) => {
        const nextUpdate = new Date(lastUpdate);
        switch (period) {
            case '1_day':
                nextUpdate.setDate(nextUpdate.getDate() + 1);
                break;
            case '1_week':
                nextUpdate.setDate(nextUpdate.getDate() + 7);
                break;
            case '2_weeks':
                nextUpdate.setDate(nextUpdate.getDate() + 14);
                break;
            case '1_month':
                nextUpdate.setMonth(nextUpdate.getMonth() + 1);
                break;
        }
        return nextUpdate;
    };

    const isPeriodElapsed = computed(() => {
        if (!user.statsUpdatePeriod || !user.statsUpdatedAt) {
            return true;
        }
        const nextUpdate = getNextUpdateDate(user.statsUpdatePeriod, user.statsUpdatedAt);
        return new Date() >= nextUpdate;
    });

    const nextUpdateDate = computed(() => {
        if (!user.statsUpdatePeriod || !user.statsUpdatedAt) {
            return '';
        }
        const nextUpdate = getNextUpdateDate(user.statsUpdatePeriod, user.statsUpdatedAt);
        return nextUpdate.toLocaleDateString('pl-PL', { day: 'numeric', month: 'long', year: 'numeric' });
    });

    let periodText = computed(() => {
        switch (user.statsUpdatePeriod) {
            case '1_day':
                return 'co 1 dzień';
            case '1_week':
                return 'co 1 tydzień';
            case '2_weeks':
                return 'co 2 tygodnie';
            case '1_month':
                return 'co 1 miesiąc';
            default:
                return '';
        }
    });

    

</script>

<template>
    <div class="bg-[#241F20] text-white p-4 md:p-8 rounded-xl shadow-2xl">
        <h1 class="text-3xl md:text-4xl font-bold text-center mb-6 text-[#F5F570]">
            Statystyki: <span class="text-white">{{ user.name }}</span>
        </h1>
        
        <div v-if="isPeriodElapsed" class="max-w-4xl mx-auto">
            <Form @submit.prevent="statsForm.post('/profil/updateStats')" class="space-y-8">
                <fieldset class="p-6 border-2 border-[#F5F570] rounded-2xl">
                    <legend class="px-4 text-xl font-bold text-[#F5F570]">Pomiary Ciała</legend>
                    
                    <div class="mb-6 bg-transparent p-4 rounded-lg" v-if="!user.statsUpdatedAt">
                        <label for="period" class="block text-sm font-medium text-white mb-2">Wybierz okres uzupełnienia pomiarów (po wybraniu nie będzie możliwości edycji)</label>
                        <select id="period" class="w-full bg-[#241F20] border border-[#F5F570] text-white rounded-lg p-2 focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570]" v-model="statsForm.period" required>
                            <option value="1_day">Co 1 dzień</option>
                            <option value="1_week">Co 1 tydzień</option>
                            <option value="2_weeks">Co 2 tygodnie</option>
                            <option value="1_month">Co 1 miesiąc</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="dimension in dimensions" :key="dimension.key" class="flex flex-col">
                            <label :for="dimension.key" class="block text-sm font-medium text-white mb-2">{{ dimension.label }}</label>
                            <input 
                                type="number" 
                                :id="dimension.key" 
                                v-model="statsForm[dimension.key]" 
                                :placeholder="dimension.label" 
                                class="form-input bg-[#241F20] border border-[#F5F570] text-white p-3 rounded-lg focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] transition"
                                min="0"
                                max="500"
                                maxlength="3"
                            >
                        </div>
                    </div>
                </fieldset>
                
                <div class="text-center">
                    <button type="submit" class="w-full md:w-auto px-10 py-3 text-lg font-bold bg-[#F5F570] text-[#241F20] rounded-xl cursor-pointer hover:bg-yellow-300 transition-transform transform hover:scale-105">
                        Zapisz Pomiary
                    </button>
                </div>
            </Form>
        </div>
        <div v-else class="max-w-4xl mx-auto text-center p-6 border-2 border-[#F5F570] rounded-2xl">
            <p class="text-xl">Możliwość dodania kolejnych pomiarów będzie dostępna po upływie ustalonego okresu <b class="text-[#F5F570] font-bold">{{ periodText }}</b>.</p>
            <p class="text-lg text-gray-400 mt-2">Następna aktualizacja możliwa: {{ nextUpdateDate }}</p>
            <p class="text-lg text-[#F5F570] font-bold">Jest możliwość zmiany okresów po zresetowaniu wszystkich statystyk.</p>
        </div>
    </div>
</template>