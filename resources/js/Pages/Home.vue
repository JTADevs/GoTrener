<script setup>
    import Layout from '../Layouts/Layout.vue';
    import { Form, Link, router, useForm } from '@inertiajs/vue3';
    import { categories } from '../Data/Categories.js';
    import { ref, computed, onMounted } from 'vue';
    import cities from '../Data/Cities.json';

    const showCategories = ref(false);
    const categorySearch = ref('');
    const citySuggestions = ref([]);

    const filteredCategories = computed(() => {
        return categories.filter(category =>
            category.toLowerCase().includes(categorySearch.value.toLowerCase())
        );
    });

    onMounted(() => {
        document.body.addEventListener('click', () => {
            showCategories.value = false;
        });
    });

    const search = useForm({
        fullname: '',
        location: '',
        category: []
    });

    const submitSearch = () => {
        search.get('/trainers', { 
            preserveState: true, 
            replace: true,
        });
    }

    const { premiumTrainers } = defineProps({
        premiumTrainers: Object,
    });

    const handleCityChange = (e) => {
        const query = e.target.value;

        if (query.length < 2) {
            citySuggestions.value = [];
            return;
        }

        const replacements = {
            'ą': 'a', 'ć': 'c', 'ę': 'e', 'ł': 'l', 'ń': 'n', 'ó': 'o', 'ś': 's', 'ź': 'z', 'ż': 'z',
            'Ą': 'A', 'Ć': 'C', 'Ę': 'E', 'Ł': 'L', 'Ń': 'N', 'Ó': 'O', 'Ś': 'S', 'Ź': 'Z', 'Ż': 'Z'
        };

        const normalize = (text) => 
            text.split('').map(char => replacements[char] || char).join('').toLowerCase();

        const normalizedQuery = normalize(query);

        const seen = new Set();
        const filtered = cities.filter(city => {
            if (normalize(city.Name).includes(normalizedQuery) && !seen.has(city.Name)) {
                seen.add(city.Name);
                return true;
            }
            return false;
        });

        citySuggestions.value = filtered.slice(0, 15);
    }
</script>

