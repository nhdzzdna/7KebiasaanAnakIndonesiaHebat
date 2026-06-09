```vue
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

const showModal = ref(false);
const activeTab = ref('guru');

const users = [
    {
        id: 1,
        nama: 'Budi Santoso',
        email: 'budi@gmail.com',
        role: 'Guru',
        status: 'Aktif',
    },
    {
        id: 2,
        nama: 'Siti Rahma',
        email: 'siti@gmail.com',
        role: 'Siswa',
        status: 'Aktif',
    },
    {
        id: 3,
        nama: 'Ahmad Fauzi',
        email: 'ahmad@gmail.com',
        role: 'Siswa',
        status: 'Nonaktif',
    },
];
</script>

<template>
    <AdminLayout>

        <div class="space-y-6">

            <!-- Header -->
            <div class="flex items-center justify-between">

                <div>

                    <h1 class="text-3xl font-bold text-gray-800">
                        Kelola Pengguna
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Kelola akun guru dan siswa
                    </p>

                </div>

                <button
                    @click="showModal = true"
                    class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-5 py-3 rounded-2xl font-medium transition"
                >
                    + Tambah Pengguna
                </button>

            </div>

            <!-- Search -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

                <input
                    type="text"
                    placeholder="Cari pengguna..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                />

            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <table class="w-full">

                    <thead class="bg-[#F6F8F6]">

                        <tr class="text-left text-sm text-gray-500">

                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr
                            v-for="user in users"
                            :key="user.id"
                            class="border-t border-gray-100"
                        >

                            <td class="px-6 py-5 font-medium text-gray-700">
                                {{ user.nama }}
                            </td>

                            <td class="px-6 py-5 text-gray-500">
                                {{ user.email }}
                            </td>

                            <td class="px-6 py-5">

                                <span
                                    class="bg-[#E7F3EE] text-[#1B7F5A] px-3 py-1 rounded-full text-sm"
                                >
                                    {{ user.role }}
                                </span>

                            </td>

                            <td class="px-6 py-5">

                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm',
                                        user.status === 'Aktif'
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-700'
                                    ]"
                                >
                                    {{ user.status }}
                                </span>

                            </td>

                            <td class="px-6 py-5">

                                <div class="flex items-center gap-3">

                                    <button
                                        class="text-blue-600 hover:underline"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        class="text-red-600 hover:underline"
                                    >
                                        Hapus
                                    </button>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- MODAL -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        >

            <!-- Card -->
            <div
                class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
            >

                <!-- Header -->
                <div
                    class="flex items-center justify-between px-6 py-5 border-b border-gray-100 shrink-0"
                >

                    <div>

                        <h2 class="text-2xl font-bold text-gray-800">
                            Tambah Pengguna Baru
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Tambahkan akun guru atau siswa
                        </p>

                    </div>

                    <button
                        @click="showModal = false"
                        class="w-10 h-10 rounded-full hover:bg-gray-100 text-xl"
                    >
                        ✕
                    </button>

                </div>

                <!-- Tabs -->
                <div class="flex border-b border-gray-100 shrink-0">

                    <button
                        @click="activeTab = 'guru'"
                        :class="[
                            'flex-1 py-4 font-medium transition',
                            activeTab === 'guru'
                                ? 'border-b-2 border-[#1B7F5A] text-[#1B7F5A]'
                                : 'text-gray-400'
                        ]"
                    >
                        👨‍🏫 Guru / Wali Kelas
                    </button>

                    <button
                        @click="activeTab = 'siswa'"
                        :class="[
                            'flex-1 py-4 font-medium transition',
                            activeTab === 'siswa'
                                ? 'border-b-2 border-[#1B7F5A] text-[#1B7F5A]'
                                : 'text-gray-400'
                        ]"
                    >
                        🧑‍🎓 Siswa
                    </button>

                </div>

                <!-- CONTENT -->
                <div class="p-6 overflow-y-auto flex-1">

                    <!-- ================= GURU ================= -->
                    <div
                        v-if="activeTab === 'guru'"
                        class="space-y-5"
                    >

                        <!-- Nama -->
                        <div>

                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                placeholder="Masukkan nama guru"
                                class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                            />

                        </div>

                        <!-- Grid -->
                        <div class="grid grid-cols-2 gap-4">

                            <!-- Email -->
                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Email
                                </label>

                                <input
                                    type="email"
                                    placeholder="guru@email.com"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                                />

                            </div>

                            <!-- Password -->
                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Password
                                </label>

                                <input
                                    type="password"
                                    placeholder="Minimal 8 karakter"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                                />

                            </div>

                        </div>

                        <!-- Kelas -->
                        <div>

                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Kelas Diampu
                            </label>

                            <select
                                class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                            >
                                <option>7A</option>
                                <option>7B</option>
                                <option>8A</option>
                            </select>

                        </div>

                        <!-- Status -->
                        <div>

                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Status Akun
                            </label>

                            <select
                                class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                            >
                                <option>Aktif</option>
                                <option>Nonaktif</option>
                            </select>

                        </div>

                    </div>

                    <!-- ================= SISWA ================= -->
                    <div
                        v-if="activeTab === 'siswa'"
                        class="space-y-5"
                    >

                        <!-- Nama -->
                        <div>

                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                placeholder="Masukkan nama siswa"
                                class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                            />

                        </div>

                        <!-- Email Password -->
                        <div class="grid grid-cols-2 gap-4">

                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Email
                                </label>

                                <input
                                    type="email"
                                    placeholder="siswa@email.com"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                                />

                            </div>

                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Password
                                </label>

                                <input
                                    type="password"
                                    placeholder="Minimal 8 karakter"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                                />

                            </div>

                        </div>

                        <!-- Kelas -->
                        <div class="grid grid-cols-2 gap-4">

                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Kelas
                                </label>

                                <select
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                                >
                                    <option>7A</option>
                                    <option>7B</option>
                                    <option>8A</option>
                                </select>

                            </div>

                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Tanggal Lahir
                                </label>

                                <input
                                    type="date"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                                />

                            </div>

                        </div>

                        <!-- Alamat -->
                        <div>

                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Alamat
                            </label>

                            <textarea
                                rows="3"
                                placeholder="Masukkan alamat lengkap"
                                class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                            ></textarea>

                        </div>

                        <!-- Orang Tua -->
                        <div class="grid grid-cols-2 gap-4">

                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Nama Orang Tua
                                </label>

                                <input
                                    type="text"
                                    placeholder="Nama orang tua"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                                />

                            </div>

                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    No HP Orang Tua
                                </label>

                                <input
                                    type="text"
                                    placeholder="08xxxxxxxx"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                                />

                            </div>

                        </div>

                        <!-- Status Akun -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status Akun
                            </label>
                            <select
                                class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none"
                            >
                                <option>Aktif</option>
                                <option>Nonaktif</option>
                            </select>
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div
                    class="flex justify-end gap-3 px-6 py-5 border-t border-gray-100 shrink-0"
                >

                    <button
                        @click="showModal = false"
                        class="px-5 py-3 border border-gray-200 rounded-2xl text-gray-600 hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>

                    <button
                        class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-6 py-3 rounded-2xl font-medium transition"
                    >
                        Simpan
                    </button>

                </div>

            </div>

        </div>
    </AdminLayout>
</template>