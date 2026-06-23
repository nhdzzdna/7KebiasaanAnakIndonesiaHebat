<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({})
    },
    recentReports: {
        type: Array,
        default: () => []
    },
    activityChart: {
        type: Array,
        default: () => []
    },
    recentActivity: {
        type: Array,
        default: () => []
    },
    topStudents: {
        type: Array,
        default: () => []
    },
});

const maxAktivitas = Math.max(
    ...(props.activityChart?.map(d => d.total_aktivitas) ?? [0]),
    1
);

function barHeight(total) {
    // minimal 8% supaya bar tetap terlihat walau 0
    return Math.max((total / maxAktivitas) * 100, 8);
}

function barColor(total) {
    if (total === maxAktivitas && total > 0) return '#F59E0B';
    if (total >= maxAktivitas * 0.6) return '#6B9D84';
    return '#DCE9E2';
}
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
                            <p class="text-sm text-gray-500">Total Guru/Wali Kelas</p>
                            <h2 class="text-4xl font-bold text-[#0F3D2E] mt-3">
                                {{ stats.total_guru }}
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
                            <p class="text-sm text-gray-500">Total Siswa</p>
                            <h2 class="text-4xl font-bold text-[#0F3D2E] mt-3">
                                {{ stats.total_siswa }}
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
                            <p class="text-sm text-gray-500">Kegiatan Tercatat</p>
                            <h2 class="text-4xl font-bold text-[#0F3D2E] mt-3">
                                {{ stats.total_reports }}
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
                            <p class="text-sm text-gray-500">Pengguna Belum Aktif</p>
                            <h2 class="text-4xl font-bold text-[#0F3D2E] mt-3">
                                {{ stats.inactive_users }}
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
                            Jumlah Laporan Kegiatan /Hari
                        </h3>
                        <span class="text-sm text-green-600 font-medium">realtime</span>
                    </div>
                    <!-- Chart dinamis -->
                    <div class="flex items-end gap-3 h-56">
                        <div
                            v-for="(day, i) in activityChart"
                            :key="i"
                            class="flex-1 flex flex-col items-center justify-end h-full"
                        >
                            <!-- Angka di atas bar -->
                            <span class="text-xs font-semibold text-gray-600 mb-1">
                                {{ day.total_aktivitas }}
                            </span>
                            <div
                                class="w-full rounded-t-xl transition-all"
                                :style="{
                                    height: barHeight(day.total_aktivitas) + '%',
                                    backgroundColor: barColor(day.total_aktivitas),
                                }"
                                :title="day.total_aktivitas + ' aktivitas'"
                            ></div>
                            <!-- Nama hari di bawah bar -->
                            <span class="text-xs text-gray-400 mt-2">{{ day.hari }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Menampilkan jumlah laporan kegiatan yang tercatat setiap hari dalam 7 hari terakhir.
                    </p>
                </div>

                <!-- Status Sistem -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-5">Status Sistem</h3>
                    <div class="space-y-5">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span>Guru Aktif</span>
                                <span>{{ stats.persen_guru_aktif }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-[#1B7F5A] rounded-full transition-all"
                                    :style="{ width: stats.persen_guru_aktif + '%' }"
                                ></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span>Siswa Aktif</span>
                                <span>{{ stats.persen_siswa_aktif }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-[#1B7F5A] rounded-full transition-all"
                                    :style="{ width: stats.persen_siswa_aktif + '%' }"
                                ></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span>Kegiatan Tervalidasi</span>
                                <span>{{ stats.persen_kegiatan_tervalidasi }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-[#1B7F5A] rounded-full transition-all"
                                    :style="{ width: stats.persen_kegiatan_tervalidasi + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Aktivitas Sistem (dinamis dari recentActivity) -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-semibold text-gray-800">Aktivitas Sistem</h3>
                        <span class="text-sm text-gray-400">realtime</span>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div
                            v-for="(act, i) in recentActivity"
                            :key="i"
                            class="flex items-center justify-between"
                        >
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <p>{{ act.description }}</p>
                            </div>
                            <span class="text-gray-400">{{ act.waktu }}</span>
                        </div>
                        <p v-if="!recentActivity?.length" class="text-gray-400">
                            Belum ada aktivitas.
                        </p>
                    </div>
                </div>

                <!-- Distribusi Role -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-5">Distribusi Pengguna</h3>
                    <div class="flex items-center justify-center h-48">
                        <div class="text-center">
                            <h2 class="text-5xl font-bold text-[#0F3D2E]">
                                {{ stats.total_users }}
                            </h2>
                            <p class="text-gray-500 mt-2">Total Pengguna Terdaftar</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-5 mt-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span>Guru ({{ stats.total_guru }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-600"></div>
                            <span>Siswa ({{ stats.total_siswa }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>