<script setup>
import { computed } from 'vue';
import { Form, useForm } from '@inertiajs/vue3';

const { user } = defineProps({ user: Object });

const form = useForm({
    name: user.name ?? '',
    email: user.email ?? '',
    phone: user.phone ?? '',
    address: user.address ?? '',
    city: user.city ?? '',
    gender: user.gender ?? '',
    imageURL: user.imageURL ?? '',
    image: null    
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
};
</script>


<template>
    <div class="min-w-[300px] max-w-[800px] flex flex-col">

        <h1 class="font-bold text-2xl bg-[#241F20] text-[#F5F570] p-3">
            Witaj, {{ user.name }}
        </h1>

        <h2 class="font-bold text-xl p-3">Twoje dane osobowe:</h2>

        <Form @submit.prevent="form.post('/profil/update', { forceFormData: true })" class="flex flex-col *:my-2 *:p-2 p-3">
            <img :src="form.imageURL || '/images/no_user.png'" class="w-[200px] h-[200px] object-cover rounded-full mx-auto">
            <input type="file" @change="handleImage" class="border-1 rounded-xl">
            <input type="text" v-model="form.name" placeholder="Imię i nazwisko" class="border-1 rounded-xl">
            <input type="text" v-model="form.phone" placeholder="Numer telefonu" class="border-1 rounded-xl">
            <input type="text" v-model="form.address" placeholder="Adres" class="border-1 rounded-xl">
            <input type="text" v-model="form.city" placeholder="Miasto" class="border-1 rounded-xl">
            <div>
                <b>Płeć: </b>
                <input type="radio" name="gender" v-model="form.gender" value="men"> Mężczyzna
                <input type="radio" name="gender" v-model="form.gender" value="women"> Kobieta
            </div>
            <button type="submit" class="w-[300px] mx-auto font-bold bg-[#241F20] text-[#F5F570] cursor-pointer border-0">Zapisz</button>
        </Form>

        <h2 class="font-bold text-xl p-3">Twoja aktualna forma:</h2>

        <p class="font-bold p-3">BMI: 
            <b v-if="!scoreForm.weight || !scoreForm.height" class="text-red-500">Uzupełnij wagę i wzrost aby obliczyć BMI</b>
            <b v-if="scoreForm.weight && scoreForm.height" :class="BMIcolor">{{ BMI }} {{ BMIcategory }}</b>
        </p>

        <Form @submit.prevent="scoreForm.put('/profil/updateScore')" class="flex flex-col *:p-2 *:rounded-xl">
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Waga [KG]</label>
                <input type="text" v-model="scoreForm.weight" placeholder="Waga [KG]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Wzrost [CM]</label>
                <input type="text" v-model="scoreForm.height" placeholder="Wzrost [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód szyi [CM]</label>
                <input type="text" v-model="scoreForm.neckCircumference" placeholder="Obwód szyi [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód klatki piersiowej [CM]</label>
                <input type="text" v-model="scoreForm.chestCircumference" placeholder="Obwód klatki piersiowej [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód talii [CM]</label>
                <input type="text" v-model="scoreForm.waistCircumference" placeholder="Obwód talii [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód brzucha [CM]</label>
                <input type="text" v-model="scoreForm.abdomenCircumference" placeholder="Obwód brzucha [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód bioder [CM]</label>
                <input type="text" v-model="scoreForm.hipCircumference" placeholder="Obwód bioder [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód bicepsa [CM]</label>
                <input type="text" v-model="scoreForm.bicepsCircumference" placeholder="Obwód bicepsa [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód nadgarstka [CM]</label>
                <input type="text" v-model="scoreForm.wristCircumference" placeholder="Obwód nadgarstka [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód uda [CM]</label>
                <input type="text" v-model="scoreForm.thighCircumference" placeholder="Obwód uda [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód łydki [CM]</label>
                <input type="text" v-model="scoreForm.calfCircumference" placeholder="Obwód łydki [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            <div class="flex flex-row *:w-[50%] items-center">
                <label>Obwód kostki [CM]</label>
                <input type="text" v-model="scoreForm.ankleCircumference" placeholder="Obwód kostki [CM]" class="border-1 mx-2 my-2 p-2 rounded-xl"></input>
            </div>
            
            <button type="submit" class="w-[300px] mx-auto font-bold bg-[#241F20] text-[#F5F570] cursor-pointer border-0">Zapisz</button>
        </Form>
    </div>
</template>
