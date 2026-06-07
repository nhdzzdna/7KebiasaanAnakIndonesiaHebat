<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>

<template>
    <AdminLayout>

        <h1 class="text-3xl font-bold mb-6">
            Dashboard Admin
        </h1>

        <div class="grid grid-cols-3 gap-4">

            <div class="bg-white p-5 rounded-xl shadow">
                Total Siswa
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                Total Guru
            </div>

            <div class="bg-white p-5 rounded-xl shadow">
                Total Kegiatan
            </div>

        </div>

    </AdminLayout>
</template>