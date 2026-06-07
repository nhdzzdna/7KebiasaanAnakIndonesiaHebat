<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>

<template>
    <AdminLayout>

        <!-- Header -->
        <div class="mb-8">

            <h1 class="text-4xl font-bold text-gray-800">
                Dashboard
            </h1>

            <p class="text-gray-500 mt-2">
                Pantau aktivitas siswa dan guru secara realtime
            </p>

        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white rounded-3xl p-7 shadow-sm border border-gray-100">

                <p class="text-gray-500 text-sm">
                    Total Siswa
                </p>

                <h2 class="text-5xl font-bold text-[#1E3A5F] mt-4">
                    120
                </h2>

            </div>

            <div class="bg-white rounded-3xl p-7 shadow-sm border border-gray-100">

                <p class="text-gray-500 text-sm">
                    Total Guru
                </p>

                <h2 class="text-5xl font-bold text-[#1E3A5F] mt-4">
                    15
                </h2>

            </div>

            <div class="bg-white rounded-3xl p-7 shadow-sm border border-gray-100">

                <p class="text-gray-500 text-sm">
                    Total Kegiatan
                </p>

                <h2 class="text-5xl font-bold text-[#1E3A5F] mt-4">
                    320
                </h2>

            </div>

        </div>

    </AdminLayout>
</template>