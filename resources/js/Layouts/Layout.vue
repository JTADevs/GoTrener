<script setup>
    import Header from "@/Components/Header.vue";
    import Footer from "@/Components/Footer.vue";
    import { onMounted, computed, watch, ref } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { getAuth, signInWithCustomToken, onAuthStateChanged } from 'firebase/auth';
    import ForceRoleSelect from "../Components/ForceRoleSelect.vue";
    // import { firebaseUser } from '../firebase.js';

    const page = usePage();
    const loggedUser = computed(() => page.props.loggedUser);
    const auth = getAuth();
    const firebaseUser = ref(null);

    // Keep the global firebaseUser state in sync with the actual auth state
    onAuthStateChanged(auth, (user) => {
        firebaseUser.value = user;
    });

    const trySignIn = () => {
        // Only try to sign in if we have a token and are not already signed in
        if (loggedUser.value?.customToken && !firebaseUser.value) {
            signInWithCustomToken(auth, loggedUser.value.customToken)
                .catch((error) => {
                    // console.error("Firebase custom sign-in error:", error);
                });
        }
    };

    // Attempt sign-in when component mounts
    onMounted(trySignIn);

    // Also attempt sign-in if the loggedUser prop changes (e.g., after login)
    watch(loggedUser, trySignIn, { immediate: true });
</script>

<template>
    <div v-if="!loggedUser || loggedUser.role!=='brak'">
        <Header/>
        <slot/>
        <Footer/>
    </div>
    <ForceRoleSelect v-if="loggedUser && loggedUser.role==='brak'"/>
</template>