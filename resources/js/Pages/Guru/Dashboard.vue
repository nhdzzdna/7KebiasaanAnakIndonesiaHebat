<script setup>
import GuruLayout from '@/Layouts/GuruLayout.vue'
import BarChartCard from '@/Components/BarChartCard.vue'

const props = defineProps({
    stats: { type: Object, required: true },
    topPerformers: { type: Array, default: () => [] },
    needAttention: { type: Array, default: () => [] },
    notSubmitted: { type: Array, default: () => [] },
    latestEvaluations: { type: Array, default: () => [] },
    topActive: { type: Array, default: () => [] },
    classWeeklyProgress: { type: Array, default: () => [] },
    classMonthlyChart: { type: Array, default: () => [] },
})

// PROGRES KEBIASAAN KELAS - MINGGU INI
// backend kirim field 'persentase' dan 'label'
const habitProgress = (props.classWeeklyProgress ?? []).map(h => ({
    name: h.label ?? '-',
    value: h.persentase ?? 0,
}))

// SISWA TERAKTIF — berdasarkan jumlah hari submit (topActive)
const maxSubmit = props.topActive.length
    ? Math.max(...props.topActive.map(i => i.total_submit ?? 0), 1)
    : 1

const topStudents = props.topActive.map((item, i) => ({
    rank: i + 1,
    name: item.user?.name ?? '-',
    totalSubmit: item.total_submit ?? 0,
    percent: Math.round(((item.total_submit ?? 0) / maxSubmit) * 100),
}))

// GRAFIK "PROFIL KEGIATAN KELAS - 30 HARI TERAKHIR"
const chartValues = (props.classMonthlyChart ?? []).map(d => d.rata_rata_kepatuhan ?? 0)
const chart30Hari = {
    labels: (props.classMonthlyChart ?? []).map(d => d.tanggal?.slice(8, 10) ?? ''),
    values: chartValues,
    highlightIndexes: chartValues
        .map((val, i) => val < 70 ? i : null)
        .filter(i => i !== null),
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
                    Ringkasan aktivitas dan perkembangan siswa kelas
                </p>
            </div>

            <a
                href="/guru/monitoring"
                class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-5 py-2.5 rounded-xl font-medium transition flex items-center gap-2 text-sm"
            >
                👁 Monitor Kelas
            </a>
        </div>

        <!-- BANNER PENGINGAT EVALUASI -->
        <div
            v-if="stats.pending_evaluations > 0"
            class="bg-[#0F3D2E] text-white rounded-2xl px-5 py-4 flex items-center justify-between gap-4 flex-wrap"
        >
            <div class="flex items-center gap-3">
                <span class="text-xl">🔔</span>
                <div>
                    <p class="font-semibold text-sm">Pengingat: Evaluasi Belum Diselesaikan</p>
                    <p class="text-green-200 text-xs mt-0.5">
                        Ada {{ stats.pending_evaluations }} kegiatan siswa yang menunggu evaluasi kamu
                    </p>
                </div>
            </div>

            
                <a href="/guru/monitoring"
                class="text-sm font-medium bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition shrink-0"
            >
                Lihat Monitoring →
            </a>
        </div>

        <!-- STATISTIK -->
        <div class="grid md:grid-cols-4 gap-5">

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-sm">Total Siswa Kelas</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ stats.total_students }}</h2>
                <p class="text-xs text-gray-400 mt-1">{{ stats.today_reports }} aktif hari ini</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-sm">Rata-rata Kepatuhan</p>
                <h2 class="text-3xl font-bold text-[#1B7F5A] mt-2">{{ stats.average_compliance }}%</h2>
                <p class="text-xs text-gray-400 mt-1">Tingkat pengumpulan {{ stats.submission_rate }}%</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-sm">Belum Input Hari Ini</p>
                <h2 class="text-3xl font-bold text-orange-500 mt-2">{{ notSubmitted.length }}</h2>
                <p class="text-xs text-gray-400 mt-1">Perlu follow-up</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-sm">Menunggu Evaluasi</p>
                <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ stats.pending_evaluations }}</h2>
                <p class="text-xs text-gray-400 mt-1">Kegiatan belum dinilai</p>
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
                            {{ student.name?.[0] ?? '?' }}
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

                        <span class="text-sm font-bold text-[#1B7F5A] shrink-0">{{ student.totalSubmit }} hari</span>
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
                Rata-rata kepatuhan kebiasaan kelas per hari (%)
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