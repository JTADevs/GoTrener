<script setup>
    import Layout from '../Layouts/Layout.vue';
    import { computed, ref } from 'vue';
    import { useForm, usePage } from '@inertiajs/vue3';

    const { trainer } = defineProps({
        trainer: Object,
    });

    const page = usePage();
    const loggedUser = computed(() => page.props.loggedUser ?? {});

    const reviewForm = useForm({
        rating: 0,
        comment: '',
    });

    const hasUserReviewed = computed(() => {
        if (!loggedUser.value?.uid || !trainer.reviews || !Array.isArray(trainer.reviews)) {
            return false;
        }
        return trainer.reviews.some(review => review.userId === loggedUser.value.uid);
    });


    const averageRating = computed(() => {
        const reviews = trainer.reviews;
        if (!reviews || !Array.isArray(reviews) || reviews.length === 0) {
            console.log('Brak opinii dla tego trenera.')
            return 0;
        }
        
        const totalStars = reviews.reduce((sum, review) => sum + review.rating, 0);
        const totalReviews = reviews.length;
        
        return totalReviews > 0 ? (totalStars / totalReviews) : 0;
    });

    const hoverRating = ref(0);

    const submitReview = () => {
        reviewForm.post(`/trainer/review/${trainer.uid}`);
    };
</script>

<template>
    <Layout>
        <div class="bg-gray-50 min-h-[calc(100vh-200px)] py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-2xl overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col items-center text-center">
                        <img :src="trainer.imageURL || '/images/no_user.png'" alt="zdjecie profilowe" class="w-48 h-48 rounded-full object-cover border-4 border-[#F5F570]" />
                        <h1 class="text-4xl font-bold text-gray-800 mt-4">{{ trainer.name }}</h1>
                        <div class="text-gray-600 mt-2">
                            <i class="fa-solid fa-location-dot mr-2 text-yellow-500"></i>
                            {{ trainer.city }}
                        </div>
                    </div>

                    <div class="mt-10">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">O mnie</h2>
                        <p class="text-gray-700 leading-relaxed">
                            {{ trainer.bio || 'Trener nie dodał jeszcze opisu.' }}
                        </p>
                    </div>

                    <div class="mt-10">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Kategorie</h2>
                        <div class="flex flex-wrap gap-3 justify-center">
                            <span v-for="cat in trainer.category" :key="cat" class="text-sm bg-[#F5F570] text-gray-800 rounded-full px-4 py-2 font-semibold">
                                {{ cat }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-10 text-center">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Media społecznościowe</h2>
                        <div class="flex justify-center space-x-6">
                            <a v-if="trainer.facebook" :href="trainer.facebook" target="_blank" class="text-blue-600 hover:text-blue-800 transition-transform duration-300 transform hover:scale-110">
                                <i class="fa-brands fa-facebook fa-3x"></i>
                            </a>
                            <a v-if="trainer.instagram" :href="trainer.instagram" target="_blank" class="text-pink-500 hover:text-pink-700 transition-transform duration-300 transform hover:scale-110">
                                <i class="fa-brands fa-instagram fa-3x"></i>
                            </a>
                            <a v-if="trainer.website" :href="trainer.website" target="_blank" class="text-gray-700 hover:text-gray-900 transition-transform duration-300 transform hover:scale-110">
                                <i class="fa-solid fa-globe fa-3x"></i>
                            </a>
                            <a v-if="trainer.tiktok" :href="trainer.tiktok" target="_blank" class="text-black hover:text-gray-800 transition-transform duration-300 transform hover:scale-110">
                                <i class="fa-brands fa-tiktok fa-3x"></i>
                            </a>
                            <a v-if="trainer.youtube" :href="trainer.youtube" target="_blank" class="text-red-600 hover:text-red-800 transition-transform duration-300 transform hover:scale-110">
                                <i class="fa-brands fa-youtube fa-3x"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Sekcja dodawania opinii -->
                    <div class="mt-10 border-t pt-8" v-if="loggedUser.role === 'client' && !hasUserReviewed">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Zostaw opinię</h2>
                        <form @submit.prevent="submitReview" class="max-w-lg mx-auto">
                            <div class="mb-6">
                                <label class="block text-lg font-semibold text-gray-700 mb-3 text-center">Twoja ocena</label>
                                <div class="flex justify-center text-yellow-400 text-4xl cursor-pointer" @mouseleave="hoverRating = 0">
                                    <i 
                                        v-for="star in 5" 
                                        :key="star"
                                        :class="[(star <= (hoverRating || reviewForm.rating)) ? 'fa-solid' : 'fa-regular', 'fa-star', 'transition-transform', 'duration-200', 'hover:scale-125']"
                                        @mouseover="hoverRating = star"
                                        @click="reviewForm.rating = star"
                                    ></i>
                                </div>
                            </div>
                            <div class="mb-6">
                                <textarea v-model="reviewForm.comment" placeholder="Napisz kilka słów o współpracy..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-shadow duration-200" rows="4"></textarea>
                            </div>
                            <input type="hidden" :value="trainer.uid" name="trainerId" />
                            <button type="submit" class="w-full px-6 py-3 bg-[#241F20] text-white font-semibold rounded-full hover:bg-[#F5F570] hover:text-[#241F20] transition-colors duration-300 cursor-pointer">Wyślij opinię</button>
                        </form>
                    </div>

                    <div class="mt-10 text-center">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Oceny</h2>
                        <div class="flex justify-center text-yellow-400 text-3xl">
                            <i v-for="star in 5" :key="star" class="fa-star" :class="{
                                'fa-solid': star <= Math.round(averageRating),
                                'fa-regular': star > Math.round(averageRating)
                            }">
                            </i>
                        </div>

                        <!-- Lista opinii -->
                        <div class="mt-8 text-left max-w-2xl mx-auto" v-if="trainer.reviews && trainer.reviews.length > 0">
                            <div class="space-y-6">
                                <div v-for="review in trainer.reviews" :key="review.userId" class="bg-gray-100 p-4 rounded-lg shadow">
                                    <div class="flex items-center mb-2">
                                        <div class="text-yellow-400">
                                            <i v-for="star in 5" :key="star" class="fa-star text-lg" :class="{
                                                'fa-solid': star <= review.rating,
                                                'fa-regular': star > review.rating
                                            }"></i>
                                        </div>
                                        <p class="ml-4 font-semibold text-gray-800">{{ review.userName }}</p>
                                    </div>
                                    <p class="text-gray-600 italic">"{{ review.comment }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>