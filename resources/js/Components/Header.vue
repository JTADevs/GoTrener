<script setup>
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed, ref, watch, onUnmounted, onMounted } from 'vue';
    import { db } from '../firebase';
    import { collection, query, where, onSnapshot } from 'firebase/firestore';
    import { getAuth, onAuthStateChanged } from 'firebase/auth';

    const page = usePage();
    const loggedUser = computed(() => page.props.loggedUser ?? {});
    const isMenuOpen = ref(false);
    const unreadCount = ref(0);
    const currentUserId = computed(() => loggedUser.value?.uid);
    const isFirebaseReady = ref(false);
    let unsubscribe = null;

    const toggleMenu = () => {
        isMenuOpen.value = !isMenuOpen.value;
    };

    onMounted(() => {
        const auth = getAuth();
        onAuthStateChanged(auth, (user) => {
            isFirebaseReady.value = !!user;
        });
    });

    const setupUnreadListener = () => {
        if (unsubscribe) unsubscribe();
        
        if (!currentUserId.value || !isFirebaseReady.value) {
            unreadCount.value = 0;
            return;
        }

        const q = query(
            collection(db, 'chats'),
            where('participants', 'array-contains', currentUserId.value)
        );

        unsubscribe = onSnapshot(q, (snapshot) => {
            let count = 0;
            snapshot.forEach((doc) => {
                const data = doc.data();
                if (data.read === false && String(data.sender_id) !== String(currentUserId.value)) {
                    count++;
                }
            });
            unreadCount.value = count;
        }, (error) => {
            // console.error("Błąd pobierania licznika wiadomości:", error);
        });
    };

    watch([currentUserId, isFirebaseReady], () => {
        setupUnreadListener();
    });

    onUnmounted(() => {
        if (unsubscribe) unsubscribe();
    });
</script>

<template>
    <header class="h-20 w-full bg-[#241F20] shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <Link href="/" class="flex items-center">
                        <img src="../../../public/images/weightlifting.png" alt="logo" class="h-10 sm:h-12 w-auto">
                        <span class="ml-2 sm:ml-3 text-xl sm:text-2xl font-bold">
                            <span class="text-[#F5F570]">GO_</span><span class="text-white">TRENER</span>
                        </span>
                    </Link>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center justify-end flex-1 space-x-1 lg:space-x-2">
                    <Link href="/trainers" class="text-gray-300 hover:text-white transition-colors duration-200 px-3 py-2 rounded-md text-sm font-medium">Znajdź trenera</Link>
                    <Link href="/" class="text-gray-300 hover:text-white transition-colors duration-200 px-3 py-2 rounded-md text-sm font-medium">Czym jest GO_TRENER?</Link>
                    <Link href="/" class="text-gray-300 hover:text-white transition-colors duration-200 px-3 py-2 rounded-md text-sm font-medium">Dla Trenerów</Link>
                    
                    <div v-if="!loggedUser.uid" class="pl-2 lg:pl-4">
                        <Link href="/auth" class="bg-[#F5F570] text-[#241F20] font-bold py-2 px-4 rounded-lg hover:bg-yellow-300 transition-colors duration-200 whitespace-nowrap text-sm">
                            Logowanie / rejestracja
                        </Link>
                    </div>
                    
                    <div v-if="loggedUser.uid" class="flex items-center space-x-4 pl-2 lg:pl-4">
                        <Link href="/profil?view=komunikator" class="relative text-gray-300 hover:text-white transition-colors duration-200">
                            <i class="fa-solid fa-comment fa-lg"></i>
                            <span v-if="unreadCount > 0" class="absolute -top-1 -right-2 bg-[#F5F570] text-[#241F20] font-bold text-xs px-1.5 py-0.5 rounded-full">
                                {{ unreadCount }}
                            </span>
                        </Link>
                        <Link href="/profil" class="text-[#F5F570] font-bold hover:text-yellow-300 transition-colors duration-200 text-sm">Profil</Link>
                        <Link href="/logout" class="text-[#F5F570] font-bold hover:text-yellow-300 transition-colors duration-200 text-sm">Wyloguj</Link>
                    </div>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="toggleMenu" class="text-gray-300 hover:text-white focus:outline-none">
                        <i :class="isMenuOpen ? 'fa-solid fa-times' : 'fa-solid fa-bars'" class="text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <transition name="slide-down">
            <div v-if="isMenuOpen" class="md:hidden bg-[#241F20] bg-opacity-95 absolute w-full">
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
                        <Link @click="toggleMenu" href="/profil" class="block text-[#F5F570] font-bold rounded-md px-3 py-2 text-base flex items-center justify-center">
                            <i class="fa-solid fa-comments mr-2"></i>
                            Wiadomości
                            <span v-if="unreadCount > 0" class="ml-2 bg-[#F5F570] text-[#241F20] text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ unreadCount }}
                            </span>
                        </Link>
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