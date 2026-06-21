<script setup>
import GuruLayout from '@/Layouts/GuruLayout.vue'
import BarChartCard from '@/Components/BarChartCard.vue'

// ====================================================================
// DATA DUMMY — struktur ini SENGAJA dibuat menyerupai bentuk data
// yang nantinya akan dikirim controller Laravel via Inertia::render().
// Begitu backend siap, cukup ganti bagian ini dengan:
//   const props = defineProps({ stats: Object, habitProgress: Array, ... })
// dan hapus seluruh konstanta di bawah ini — logika template TIDAK perlu diubah.
// ====================================================================

// 4 KARTU STATISTIK — sesuai SKPL hal. 35:
// "total siswa kelas, rata-rata kepatuhan kelas, jumlah siswa yang belum
// input hari ini, dan jumlah siswa berprestasi (kebiasaan 100%)"
const stats = {
    totalSiswa: 32,
    rataRataKepatuhan: 84,
    belumInputHariIni: 4,
    siswaBerprestasi: 5,
}

// BANNER PENGINGAT — sesuai SKPL: "banner pengingat apabila ada rekap
// mingguan yang belum diselesaikan"
const pengingatRekap = {
    show: true,
    pesan: 'Harap lengkapi penilaian 8 siswa sebelum Jumat, 20 Januari 2025',
}

// PROGRES KEBIASAAN KELAS - MINGGU INI
const habitProgress = [
    { name: 'Bangun Pagi', value: 92 },
    { name: 'Ibadah & Doa', value: 78 },
    { name: 'Makan Sehat & Bergizi', value: 85 },
    { name: 'Olahraga', value: 70 },
    { name: 'Belajar Mandiri', value: 88 },
    { name: 'Aktivitas Sosial', value: 65 },
    { name: 'Tidur Cepat', value: 80 },
]

// SISWA TERAKTIF — berdasarkan persentase kepatuhan, ditampilkan dengan ranking
const topStudents = [
    { rank: 1, name: 'Kurnia Ardiningrum', percent: 98 },
    { rank: 2, name: 'Azzah Fauziya', percent: 94 },
    { rank: 3, name: 'Nayla Cahaya', percent: 91 },
    { rank: 4, name: 'Sabrina Alya', percent: 87 },
    { rank: 5, name: 'Nathania', percent: 83 },
]

// GRAFIK "PROFIL KEGIATAN KELAS - 30 HARI TERAKHIR"
// labels: tanggal, values: jumlah kegiatan tercatat pada tanggal itu
const chart30Hari = {
    labels: ['23', '24', '25', '26', '27', '28', '29', '30', '31', '1', '2', '3'],
    values: [24, 26, 22, 28, 30, 25, 27, 31, 29, 26, 28, 24],
    // index bar yang jadi puncak aktivitas, diberi warna oranye (sesuai mockup SKPL)
    highlightIndexes: [4, 8],
}
</script>

<template>

