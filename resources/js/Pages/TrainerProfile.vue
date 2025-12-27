<script setup>
    import Layout from '../Layouts/Layout.vue';
    import Chat from '../Components/Chat.vue';
    import { computed, ref } from 'vue';
    import { useForm, usePage } from '@inertiajs/vue3';

    const { trainer } = defineProps({
        trainer: Object,
    });

    const page = usePage();
    const loggedUser = computed(() => page.props.loggedUser ?? {});

    const chatVisible = ref(false);

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
            return 0;
        }
        
        const totalStars = reviews.reduce((sum, review) => sum + review.rating, 0);
        const totalReviews = reviews.length;
        
        return totalReviews > 0 ? (totalStars / totalReviews) : 0;
    });

    const hoverRating = ref(0);

    const submitReview = () => {
        if (reviewForm.rating === 0) {
            alert('Musisz wystawić ocenę od 1 do 5 gwiazdek.');
            return;
        }
        if (!reviewForm.comment || reviewForm.comment.trim() === '') {
            alert('Musisz napisać komentarz.');
            return;
        }
        reviewForm.post(`/trainer/review/${trainer.uid}`);
    };

    const selectedImage = ref(null);
</script>

<template>
    <Layout>
        <div class="bg-gray-100 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Profile Header -->
                <div class="relative bg-white shadow-xl rounded-lg -mt-16 mb-8 overflow-hidden">
                    <div class="h-48 bg-cover bg-center" style="background-image: url('/images/gym3.jpg');"></div>
                    <div class="absolute top-24 left-1/2 -translate-x-1/2">
                        <img :src="trainer.imageURL || '/images/no_user.png'" alt="zdjecie profilowe" class="w-40 h-40 rounded-full object-cover border-8 border-white bg-white shadow-lg cursor-pointer" @click="selectedImage = trainer.imageURL || '/images/no_user.png'" />
                    </div>
                    <div class="text-center pt-24 pb-8">
                        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ trainer.name }}</h1>
                        <div class="flex items-center justify-center text-gray-500 mt-2">
                            <i class="fa-solid fa-location-dot mr-2 text-yellow-500"></i>
                            <span>{{ trainer.city }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Left Column -->
                    <div class="md:col-span-1 space-y-8">
                        <!-- About me -->
                        <div class="bg-white p-6 rounded-lg shadow-lg">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">O mnie</h2>
                            <p class="text-gray-700 leading-relaxed">
                                {{ trainer.bio || 'Trener nie dodał jeszcze opisu.' }}
                            </p>
                            <p v-if="trainer.motto" class="mt-4 text-gray-600 italic font-semibold text-center">
                                Motto: "{{ trainer.motto }}"
                            </p>
                        </div>

                        <!-- Chat Card -->
                        <div v-if="loggedUser.role === 'client' && loggedUser.uid !== trainer.uid" class="bg-white p-6 rounded-lg shadow-lg">
                            <button @click="chatVisible = !chatVisible" class="w-full px-6 py-3 bg-yellow-300 text-[#241F20] font-bold rounded-full transition-all duration-300 shadow-lg mb-4 cursor-pointer">
                                <i class="fa-solid fa-comments mr-2"></i>
                                {{ chatVisible ? 'Ukryj czat' : 'Napisz wiadomość' }}
                            </button>
                            <div v-if="chatVisible">
                                <Chat 
                                    :current-user="{ id: loggedUser.uid, ...loggedUser }" 
                                    :recipient-user="{ id: trainer.uid, ...trainer }" 
                                />
                            </div>
                        </div>
                        
                        <!-- Categories -->
                        <div class="bg-white p-6 rounded-lg shadow-lg">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Kategorie</h2>
                            <div class="flex flex-wrap gap-3">
                                <span v-for="cat in trainer.category" :key="cat" class="text-sm bg-yellow-300 text-gray-800 rounded-full px-4 py-2 font-semibold shadow">
                                    {{ cat }}
                                </span>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="bg-white p-6 rounded-lg shadow-lg mb-5">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Media społecznościowe</h2>
                            <div class="flex justify-around items-center pt-2">
                                <a v-if="trainer.facebook" :href="trainer.facebook" target="_blank" class="text-blue-600 hover:text-blue-800 transition-transform duration-300 transform hover:scale-125"><i class="fa-brands fa-facebook fa-3x"></i></a>
                                <a v-if="trainer.instagram" :href="trainer.instagram" target="_blank" class="text-pink-500 hover:text-pink-700 transition-transform duration-300 transform hover:scale-125"><i class="fa-brands fa-instagram fa-3x"></i></a>
                                <a v-if="trainer.website" :href="trainer.website" target="_blank" class="text-gray-700 hover:text-gray-900 transition-transform duration-300 transform hover:scale-125"><i class="fa-solid fa-globe fa-3x"></i></a>
                                <a v-if="trainer.tiktok" :href="trainer.tiktok" target="_blank" class="text-black hover:text-gray-800 transition-transform duration-300 transform hover:scale-125"><i class="fa-brands fa-tiktok fa-3x"></i></a>
                                <a v-if="trainer.youtube" :href="trainer.youtube" target="_blank" class="text-red-600 hover:text-red-800 transition-transform duration-300 transform hover:scale-125"><i class="fa-brands fa-youtube fa-3x"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="md:col-span-2 space-y-8">
                        <!-- Gallery -->
                        <div v-if="trainer.gallery && trainer.gallery.length > 0" class="bg-white p-6 rounded-lg shadow-lg">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Galeria / Metamorfozy</h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <img v-for="(photo, index) in trainer.gallery" :key="index" :src="photo" alt="Metamorfoza" class="w-full h-40 object-cover rounded-lg shadow-sm hover:scale-105 transition-transform duration-300 cursor-pointer" @click="selectedImage = photo">
                            </div>
                        </div>

                         <!-- Average Rating -->
                         <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Średnia ocena</h2>
                            <div class="flex items-center justify-center text-yellow-400 text-4xl">
                                <span class="text-gray-800 text-5xl font-bold mr-4">{{ averageRating.toFixed(1) }}</span>
                                <div>
                                    <i v-for="star in 5" :key="star" class="fa-star" :class="{
                                        'fa-solid': star <= Math.round(averageRating),
                                        'fa-regular': star > Math.round(averageRating)
                                    }"></i>
                                    <p class="text-sm text-gray-500 mt-1">Na podstawie {{ trainer.reviews?.length || 0 }} opinii</p>
                                </div>
                            </div>
                        </div>

                        <!-- Review Form -->
                        <div class="bg-white p-8 rounded-lg shadow-lg" v-if="loggedUser.role === 'client' && !hasUserReviewed">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Zostaw opinię</h2>
                            <form @submit.prevent="submitReview" class="space-y-6">
                                <div class="text-center">
                                    <label class="block text-lg font-semibold text-gray-700 mb-3">Twoja ocena</label>
                                    <div class="flex justify-center text-yellow-400 text-5xl cursor-pointer" @mouseleave="hoverRating = 0">
                                        <i v-for="star in 5" :key="star" :class="[(star <= (hoverRating || reviewForm.rating)) ? 'fa-solid' : 'fa-regular', 'fa-star', 'transition-all', 'duration-150', 'hover:scale-125']" @mouseover="hoverRating = star" @click="reviewForm.rating = star"></i>
                                    </div>
                                </div>
                                <div>
                                    <textarea v-model="reviewForm.comment" placeholder="Jak oceniasz współpracę z trenerem?" class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="w-full px-6 py-4 bg-[#241F20] text-white font-bold text-lg rounded-full hover:bg-yellow-400 hover:text-black transition-all duration-300 transform hover:scale-105 shadow-lg cursor-pointer">Wyślij opinię</button>
                            </form>
                        </div>
                        
                        <!-- Reviews List -->
                        <div class="bg-white p-8 rounded-lg shadow-lg" v-if="trainer.reviews && trainer.reviews.length > 0">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Opinie użytkowników</h2>
                            <div class="space-y-8">
                                <div v-for="review in trainer.reviews" :key="review.userId" class="flex items-start space-x-4">
                                    <img :src="review.imageURL  || '/images/no_user.png'" alt="User" class="w-12 h-12 rounded-full object-cover">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="font-bold text-gray-800">{{ review.userName }}</p>
                                            <div class="text-yellow-400 flex items-center">
                                                <i v-for="star in 5" :key="star" class="fa-star text-md" :class="{ 'fa-solid': star <= review.rating, 'fa-regular': star > review.rating }"></i>
                                            </div>
                                        </div>
                                        <p class="text-gray-600 mt-1 bg-gray-50 p-3 rounded-lg italic">"{{ review.comment }}"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center text-gray-500 py-8">
                            Ten trener nie ma jeszcze żadnych opinii.
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <div v-if="selectedImage" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.9);" @click="selectedImage = null">
            <div class="relative max-w-5xl max-h-full">
                <button @click="selectedImage = null" class="absolute -top-12 right-0 text-white text-4xl hover:text-gray-300 font-bold">&times;</button>
                <img :src="selectedImage" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl" @click.stop />
            </div>
        </div>
    </Layout>
</template>