<script setup>
    import { ref, onMounted } from 'vue';

    const props = defineProps({
        user: Object,
    });

    onMounted(() => {
        if (props.user.role !== 'trainer') {
            window.location.href = '/';
        }
    });

    const selectedPlan = ref(null);

    const plans = [
        {
            id: 1,
            name: 'Miesięczny',
            price: '19.99',
            duration: 'za miesiąc',
            period: 'msc',
            popular: false,
            desc: 'Dla tych, którzy chcą spróbować.'
        },
        {
            id: 2,
            name: 'Kwartalny',
            price: '49.99',
            duration: 'za 3 miesiące',
            period: '3 msc',
            popular: true,
            desc: 'Najczęściej wybierany pakiet.',
            savings: 'Oszczędzasz 17%'
        },
        {
            id: 3,
            name: 'Półroczny',
            price: '89.99',
            duration: 'za 6 miesięcy',
            period: '6 msc',
            popular: false,
            desc: 'Długoterminowa współpraca.',
            savings: 'Oszczędzasz 25%'
        },
        {
            id: 4,
            name: 'Roczny',
            price: '159.99',
            duration: 'za rok',
            period: 'rok',
            popular: false,
            desc: 'Maksymalne korzyści.',
            savings: 'Oszczędzasz 33%'
        },
    ];

    const selectPlan = (plan) => {
        selectedPlan.value = plan;
        setTimeout(() => {
            const summaryElement = document.getElementById('plan-summary');
            if (summaryElement) {
                summaryElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 100);
    };

    const clearSelection = () => {
        selectedPlan.value = null;
    };

    const purchasePlan = () => {
        if (!selectedPlan.value) return;
        alert(`Rozpoczynam płatność za: ${selectedPlan.value.name} - ${selectedPlan.value.price} zł`);
        // Tutaj logika integracji z płatnościami
    };
</script>

<template>
    <div class="py-6 space-y-8 pb-24">
        <div class="text-center space-y-4">
            <h2 class="text-3xl font-bold text-gray-800">Promuj swój profil i zdobywaj nowych podopiecznych</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Wyróżnij się na tle innych trenerów. Dzięki promowaniu zyskujesz <strong>wyróżnienie na stronie głównej</strong>, <strong>pierwsze miejsce w wyszukiwarce</strong> oraz <strong>większą widoczność profilu</strong>.
            </p>
        </div>

        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-4">
            <div v-for="plan in plans" :key="plan.id" 
                class="relative flex flex-col p-6 bg-white rounded-2xl shadow-lg border-2 transition-all duration-300 cursor-pointer"
                :class="[
                    plan.popular ? 'border-[#F5F570]' : 'border-transparent',
                    selectedPlan?.id === plan.id ? 'ring-4 ring-[#F5F570] transform scale-105' : 'hover:border-gray-200 hover:-translate-y-2'
                ]"
                @click="selectPlan(plan)"
            >
                <div v-if="plan.popular" class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-[#F5F570] text-gray-900 px-4 py-1 rounded-full text-sm font-bold shadow-md">
                    POLECANY
                </div>
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-800">{{ plan.name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ plan.desc }}</p>
                </div>

                <div class="flex items-baseline" :class="plan.savings ? 'mb-2' : 'mb-6'">
                    <span class="text-3xl font-bold text-gray-900">{{ plan.price }}</span>
                    <span class="text-lg text-gray-600 font-medium ml-1">zł</span>
                    <span class="block text-gray-400 text-sm ml-2">/ {{ plan.period }}</span>
                </div>

                <div v-if="plan.savings" class="mb-6">
                    <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-1 rounded-full">
                        {{ plan.savings }}
                    </span>
                </div>

                <ul class="space-y-3 mb-8 flex-1">
                    <li class="flex items-start space-x-3 text-sm text-gray-600">
                        <i class="fa-solid fa-check text-green-500 mt-1"></i>
                        <span>Promowanie na stronie głównej</span>
                    </li>
                    <li class="flex items-start space-x-3 text-sm text-gray-600">
                        <i class="fa-solid fa-check text-green-500 mt-1"></i>
                        <span>Wyróżnienie w wyszukiwarce</span>
                    </li>
                    <li class="flex items-start space-x-3 text-sm text-gray-600">
                        <i class="fa-solid fa-check text-green-500 mt-1"></i>
                        <span>Oznaczenie "Promowany"</span>
                    </li>
                </ul>

                <button class="w-full py-3 px-4 rounded-xl font-bold text-gray-900 transition-colors duration-200 cursor-pointer hover:bg-[#241F20] hover:text-[#F5F570]"
                    :class="selectedPlan?.id === plan.id ? 'bg-[#F5F570]' : 'bg-gray-100'">
                    {{ selectedPlan?.id === plan.id ? 'Wybrano' : 'Wybierz' }}
                </button>
            </div>
        </div>

        <!-- Selected Plan Summary & Actions -->
        <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 translate-y-4"
            enter-to-class="transform opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform opacity-100 translate-y-0"
            leave-to-class="transform opacity-0 translate-y-4"
        >
            <div v-if="selectedPlan" id="plan-summary" class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-50 md:sticky md:bottom-4 md:mx-4 md:rounded-2xl md:shadow-xl md:border-none">
                <div class="max-w-[1000px] mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <div class="bg-gray-100 p-3 rounded-xl hidden md:block">
                            <i class="fa-solid fa-cart-shopping text-xl text-gray-700"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Wybrany plan:</p>
                            <h4 class="text-lg font-bold text-gray-900 leading-tight">
                                {{ selectedPlan.name }} 
                                <span class="text-[#F5F570] mx-1">•</span> 
                                {{ selectedPlan.price }} zł / {{ selectedPlan.period }}
                            </h4>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <button @click="clearSelection" 
                            class="px-6 py-3 rounded-xl font-bold text-gray-600 hover:bg-gray-100 hover:text-red-500 transition-colors duration-200 flex-1 md:flex-none border border-gray-200 cursor-pointer"
                        >
                            <span class="hidden md:inline">Usuń wybór</span>
                            <span class="md:hidden">Anuluj</span>
                        </button>
                        
                        <button @click="purchasePlan" 
                            class="px-8 py-3 rounded-xl font-bold text-[#241F20] bg-[#F5F570] hover:bg-[#e4e46a] shadow-lg shadow-yellow-200/50 transition-all duration-200 transform hover:-translate-y-0.5 flex-1 md:flex-none flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span>Wykup teraz</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <div v-if="selectedPlan" class="h-24 md:hidden"></div>
    </div>
</template>
