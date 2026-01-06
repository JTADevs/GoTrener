<script setup>
import { Form, router, useForm } from '@inertiajs/vue3';
import { dimensions } from '../Data/Dimensions';
import { computed, ref, onMounted } from 'vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

    const { user } = defineProps({
        user: Object,
    });

    const statsForm = useForm({
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

    function resetStats()
    {
        if (!confirm("Czy napewno chcesz zresetować historię statystyk?")) return;
        router.post('/profil/resetStats');
    }

    // Charting Logic
    const selectedDimension = ref('weight');
    const selectedRange = ref('all'); // all, 1w, 1m, 3m, 6m, 1y

    const ranges = [
        { key: '1w', label: '1 tydzień' },
        { key: '1m', label: '1 miesiąc' },
        { key: '3m', label: '3 miesiące' },
        { key: '6m', label: '6 miesięcy' },
        { key: '1y', label: '1 rok' },
        { key: 'all', label: 'Całość' },
    ];

    const filteredHistory = computed(() => {
        if (!user.statsHistory || user.statsHistory.length === 0) return [];
        
        if (selectedRange.value === 'all') return user.statsHistory;

        const now = new Date();
        const cutoffDate = new Date();

        switch(selectedRange.value) {
            case '1w':
                cutoffDate.setDate(now.getDate() - 7);
                break;
            case '1m':
                cutoffDate.setMonth(now.getMonth() - 1);
                break;
            case '3m':
                cutoffDate.setMonth(now.getMonth() - 3);
                break;
            case '6m':
                cutoffDate.setMonth(now.getMonth() - 6);
                break;
            case '1y':
                cutoffDate.setFullYear(now.getFullYear() - 1);
                break;
        }

        return user.statsHistory.filter(entry => new Date(entry.id) >= cutoffDate);
    });
    
    const chartData = computed(() => {
        const history = filteredHistory.value;
        if (history.length === 0) return { labels: [], datasets: [] };
        
        const labels = history.map(entry => entry.id); // Dates
        const data = history.map(entry => entry[selectedDimension.value] || null);
        
        return {
            labels: labels,
            datasets: [
                {
                    label: dimensions.find(d => d.key === selectedDimension.value)?.label || selectedDimension.value,
                    backgroundColor: '#F5F570',
                    borderColor: '#F5F570',
                    data: data,
                    fill: false,
                    tension: 0.1
                }
            ]
        }
    });

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                grid: {
                    color: '#4B5563'
                },
                ticks: {
                    color: 'white'
                }
            },
            x: {
                grid: {
                    color: '#4B5563'
                },
                ticks: {
                    color: 'white'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: 'white'
                }
            }
        }
    };

    const currentStats = computed(() => {
        const history = filteredHistory.value;
        if (history.length === 0) return null;
        
        // Get non-null values for the selected dimension within the filtered range
        const validEntries = history.filter(entry => entry[selectedDimension.value] !== undefined && entry[selectedDimension.value] !== '' && entry[selectedDimension.value] !== null);
        
        if (validEntries.length === 0) return null;

        const first = parseFloat(validEntries[0][selectedDimension.value]);
        const last = parseFloat(validEntries[validEntries.length - 1][selectedDimension.value]);
        const diff = last - first;
        
        return {
            first,
            last,
            diff: diff.toFixed(1),
            trend: diff > 0 ? 'up' : (diff < 0 ? 'down' : 'neutral')
        };
    });

</script>

<template>
    <div class="bg-[#241F20] text-white p-4 md:p-8 rounded-xl shadow-2xl">
        <h1 class="text-3xl md:text-4xl font-bold text-center mb-6 text-[#F5F570]">
            Statystyki: <span class="text-white">{{ user.name }}</span>
        </h1>
        
        <div class="max-w-4xl mx-auto space-y-12">
            <!-- Form Section -->
            <Form @submit.prevent="statsForm.post('/profil/updateStats')" class="space-y-8">
                <fieldset class="p-6 border-2 border-[#F5F570] rounded-2xl">
                    <legend class="px-4 text-xl font-bold text-[#F5F570]">Pomiary Ciała (Dzisiaj)</legend>
                    <p class="mb-4 text-gray-400">Wprowadź wartości, które chcesz zaktualizować na dzień dzisiejszy. Puste pola nie nadpiszą istniejących wartości.</p>

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
                                step="0.1"
                            >
                        </div>
                    </div>
                </fieldset>
                
                <div class="text-center space-y-4">
                    <button type="submit" class="w-full md:w-auto px-10 py-3 text-lg font-bold bg-[#F5F570] text-[#241F20] rounded-xl cursor-pointer hover:bg-yellow-300 transition-transform transform hover:scale-105">
                        Zapisz Pomiary
                    </button>
                    
                    <div v-if="user.statsUpdatedAt" class="pt-4 border-t border-gray-700">
                         <button class="text-red-400 hover:text-red-300 underline text-sm cursor-pointer" @click="resetStats()" type="button">Zresetuj historię statystyk</button>
                    </div>
                </div>
            </Form>

            <!-- Charts Section -->
            <div v-if="user.statsHistory && user.statsHistory.length > 0" class="border-t border-gray-700 pt-8">
                <h2 class="text-2xl font-bold text-[#F5F570] mb-6 text-center">Postępy</h2>
                
                <div class="flex flex-col space-y-6">
                    <!-- Controls -->
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 md:space-x-4">
                        <div class="w-full md:w-1/2">
                            <label for="chart-dimension" class="block text-sm text-gray-400 mb-1">Wybierz parametr</label>
                            <select id="chart-dimension" v-model="selectedDimension" class="w-full bg-[#241F20] border border-[#F5F570] text-white rounded-lg p-2 focus:ring-2 focus:ring-[#F5F570]">
                                <option v-for="dim in dimensions" :key="dim.key" :value="dim.key">
                                    {{ dim.label }}
                                </option>
                            </select>
                        </div>

                         <!-- Time Range Selector -->
                        <div class="w-full md:w-1/2">
                            <label for="time-range" class="block text-sm text-gray-400 mb-1">Zakres czasu</label>
                            <select id="time-range" v-model="selectedRange" class="w-full bg-[#241F20] border border-[#F5F570] text-white rounded-lg p-2 focus:ring-2 focus:ring-[#F5F570]">
                                <option v-for="range in ranges" :key="range.key" :value="range.key">
                                    {{ range.label }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Stats Summary -->
                    <div v-if="currentStats" class="flex justify-around items-center bg-[#241F20] border border-gray-700 p-4 rounded-xl">
                        <div class="text-center">
                            <span class="block text-xs text-gray-400">Początek okresu</span>
                            <span class="text-lg font-bold">{{ currentStats.first }}</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-xs text-gray-400">Obecnie</span>
                            <span class="text-lg font-bold">{{ currentStats.last }}</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-xs text-gray-400">Zmiana</span>
                            <span class="text-lg font-bold" :class="{'text-green-500': currentStats.trend === 'down' && selectedDimension === 'weight', 'text-red-500': currentStats.trend === 'up' && selectedDimension === 'weight', 'text-[#F5F570]': selectedDimension !== 'weight'}">
                                {{ currentStats.diff > 0 ? '+' : '' }}{{ currentStats.diff }}
                            </span>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="h-[400px] w-full bg-[#1a1718] p-4 rounded-xl border border-gray-800">
                         <Line :data="chartData" :options="chartOptions" />
                    </div>
                </div>
            </div>
             <div v-else class="text-center text-gray-500 pt-8">
                Brak wystarczających danych do wyświetlenia wykresów. Dodaj pierwsze pomiary!
            </div>
        </div>
    </div>
</template>