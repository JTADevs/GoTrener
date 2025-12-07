<script setup>
    import Calendar from '../Components/Calendar.vue';
    import ProfileData from '../Components/ProfileData.vue';
    import Layout from '../Layouts/Layout.vue';
    import { ref } from 'vue';

    defineProps({
        user: Object,
    });

    const sidebarOpen = ref(false);
    const activeView = ref('profil');
</script>

<template>
    <Layout>
        <div class="bg-gray-100 min-h-[calc(100vh-80px)]">
            <div class="lg:flex">
                <!-- Sidebar -->
                <aside class="bg-[#241F20] text-white lg:w-64 lg:min-h-[calc(100vh-80px)]">
                    <!-- Mobile Header for Sidebar -->
                    <div class="flex justify-between items-center p-4 lg:hidden">
                        <h2 class="font-bold text-lg text-[#F5F570]">Menu Profilu</h2>
                        <button @click="sidebarOpen = !sidebarOpen" class="text-white focus:outline-none">
                            <i :class="sidebarOpen ? 'fa-solid fa-times' : 'fa-solid fa-bars'" class="text-xl"></i>
                        </button>
                    </div>

                    <!-- Sidebar Content -->
                    <nav :class="sidebarOpen ? 'block' : 'hidden'" class="lg:block py-4">
                        <button @click="activeView = 'profil'" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'profil' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-user-gear w-6 text-center"></i>
                            <span class="mx-4 font-medium">Profil</span>
                        </button>
                        <button @click="activeView = 'calendar'" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'calendar' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-calendar-days w-6 text-center"></i>
                            <span class="mx-4 font-medium">Kalendarz</span>
                        </button>
                        <button @click="activeView = 'treningi'" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'treningi' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-dumbbell w-6 text-center"></i>
                            <span class="mx-4 font-medium">Moje treningi</span>
                        </button>
                        <button @click="activeView = 'komunikator'" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'komunikator' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-comments w-6 text-center"></i>
                            <span class="mx-4 font-medium">Komunikator</span>
                        </button>
                        <button @click="activeView = 'statystyki'" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'statystyki' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-chart-line w-6 text-center"></i>
                            <span class="mx-4 font-medium">Statystyki</span>
                        </button>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden">
                        <ProfileData v-if="activeView === 'profil'" :user="user"/>
                        <Calendar v-if="activeView === 'calendar'" class="p-6" :user="user"></Calendar>
                        <div v-if="activeView === 'treningi'" class="p-6">Moje treningi - wkrótce</div>
                        <div v-if="activeView === 'komunikator'" class="p-6">Komunikator - wkrótce</div>
                        <div v-if="activeView === 'statystyki'" class="p-6">Statystyki - wkrótce</div>
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