<template>
    <Layout>
        <section class="flex flex-col min-h-[calc(100vh-200px)] bg-gray-100 text-gray-800">

            <!-- Hero Section -->
            <div class="relative w-full min-h-[60vh] md:min-h-[50vh] bg-cover bg-center flex items-center justify-center" style="background-image: url('/images/gym.jpg');">
                <div class="absolute inset-0 bg-black/50"></div>
                <div class="relative z-10 flex flex-col justify-center items-center text-center text-white p-4 w-full">
                    <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold tracking-tight leading-tight drop-shadow-lg">ODKRYJ SWÓJ POTENCJAŁ</h1>
                    <p class="mt-4 text-sm sm:text-lg md:text-xl max-w-2xl drop-shadow-md px-4">Znajdź idealnego trenera personalnego, który pomoże Ci osiągnąć Twoje cele fitness.</p>
                    <Form @submit.prevent="submitSearch" class="flex flex-col md:flex-row items-center justify-center gap-4 w-full px-4 md:px-0 max-w-3xl mt-8">
                        <div class="relative flex-grow w-full min-w-0">
                            <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <input type="text" id="city" v-model="search.location" @input="handleCityChange" list="city-suggestions" placeholder="Miasto" class="w-full p-3 pl-12 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-shadow bg-white/90 text-gray-800 placeholder-gray-500">
                            <datalist id="city-suggestions">
                                <option v-for="city in citySuggestions" :key="city.Id" :value="city.Name" class="bg-[#241F20]"></option>
                            </datalist>
                        </div>
                        <div class="relative flex-grow w-full min-w-0">
                            <i class="fa-solid fa-tags absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <button @click.stop="showCategories = !showCategories" type="button" class="w-full text-left p-3 pl-12 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-shadow overflow-hidden bg-white/90 text-gray-800 placeholder-gray-500">
                                <span v-if="search.category.length === 0">Kategoria</span>
                                <span v-else class="text-sm block truncate">{{ search.category.join(', ') }}</span>
                            </button>
                            <div v-if="showCategories" class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg" @click.stop>
                                <div class="p-2">
                                    <input type="text" placeholder="Szukaj kategorii..." class="w-full p-2 border-b border-gray-200 focus:outline-none cursor-pointer" v-model="categorySearch">
                                </div>
                                <div class="max-h-60 overflow-y-auto">
                                    <label v-for="category in filteredCategories" :key="category" class="flex items-center p-2 hover:bg-gray-100 cursor-pointer text-gray-800">
                                        <input type="checkbox" :value="category" v-model="search.category" class="mr-2 cursor-pointer">
                                        {{ category }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="w-full md:w-auto px-8 py-3 bg-[#F5F570] text-[#241F20] font-semibold rounded-full hover:bg-[#241F20] hover:text-[#F5F570] transition-all duration-300 transform hover:scale-105 shadow-md cursor-pointer">
                            <i class="fa-solid fa-search mr-2"></i>Szukaj
                        </button>
                    </Form>
                </div>
            </div>

            <!-- How It Works Section -->
            <div class="w-full py-12 md:py-16 bg-white">
                <div class="w-[90%] md:w-[80%] max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 text-center">
                    <div class="flex flex-col items-center space-y-4">
                        <div class="bg-[#F5F570] p-5 rounded-full shadow-lg transform hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-search fa-3x text-[#241F20]"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold">1. Wyszukaj</h3>
                        <p class="text-gray-600 text-sm md:text-base">Znajdź trenera dopasowanego do Twoich potrzeb, specjalizacji i lokalizacji.</p>
                    </div>
                    <div class="flex flex-col items-center space-y-4">
                        <div class="bg-[#F5F570] p-5 rounded-full shadow-lg transform hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-calendar-alt fa-3x text-[#241F20]"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold">2. Zaplanuj</h3>
                        <p class="text-gray-600 text-sm md:text-base">Skontaktuj się z trenerem i umów się na pierwszą sesję treningową.</p>
                    </div>
                    <div class="flex flex-col items-center space-y-4">
                        <div class="bg-[#F5F570] p-5 rounded-full shadow-lg transform hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-dumbbell fa-3x text-[#241F20]"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold">3. Trenuj</h3>
                        <p class="text-gray-600 text-sm md:text-base">Realizuj swój plan, osiągaj cele i ciesz się lepszą formą pod okiem eksperta.</p>
                    </div>
                </div>
            </div>

            <!-- Recommended Trainers Section -->
            <div class="w-full py-12 md:py-20 bg-gray-100" v-if="premiumTrainers">
                <div class="w-[90%] md:w-[80%] max-w-7xl mx-auto">
                    <h2 class="font-extrabold text-3xl md:text-4xl text-center mb-8 md:mb-12">Polecani Trenerzy</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-10">
                        <Link :href="`/trainer/${trainer.uid}`" v-for="trainer in premiumTrainers" :key="trainer.uid" class="group ">
                            <div class="bg-white rounded-lg shadow-xl text-center p-6 transform group-hover:-translate-y-2 transition-transform duration-300 flex flex-col items-center h-full border-2 border-[#F5F570]">
                                <img :src="trainer.imageURL || '/images/no_user.png'" alt="trener" class="rounded-full w-32 h-32 mx-auto border-4 border-[#F5F570] object-cover">
                                <p class="text-xl font-semibold mt-4">{{ trainer.name }}</p>
                                <p class="text-gray-500">
                                    <i class="fa-solid fa-location-dot mr-2 text-yellow-500"></i>
                                    {{ trainer.city }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Why Us Section -->
            <div class="bg-white py-12 md:py-20">
                <div class="w-[90%] md:w-[80%] max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-8 md:gap-12">
                    <div class="w-full md:w-1/2">
                        <img src="../../../public/images/gym2.jpg" alt="Fitness" class="rounded-lg shadow-2xl w-full h-auto object-cover">
                    </div>
                    <div class="w-full md:w-1/2">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-4 md:mb-6">Dlaczego Warto Nam Zaufać?</h2>
                        <ul class="space-y-4 text-base md:text-lg text-gray-600">
                            <li class="flex items-start"><i class="fas fa-check-circle text-yellow-500 mt-1 mr-3"></i><span><strong>Najlepsi Specjaliści:</strong> Baza zweryfikowanych i doświadczonych trenerów z całej Polski.</span></li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-yellow-500 mt-1 mr-3"></i><span><strong>Elastyczność:</strong> Dopasuj treningi do swojego harmonogramu i budżetu.</span></li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-yellow-500 mt-1 mr-3"></i><span><strong>Wsparcie i Motywacja:</strong> Znajdź nie tylko trenera, ale i mentora na swojej drodze do celu.</span></li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-yellow-500 mt-1 mr-3"></i><span><strong>Prawdziwe Opinie:</strong> Sprawdzaj oceny i opinie innych użytkowników, by dokonać najlepszego wyboru.</span></li>
                        </ul>
                    </div>
                </div>
            </div>

        </section>
    </Layout>
</template>
