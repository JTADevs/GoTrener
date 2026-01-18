<script setup>
    import { onMounted } from 'vue';

    const props = defineProps({
        user: Object,
    });

    onMounted(() => {
        if (props.user.role !== 'trainer') {
            window.location.href = '/';
        }
    });
</script>

<template>
    <div>
        <h1>AI</h1>
    </div>
</template>
