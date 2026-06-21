<script setup>
import GuruLayout from '@/Layouts/GuruLayout.vue'

// ====================================================================
// PROPS — struktur ini DIAMBIL PERSIS dari dokumentasi API Nahdia
// untuk endpoint GET /guru/dashboard (Inertia::render('Guru/Dashboard', ...)).
// Nama field di sini HARUS sama persis dengan yang dikirim controller,
// termasuk huruf besar/kecil dan snake_case/camelCase-nya — Inertia tidak
// akan otomatis menyesuaikan kalau ada typo nama field.
// ====================================================================
const props = defineProps({
    stats: {
        type: Object,
        required: true,
        // shape: { total_students, today_reports, average_compliance,
        //          pending_evaluations, submission_rate }
    },
    topPerformers: { type: Array, default: () => [] },     // top 5 by compliance_percentage tertinggi
    needAttention: { type: Array, default: () => [] },     // siswa compliance < 60%
    notSubmitted: { type: Array, default: () => [] },      // siswa belum lapor hari ini
    latestEvaluations: { type: Array, default: () => [] }, // 5 evaluasi terakhir guru
    topActive: { type: Array, default: () => [] },         // top 5 by JUMLAH HARI SUBMIT (beda dari topPerformers)
    classWeeklyProgress: { type: Array, default: () => [] }, // 7 elemen: { field, label, persentase }
    classMonthlyChart: { type: Array, default: () => [] },   // 30 elemen: { tanggal, rata_rata_kepatuhan }
})

// Helper kecil: format tanggal "2026-05-22" jadi "22 Mei" biar enak dibaca,
// dipakai di sumbu grafik 30 hari. Pure function, tidak butuh data luar.
function formatTanggalPendek(tanggalISO) {
    const bulanSingkat = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
    const [, bulan, hari] = tanggalISO.split('-')
    return `${parseInt(hari)} ${bulanSingkat[parseInt(bulan) - 1]}`
}

