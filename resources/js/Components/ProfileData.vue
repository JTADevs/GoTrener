<script setup>
import { computed } from 'vue';
import { Form, useForm } from '@inertiajs/vue3';
import { categories } from '../Data/categories.js';

const { user } = defineProps({ 
    user: Object,
});

const form = useForm({
    name: user.name ?? '',
    email: user.email ?? '',
    phone: user.phone ?? '',
    address: user.address ?? '',
    city: user.city ?? '',
    gender: user.gender ?? '',
    imageURL: user.imageURL ?? '',
    image: null,
    facebook: user.facebook ?? '',
    instagram: user.instagram ?? '',
    website: user.website ?? '',
    category: user.category ?? [],    
});

const scoreForm = useForm({
    weight: user.currentDimensions.weight ?? '',
    height: user.currentDimensions.height ?? '',
    neckCircumference: user.currentDimensions.neckCircumference ?? '',
    chestCircumference: user.currentDimensions.chestCircumference ?? '',
    waistCircumference: user.currentDimensions.waistCircumference ?? '',
    abdomenCircumference: user.currentDimensions.abdomenCircumference ?? '',
    hipCircumference: user.currentDimensions.hipCircumference ?? '',
    bicepsCircumference: user.currentDimensions.bicepsCircumference ?? '',
    wristCircumference: user.currentDimensions.wristCircumference ?? '',
    thighCircumference: user.currentDimensions.thighCircumference ?? '',
    calfCircumference: user.currentDimensions.calfCircumference ?? '',
    ankleCircumference: user.currentDimensions.ankleCircumference ?? '',
});

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
</script>

