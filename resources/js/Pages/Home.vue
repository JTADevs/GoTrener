<script setup>
    import Layout from '../Layouts/Layout.vue';
</script>

<template>
    <Layout>
        <section class="flex flex-col min-h-[calc(100vh-200px)]">

        <div class="flex w-full h-[40vh] bg-cover bg-center items-end relative" style="background-image: url('/images/gym.jpg');">
            <div class="relative text-white mx-auto my-5 h-[200px]">
            <h1 class="text-[40px] font-bold">ODKRYJ SWÓJ POTENCJAŁ.</h1>
            <form action="" class="flex flex-row mt-10">
                <input type="text" name="dyscyplina" placeholder="Dyscyplina" class="bg-white rounded-xl text-black p-3 mr-5 w-[300px]">
                <input type="text" name="lokalizacja" placeholder="Lokalizacja" class="bg-white rounded-xl text-black p-3 mr-5 w-[300px]">
                <button class="text-[#241F20] bg-[#F5F570] p-3 rounded-xl font-bold cursor-pointer">Szukaj trenera</button>
            </form>
            </div>
        </div>

        <div class="w-full py-5">
            <div class="w-[80%] mx-auto flex flex-col md:flex-row justify-center items-center md:space-x-10 space-y-8 md:space-y-0 font-bold">
                <div class="flex flex-col items-center space-y-3">
                    <i class="fas fa-search fa-2x text-gray-700"></i>
                    <p class="text-center text-lg md:text-base">Wyszukaj trenera osobistego</p>
                </div>
                <div class="flex flex-col items-center space-y-3">
                    <i class="fas fa-calendar-alt fa-2x text-gray-700"></i>
                    <p class="text-center text-lg md:text-base">Zaplanuj trening</p>
                </div>
                <div class="flex flex-col items-center space-y-3">
                    <i class="fas fa-dumbbell fa-2x text-gray-700"></i>
                    <p class="text-center text-lg md:text-base">Trenuj i osiągaj cele</p>
                </div>
            </div>
        </div>

        <div class="flex-grow bg-gray-200 w-full py-10">
            <div class="w-[80%] mx-auto p-2">
                <h1 class="font-bold text-[30px] text-center mb-8">POLECANI TRENERZY</h1>
                <div class="flex flex-wrap justify-center gap-10">
                    <div class="flex flex-col items-center">
                        <img src="../../../public/images/trener.jpg" alt="trener1" class="rounded-full w-[120px] border-[7px] border-white">
                        <p class="text-[18px] mt-2">Michał Sławek</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <img src="../../../public/images/trener.jpg" alt="trener1" class="rounded-full w-[120px] border-[7px] border-white">
                        <p class="text-[18px] mt-2">Tymoteusz Palak</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <img src="../../../public/images/trener.jpg" alt="trener1" class="rounded-full w-[120px] border-[7px] border-white">
                        <p class="text-[18px] mt-2">Jakub Kurzacz</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <img src="../../../public/images/trener.jpg" alt="trener1" class="rounded-full w-[120px] border-[7px] border-white">
                        <p class="text-[18px] mt-2">Skubała Sebastian</p>
                    </div>
                </div>
            </div>
        </div>

        </section>

    </Layout>
</template>