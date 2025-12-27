<script setup>
import { computed, ref } from 'vue';
import { Form, useForm } from '@inertiajs/vue3';

const { user } = defineProps({
    user: Object,
});

const showModal = ref(false);
const selectedRole = ref(null);
const form = useForm({
    role: '',
});

const selectRole = (role) => {
    selectedRole.value = role;
    showModal.value = true;
};

const confirmSelection = () => {
    form.role = selectedRole.value;
    form.post('/set_role', {
        onSuccess: () => {
            showModal.value = false;
            // Przeładowanie strony nastąpi automatycznie dzięki Inertia
        }
    });
};

const cancelSelection = () => {
    showModal.value = false;
    selectedRole.value = null;
};

const roleLabel = (role) => {
    return role === 'client' ? 'Klient' : 'Trener';
};
</script>

<template>
    <div class="fixed h-dvh w-dvw top-0 left-0 bg-center bg-cover blur shadow-2xl scale-[1.1]" style="background-image: url('/images/gym.jpg');"></div>
    <div v-if="!showModal" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-8 p-10 bg-black/40 backdrop-blur-md rounded-3xl shadow-2xl border border-white/10">
        <h2 class="text-4xl font-bold text-white tracking-wide drop-shadow-lg">Kim jesteś?</h2>
        <div class="flex flex-col sm:flex-row gap-6">
            <button @click="selectRole('client')" class="min-w-[200px] px-8 py-4 bg-[#F5F570] text-[#241F20] text-xl font-bold rounded-xl shadow-lg hover:bg-[#e6e665] hover:scale-105 transition-all duration-300 transform cursor-pointer">Klient</button>
            <button @click="selectRole('trainer')" class="min-w-[200px] px-8 py-4 bg-[#241F20] text-[#F5F570] text-xl font-bold rounded-xl shadow-lg border-2 border-[#F5F570] hover:bg-[#1a1617] hover:scale-105 transition-all duration-300 transform cursor-pointer">Trener</button>
        </div>
    </div>

    <div v-else class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-6 p-10 bg-black/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 z-50">
        <h3 class="text-2xl font-bold text-white text-center">Czy na pewno chcesz wybrać rolę: <br/><span class="text-[#F5F570]">{{ roleLabel(selectedRole) }}</span>?</h3>
        <p class="text-gray-300 text-sm text-center max-w-xs">Tej operacji nie będzie można cofnąć samodzielnie.</p>
        <div class="flex gap-4 mt-2">
            <button @click="confirmSelection" :disabled="form.processing" class="px-6 py-2 bg-[#F5F570] text-[#241F20] font-bold rounded-lg hover:bg-[#e6e665] transition-colors cursor-pointer">
                {{ form.processing ? 'Zapisywanie...' : 'Tak, potwierdzam' }}
            </button>
            <button @click="cancelSelection" :disabled="form.processing" class="px-6 py-2 bg-transparent border border-white text-white font-bold rounded-lg hover:bg-white/10 transition-colors cursor-pointer">
                Anuluj
            </button>
        </div>
    </div>
</template>