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
        <div class="min-h-[calc(100vh-80px)] flex justify-center items-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-6xl">
                <div class="flex flex-col items-center mb-12">
                    <div class="bg-[#F5F570] rounded-full p-6 shadow-xl shadow-yellow-400/20 mb-6 transform hover:scale-105 transition-transform duration-300 ring-8 ring-[#241F20]">
                        <img src="../../../public/images/weightlifting_black.png" alt="logo" class="h-14 w-auto">
                    </div>
                    <h1 class="text-5xl font-black tracking-tighter text-[#241F20] drop-shadow-sm flex items-center">
                        GO_TRENER
                    </h1>
                    <p class="text-[#241F20] font-bold mt-3 tracking-[0.2em] uppercase text-xs">Twoja droga do formy</p>
                </div>

                <!-- Main Container using Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">
                    
                    <!-- Login Section -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform transition-all hover:shadow-2xl h-full flex flex-col">
                        <div class="p-8 sm:p-10">
                            <div class="text-center mb-8">

                                <h2 class="text-3xl font-extrabold text-[#241F20]">Witaj z powrotem!</h2>
                                <p class="mt-2 text-sm text-gray-600">Zaloguj się, aby kontynuować trening.</p>
                            </div>

                            <Form @submit.prevent="formLogin.post('/login')" class="space-y-6">
                                <div>
                                    <label for="login-email" class="sr-only">Email</label>
                                    <input id="login-email" type="text" v-model="formLogin.loginEmail" 
                                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-[#F5F570] focus:border-[#F5F570] focus:z-10 sm:text-sm transition-colors" 
                                        placeholder="Adres Email">
                                </div>
                                <div>
                                    <label for="login-password" class="sr-only">Hasło</label>
                                    <input id="login-password" type="password" v-model="formLogin.loginPassword" 
                                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-[#F5F570] focus:border-[#F5F570] focus:z-10 sm:text-sm transition-colors" 
                                        placeholder="Hasło">
                                </div>

                                <div v-if="page.props.flash?.loginError" class="rounded-md bg-red-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-circle-exclamation text-red-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">{{ page.props.flash.loginError }}</h3>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" 
                                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-[#241F20] bg-[#F5F570] hover:bg-[#e4e46a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F5F570] transition-all transform hover:-translate-y-0.5 shadow-lg shadow-yellow-200/50 cursor-pointer">
                                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                        <i class="fa-solid fa-right-to-bracket text-[#241F20]/50 group-hover:text-[#241F20] transition-colors"></i>
                                    </span>
                                    Zaloguj się
                                </button>
                            </Form>

                            <div class="mt-8">
                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-gray-300"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm">
                                        <span class="px-2 bg-white text-gray-500">Lub kontynuuj przez</span>
                                    </div>
                                </div>

                                <div class="mt-6 grid grid-cols-1 gap-3">
                                    <button @click="handleGoogleLogin" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer group">
                                        <img src="../../../public/images/google-logo.webp" alt="google" class="h-5 w-5 mr-3 group-hover:scale-110 transition-transform">
                                        <span>Zaloguj przez Google</span>
                                    </button>
                                    <button class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer group opacity-60 cursor-not-allowed">
                                        <img src="../../../public/images/apple-logo.svg" alt="apple" class="h-5 w-5 mr-3">
                                        <span>Zaloguj przez Apple</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Register Section -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform transition-all hover:shadow-2xl h-full flex flex-col">
                        <div class="p-8 sm:p-10">
                            <div class="text-center mb-8">
                                <h2 class="text-3xl font-extrabold text-[#241F20]">Dołącz do nas!</h2>
                                <p class="mt-2 text-sm text-gray-600">Stwórz konto i zacznij swoją przygodę.</p>
                            </div>

                            <Form @submit.prevent="registerForm.post('/register')" class="space-y-6">
                                <div>
                                    <label for="register-name" class="sr-only">Imię i nazwisko</label>
                                    <input id="register-name" type="text" v-model="registerForm.registerName" 
                                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-[#F5F570] focus:border-[#F5F570] focus:z-10 sm:text-sm transition-colors" 
                                        placeholder="Imię i nazwisko">
                                </div>
                                <div>
                                    <label for="register-email" class="sr-only">Email</label>
                                    <input id="register-email" type="text" v-model="registerForm.registerEmail" 
                                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-[#F5F570] focus:border-[#F5F570] focus:z-10 sm:text-sm transition-colors" 
                                        placeholder="Adres Email">
                                </div>
                                <div>
                                    <label for="register-password" class="sr-only">Hasło</label>
                                    <input id="register-password" type="password" v-model="registerForm.registerPassword" 
                                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-[#F5F570] focus:border-[#F5F570] focus:z-10 sm:text-sm transition-colors" 
                                        placeholder="Hasło">
                                </div>

                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <span class="block text-sm font-medium text-gray-700 mb-3 text-center">Jestem:</span>
                                    <div class="flex items-center justify-center space-x-6">
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="role-client" name="role" type="radio" v-model="registerForm.role" value="client" 
                                                class="focus:ring-[#F5F570] h-4 w-4 text-[#F5F570] border-gray-300 rounded cursor-pointer">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="role-client" class="font-medium text-gray-700 cursor-pointer">Podopiecznym</label>
                                            </div>
                                        </div>
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="role-trainer" name="role" type="radio" v-model="registerForm.role" value="trainer" 
                                                class="focus:ring-[#F5F570] h-4 w-4 text-[#F5F570] border-gray-300 rounded cursor-pointer">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="role-trainer" class="font-medium text-gray-700 cursor-pointer">Trenerem</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="page.props.flash?.registerError" class="rounded-md bg-red-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-circle-exclamation text-red-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">{{ page.props.flash.registerError }}</h3>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="page.props.errors" class="space-y-2">
                                    <div v-for="(messages, field) in page.props.errors" :key="field" class="text-sm text-red-600 text-center">
                                        <div v-for="msg in messages" :key="msg">{{ msg }}</div>
                                    </div>
                                </div>

                                <div v-if="page.props.flash?.registerSuccess" class="rounded-md bg-green-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-check-circle text-green-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-green-800">{{ page.props.flash.registerSuccess }}</h3>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" 
                                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-[#241F20] bg-[#F5F570] hover:bg-[#e4e46a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F5F570] transition-all transform hover:-translate-y-0.5 shadow-lg shadow-yellow-200/50 cursor-pointer">
                                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                        <i class="fa-solid fa-user-plus text-[#241F20]/50 group-hover:text-[#241F20] transition-colors"></i>
                                    </span>
                                    Zarejestruj się
                                </button>
                            </Form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
/* Custom animations or utils if needed */
</style>