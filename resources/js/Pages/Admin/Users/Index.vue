<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    users: Array
});
</script>

<template>
    <AdminLayout>

        <div class="mb-8">

            <h1 class="text-4xl font-bold text-gray-800">
                Kelola User
            </h1>

            <p class="text-gray-500 mt-2">
                Daftar semua pengguna sistem
            </p>

        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="text-left p-5">
                            Nama
                        </th>

                        <th class="text-left p-5">
                            Email
                        </th>

                        <th class="text-left p-5">
                            Role
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="border-t"
                    >

                        <td class="p-5">
                            {{ user.name }}
                        </td>

                        <td class="p-5">
                            {{ user.email }}
                        </td>

                        <td class="p-5">
                            {{ user.role }}
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </AdminLayout>
</template>