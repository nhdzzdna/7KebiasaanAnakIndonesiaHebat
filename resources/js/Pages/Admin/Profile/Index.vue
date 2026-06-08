```vue
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { reactive } from 'vue';

// State untuk toggle pengaturan sistem
const settings = reactive({
    notifikasiEmail: true,   // default ON
    laporanOtomatis: false,  // default OFF
    alertPenggunaBaru: true, // default ON
});
</script>

<template>

    <AdminLayout>

        <div class="space-y-5">

            <!-- Header -->
            <div>

                <h1 class="text-3xl font-bold text-gray-800">
                    Profil & Pengaturan
                </h1>

                <p class="text-gray-500 mt-1">
                    Kelola data akun dan preferensi sistem
                </p>

            </div>

            <!-- Profile Banner -->
            <div
                class="bg-[#0F3D2E]
                rounded-3xl overflow-hidden relative px-8 py-6"
            >
                <div
                    class="flex items-center justify-between relative z-10"
                >

                    <!-- Left -->
                    <div class="flex items-center gap-4">
                        
                        <!-- Info -->
                        <div>

                            <h2
                                class="text-white text-3xl font-bold"
                            >
                                Administrator
                            </h2>

                            <p
                                class="text-green-100 mt-1"
                            >
                                Aktif sejak Januari 2025
                            </p>

                            <!-- Badge -->
                            <div
                                class="flex items-center gap-2 mt-4"
                            >

                                <span
                                    class="bg-green-100
                                    text-green-700
                                    px-3 py-1
                                    rounded-full
                                    text-sm font-medium"
                                >
                                    Aktif
                                </span>

                                <span
                                    class="bg-pink-100
                                    text-pink-700
                                    px-3 py-1
                                    rounded-full
                                    text-sm font-medium"
                                >
                                    ADMIN
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-start">

                <!-- LEFT -->
                <div class="xl:col-span-3 space-y-5">

                    <!-- Data Akun -->
                    <div
                        class="bg-white rounded-3xl shadow-md border border-gray-100 p-6"
                    >

                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">

                            <h3
                                class="text-xl font-bold text-gray-800 flex items-center gap-2"
                            >
                                <span class="text-purple-500">👤</span>
                                Data Akun Admin
                            </h3>

                            <button
                                class="bg-[#FFF3E6]
                                text-[#F59E0B]
                                px-4 py-2
                                rounded-xl
                                text-sm font-medium"
                            >
                                ✏️ Edit
                            </button>

                        </div>

                        <!-- Form -->
                        <div class="space-y-5">

                            <!-- Nama -->
                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Nama
                                </label>

                                <input
                                    type="text"
                                    value="Administrator"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                                />

                            </div>

                            <!-- Email -->
                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Email
                                </label>

                                <input
                                    type="email"
                                    value="Admin@sekolah.sch.id"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                                />

                            </div>

                        </div>

                        <!-- Button -->
                        <div class="flex justify-end gap-3 mt-4">

                            <button
                                class="px-5 py-3 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all duration-200"
                            >
                                Batal
                            </button>

                            <button
                                class="bg-[#1B7F5A]
                                hover:bg-[#166347]
                                transition-all duration-200
                                text-white
                                px-5 py-3
                                rounded-xl
                                font-medium"
                            >
                                Simpan Perubahan
                            </button>

                        </div>

                    </div>

                    <!-- Password -->
                    <div
                        class="bg-white rounded-3xl shadow-md border border-gray-100 p-6"
                    >

                        <!-- Header -->
                        <h3
                            class="text-xl font-bold text-gray-800 flex items-center gap-2 mb-6"
                        >
                            <span class="text-yellow-500">🔒</span>
                            Ganti Password
                        </h3>

                        <!-- Form -->
                        <div class="space-y-5">

                            <!-- Password Lama -->
                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Password Lama
                                </label>

                                <input
                                    type="password"
                                    placeholder="Masukkan password lama"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                                />

                            </div>

                            <!-- Password Baru -->
                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Password Baru
                                </label>

                                <input
                                    type="password"
                                    placeholder="Minimal 8 karakter"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                                />

                            </div>

                            <!-- Konfirmasi -->
                            <div>

                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Konfirmasi Password Baru
                                </label>

                                <input
                                    type="password"
                                    placeholder="Ulangi password baru"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                                />

                            </div>

                        </div>

                        <!-- Button -->
                        <div class="flex justify-end mt-4">

                            <button
                                class="bg-[#1B7F5A]
                                hover:bg-[#166347]
                                transition-all duration-200
                                text-white
                                px-5 py-3
                                rounded-xl
                                font-medium"
                            >
                                Simpan Password
                            </button>

                        </div>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="xl:col-span-2 space-y-5 min-w-[350px]">

                    <!-- Pengaturan Sistem -->
                    <div
                        class="bg-white rounded-3xl shadow-md border border-gray-100 p-6"
                    >

                        <!-- Header -->
                        <h3
                            class="text-xl font-bold text-gray-800 flex items-center gap-2 mb-6"
                        >
                            <span class="text-gray-400">⚙️</span>
                            Pengaturan Sistem
                        </h3>

                        <!-- Notification -->
                        <div class="space-y-5">

                            <!-- Item -->
                            <div
                                class="flex items-start justify-between gap-4"
                            >

                                <div>

                                    <h4 class="font-semibold text-gray-700">
                                        Notifikasi Email
                                    </h4>

                                    <p class="text-[15px] text-gray-500 mt-1">
                                        Terima ringkasan aktivitas harian
                                    </p>

                                </div>

                                <!-- Toggle -->
                                <button
                                    @click="settings.notifikasiEmail = !settings.notifikasiEmail"
                                    :class="settings.notifikasiEmail ? 'bg-[#1B7F5A]' : 'bg-gray-200'"
                                    class="w-12 h-7 shrink-0 rounded-full relative transition-colors duration-200 flex items-center px-1"
                                >
                                    <div
                                        :style="{ transform: settings.notifikasiEmail ? 'translateX(0px)' : 'translateX(20px)' }"
                                        class="w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                                    ></div>
                                </button>

                            </div>

                            <!-- Item -->
                            <div
                                class="flex items-start justify-between gap-4"
                            >

                                <div>

                                    <h4 class="font-semibold text-gray-700">
                                        Laporan Otomatis
                                    </h4>

                                    <p class="text-[15px] text-gray-500 mt-1">
                                        Kirim laporan mingguan ke email
                                    </p>

                                </div>

                                <!-- Toggle -->
                                <button
                                    @click="settings.laporanOtomatis = !settings.laporanOtomatis"
                                    :class="settings.laporanOtomatis ? 'bg-[#1B7F5A]' : 'bg-gray-200'"
                                    class="w-12 h-7 shrink-0 rounded-full relative transition-colors duration-200 flex items-center px-1"
                                >
                                    <div
                                        :style="{ transform: settings.laporanOtomatis ? 'translateX(0px)' : 'translateX(20px)' }"
                                        class="w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                                    ></div>
                                </button>
                            </div>

                            <!-- Item -->
                            <div
                                class="flex items-start justify-between gap-4"
                            >

                                <div>

                                    <h4 class="font-semibold text-gray-700">
                                        Alert Pengguna Baru
                                    </h4>

                                    <p class="text-[15px] text-gray-500 mt-1">
                                        Notifikasi saat ada akun baru dibuat
                                    </p>

                                </div>

                                <!-- Toggle -->
                                <button
                                    @click="settings.alertPenggunaBaru = !settings.alertPenggunaBaru"
                                    :class="settings.alertPenggunaBaru ? 'bg-[#1B7F5A]' : 'bg-gray-200'"
                                    class="w-12 h-7 shrink-0 rounded-full relative transition-colors duration-200 flex items-center px-1"
                                >
                                    <div
                                        :style="{ transform: settings.alertPenggunaBaru ? 'translateX(0px)' : 'translateX(20px)' }"
                                        class="w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                                    ></div>
                                </button>
                            </div>

                        </div>

                        <!-- Button -->
                        <div class="flex justify-end mt-8">

                            <button
                                class="bg-[#1B7F5A]
                                hover:bg-[#166347]
                                transition-all duration-200
                                text-white
                                px-5 py-3
                                rounded-xl
                                font-medium"
                            >
                                Simpan
                            </button>

                        </div>

                    </div>

                    <!-- Danger Zone -->
                    <div
                        class="bg-white rounded-3xl shadow-md border border-red-200 p-6"
                    >

                        <!-- Header -->
                        <h3
                            class="text-xl font-bold text-red-500 flex items-center gap-2 mb-6"
                        >
                            <span class="text-yellow-500">⚠️</span>
                            Zona Berbahaya
                        </h3>

                        <div class="space-y-5">

                            <!-- Reset -->
                            <div
                                class="flex items-center justify-between gap-4"
                            >

                                <div>

                                    <h4 class="font-semibold text-gray-700">
                                        Reset semua data kegiatan
                                    </h4>

                                    <p class="text-[15px] text-gray-500 mt-1">
                                        Hapus semua data kegiatan semester ini
                                    </p>

                                </div>

                                <button
                                    class="border border-red-300
                                    text-red-500
                                    hover:bg-red-50
                                    transition-all duration-200
                                    px-4 py-3
                                    rounded-xl
                                    text-sm font-medium"
                                >
                                    Reset
                                </button>

                            </div>

                            <!-- Disable -->
                            <div
                                class="flex items-center justify-between gap-4"
                            >

                                <div>

                                    <h4 class="font-semibold text-gray-700">
                                        Nonaktifkan semua siswa
                                    </h4>

                                    <p class="text-[15px] text-gray-500 mt-1">
                                        Nonaktifkan seluruh akun siswa sekaligus
                                    </p>

                                </div>

                                <button
                                    class="border border-red-300
                                    text-red-500
                                    hover:bg-red-50
                                    transition-all duration-200
                                    px-4 py-3
                                    rounded-xl
                                    text-sm font-medium"
                                >
                                    Nonaktifkan
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>
```