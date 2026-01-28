<script setup>
    import { ref, onMounted, onUnmounted, computed } from 'vue';
    import { router, Form, useForm } from '@inertiajs/vue3';

    import Communicator from '../../Components/Communicator.vue';
    import ProfileLayout from '../../Layouts/ProfileLayout.vue';
    import Promotion from '../../Components/Promotion.vue';
    import SidebarNav from '../../Components/SidebarNav.vue';

    import { categories } from '../../Data/Categories.js';
    import { dimensions } from '../../Data/Dimensions.js';
    import cities from '../../Data/Cities.json';

    const props = defineProps({
        user: Object,
        view: String,
    });

    const activeView = ref(props.view || 'profil');

    // Profile Data Logic
    const form = useForm({
        name: props.user.name ?? '',
        email: props.user.email ?? '',
        phone: props.user.phone ?? '',
        address: props.user.address ?? '',
        city: props.user.city ?? '',
        gender: props.user.gender ?? '',
        imageURL: props.user.imageURL ?? '',
        image: null,
        facebook: props.user.facebook ?? '',
        instagram: props.user.instagram ?? '',
        website: props.user.website ?? '',
        tiktok: props.user.tiktok ?? '',
        youtube: props.user.youtube ?? '',
        bio: props.user.bio ?? '',
        motto: props.user.motto ?? '',
        category: props.user.category ?? [],    
    });

    const scoreForm = useForm({
        weight: props.user.currentDimensions?.weight ?? '',
        height: props.user.currentDimensions?.height ?? '',
        neckCircumference: props.user.currentDimensions?.neckCircumference ?? '',
        chestCircumference: props.user.currentDimensions?.chestCircumference ?? '',
        waistCircumference: props.user.currentDimensions?.waistCircumference ?? '',
        abdomenCircumference: props.user.currentDimensions?.abdomenCircumference ?? '',
        hipCircumference: props.user.currentDimensions?.hipCircumference ?? '',
        bicepsCircumference: props.user.currentDimensions?.bicepsCircumference ?? '',
        wristCircumference: props.user.currentDimensions?.wristCircumference ?? '',
        thighCircumference: props.user.currentDimensions?.thighCircumference ?? '',
        calfCircumference: props.user.currentDimensions?.calfCircumference ?? '',
        ankleCircumference: props.user.currentDimensions?.ankleCircumference ?? '',
    });

    const galleryForm = useForm({
        photos: [],
    });

    const citySuggestions = ref([]);

    const BMI = computed(() => {
        const w = parseFloat(scoreForm.weight);
        const h = parseFloat(scoreForm.height) / 100;
        if (!w || !h) return null;
        return (w / (h * h)).toFixed(2);
    });

    const BMIcolor = computed(() => {
        if (!BMI.value) return "text-gray-500";
        const b = parseFloat(BMI.value);
        if (b < 18.5) return "text-blue-500";
        if (b >= 18.5 && b <= 24.9) return "text-green-500";
        if (b >= 25 && b <= 29.9) return "text-orange-500";
        if (b >= 30 && b <= 34.9) return "text-red-600";
        if (b >= 35 && b <= 39.9) return "text-red-700";
        if (b >= 40) return "text-red-800";
        return "text-gray-500";
    });

    const BMIcategory = computed(() => {
        if (!BMI.value) return "Uzupełnij wagę i wzrost aby obliczyć BMI";
        const b = parseFloat(BMI.value);
        if (b < 18.5) return "Niedowaga";
        if (b >= 18.5 && b <= 24.9) return "Waga prawidłowa";
        if (b >= 25 && b <= 29.9) return "Nadwaga";
        if (b >= 30 && b <= 34.9) return "Otyłość I stopnia";
        if (b >= 35 && b <= 39.9) return "Otyłość II stopnia";
        if (b >= 40) return "Otyłość III stopnia";
        return "Nieokreślone";
    });

    const handleImage = (e) => {
        const file = e.target.files[0];
        if (!file) return;

        form.image = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            form.imageURL = e.target.result;
        };
        reader.readAsDataURL(file);
    };

    const handleGalleryUpload = (e) => {
        const files = Array.from(e.target.files);
        if (files.length > 10) {
            alert('Możesz wybrać maksymalnie 10 zdjęć.');
            e.target.value = '';
            galleryForm.photos = [];
            return;
        }
        galleryForm.photos = files;
    };

    const handleCityChange = (e) => {
        const query = e.target.value;

        if (query.length < 2) {
            citySuggestions.value = [];
            return;
        }

        const replacements = {
            'ą': 'a', 'ć': 'c', 'ę': 'e', 'ł': 'l', 'ń': 'n', 'ó': 'o', 'ś': 's', 'ź': 'z', 'ż': 'z',
            'Ą': 'A', 'Ć': 'C', 'Ę': 'E', 'Ł': 'L', 'Ń': 'N', 'Ó': 'O', 'Ś': 'S', 'Ź': 'Z', 'Ż': 'Z'
        };

        const normalize = (text) => 
            text.split('').map(char => replacements[char] || char).join('').toLowerCase();

        const normalizedQuery = normalize(query);

        const seen = new Set();
        const filtered = cities.filter(city => {
            if (normalize(city.Name).includes(normalizedQuery) && !seen.has(city.Name)) {
                seen.add(city.Name);
                return true;
            }
            return false;
        });

        citySuggestions.value = filtered.slice(0, 15);
    }

    const formatDate = (dateString) => {
        if (!dateString) return '';
        return new Date(dateString).toLocaleDateString('pl-PL', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    const getDaysLeft = (dateString) => {
        if (!dateString) return '';
        const today = new Date();
        const target = new Date(dateString);
        const diffTime = target - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 0) return 'wygasło';
        if (diffDays === 0) return 'dzisiaj';
        if (diffDays === 1) return 'jutro';
        return `${diffDays} dni`;
    };

    const updateViewFromUrl = () => {
        const params = new URLSearchParams(window.location.search);
        const view = params.get('view');
        if (view) {
            activeView.value = view;
        } else {
            activeView.value = 'profil';
        }
    }

    const changeView = (viewName) => {
        if (viewName === 'calendar') {
            router.visit('/profile/calendar');
            return;
        }
        activeView.value = viewName;
        const url = new URL(window.location.href);
        if (viewName === 'profil') {
            url.searchParams.delete('view');
        } else {
            url.searchParams.set('view', viewName);
        }
        window.history.pushState({}, '', url);
    };

    onMounted(() => {
        window.addEventListener('popstate', updateViewFromUrl);
    });

    onUnmounted(() => {
        window.removeEventListener('popstate', updateViewFromUrl);
    });
</script>

<template>
    <ProfileLayout>
        <div class="bg-gray-100 flex-1 flex flex-col">
            <div class="flex flex-col lg:flex-row flex-1">
                <SidebarNav :user="user" :activeView="activeView" @change-view="changeView" />

                <main class="flex-1 md:p-4 lg:p-8">
                    <div class="w-full max-w-[1000px] mx-auto pb-12">
                        <div v-if="activeView === 'profil'" class="space-y-8">
                            <div class="bg-white shadow-xl overflow-hidden border border-gray-100">
                                <header v-if="activeView === 'profil'" class="bg-[#241F20] text-white py-8 px-6 shadow-lg">
                                    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between">
                                        <div class="text-center md:text-left">
                                            <h1 class="text-3xl font-bold text-[#F5F570] mb-2 drop-shadow-sm">Witaj, {{ user.name }}</h1>
                                            <p class="text-gray-300 text-lg">Zarządzaj informacjami o swoim profilu i śledź postępy.</p>
                                        </div>
                                    </div>
                                </header>
                                <div class="bg-white px-6 py-5 border-b border-gray-100 flex items-center">
                                    <span class="w-1 h-6 bg-[#F5F570] rounded-r mr-3"></span>
                                    <h2 class="text-xl font-bold text-gray-800">Dane Osobowe</h2>
                                </div>

                                <div class="p-6 md:p-8">
                                    <Form @submit.prevent="form.post('/profile/update', { forceFormData: true })" class="space-y-8">
                                        
                                        <!-- Profile Image -->
                                        <div class="flex flex-col items-center mb-8">
                                            <div class="relative group">
                                                <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-white shadow-xl ring-2 ring-gray-100">
                                                    <img :src="form.imageURL || '/images/no_user.png'" class="w-full h-full object-cover">
                                                </div>
                                                <label for="image-upload" class="absolute bottom-1 right-1 bg-[#241F20] text-[#F5F570] p-3 rounded-full cursor-pointer shadow-lg hover:bg-gray-800 transition-all transform hover:scale-110 border-2 border-white" title="Zmień zdjęcie">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-8.9l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </label>
                                            </div>
                                            <input id="image-upload" type="file" @change="handleImage" class="hidden">
                                        </div>

                                        <!-- Form Grid -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                            <div class="space-y-2">
                                                <label for="name" class="block text-sm font-semibold text-gray-700">Imię i nazwisko</label>
                                                <input type="text" id="name" v-model="form.name" placeholder="Imię i nazwisko" class="w-full px-4 py-3 bg-white border border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] transition-colors outline-none">
                                            </div>
                                            <div class="space-y-2">
                                                <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                                                <input type="email" id="email" v-model="form.email" placeholder="adres@email.com" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 text-gray-500 rounded-xl cursor-not-allowed outline-none" readonly>
                                            </div>
                                            <div class="space-y-2">
                                                <label for="phone" class="block text-sm font-semibold text-gray-700">Numer telefonu</label>
                                                <input type="text" id="phone" v-model="form.phone" placeholder="Numer telefonu" class="w-full px-4 py-3 bg-white border border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] transition-colors outline-none">
                                            </div>
                                            <div class="space-y-2">
                                                <label for="address" class="block text-sm font-semibold text-gray-700">Adres</label>
                                                <input type="text" id="address" v-model="form.address" placeholder="Ulica i numer" class="w-full px-4 py-3 bg-white border border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] transition-colors outline-none">
                                            </div>
                                            <div class="space-y-2">
                                                <label for="city" class="block text-sm font-semibold text-gray-700">Miasto</label>
                                                <input type="text" id="city" v-model="form.city" @input="handleCityChange" list="city-suggestions-profile" placeholder="Wpisz miasto..." class="w-full px-4 py-3 bg-white border border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] transition-colors outline-none">
                                                <datalist id="city-suggestions-profile">
                                                    <option v-for="city in citySuggestions" :key="city.Id" :value="city.Name"></option>
                                                </datalist>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-sm font-semibold text-gray-700">Płeć</label>
                                                <div class="flex items-center space-x-6 h-[50px]">
                                                    <label class="inline-flex items-center cursor-pointer group">
                                                        <input type="radio" name="gender" v-model="form.gender" value="men" class="w-5 h-5 text-[#F5F570] border-gray-300 focus:ring-[#F5F570] cursor-pointer">
                                                        <span class="ml-2 text-gray-700 group-hover:text-gray-900 transition-colors">Mężczyzna</span>
                                                    </label>
                                                    <label class="inline-flex items-center cursor-pointer group">
                                                        <input type="radio" name="gender" v-model="form.gender" value="women" class="w-5 h-5 text-[#F5F570] border-gray-300 focus:ring-[#F5F570] cursor-pointer">
                                                        <span class="ml-2 text-gray-700 group-hover:text-gray-900 transition-colors">Kobieta</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Trainer promotion status -->
                                        <div v-if="user.role === 'trainer'" class="overflow-hidden">
                                            <div class="py-5 border-b border-gray-100 flex items-center">
                                                <span class="w-1 h-6 bg-[#F5F570] rounded-r mr-3"></span>
                                                <h2 class="text-xl font-bold text-gray-800">Status Promowania</h2>
                                            </div>
                                            <div class="p-6 md:p-8">
                                                <!-- Premium Active -->
                                                <div v-if="user.is_premium && user.is_premium_date" class="relative overflow-hidden bg-[#241F20] rounded-2xl p-6 text-white space-y-6">
                                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                                                        <div class="flex items-center gap-4">
                                                            <div class="w-14 h-14 rounded-xl bg-[#F5F570] flex items-center justify-center text-[#241F20] shadow-sm shrink-0">
                                                                <i class="fa-solid fa-crown text-2xl"></i>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-lg font-bold text-[#F5F570]">Konto Premium Aktywne</h3>
                                                                <p class="text-gray-300 text-sm">Twój profil jest wyróżniony i wyświetla się wyżej w wynikach wyszukiwania.</p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex items-center gap-6 bg-white/5 p-4 rounded-xl border border-white/10">
                                                            <div class="text-right">
                                                                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Wygasa</p>
                                                                <p class="text-white font-bold whitespace-nowrap">{{ formatDate(user.is_premium_date) }}</p>
                                                            </div>
                                                            <div class="w-px h-8 bg-white/10"></div>
                                                            <div class="text-right">
                                                                <p class="text-xs text-[#F5F570]/70 uppercase font-bold tracking-wider mb-1">Pozostało</p>
                                                                <p class="text-[#F5F570] font-bold whitespace-nowrap">{{ getDaysLeft(user.is_premium_date) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex justify-end pt-4 border-t border-white/10 relative z-10">
                                                        <a href="/profile?view=promowanie" @click.prevent="changeView('promowanie')" class="w-full md:w-auto px-6 py-3 bg-[#F5F570] text-[#241F20] font-bold rounded-xl shadow-lg hover:bg-[#e4e46a] transition-colors duration-200 flex items-center justify-center gap-2">
                                                            <span>Przedłuż pakiet</span>
                                                            <i class="fa-solid fa-arrow-right text-sm"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                
                                                <!-- No Premium -->
                                                <div v-else class="flex flex-col md:flex-row items-center justify-between gap-6">
                                                    <div class="flex items-center gap-4 w-full md:w-auto">
                                                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                                                            <i class="fa-solid fa-arrow-trend-up text-2xl"></i>
                                                        </div>
                                                        <div>
                                                            <h3 class="text-lg font-bold text-gray-800">Promowanie profilu</h3>
                                                            <p class="text-gray-500 text-sm">Wyróżnij się na tle innych i zdobywaj więcej klientów.</p>
                                                        </div>
                                                    </div>
                                                    <a href="/profile?view=promowanie" @click.prevent="changeView('promowanie')" class="w-full md:w-auto px-6 py-3 bg-[#241F20] text-[#F5F570] font-bold rounded-xl shadow-lg hover:bg-gray-800 transition-colors duration-200 flex items-center justify-center gap-2 whitespace-nowrap">
                                                        <span>Włącz promowanie</span>
                                                        <i class="fa-solid fa-arrow-right text-sm"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Trainer Specific -->
                                        <div v-if="user.role === 'trainer'" class="space-y-8 animate-fade-in">
                                            <div class="border-t border-gray-100 pt-8">
                                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                                                    <span class="w-1 h-6 bg-[#F5F570] rounded-r mr-2"></span>
                                                    Informacje o Trenerze
                                                </h3>
                                                <div class="space-y-6">
                                                    <div class="space-y-2">
                                                        <label for="bio" class="block text-sm font-semibold text-gray-700">Opis (Bio)</label>
                                                        <textarea id="bio" v-model="form.bio" placeholder="Napisz coś o sobie, co przyciągnie podopiecznych..." class="w-full px-4 py-3 bg-white border border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] transition-colors h-32 resize-y outline-none shadow-sm"></textarea>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label for="motto" class="block text-sm font-semibold text-gray-700">Motto</label>
                                                        <input type="text" id="motto" v-model="form.motto" placeholder="Twoje motywacyjne hasło" class="w-full px-4 py-3 bg-white border border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] transition-colors outline-none shadow-sm">
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                                    <span class="w-1 h-6 bg-[#F5F570] rounded-r mr-2"></span>
                                                    Media Społecznościowe
                                                </h3>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="relative">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                                        </div>
                                                        <input type="text" v-model="form.facebook" placeholder="Facebook URL" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] outline-none text-sm">
                                                    </div>
                                                    <div class="relative">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                                        </div>
                                                        <input type="text" v-model="form.instagram" placeholder="Instagram URL" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] outline-none text-sm">
                                                    </div>
                                                    <div class="relative">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                                        </div>
                                                        <input type="text" v-model="form.website" placeholder="Strona WWW" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] outline-none text-sm">
                                                    </div>
                                                    <div class="relative">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.589 6.686a4.793 4.793 0 0 1-1.291.594c.547-.358.979-.9 1.173-1.619a9.7 9.7 0 0 1-2.924 1.109 4.605 4.605 0 0 0-7.962 4.195A13.066 13.066 0 0 1 1.79 5.562a4.57 4.57 0 0 0 1.424 6.138 4.542 4.542 0 0 1-2.083-.574v.058a4.606 4.606 0 0 0 3.696 4.512 4.646 4.646 0 0 1-2.078.078 4.608 4.608 0 0 0 4.305 3.193A9.262 9.262 0 0 1 1 20.918a13.024 13.024 0 0 0 7.036 2.06c8.441 0 13.056-6.984 13.056-13.037 0-.199-.005-.397-.014-.593a9.352 9.352 0 0 0 2.296-2.372z"/></svg>
                                                        </div>
                                                        <input type="text" v-model="form.tiktok" placeholder="TikTok URL" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] outline-none text-sm">
                                                    </div>
                                                    <div class="relative">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                                        </div>
                                                        <input type="text" v-model="form.youtube" placeholder="Youtube URL" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] outline-none text-sm">
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                                    <span class="w-1 h-6 bg-[#F5F570] rounded-r mr-2"></span>
                                                    Twoje specjalizacje
                                                </h3>
                                                <div class="border border-gray-200 rounded-xl p-4 max-h-60 overflow-y-auto bg-gray-50 grid grid-cols-2 sm:grid-cols-3 gap-3">
                                                    <label v-for="cat in categories" :key="cat" class="flex items-center p-2 rounded-lg hover:bg-white transition-all cursor-pointer">
                                                        <input type="checkbox" :value="cat" v-model="form.category" class="rounded text-[#F5F570] focus:ring-[#F5F570] border-gray-300 shrink-0">
                                                        <span class="ml-2 text-sm text-gray-700 select-none">{{ cat }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex justify-end pt-4">
                                            <button type="submit" class="w-full md:w-auto px-8 py-4 bg-[#241F20] text-[#F5F570] font-bold rounded-xl shadow-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200 transform hover:-translate-y-1">
                                                Zapisz Zmiany
                                            </button>
                                        </div>
                                    </Form>
                                </div>
                            </div>

                            <!-- Gallery Section (Trainer Only) -->
                            <div v-if="user.role === 'trainer'" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                                <div class="bg-white px-6 py-5 border-b border-gray-100 flex items-center">
                                    <span class="w-1 h-6 bg-[#F5F570] rounded-r mr-3"></span>
                                    <h2 class="text-xl font-bold text-gray-800">Galeria / Metamorfozy</h2>
                                </div>
                                <div class="p-6 md:p-8">
                                    <Form @submit.prevent="galleryForm.post('/profile/gallery', { forceFormData: true, onSuccess: () => galleryForm.reset() })" class="space-y-6">
                                        <div class="w-full border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors relative">
                                            <input 
                                                type="file" 
                                                id="gallery-upload-profile"
                                                multiple 
                                                accept="image/*"
                                                @change="handleGalleryUpload" 
                                                class="hidden"
                                            />
                                            <label for="gallery-upload-profile" class="cursor-pointer block w-full">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="p-3 bg-gray-100 rounded-full mb-3 group-hover:bg-white transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 group-hover:text-[#241F20] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                        </svg>
                                                    </div>
                                                    <span class="text-gray-900 font-semibold text-base">Kliknij, aby dodać zdjęcia</span>
                                                    <span class="text-gray-500 text-xs mt-1">PNG, JPG do 5MB (max 10)</span>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <div v-if="galleryForm.photos.length > 0" class="flex items-center gap-3 text-green-700 bg-green-50 p-4 rounded-xl border border-green-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium">Wybrano {{ galleryForm.photos.length }} plików do przesłania.</span>
                                        </div>

                                        <div class="flex justify-end">
                                            <button type="submit" class="px-6 py-3 bg-[#241F20] text-[#F5F570] font-bold rounded-lg shadow disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-800 transition-colors" :disabled="galleryForm.photos.length === 0">
                                                Wyślij zdjęcia
                                            </button>
                                        </div>
                                    </Form>

                                    <!-- Existing Gallery Grid -->
                                    <div v-if="user.gallery && user.gallery.length > 0" class="mt-12">
                                        <div class="flex items-center mb-6">
                                            <h3 class="text-lg font-bold text-gray-800">Twoja Galeria</h3>
                                            <span class="ml-3 px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">{{ user.gallery.length }} zdjęć</span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                            <div v-for="(photo, index) in user.gallery" :key="index" class="group relative aspect-square bg-gray-100 rounded-xl overflow-hidden shadow-sm cursor-pointer">
                                                <img :src="photo" alt="Galeria" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Body Measurements Card -->
                            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                                <div class="bg-white px-6 py-5 border-b border-gray-100 flex items-center">
                                    <span class="w-1 h-6 bg-[#F5F570] rounded-r mr-3"></span>
                                    <h2 class="text-xl font-bold text-gray-800">Pomiary Ciała</h2>
                                </div>
                                <div class="p-6 md:p-8">
                                    <Form @submit.prevent="scoreForm.put('/profile/updateScore')" class="space-y-8">
                                        
                                        <!-- BMI Info -->
                                        <div class="bg-gradient-to-br from-[#241F20] to-gray-800 rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
                                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-[#F5F570] rounded-full opacity-10 blur-xl"></div>
                                            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                                                <div>
                                                    <h3 class="text-gray-300 font-medium tracking-wide text-sm uppercase">Twoje BMI</h3>
                                                    <div class="mt-2 text-5xl font-extrabold text-[#F5F570] tracking-tight">
                                                        {{ BMI || '--' }}
                                                    </div>
                                                </div>
                                                <div class="flex-1 w-full md:w-auto text-center md:text-right">
                                                    <div class="inline-block px-6 py-3 bg-white/10 rounded-xl backdrop-blur-md border border-white/10">
                                                        <p class="text-sm text-gray-300 mb-1">Kategoria</p>
                                                        <p :class="BMIcolor" class="text-xl font-bold">{{ BMIcategory || '--' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <div v-for="dimension in dimensions" :key="dimension.key" class="space-y-2">
                                                <label :for="dimension.key" class="block text-sm font-semibold text-gray-700">{{ dimension.label }}</label>
                                                <div class="relative group">
                                                    <input type="text" :id="dimension.key" v-model="scoreForm[dimension.key]" placeholder="0" class="w-full pl-4 pr-12 py-3 bg-white border border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-[#F5F570] focus:border-[#F5F570] transition-all outline-none group-hover:border-gray-400">
                                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">cm</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex justify-end pt-4">
                                            <button type="submit" class="w-full md:w-auto px-8 py-4 bg-[#241F20] text-[#F5F570] font-bold rounded-xl shadow-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200 transform hover:-translate-y-1">
                                                Zapisz Pomiary
                                            </button>
                                        </div>
                                    </Form>
                                </div>
                            </div>
                        </div>


                        <!-- Other Views -->
                        <Communicator v-if="activeView === 'komunikator'" :currentUser="user" />
                        <Promotion v-if="activeView === 'promowanie'" :user="user"/>
                    </div>
                </main>
            </div>
        </div>
    </ProfileLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