// Cari nilai tertinggi di classMonthlyChart, dipakai untuk highlight bar
// puncak aktivitas (gantinya highlightIndexes manual yang dulu saya hardcode).
const puncakAktivitas = props.classMonthlyChart.length
    ? Math.max(...props.classMonthlyChart.map((d) => d.rata_rata_kepatuhan))
    : 0
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
                    Ringkasan aktivitas dan perkembangan siswa
                </p>
            </div>

            <a
                :href="route('guru.monitoring.index')"
                class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-5 py-2.5 rounded-xl font-medium transition flex items-center gap-2 text-sm"
            >👁 Monitor Kelas
            </a>
        </div>

        <!-- KONDISI: GURU BELUM PUNYA KELAS — sesuai dokumentasi API,
             kalau guru belum ditugaskan ke kelas manapun semua array kosong
             dan semua angka stats = 0, bukan error. Tampilkan pesan ramah. -->
        <div
            v-if="stats.total_students === 0"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-gray-400 text-sm">
                Anda belum ditugaskan sebagai wali kelas manapun. Hubungi admin untuk penugasan kelas.
            </p>
        </div>

        <template v-else>

            <!-- 5 KARTU STATISTIK -->
            <div class="grid md:grid-cols-5 gap-4">

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-sm">Total Siswa</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-2">{{ stats.total_students }}</h2>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-sm">Lapor Hari Ini</p>
                    <h2 class="text-2xl font-bold text-blue-600 mt-2">{{ stats.today_reports }}</h2>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-sm">Rata-rata Kepatuhan</p>
                    <h2 class="text-2xl font-bold text-[#1B7F5A] mt-2">{{ stats.average_compliance }}%</h2>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-sm">Menunggu Evaluasi</p>
                    <h2 class="text-2xl font-bold text-orange-500 mt-2">{{ stats.pending_evaluations }}</h2>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-sm">Tingkat Pelaporan</p>
                    <h2 class="text-2xl font-bold text-blue-500 mt-2">{{ stats.submission_rate }}%</h2>
                </div>

            </div>

            <!-- BANNER: SISWA BELUM LAPOR HARI INI -->
            <div
                v-if="notSubmitted.length > 0"
                class="bg-[#0F3D2E] text-white rounded-2xl px-5 py-4 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="text-xl">🔔</span>
                    <div>
                        <p class="font-semibold text-sm">{{ notSubmitted.length }} Siswa Belum Lapor Hari Ini</p>
                        <p class="text-green-200 text-xs mt-0.5">
                            {{ notSubmitted.slice(0, 3).map(s => s.name ?? s.nama).join(', ') }}
                            <span v-if="notSubmitted.length > 3"> dan {{ notSubmitted.length - 3 }} lainnya</span>
                        </p>
                    </div>
                </div>

                <a
                    :href="route('guru.monitoring.index')"
                    class="text-sm font-medium bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition shrink-0"
                >Lihat Detail →
                </a>
            </div>

            <!-- MAIN GRID -->
            <div class="grid lg:grid-cols-3 gap-6">

                <!-- PROGRES KEBIASAAN KELAS - MINGGU INI -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">

                    <h3 class="text-lg font-bold text-gray-800 mb-6">
                        Progres Kebiasaan Kelas — Minggu Ini
                    </h3>

                    <div v-if="classWeeklyProgress.length === 0" class="text-sm text-gray-400 text-center py-6">
                        Belum ada data kebiasaan minggu ini
                    </div>

                    <div v-else class="space-y-5">
                        <div v-for="habit in classWeeklyProgress" :key="habit.field">
                            <div class="flex justify-between mb-2">
                                <span class="font-medium text-gray-700 text-sm">{{ habit.label }}</span>
                                <span class="font-semibold text-gray-600 text-sm">{{ habit.persentase }}%</span>
                            </div>
                            <div class="w-full h-2.5 rounded-full bg-gray-100">
                                <div
                                    class="h-2.5 rounded-full bg-[#1B7F5A]"
                                    :style="{ width: habit.persentase + '%' }"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SISWA TERAKTIF (topActive: by JUMLAH HARI SUBMIT) -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">

                    <h3 class="text-lg font-bold text-gray-800 mb-1">
                        Siswa Teraktif
                    </h3>
                    <p class="text-xs text-gray-400 mb-5">Berdasarkan jumlah hari melapor</p>

                    <div v-if="topActive.length === 0" class="text-sm text-gray-400 text-center py-6">
                        Belum ada data
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="(student, index) in topActive"
                            :key="student.id ?? index"
                            class="flex items-center gap-3">
                            <span class="w-5 text-sm font-semibold text-gray-400">{{ index + 1 }}</span>

                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center font-semibold text-green-700 text-xs shrink-0">
                                {{ (student.name ?? student.nama)[0] }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-700 text-sm truncate">{{ student.name ?? student.nama }}</p>
                            </div>

                            <span class="text-sm font-bold text-[#1B7F5A] shrink-0">
                                {{ student.total_hari_lapor ?? student.jumlah_hari ?? '-' }} hari
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2 KOLOM: PERLU PERHATIAN + EVALUASI TERBARU -->
            <div class="grid lg:grid-cols-2 gap-6">

                <!-- PERLU PERHATIAN (compliance < 60%) -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Perlu Perhatian</h3>
                    <p class="text-xs text-gray-400 mb-5">Siswa dengan kepatuhan di bawah 60%</p>

                    <div v-if="needAttention.length === 0" class="text-sm text-gray-400 text-center py-6">
                        Tidak ada siswa yang perlu perhatian khusus saat ini 🎉
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="(student, index) in needAttention"
                            :key="student.id ?? index"
                            class="flex items-center justify-between border border-red-100 bg-red-50/50 rounded-xl px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-semibold text-xs shrink-0">
                                    {{ (student.name ?? student.nama)[0] }}
                                </div>
                                <p class="font-medium text-gray-700 text-sm">{{ student.name ?? student.nama }}</p>
                            </div>
                            <span class="text-sm font-bold text-red-500">
                                {{ student.compliance_percentage ?? student.compliance ?? 0 }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- EVALUASI TERBARU -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-5">Evaluasi Terbaru</h3>

                    <div v-if="latestEvaluations.length === 0" class="text-sm text-gray-400 text-center py-6">
                        Belum ada evaluasi yang diberikan
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="(item, index) in latestEvaluations"
                            :key="item.id ?? index"
                            class="flex items-start justify-between border-b border-gray-50 last:border-0 pb-3 last:pb-0">
                            <div>
                                <p class="font-medium text-gray-700 text-sm">{{ item.nama ?? item.student_name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ item.catatan_guru ?? '-' }}</p>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 rounded-full bg-green-100 text-green-700 shrink-0">
                                {{ item.nilai_guru }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAFIK 30 HARI TERAKHIR -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">

                <h3 class="text-lg font-bold text-gray-800 mb-1">
                    Profil Kegiatan Kelas — 30 Hari Terakhir
                </h3>
                <p class="text-gray-400 text-xs mb-5">
                    Rata-rata kepatuhan kelas per hari
                </p>

                <div v-if="classMonthlyChart.length === 0" class="text-sm text-gray-400 text-center py-10">
                    Belum ada data aktivitas
                </div>

                <div v-else class="flex items-end justify-between gap-1 h-40">
                    <div
                        v-for="(day, i) in classMonthlyChart"
                        :key="i"
                        class="flex-1 rounded-t transition"
                        :class="day.rata_rata_kepatuhan === puncakAktivitas ? 'bg-[#F59E0B]' : 'bg-[#1B7F5A]'"
                        :style="{ height: day.rata_rata_kepatuhan + '%' }"
                        :title="`${formatTanggalPendek(day.tanggal)}: ${day.rata_rata_kepatuhan}%`"
                    />
                </div>
            </div>
        </template>
    </div>
</GuruLayout>
</template>