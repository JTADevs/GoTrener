<script setup>
    import Layout from '@/Layouts/Layout.vue';
    import { Form, Link, router, useForm } from '@inertiajs/vue3';
    import { categories } from '../Data/Categories.js';
    import { ref, computed, onMounted } from 'vue';

    const { trainers, filters } = defineProps({
        trainers: Object,
        filters: Object,
    });

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

    const getAverageRating = (reviews) => {
        if (!reviews || reviews.length === 0) {
            return 0;
        }
        const total = reviews.reduce((acc, review) => acc + review.rating, 0);
        return Math.round(total / reviews.length);
    };

    const goTo = (page) => {
        const currentFilters = {
            fullname: filters.fullname,
            location: filters.location,
            category: filters.category,
        };
        
        router.get('/trainers', { ...currentFilters, page }, { preserveScroll: true });
    };

    const goToProfile = (trainerId) => {
        router.get(`/trainer/${trainerId}`);
    };

    const search = useForm({
        fullname: filters.fullname || '',
        location: filters.location || '',
        category: filters.category || []
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
        <div class="bg-gray-50">
            <div class="max-w-[1200px] mx-auto p-4 min-h-[calc(100vh-200px)]">
                <div class="bg-white p-8 rounded-xl shadow-lg mb-12 mt-4">
                    <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Znajdź swojego trenera</h2>
                    <Form @submit.prevent="submitSearch" class="flex flex-col md:flex-row items-center justify-center gap-4">
                        <div class="relative flex-grow w-full min-w-0">
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="fullname" v-model="search.fullname" placeholder="Imię i nazwisko" class="w-full p-3 pl-12 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-shadow"/>
                        </div>
                        <div class="relative flex-grow w-full min-w-0">
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="location" v-model="search.location" placeholder="Miejscowość" class="w-full p-3 pl-12 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-shadow"/>
                        </div>
                        <div class="relative flex-grow w-full min-w-0">
                            <i class="fa-solid fa-tags absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <button @click.stop="showCategories = !showCategories" type="button" class="w-full text-left p-3 pl-12 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-shadow overflow-hidden">
                                <span v-if="search.category.length === 0">Kategoria</span>
                                <span v-else class="text-sm block truncate">{{ search.category.join(', ') }}</span>
                            </button>
                            <div v-if="showCategories" class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg" @click.stop>
                                <div class="p-2">
                                    <input type="text" placeholder="Szukaj kategorii..." class="w-full p-2 border-b border-gray-200 focus:outline-none" v-model="categorySearch">
                                </div>
                                <div class="max-h-60 overflow-y-auto">
                                    <label v-for="category in filteredCategories" :key="category" class="flex items-center p-2 hover:bg-gray-100 cursor-pointer">
                                        <input type="checkbox" :value="category" v-model="search.category" class="mr-2">
                                        {{ category }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="w-full md:w-auto px-8 py-3 bg-[#241F20] text-white font-semibold rounded-full hover:bg-[#F5F570] hover:text-[#241F20] transition-all duration-300 transform hover:scale-105 shadow-md">
                            <i class="fa-solid fa-search mr-2"></i>Szukaj
                        </button>
                    </Form>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Nasi Trenerzy</h1>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div 
                        v-for="trainer in trainers.data" 
                        :key="trainer.uid" 
                        class="p-6 flex flex-col items-center bg-white shadow-lg rounded-xl transform hover:-translate-y-2 transition-transform duration-300 cursor-pointer"
                        @click="goToProfile(trainer.uid)"
                    >
                        <img 
                            :src="trainer.imageURL || '/images/no_user.png'" 
                            alt="zdjęcie trenera" 
                            class="w-24 h-24 rounded-full mb-4 object-cover border-4 border-gray-200"
                        />
                        <h2 class="font-bold text-xl text-gray-800">{{ trainer.name }}</h2>
                        <div class="flex items-center font-semibold text-gray-600 my-2">
                            <i class="fa-solid fa-location-dot mr-2 text-yellow-500"></i>
                            {{ trainer.city }}
                        </div>
                        <div class="flex flex-wrap gap-2 my-3 justify-center">
                            <span
                                v-for="cat in trainer.category"
                                :key="cat"
                                class="text-xs bg-[#F5F570] text-gray-800 rounded-full px-3 py-1 font-semibold"
                            >
                                {{ cat }}
                            </span>
                        </div>
                        <div class="flex text-yellow-400 text-lg mt-auto items-center">
                            <template v-if="trainer.reviews && trainer.reviews.length > 0">
                                <i v-for="star in 5" :key="star" class="fa-star" :class="{
                                    'fa-solid': star <= getAverageRating(trainer.reviews),
                                    'fa-regular': star > getAverageRating(trainer.reviews)
                                }"></i>
                                <span class="text-gray-600 text-sm ml-2">({{ trainer.reviews.length }})</span>
                            </template>
                            <template v-else>
                                <i v-for="star in 5" :key="star" class="fa-regular fa-star"></i>
                                <span class="text-gray-600 text-sm ml-2">(0)</span>
                            </template>
                        </div>
                        <Link :href="`/trainer/${trainer.uid}`" class="mt-4 px-6 py-2 bg-[#241F20] text-white font-semibold rounded-full hover:bg-[#F5F570] hover:text-[#241F20] transition-colors duration-300 cursor-pointer">
                            Zobacz profil
                        </Link>
                    </div>
                </div>

                <div class="flex gap-4 mt-8 justify-center">
                    <button 
                        v-if="trainers.prevPage"
                        @click.stop="goTo(trainers.prevPage)"
                        class="px-5 py-2 bg-[#241F20] text-white font-semibold rounded-full hover:bg-[#F5F570] hover:text-[#241F20] transition-colors duration-300 disabled:opacity-50 cursor-pointer"
                        :disabled="!trainers.prevPage"
                    >
                        <i class="fa-solid fa-arrow-left mr-2"></i> Poprzednia
                    </button>

                    <button 
                        v-if="trainers.nextPage"
                        @click.stop="goTo(trainers.nextPage)"
                        class="px-5 py-2 bg-[#241F20] text-white font-semibold rounded-full hover:bg-[#F5F570] hover:text-[#241F20] transition-colors duration-300 disabled:opacity-50 cursor-pointer"
                        :disabled="!trainers.nextPage"
                    >
                        Następna <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>