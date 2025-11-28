<script setup>
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed } from 'vue';

    const page = usePage();
    const loggedUser = computed(() => page.props.loggedUser ?? {});
</script>

<template>
    <header class="h-[80px] w-full bg-[#241F20]">
        <div class="flex flex-row w-[80%] mx-auto items-center justify-between">
            <Link href="/" class="cursor-pointer flex flex-row h-[80px] items-center">
                <img src="../../../public/images/weightlifting.png" alt="logo" class="h-full ml-6">
                <span class="text-[#F5F570] ml-4 font-bold text-[20px]">GO_</span><span class="text-[20px] text-white font-bold">TRENER</span>
            </Link>
            <div class="*:mx-5">
                <Link href="/" class="text-white">Znajdź trenera</Link>
                <Link href="/" class="text-white">Czym jest GO_TRENER?</Link>
                <Link href="/" class="text-white">Dla Trenerów</Link>
                <Link v-if="!loggedUser.uid" href="/auth" class="text-[#241F20] bg-[#F5F570] font-bold p-2 rounded-3xl">Logowanie / rejestracja</Link>
                <Link v-if="loggedUser.uid" href="/profil" class="text-[#F5F570] font-bold">Profil</Link>
                <Link v-if="loggedUser.uid" href="/logout" class="text-[#F5F570] font-bold ml-4">Wyloguj</Link>
            </div>
        </div>
    </header>
</template>