<template>
    <div>
        <header class="bg-[#241F20] text-white p-4">
            <h1 class="text-2xl font-bold text-[#F5F570]">Witaj, {{ user.name }}</h1>
            <p class="text-gray-300">Zarządzaj informacjami o swoim profilu i śledź postępy.</p>
        </header>

        <div class="p-6 space-y-8">
            <!-- Personal Data Form -->
            <Form @submit.prevent="form.post('/profil/update', { forceFormData: true })" class="space-y-6">
                <fieldset class="p-6 border border-gray-200 rounded-lg">
                    <legend class="px-2 text-lg font-semibold text-gray-700">Dane Osobowe</legend>
                    
                    <div class="flex flex-col items-center mb-6">
                        <img :src="form.imageURL || '/images/no_user.png'" class="w-32 h-32 object-cover rounded-full border-4 border-gray-200 shadow-md">
                        <label for="image-upload" class="mt-4 px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-gray-600">
                            Zmień zdjęcie
                        </label>
                        <input id="image-upload" type="file" @change="handleImage" class="hidden">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Imię i nazwisko</label>
                            <input type="text" id="name" v-model="form.name" placeholder="Imię i nazwisko" class="form-input  border-1 p-1 rounded-xl text-center">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" v-model="form.email" placeholder="adres@email.com" class="form-input border-1 p-1 rounded-xl text-center" readonly>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Numer telefonu</label>
                            <input type="text" id="phone" v-model="form.phone" placeholder="Numer telefonu" class="form-input border-1 p-1 rounded-xl text-center">
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                            <input type="text" id="address" v-model="form.address" placeholder="Adres" class="form-input border-1 p-1 rounded-xl text-center">
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Miasto</label>
                            <input type="text" id="city" v-model="form.city" placeholder="Miasto" class="form-input border-1 p-1 rounded-xl text-center">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Płeć</label>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="gender" v-model="form.gender" value="men" class="form-radio">
                                    <span class="ml-2">Mężczyzna</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="gender" v-model="form.gender" value="women" class="form-radio">
                                    <span class="ml-2">Kobieta</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Trainer-specific fields -->
                    <div v-if="user.role === 'trainer'" class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-semibold text-gray-700 mb-4">Ustawienia Trenera</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <input type="text" v-model="form.facebook" placeholder="Link do Facebooka" class="form-input border-1 p-1 rounded-xl text-center">
                            <input type="text" v-model="form.instagram" placeholder="Link do Instagrama" class="form-input border-1 p-1 rounded-xl text-center">
                            <input type="text" v-model="form.website" placeholder="Link do strony WWW" class="form-input border-1 p-1 rounded-xl text-center">
                        </div>
                        <h4 class="font-semibold text-gray-600 mt-6 mb-2">Twoje specjalizacje:</h4>
                        <div class="border rounded-lg p-4 max-h-60 overflow-y-auto bg-gray-50 grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div v-for="cat in categories" :key="cat" class="flex items-center">
                                <input type="checkbox" :id="`cat-${cat}`" :value="cat" v-model="form.category" class="form-checkbox">
                                <label :for="`cat-${cat}`" class="ml-2 text-sm cursor-pointer">{{ cat }}</label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <button type="submit" class="w-full md:w-auto px-8 py-3 font-bold bg-[#241F20] text-[#F5F570] rounded-lg cursor-pointer hover:bg-gray-800 transition-colors">Zapisz Dane Osobowe</button>
            </Form>

            <!-- Body Measurements Form -->
            <Form @submit.prevent="scoreForm.put('/profil/updateScore')" class="space-y-6">
                <fieldset class="p-6 border border-gray-200 rounded-lg">
                    <legend class="px-2 text-lg font-semibold text-gray-700">Pomiary Ciała</legend>
                    
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-6">
                        <h3 class="font-bold text-gray-800">Twoje BMI: 
                            <span v-if="!BMI" class="text-red-500 font-normal">Uzupełnij wagę i wzrost, aby obliczyć.</span>
                            <span v-else :class="BMIcolor" class="font-extrabold">{{ BMI }} ({{ BMIcategory }})</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="(label, key) in { weight: 'Waga [kg]', height: 'Wzrost [cm]', neckCircumference: 'Obwód szyi [cm]', chestCircumference: 'Obwód klatki [cm]', waistCircumference: 'Obwód talii [cm]', abdomenCircumference: 'Obwód brzucha [cm]', hipCircumference: 'Obwód bioder [cm]', bicepsCircumference: 'Obwód bicepsa [cm]', wristCircumference: 'Obwód nadgarstka [cm]', thighCircumference: 'Obwód uda [cm]', calfCircumference: 'Obwód łydki [cm]', ankleCircumference: 'Obwód kostki [cm]' }" :key="key">
                            <label :for="key" class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
                            <input type="text" :id="key" v-model="scoreForm[key]" :placeholder="label" class="form-input border-1 p-1 rounded-xl text-center">
                        </div>
                    </div>
                </fieldset>
                <button type="submit" class="w-full md:w-auto px-8 py-3 font-bold bg-[#241F20] text-[#F5F570] rounded-lg cursor-pointer hover:bg-gray-800 transition-colors">Zapisz Pomiary</button>
            </Form>
        </div>
    </div>
</template>

<style lang="postcss" scoped>
@keyframes pulse-ring {
  0% {
    box-shadow: 0 0 0 0 rgba(245, 245, 112, 0.7);
  }
  70% {
    box-shadow: 0 0 0 6px rgba(245, 245, 112, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(245, 245, 112, 0);
  }
}

.form-input {
    @apply w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500;
}
.form-radio {
    @apply text-yellow-600 focus:ring-yellow-500;
}
.form-checkbox {
    @apply appearance-none h-5 w-5 align-middle border-2 border-gray-400 rounded-md cursor-pointer;
    @apply focus:outline-none;
    transition: background-color 0.2s;
}
.form-checkbox:checked {
    background-color: #F5F570;
    border-color: #d8d860; /* Darker yellow for contrast */
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z' fill='%23241F20'/%3e%3c/svg%3e");
    background-position: center;
    background-size: 110%;
    background-repeat: no-repeat;
}
.form-checkbox:focus {
    animation: pulse-ring 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
