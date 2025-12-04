<script setup>
    import Layout from '../Layouts/Layout.vue';
    import { Form, router, useForm } from '@inertiajs/vue3';
    import { categories } from '../Data/Categories.js';
    import { ref, computed, onMounted } from 'vue';

    const showCategories = ref(false);
    const categorySearch = ref('');

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
</script>

<template>
    <Layout>
        <section class="flex flex-col min-h-[calc(100vh-200px)] bg-gray-100 text-gray-800">

            <!-- Hero Section -->
            <div class="relative w-full h-[50vh] bg-cover bg-center" style="background-image: url('/images/gym.jpg');">
                <div class="absolute inset-0 bg-opacity-50 flex flex-col justify-center items-center text-center text-white p-4">
                    <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight leading-tight">ODKRYJ SWÓJ POTENCJAŁ</h1>
                    <p class="mt-4 text-lg md:text-xl max-w-2xl">Znajdź idealnego trenera personalnego, który pomoże Ci osiągnąć Twoje cele fitness.</p>
                    <Form @submit.prevent="submitSearch" class="flex flex-col md:flex-row items-center justify-center gap-4 w-full max-w-3xl mt-8">
                        <div class="relative flex-grow w-full min-w-0">
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="location" v-model="search.location" placeholder="Miejscowość" class="w-full p-3 pl-12 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-shadow bg-white/90 text-gray-800 placeholder-gray-500"/>
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
            <div class="w-full py-16 bg-white">
                <div class="w-[80%] mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                    <div class="flex flex-col items-center space-y-4">
                        <div class="bg-[#F5F570] p-5 rounded-full shadow-lg">
                            <i class="fas fa-search fa-3x text-[#241F20]"></i>
                        </div>
                        <h3 class="text-2xl font-bold">1. Wyszukaj</h3>
                        <p class="text-gray-600">Znajdź trenera dopasowanego do Twoich potrzeb, specjalizacji i lokalizacji.</p>
                    </div>
                    <div class="flex flex-col items-center space-y-4">
                        <div class="bg-[#F5F570] p-5 rounded-full shadow-lg">
                            <i class="fas fa-calendar-alt fa-3x text-[#241F20]"></i>
                        </div>
                        <h3 class="text-2xl font-bold">2. Zaplanuj</h3>
                        <p class="text-gray-600">Skontaktuj się z trenerem i umów się na pierwszą sesję treningową.</p>
                    </div>
                    <div class="flex flex-col items-center space-y-4">
                        <div class="bg-[#F5F570] p-5 rounded-full shadow-lg">
                            <i class="fas fa-dumbbell fa-3x text-[#241F20]"></i>
                        </div>
                        <h3 class="text-2xl font-bold">3. Trenuj</h3>
                        <p class="text-gray-600">Realizuj swój plan, osiągaj cele i ciesz się lepszą formą pod okiem eksperta.</p>
                    </div>
                </div>
            </div>

            <!-- Recommended Trainers Section -->
            <div class="w-full py-20 bg-gray-100">
                <div class="w-[80%] mx-auto">
                    <h2 class="font-extrabold text-4xl text-center mb-12">Polecani Trenerzy</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10">
                        <div class="bg-white rounded-lg shadow-xl text-center p-6 transform hover:-translate-y-2 transition-transform duration-300">
                            <img src="../../../public/images/trener.jpg" alt="trener1" class="rounded-full w-32 h-32 mx-auto border-4 border-[#F5F570] object-cover">
                            <p class="text-xl font-semibold mt-4">Michał Sławek</p>
                            <p class="text-gray-500">Trener Siłowy</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-xl text-center p-6 transform hover:-translate-y-2 transition-transform duration-300">
                            <img src="../../../public/images/trener.jpg" alt="trener2" class="rounded-full w-32 h-32 mx-auto border-4 border-[#F5F570] object-cover">
                            <p class="text-xl font-semibold mt-4">Tymoteusz Palak</p>
                            <p class="text-gray-500">CrossFit</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-xl text-center p-6 transform hover:-translate-y-2 transition-transform duration-300">
                            <img src="../../../public/images/trener.jpg" alt="trener3" class="rounded-full w-32 h-32 mx-auto border-4 border-[#F5F570] object-cover">
                            <p class="text-xl font-semibold mt-4">Jakub Kurzacz</p>
                             <p class="text-gray-500">Kalistenika</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-xl text-center p-6 transform hover:-translate-y-2 transition-transform duration-300">
                            <img src="../../../public/images/trener.jpg" alt="trener4" class="rounded-full w-32 h-32 mx-auto border-4 border-[#F5F570] object-cover">
                            <p class="text-xl font-semibold mt-4">Sebastian Skubała</p>
                            <p class="text-gray-500">Dietetyk</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Why Us Section -->
            <div class="bg-white py-20">
                <div class="w-[80%] mx-auto flex flex-col md:flex-row items-center gap-12">
                    <div class="md:w-1/2">
                        <img src="../../../public/images/gym2.jpg" alt="Fitness" class="rounded-lg shadow-2xl w-full h-auto object-cover">
                    </div>
                    <div class="md:w-1/2">
                        <h2 class="text-4xl font-extrabold mb-6">Dlaczego Warto Nam Zaufać?</h2>
                        <ul class="space-y-4 text-lg text-gray-600">
                            <li class="flex items-start"><i class="fas fa-check-circle text-[#F5F570] mt-1 mr-3"></i><span><strong>Najlepsi Specjaliści:</strong> Baza zweryfikowanych i doświadczonych trenerów z całej Polski.</span></li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-[#F5F570] mt-1 mr-3"></i><span><strong>Elastyczność:</strong> Dopasuj treningi do swojego harmonogramu i budżetu.</span></li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-[#F5F570] mt-1 mr-3"></i><span><strong>Wsparcie i Motywacja:</strong> Znajdź nie tylko trenera, ale i mentora na swojej drodze do celu.</span></li>
                            <li class="flex items-start"><i class="fas fa-check-circle text-[#F5F570] mt-1 mr-3"></i><span><strong>Prawdziwe Opinie:</strong> Sprawdzaj oceny i opinie innych użytkowników, by dokonać najlepszego wyboru.</span></li>
                        </ul>
                    </div>
                </div>
            </div>

        </section>
    </Layout>
</template>
