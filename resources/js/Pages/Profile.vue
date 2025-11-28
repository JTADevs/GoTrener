<script setup>
    import ProfileData from '../Components/ProfileData.vue';
    import Layout from '../Layouts/Layout.vue';
    import { Link } from '@inertiajs/vue3';

    defineProps({
        user: Object,
        url: String
    });
</script>

<template>
    <Layout>
        <section class="min-h-[calc(100vh-200px)] mx-auto flex flex-row">
            <div class="bg-[#241F20] py-20 flex flex-col  *:text-white *:px-10 *:py-5 *:font-bold *:text-center">
                <Link href="/profil" :class="url === 'profil' ? 'active-link' : ''">Profil</Link>
                <Link href="/">Moje treningi</Link>
                <Link href="/">Komunikator</Link>
                <Link href="/">Statystyki</Link>
                <Link href="/">Kalendarz</Link>
            </div>

            <div class="w-[800px] mx-auto m-10 shadow-xl">
                <ProfileData v-if="url === 'profil'" :user="user" :url="url"/>
            </div>

        </section>
    </Layout>
</template>

<style>
    .active-link{
        background-color: #F5F570;
        color: #241F20;
    }
</style>
