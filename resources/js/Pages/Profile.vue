<script setup>
    import ProfileData from '../Components/ProfileData.vue';
    import Layout from '../Layouts/Layout.vue';
    import { Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    defineProps({
        user: Object,
        url: String
    });

    const sidebarOpen = ref(false);
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
                        <Link href="/profil" class="flex items-center px-6 py-3 transition-colors duration-200" :class="url === 'profil' ? 'active-link' : 'hover:bg-gray-700'">
                            <i class="fa-solid fa-user-gear w-6 text-center"></i>
                            <span class="mx-4 font-medium">Profil</span>
                        </Link>
                        <Link href="/" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors duration-200">
                            <i class="fa-solid fa-dumbbell w-6 text-center"></i>
                            <span class="mx-4 font-medium">Moje treningi</span>
                        </Link>
                        <Link href="/" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors duration-200">
                            <i class="fa-solid fa-comments w-6 text-center"></i>
                            <span class="mx-4 font-medium">Komunikator</span>
                        </Link>
                        <Link href="/" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors duration-200">
                            <i class="fa-solid fa-chart-line w-6 text-center"></i>
                            <span class="mx-4 font-medium">Statystyki</span>
                        </Link>
                        <Link href="/" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors duration-200">
                            <i class="fa-solid fa-calendar-days w-6 text-center"></i>
                            <span class="mx-4 font-medium">Kalendarz</span>
                        </Link>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden">
                        <ProfileData v-if="url === 'profil'" :user="user" :url="url"/>
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
