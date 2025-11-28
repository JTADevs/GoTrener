<script setup>
    import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="h-[120px] w-full bg-[#241F20] flex flex-col justify-center *:my-3 py-5">
        <div class="w-[80%] mx-auto">
            <h1 class="text-white font-bold text-[20px]">DOŁĄCZ DO SPOŁECZNOŚCI <span class="text-[#F5F570]">GO_</span>TRENER!</h1>
        </div>
        <div class="w-[80%] mx-auto flex flex-row justify-between *:text-white">
            <div>
                <Link href="/" class="bg-[#F5F570] font-bold text-[#241F20] p-2 rounded-2xl mr-5">ZNAJDŹ TRENERA</Link>
                <Link href="/" class="bg-[#352B2C] font-bold p-2 rounded-2xl">ZOSTAŃ TRENEREM</Link>
            </div>
            <div class="*:mx-5">
                <Link href="/">O nas</Link>
                <Link href="/">Kontakt</Link>
                <Link href="/">Polityka Prywatności</Link>
                <a href="https://www.instagram.com" target="_blank" class="text-white hover:text-[#F5F570]">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>
            </div>
        </div>
    </footer>
</template>