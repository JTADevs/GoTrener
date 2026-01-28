<script setup>
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    activeView: String,
});

const emit = defineEmits(['change-view']);

const sidebarOpen = ref(false);

const changeView = (viewName) => {
    emit('change-view', viewName);
    sidebarOpen.value = false;
};
</script>

<template>
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
            <button @click="changeView('komunikator')" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer" :class="activeView === 'komunikator' ? 'active-link' : 'hover:bg-gray-700'">
                <i class="fa-solid fa-comments w-6 text-center"></i>
                <span class="mx-4 font-medium">Komunikator</span>
            </button>
            <div v-if="user.role === 'trainer'">
                <button @click="changeView('promowanie')" class="w-full flex items-center px-6 py-3 transition-colors duration-200 cursor-pointer text-[#F5F570]" :class="activeView === 'promowanie' ? 'active-link' : 'hover:bg-gray-700'">
                    <i class="fa-solid fa-bullhorn w-6 text-center"></i>
                    <span class="mx-4 font-medium">Promowanie</span>
                </button>
            </div>
        </nav>
    </aside>
</template>

<style scoped>
    .active-link {
        background-color: #F5F570;
        color: #241F20;
        font-weight: 600;
    }
</style>
