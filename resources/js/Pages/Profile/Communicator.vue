<script setup>
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';
    import Chat from '../../Components/Chat.vue';
    import SidebarNav from '../../Components/SidebarNav.vue';
    import { usePage } from '@inertiajs/vue3';

    const page = usePage();
    const user = page.props.user;

    const emit = defineEmits(['change-view']);

    const changeView = (viewName) => {
        emit('change-view', viewName);
    };
</script>

<template>
    <SidebarNav :user="user" :activeView="activeView" @change-view="changeView" />

</template>