<GuruLayout>

    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Dashboard Wali Kelas
                </h1>
                <p class="text-gray-500 mt-1 text-sm">
                    Ringkasan aktivitas dan perkembangan siswa kelas 7A
                </p>
            </div>

            <a
                href="/guru/monitoring"
                class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-5 py-2.5 rounded-xl font-medium transition flex items-center gap-2 text-sm"
            >
                👁 Monitor Kelas
            </a>
        </div>

        <!-- BANNER PENGINGAT REKAP MINGGUAN -->
        <div
            v-if="pengingatRekap.show"
            class="bg-[#0F3D2E] text-white rounded-2xl px-5 py-4 flex items-center justify-between gap-4 flex-wrap"
        >
            <div class="flex items-center gap-3">
                <span class="text-xl">🔔</span>
                <div>
                    <p class="font-semibold text-sm">Pengingat: Rekap Mingguan Belum Diselesaikan</p>
                    <p class="text-green-200 text-xs mt-0.5">{{ pengingatRekap.pesan }}</p>
                </div>
            </div>

            <a
                href="/guru/rekap"
                class="text-sm font-medium bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition shrink-0"
            >
                Lihat Rekap →
            </a>
        </div>

        <!-- STATISTIK -->
        <div class="grid md:grid-cols-4 gap-5">

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-sm">Total Siswa Kelas</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ stats.totalSiswa }}</h2>
                <p class="text-xs text-gray-400 mt-1">28 aktif hari ini</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-sm">Rata-rata Kepatuhan</p>
                <h2 class="text-3xl font-bold text-[#1B7F5A] mt-2">{{ stats.rataRataKepatuhan }}%</h2>
                <p class="text-xs text-gray-400 mt-1">+4% vs minggu lalu</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-sm">Belum Input Hari Ini</p>
                <h2 class="text-3xl font-bold text-orange-500 mt-2">{{ stats.belumInputHariIni }}</h2>
                <p class="text-xs text-gray-400 mt-1">Perlu follow-up</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-sm">Siswa Berprestasi</p>
                <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ stats.siswaBerprestasi }}</h2>
                <p class="text-xs text-gray-400 mt-1">Kebiasaan 100%</p>
            </div>

        </div>

        <!-- MAIN GRID -->
        <div class="grid lg:grid-cols-3 gap-6">

            <!-- PROGRES KEBIASAAN -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">

                <h3 class="text-lg font-bold text-gray-800 mb-6">
                    Progres Kebiasaan Kelas — Minggu Ini
                </h3>

                <div class="space-y-5">
                    <div v-for="habit in habitProgress" :key="habit.name">
                        <div class="flex justify-between mb-2">
                            <span class="font-medium text-gray-700 text-sm">{{ habit.name }}</span>
                            <span class="font-semibold text-gray-600 text-sm">{{ habit.value }}%</span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-gray-100">
                            <div
                                class="h-2.5 rounded-full bg-[#1B7F5A]"
                                :style="{ width: habit.value + '%' }"
                            />
                        </div>
                    </div>
                </div>

            </div>

            <!-- SISWA TERAKTIF -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">

                <h3 class="text-lg font-bold text-gray-800 mb-6">
                    Siswa Teraktif
                </h3>

                <div class="space-y-4">
                    <div
                        v-for="student in topStudents"
                        :key="student.rank"
                        class="flex items-center gap-3"
                    >
                        <span class="w-5 text-sm font-semibold text-gray-400">{{ student.rank }}</span>

                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center font-semibold text-green-700 text-xs shrink-0">
                            {{ student.name[0] }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-700 text-sm truncate">{{ student.name }}</p>
                            <div class="w-full h-1.5 rounded-full bg-gray-100 mt-1">
                                <div
                                    class="h-1.5 rounded-full bg-[#1B7F5A]"
                                    :style="{ width: student.percent + '%' }"
                                />
                            </div>
                        </div>

                        <span class="text-sm font-bold text-[#1B7F5A] shrink-0">{{ student.percent }}%</span>
                    </div>
                </div>

                <!-- AKSES CEPAT -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Akses Cepat</p>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="/guru/monitoring" class="text-xs font-medium text-center bg-gray-50 hover:bg-gray-100 text-gray-700 px-3 py-2.5 rounded-lg transition">
                            📋 Monitor Kelas
                        </a>
                        <a href="/guru/rekap" class="text-xs font-medium text-center bg-gray-50 hover:bg-gray-100 text-gray-700 px-3 py-2.5 rounded-lg transition">
                            📊 Buat Rekap
                        </a>
                        <a href="/guru/rekap" class="text-xs font-medium text-center bg-gray-50 hover:bg-gray-100 text-gray-700 px-3 py-2.5 rounded-lg transition">
                            ⬇ Ekspor Data
                        </a>
                        <a href="/guru/monitoring" class="text-xs font-medium text-center bg-gray-50 hover:bg-gray-100 text-gray-700 px-3 py-2.5 rounded-lg transition">
                            ✏ Beri Catatan
                        </a>
                    </div>
                </div>

            </div>

        </div>

        <!-- GRAFIK PROFIL KEGIATAN KELAS - 30 HARI TERAKHIR -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">

            <h3 class="text-lg font-bold text-gray-800 mb-1">
                Profil Kegiatan Kelas — 30 Hari Terakhir
            </h3>
            <p class="text-gray-400 text-xs mb-5">
                Jumlah siswa yang mencatat kegiatan per hari
            </p>

            <BarChartCard
                :labels="chart30Hari.labels"
                :values="chart30Hari.values"
                :highlight-indexes="chart30Hari.highlightIndexes"
                :height="200"
            />

        </div>

    </div>

</GuruLayout>

</template>