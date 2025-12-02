<script setup>
    import Layout from '@/Layouts/Layout.vue';
    import { Link, router } from '@inertiajs/vue3';

    const { trainers } = defineProps({
        trainers: Object,
    });

    const goTo = (page) => {
        router.get('/trainers', { page }, { preserveScroll: true });
    };

    const goToProfile = (trainerId) => {
        router.get(`/trainer/${trainerId}`);
    };
</script>

<template>
    <Layout>
        <div class="bg-gray-50">
            <div class="max-w-[1200px] mx-auto p-4 min-h-[calc(100vh-200px)]">
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
                        <div class="flex text-yellow-400 text-lg mt-auto">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-regular fa-star"></i>
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
