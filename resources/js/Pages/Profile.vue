<script setup>
    import Calendar from '../Components/Calendar.vue';
    import ProfileData from '../Components/ProfileData.vue';
    import Stats from '../Components/Stats.vue';
    import Communicator from '../Components/Communicator.vue';
    import Layout from '../Layouts/Layout.vue';
    import { ref, onMounted, onUnmounted } from 'vue';
    import Trainings from '../Components/Trainings.vue';
    import Diet from '../Components/Diet.vue';
    import TrainingPlans from '../Components/TrainingPlans.vue';

    const props = defineProps({
        user: Object,
        view: String,
        mentees: Array,
        trainings: Array,
        diets: Array,
        trainingPlans: Array,
    });

    const sidebarOpen = ref(false);
    const activeView = ref(props.view || 'profil');

    const updateViewFromUrl = () => {
        const params = new URLSearchParams(window.location.search);
        const view = params.get('view');
        if (view) {
            activeView.value = view;
        } else {
            activeView.value = 'profil';
        }
    }

    const changeView = (viewName) => {
        activeView.value = viewName;
        const url = new URL(window.location.href);
        url.searchParams.set('view', viewName);
        window.history.pushState({}, '', url);
        sidebarOpen.value = false;
    };

    onMounted(() => {
        window.addEventListener('popstate', updateViewFromUrl);
    });

    onUnmounted(() => {
        window.removeEventListener('popstate', updateViewFromUrl);
    });
</script>

<template>
    <Layout>
        <div class="bg-gray-100 flex-1 flex flex-col">
            <div class="flex flex-col lg:flex-row flex-1">
                <!-- Sidebar -->
                <aside class="bg-[#241F20] text-white lg:w-64">
                    <!-- Mobile Header for Sidebar -->
                    <div class="flex justify-between items-center p-4 lg:hidden">
                        <h2 class="font-bold text-lg text-[#F5F570]">Menu Profilu</h2>
                        <button @click="sidebarOpen = !sidebarOpen" class="text-white focus:outline-none">
                            <i :class="sidebarOpen ? 'fa-solid fa-times' : 'fa-solid fa-bars'" class="text-xl"></i>
                        </button>
                    </div>

                    <!-- Sidebar Content -->
                    <nav :class="sidebarOpen ? 'block' : 'hidden'" class="lg:block py-4">
                        <button @click="changeView('profil')" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'profil' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-user w-6 text-center"></i>
                            <span class="mx-4 font-medium">Profil</span>
                        </button>
                        <button @click="changeView('calendar')" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'calendar' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-calendar-days w-6 text-center"></i>
                            <span class="mx-4 font-medium">Kalendarz</span>
                        </button>
                        <button @click="changeView('treningi')" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'treningi' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-dumbbell w-6 text-center"></i>
                            <span class="mx-4 font-medium">Moje treningi</span>
                        </button>
                        <button @click="changeView('dieta')" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'dieta' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-utensils w-6 text-center"></i>
                            <span class="mx-4 font-medium">Moje diety</span>
                        </button>
                        <button @click="changeView('plan')" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'plan' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-clipboard-list w-6 text-center"></i>
                            <span class="mx-4 font-medium">Moje plany</span>
                        </button>
                        <button @click="changeView('komunikator')" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'komunikator' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-comments w-6 text-center"></i>
                            <span class="mx-4 font-medium">Komunikator</span>
                        </button>
                        <button @click="changeView('statystyki')" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'statystyki' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-chart-line w-6 text-center"></i>
                            <span class="mx-4 font-medium">Statystyki</span>
                        </button>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main class="flex-1 md:p-4 lg:p-8">
                    <div class="w-full max-w-[1000px] mx-auto">
                        <ProfileData v-if="activeView === 'profil'" :user="user"/>
                        <Calendar v-if="activeView === 'calendar'" :user="user"/>
                        <Trainings v-if="activeView === 'treningi'" :user="user" :mentees="mentees" :trainings="trainings"/>
                        <Diet v-if="activeView === 'dieta'" :user="user" :mentees="mentees" :diets="diets"/>
                        <TrainingPlans v-if="activeView === 'plan'" :user="user" :mentees="mentees" :trainingPlans="trainingPlans"/>
                        <Communicator v-if="activeView === 'komunikator'" :currentUser="user" />
                        <Stats v-if="activeView === 'statystyki'" :user="user"/>
                    </div>
                </main>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
    .active-link {
        background-color: #F5F570;
        color: #241F20;
        font-weight: 600;
    }
</style>
