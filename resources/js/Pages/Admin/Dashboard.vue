```vue
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>

<template>
    <AdminLayout>

        <div class="space-y-6">

            <!-- Header -->
            <div>

                <h1 class="text-3xl font-bold text-gray-800">
                    Dashboard Admin
                </h1>

                <p class="text-gray-500 mt-1">
                    Pantau statistik dan aktivitas sistem sekolah
                </p>

            </div>

            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Total Guru/Wali Kelas
                            </p>

                            <h2 class="text-4xl font-bold text-[#0F3D2E] mt-3">
                                24
                            </h2>

                        </div>

                        <div class="w-12 h-12 rounded-full bg-[#E7F3EE] flex items-center justify-center text-xl">
                            👨‍🏫
                        </div>

                    </div>

                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Total Siswa Aktif
                            </p>

                            <h2 class="text-4xl font-bold text-[#0F3D2E] mt-3">
                                324
                            </h2>

                        </div>

                        <div class="w-12 h-12 rounded-full bg-[#EEF5FF] flex items-center justify-center text-xl">
                            👨‍🎓
                        </div>

                    </div>

                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Kegiatan Tercatat
                            </p>

                            <h2 class="text-4xl font-bold text-[#0F3D2E] mt-3">
                                1847
                            </h2>

                        </div>

                        <div class="w-12 h-12 rounded-full bg-[#FFF7E8] flex items-center justify-center text-xl">
                            ✅
                        </div>

                    </div>

                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Pengguna Belum Aktif
                            </p>

                            <h2 class="text-4xl font-bold text-[#0F3D2E] mt-3">
                                43
                            </h2>

                        </div>

                        <div class="w-12 h-12 rounded-full bg-[#FFECEC] flex items-center justify-center text-xl">
                            ⏳
                        </div>

                    </div>

                </div>

            </div>

            <!-- Grafik & Status -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <!-- Grafik -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

                    <div class="flex items-center justify-between mb-5">

                        <h3 class="font-semibold text-gray-800">
                            Grafik Aktivitas / 7 Hari Terakhir
                        </h3>

                        <span class="text-sm text-green-600 font-medium">
                            realtime
                        </span>

                    </div>

                    <!-- Fake Chart -->
                    <div class="flex items-end gap-3 h-56">

                        <div class="flex-1 bg-[#DCE9E2] rounded-t-xl h-24"></div>
                        <div class="flex-1 bg-[#DCE9E2] rounded-t-xl h-20"></div>
                        <div class="flex-1 bg-[#6B9D84] rounded-t-xl h-36"></div>
                        <div class="flex-1 bg-[#6B9D84] rounded-t-xl h-44"></div>
                        <div class="flex-1 bg-[#F59E0B] rounded-t-xl h-52"></div>
                        <div class="flex-1 bg-[#DCE9E2] rounded-t-xl h-28"></div>
                        <div class="flex-1 bg-[#6B9D84] rounded-t-xl h-40"></div>
                        <div class="flex-1 bg-[#DCE9E2] rounded-t-xl h-30"></div>

                    </div>

                </div>

                <!-- Status Sistem -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

                    <h3 class="font-semibold text-gray-800 mb-5">
                        Status Sistem
                    </h3>

                    <div class="space-y-5">

                        <div>

                            <div class="flex justify-between text-sm mb-2">
                                <span>Guru Aktif</span>
                                <span>88%</span>
                            </div>

                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full w-[88%] bg-[#1B7F5A] rounded-full"></div>
                            </div>

                        </div>

                        <div>

                            <div class="flex justify-between text-sm mb-2">
                                <span>Siswa Aktif</span>
                                <span>72%</span>
                            </div>

                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full w-[72%] bg-[#1B7F5A] rounded-full"></div>
                            </div>

                        </div>

                        <div>

                            <div class="flex justify-between text-sm mb-2">
                                <span>Kegiatan Tervalidasi</span>
                                <span>65%</span>
                            </div>

                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full w-[65%] bg-[#1B7F5A] rounded-full"></div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <!-- Aktivitas -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

                    <div class="flex items-center justify-between mb-5">

                        <h3 class="font-semibold text-gray-800">
                            Aktivitas Sistem
                        </h3>

                        <span class="text-sm text-gray-400">
                            realtime
                        </span>

                    </div>

                    <div class="space-y-4 text-sm">

                        <div class="flex items-center justify-between">

                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <p>Akun siswa berhasil dibuat</p>
                            </div>

                            <span class="text-gray-400">
                                09:41
                            </span>

                        </div>

                        <div class="flex items-center justify-between">

                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <p>Guru login ke sistem</p>
                            </div>

                            <span class="text-gray-400">
                                09:30
                            </span>

                        </div>

                        <div class="flex items-center justify-between">

                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                <p>Password akun berhasil direset</p>
                            </div>

                            <span class="text-gray-400">
                                08:55
                            </span>

                        </div>

                    </div>

                </div>

                <!-- Distribusi Role -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">

                    <h3 class="font-semibold text-gray-800 mb-5">
                        Distribusi Pengguna
                    </h3>

                    <div class="flex items-center justify-center h-48">

                        <div class="text-center">

                            <h2 class="text-5xl font-bold text-[#0F3D2E]">
                                336
                            </h2>

                            <p class="text-gray-500 mt-2">
                                Total Pengguna Terdaftar
                            </p>

                        </div>

                    </div>

                    <div class="flex items-center gap-5 mt-4 text-sm">

                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span>Guru (24)</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-600"></div>
                            <span>Siswa (312)</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </AdminLayout>
</template>