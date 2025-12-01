<script setup>
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed, ref } from 'vue';

    const page = usePage();
    const loggedUser = computed(() => page.props.loggedUser ?? {});
    const isMenuOpen = ref(false);

    const toggleMenu = () => {
        isMenuOpen.value = !isMenuOpen.value;
    };
</script>

<template>
    <header class="h-20 w-full bg-[#241F20] shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <Link href="/" class="flex items-center">
                        <img src="../../../public/images/weightlifting.png" alt="logo" class="h-12 w-auto">
                        <span class="ml-3 text-2xl font-bold">
                            <span class="text-[#F5F570]">GO_</span><span class="text-white">TRENER</span>
                        </span>
                    </Link>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center space-x-6">
                    <Link href="/trainers" class="text-gray-300 hover:text-white transition-colors duration-200">Znajdź trenera</Link>
                    <Link href="/" class="text-gray-300 hover:text-white transition-colors duration-200">Czym jest GO_TRENER?</Link>
                    <Link href="/" class="text-gray-300 hover:text-white transition-colors duration-200">Dla Trenerów</Link>
                    
                    <div v-if="!loggedUser.uid" class="pl-4">
                        <Link href="/auth" class="bg-[#F5F570] text-[#241F20] font-bold py-2 px-4 rounded-lg hover:bg-yellow-300 transition-colors duration-200">
                            Logowanie / rejestracja
                        </Link>
                    </div>
                    
                    <div v-if="loggedUser.uid" class="flex items-center space-x-4 pl-4">
                        <Link href="/profil" class="text-[#F5F570] font-bold hover:text-yellow-300 transition-colors duration-200">Profil</Link>
                        <Link href="/logout" class="text-[#F5F570] font-bold hover:text-yellow-300 transition-colors duration-200">Wyloguj</Link>
                    </div>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="toggleMenu" class="text-gray-300 hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path v-if="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                            <path v-if="isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <transition name="slide-down">
            <div v-if="isMenuOpen" class="md:hidden bg-[#241F20] bg-opacity-95">
                <nav class="px-2 pt-2 pb-4 space-y-1 sm:px-3 text-center">
                    <Link @click="toggleMenu" href="/trainers" class="block text-gray-300 hover:text-white rounded-md px-3 py-2 text-base font-medium">Znajdź trenera</Link>
                    <Link @click="toggleMenu" href="/" class="block text-gray-300 hover:text-white rounded-md px-3 py-2 text-base font-medium">Czym jest GO_TRENER?</Link>
                    <Link @click="toggleMenu" href="/" class="block text-gray-300 hover:text-white rounded-md px-3 py-2 text-base font-medium">Dla Trenerów</Link>
                    
                    <div v-if="!loggedUser.uid" class="border-t border-gray-700 pt-4 mt-2">
                        <Link @click="toggleMenu" href="/auth" class="block w-full text-center bg-[#F5F570] text-[#241F20] font-bold py-2 px-4 rounded-lg hover:bg-yellow-300 transition-colors duration-200">
                            Logowanie / rejestracja
                        </Link>
                    </div>
                    
                    <div v-if="loggedUser.uid" class="border-t border-gray-700 pt-4 mt-2 space-y-2">
                        <Link @click="toggleMenu" href="/profil" class="block text-[#F5F570] font-bold rounded-md px-3 py-2 text-base">Profil</Link>
                        <Link @click="toggleMenu" href="/logout" class="block text-[#F5F570] font-bold rounded-md px-3 py-2 text-base">Wyloguj</Link>
                    </div>
                </nav>
            </div>
        </transition>
    </header>
</template>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active {
    transition: all 0.3s ease-in-out;
}
.slide-down-enter-from, .slide-down-leave-to {
    transform: translateY(-100%);
    opacity: 0;
}
</style>