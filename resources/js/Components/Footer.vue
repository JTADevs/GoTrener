<script setup>
    import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="bg-[#241F20] text-gray-300">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                
                <!-- Branding Section -->
                <div class="md:col-span-1 lg:col-span-2">
                    <Link href="/" class="flex items-center">
                        <img src="../../../public/images/weightlifting.png" alt="logo" class="h-10 w-auto">
                        <span class="ml-3 text-xl font-bold">
                            <span class="text-[#F5F570]">GO_</span><span class="text-white">TRENER</span>
                        </span>
                    </Link>
                    <p class="mt-4 text-sm max-w-xs">
                        Dołącz do naszej społeczności i znajdź idealnego trenera lub rozwiń swoją karierę jako profesjonalista.
                    </p>
                </div>

                <!-- Navigation -->
                <div>
                    <h3 class="text-sm font-semibold tracking-wider uppercase text-white">Nawigacja</h3>
                    <ul class="mt-4 space-y-2">
                        <li><Link href="/" class="hover:text-white transition-colors">O nas</Link></li>
                        <li><Link href="/trainers" class="hover:text-white transition-colors">Znajdź trenera</Link></li>
                        <li><Link href="/" class="hover:text-white transition-colors">Dla Trenerów</Link></li>
                        <li><Link href="/" class="hover:text-white transition-colors">Kontakt</Link></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-sm font-semibold tracking-wider uppercase text-white">Prawne</h3>
                    <ul class="mt-4 space-y-2">
                        <li><Link href="/" class="hover:text-white transition-colors">Polityka Prywatności</Link></li>
                        <li><Link href="/" class="hover:text-white transition-colors">Regulamin</Link></li>
                    </ul>
                </div>

            </div>

            <div class="mt-8 border-t border-gray-700 pt-8 flex flex-col md:flex-row items-center justify-between">
                <p class="text-sm text-gray-400 md:order-1">
                    &copy; {{ new Date().getFullYear() }} GO_TRENER. Wszelkie prawa zastrzeżone.
                </p>
                <div class="flex items-center space-x-6 md:order-2 mt-4 md:mt-0">
                    <a href="https://www.instagram.com" target="_blank" class="text-gray-400 hover:text-[#F5F570] transition-colors">
                        <i class="fab fa-instagram fa-lg"></i>
                        <span class="sr-only">Instagram</span>
                    </a>
                    <a href="https://www.facebook.com" target="_blank" class="text-gray-400 hover:text-[#F5F570] transition-colors">
                        <i class="fab fa-facebook fa-lg"></i>
                        <span class="sr-only">Facebook</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>