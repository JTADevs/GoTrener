<script setup>
    import Layout from '../Layouts/Layout.vue';
    import { Form, useForm, usePage, router } from '@inertiajs/vue3';
    import { getAuth, GoogleAuthProvider, signInWithPopup } from 'firebase/auth';

    const formLogin = useForm({
        loginEmail: '',
        loginPassword: ''
    });

    const registerForm = useForm({
        registerName: '',
        registerEmail: '',
        registerPassword: '',
        role: ''
    })

    const page = usePage();

    const handleGoogleLogin = () => {
        const auth = getAuth();
        const provider = new GoogleAuthProvider();

        signInWithPopup(auth, provider)
            .then((result) => {
                result.user.getIdToken().then((token) => {
                    router.post('/login_google', { token: token });
                });
            }).catch((error) => {
                console.error("Błąd logowania Google:", error);
            });
    }
</script>

<template>  
    <Layout>
        <div class="min-h-[calc(100vh-200px)] flex flex-row justify-center items-center space-x-10 bg-gray-100">
            <div class="bg-white rounded-xl p-6 shadow-xl w-[300px] h-[500px]">
                <p class="flex flex-row justify-center items-center">
                    <img src="../../../public/images/weightlifting_black.png" alt="logo" class="h-[30px] mr-2">
                    <span class=" font-bold">GO_TRENER</span>
                </p>
                <h2 class="text-center font-bold text-[26px] mb-2">Witaj z powrotem!</h2>
                <Form @submit.prevent="formLogin.post('/login')" class="flex flex-col *:my-1 *:p-2">
                    <input type="text" v-model="formLogin.loginEmail" class="border-1" placeholder="Email">
                    <input type="password" v-model="formLogin.loginPassword" class="border-1" placeholder="Hasło">
                    <button type="submit" class="bg-[#F5F570] p-2 rounded-xl cursor-pointer">Zaloguj się</button>
                </Form>
                <div v-if="page.props.flash?.loginError" class="text-red-600">
                    {{ page.props.flash.loginError }}
                </div>
                <hr class="my-2">
                <p class="text-center">Lub</p>
                <hr class="my-2">
                <div class="flex flex-col items-center justify-center *:my-1 *:rounded-xl *:w-[200px] *:cursor-pointer *:p-2">
                    <button class="flex flex-row items-center justify-center border-1 p-1"><img src="../../../public/images/apple-logo.svg" alt="apple" class="w-[20px] mr-2"> <span>Zaloguj przez Apple</span></button>
                    <button class="flex flex-row items-center justify-center border-1 p-1" @click="handleGoogleLogin"><img src="../../../public/images/google-logo.webp" alt="google" class="w-[20px] mr-2"> <span>Zaloguj przez Google</span></button>
                </div>
            </div>


            <div class="bg-white rounded-xl p-6 shadow-xl w-[300px] h-[500px]">
                <p class="flex flex-row justify-center items-center">
                    <img src="../../../public/images/weightlifting_black.png" alt="logo" class="h-[30px] mr-2">
                    <span class=" font-bold">GO_TRENER</span>
                </p>
                <h2 class="text-center font-bold text-[26px] mb-2">Dołącz do nas!</h2>
                <Form @submit.prevent="registerForm.post('/register')" class="flex flex-col *:my-2">
                    <input type="text" v-model="registerForm.registerName" class="border-1 p-1" placeholder="Imie i nazwisko">
                    <input type="text" v-model="registerForm.registerEmail" class="border-1 p-1" placeholder="Email">
                    <input type="password" v-model="registerForm.registerPassword" class="border-1 p-1" placeholder="Hasło">
                    <div class="flex items-center justify-center *:mx-2">
                        <label><input type="radio" v-model="registerForm.role" value="client" name="role"> Klient</label>
                        <label><input type="radio" v-model="registerForm.role" value="trainer" name="role"> Trener</label>
                    </div>
                    <button type="submit" class="bg-[#F5F570] p-2 rounded-xl cursor-pointer">Zarejestruj się</button>
                </Form>
                <div v-if="page.props.flash?.registerError" class="text-red-600">
                    {{ page.props.flash.registerError }}
                </div>

                <div v-if="page.props.errors" class="text-red-600">
                    <div v-for="(messages, field) in page.props.errors" :key="field">
                        <div v-for="msg in messages" :key="msg">
                            {{ msg }}
                        </div>
                    </div>
                </div>
                <div v-if="page.props.flash?.registerSuccess" class="text-green-600">
                    {{ page.props.flash.registerSuccess }}
                </div>




            </div>
        </div>
    </Layout>
</template>