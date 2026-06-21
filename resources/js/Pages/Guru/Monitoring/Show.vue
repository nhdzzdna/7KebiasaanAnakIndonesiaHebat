<script setup>
import GuruLayout from '@/Layouts/GuruLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

// BUG FIX: sebelumnya pakai data dummy hardcode "Budi Santoso" (id: 2)
// jadi siapapun yang diklik di halaman Index, isinya selalu sama.
// Sekarang pakai defineProps, datanya beneran ikut siswa yang dipilih.
const props = defineProps({
    kegiatan: Object,
    siswaSummary: Object,
    chart12Hari: Array,
})

const k = props.kegiatan

const siswa = computed(() => ({
    id: k.id,
    nama: k.user?.name ?? '-',
    kelas: k.user?.studentProfile?.schoolClass?.name ?? '-',
    tanggal: k.tanggal,
    kepatuhanBulanIni: props.siswaSummary.compliance_bulan_ini,
    streak: props.siswaSummary.streak,
    totalHari: props.siswaSummary.total_hari_bulan_ini,
    statusAkun: props.siswaSummary.status_akun,
}))

const grafik12Hari = computed(() => ({
    labels: props.chart12Hari.map(c => c.tanggal.slice(8, 10)),
    values: props.chart12Hari.map(c => c.compliance),
}))

const kebiasaan = computed(() => [
    { icon: '🌅', nama: 'Bangun Pagi', status: !!k.waktu_bangun, detail: k.waktu_bangun ? `Bangun pukul ${k.waktu_bangun}` : '-', foto: null },
    { icon: '🙏', nama: 'Ibadah & Doa', status: (k.detail_ibadah_centang?.length > 0), detail: k.detail_ibadah_lain || (k.detail_ibadah_centang || []).join(', ') || '-', foto: null },
    { icon: '🥗', nama: 'Makan Sehat', status: !!k.menu_makan, detail: k.menu_makan || '-', foto: null },
    { icon: '🏃', nama: 'Olahraga', status: !!k.jenis_olahraga, detail: k.jenis_olahraga ? `${k.jenis_olahraga} (${k.durasi_olahraga ?? 0} menit)` : '-', foto: null },
    { icon: '📚', nama: 'Belajar Mandiri', status: !!k.belajar_mandiri, detail: k.belajar_mandiri ? `${k.belajar_mandiri} (${k.durasi_belajar ?? 0} menit)` : '-', foto: null },
    { icon: '🤝', nama: 'Aktivitas Sosial', status: !!k.aktivitas_sosial, detail: k.aktivitas_sosial || '-', foto: null },
    { icon: '😴', nama: 'Tidur Cepat', status: !!k.waktu_tidur, detail: k.waktu_tidur ? `Tidur pukul ${k.waktu_tidur}` : '-', foto: k.bukti_foto ? `/storage/${k.bukti_foto}` : null },
])

const selfieValidasi = computed(() => ({
    foto: k.selfie_validasi ? `/storage/${k.selfie_validasi}` : null,
    valid: !!k.selfie_validasi,
}))

const bukaAccordion = ref(null)
function toggleAccordion(index) {
    bukaAccordion.value = bukaAccordion.value === index ? null : index
}

const pilihanNilai = [
    { value: 'A', label: 'A — Baik Sekali' },
    { value: 'B', label: 'B — Baik' },
    { value: 'C', label: 'C — Cukup' },
    { value: 'D', label: 'D — Perlu Bimbingan' },
]

// Pre-fill kalau laporan ini sudah pernah dievaluasi sebelumnya
const nilai = ref(k.nilai_guru ?? null)
const evaluasi = ref(k.catatan_guru ?? '')
const errorNilai = ref('')
const savedMessage = ref('')

function simpanEvaluasi() {
    if (!nilai.value) {
        errorNilai.value = 'Nilai predikat wajib dipilih'
        savedMessage.value = ''
        return
    }
    errorNilai.value = ''

    // BUG FIX: backend butuh key "nilai_guru" & "catatan_guru" (bukan "nilai"/"catatan")
    router.patch(`/guru/monitoring/${k.id}/evaluasi`, {
        nilai_guru: nilai.value,
        catatan_guru: evaluasi.value,
    }, {
        onSuccess: () => {
            savedMessage.value = 'Evaluasi berhasil disimpan'
        },
        onError: () => {
            errorNilai.value = 'Gagal menyimpan, coba lagi'
        },
    })
}
</script>

<template>
<GuruLayout>
    <div class="space-y-5">
        <!-- ===== HEADER ===== -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <!-- Breadcrumb navigasi: Monitoring Kelas > Detail Siswa -->
                <div class="flex items-center gap-2 text-sm">
                    <!-- Link kembali ke halaman monitoring, pakai Link Inertia -->
                    <Link href="/guru/monitoring" class="text-gray-400 hover:text-gray-600">
                        Monitoring Kelas
                    </Link>
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-700 font-medium">Detail Siswa</span>
                </div>
                <!-- Nama siswa dan kelas sebagai judul halaman -->
                <h1 class="text-xl font-bold text-gray-800 mt-1">
                    {{ siswa.nama }} — Kelas {{ siswa.kelas }}
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ siswa.tanggal }}</p>
            </div>
            <!-- Tombol simpan evaluasi di kanan atas (shortcut selain tombol bawah) -->
            <button
                @click="simpanEvaluasi"
                class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition"
            >
                💾 Simpan Evaluasi
            </button>
        </div>
        <!-- Layout 3 kolom: panel kiri (1/4), tengah (2/4), kanan (1/4) -->
        <div class="grid lg:grid-cols-4 gap-5 items-start">
            <!-- ===== PANEL KIRI: RINGKASAN SISWA ===== -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
                <!-- Avatar inisial huruf pertama nama siswa -->
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-xl mx-auto mb-3">
                    {{ siswa.nama[0] }}
                </div>
                <!-- Nama dan kelas siswa -->
                <h3 class="font-bold text-gray-800">{{ siswa.nama }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Kelas {{ siswa.kelas }}</p>
                <!-- Badge status akun (Aktif/Nonaktif) -->
                <span class="inline-block mt-2 text-xs px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                    {{ siswa.statusAkun }}
                </span>
                <!-- Statistik 3 angka: kepatuhan, streak, total hari -->
                <div class="grid grid-cols-1 gap-3 mt-5 pt-5 border-t border-gray-100">
                    <!-- Persentase kepatuhan bulan ini -->
                    <div>
                        <p class="text-2xl font-bold text-[#1B7F5A]">{{ siswa.kepatuhanBulanIni }}%</p>
                        <p class="text-xs text-gray-400">Kepatuhan Bulan Ini</p>
                    </div>
                    <!-- Streak hari berturut-turut -->
                    <div>
                        <p class="text-2xl font-bold text-gray-800">🔥 {{ siswa.streak }}</p>
                        <p class="text-xs text-gray-400">Streak Hari</p>
                    </div>
                    <!-- Total hari yang sudah dicatat -->
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ siswa.totalHari }}</p>
                        <p class="text-xs text-gray-400">Total Hari Tercatat</p>
                    </div>
                </div>
            </div>
            <!-- ===== KOLOM TENGAH: GRAFIK + DETAIL KEBIASAAN ===== -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Card grafik perkembangan 12 hari terakhir -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-700 text-sm mb-4">
                        📈 Grafik Perkembangan — 12 Hari Terakhir
                    </h3>
                    <div class="flex items-end justify-between gap-1.5 h-32">
                        <div
                            v-for="(val, i) in grafik12Hari.values"
                            :key="i"
                            class="flex-1 flex flex-col items-center gap-1"
                        >
                            <div
                                class="w-full rounded-t"
                                :class="val >= 80 ? 'bg-[#1B7F5A]' : 'bg-gray-200'"
                                :style="{ height: val + '%' }"
                                :title="val + '%'"
                            />
                            <span class="text-[9px] text-gray-400">{{ grafik12Hari.labels[i] }}</span>
                        </div>
                    </div>
                </div>
                <!-- Card detail 7 kebiasaan dengan accordion -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b">
                        <h3 class="font-bold text-gray-700 text-sm">📋 Detail 7 Kebiasaan — Hari Ini</h3>
                    </div>
                    <div v-for="(k, i) in kebiasaan" :key="i" class="border-b border-gray-50 last:border-0">
                        <div
                            class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-gray-50"
                            @click="toggleAccordion(i)"
                        >
                            <div class="flex items-center gap-3">
                                <span>{{ k.icon }}</span>
                                <span class="font-medium text-gray-700 text-sm">{{ k.nama }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span
                                    class="text-xs px-2 py-1 rounded-full"
                                    :class="k.status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'"
                                >
                                    {{ k.status ? '✓ Lengkap' : '— Tidak Dicatat' }}
                                </span>
                                <span
                                    class="text-xs text-gray-400 transition-transform"
                                    :class="bukaAccordion === i ? 'rotate-180' : ''"
                                >▼</span>
                            </div>
                        </div>
                        <div v-if="bukaAccordion === i" class="px-5 pb-4 bg-gray-50 space-y-3">
                            <p class="text-sm text-gray-500">{{ k.detail }}</p>
                            <button
                                v-if="k.foto"
                                class="text-xs font-medium text-[#1B7F5A] border border-[#1B7F5A] rounded-lg px-3 py-1.5 hover:bg-[#1B7F5A] hover:text-white transition"
                                @click.stop
                            >
                                🖼 Lihat Foto
                            </button>
                            <p v-else class="text-xs text-gray-400">
                                Tidak ada foto bukti dilampirkan
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ===== KOLOM KANAN: SELFIE + PENILAIAN ===== -->
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b flex items-center justify-between">
                        <h4 class="font-bold text-sm text-gray-700">📸 Selfie Validasi</h4>
                        <span
                            class="text-xs px-2 py-0.5 rounded-full"
                            :class="selfieValidasi.valid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                        >
                            {{ selfieValidasi.valid ? '✓ Selfie Valid' : '✗ Tidak Valid' }}
                        </span>
                    </div>
                    <div class="p-4">
                        <img :src="selfieValidasi.foto" class="w-full h-64 object-cover rounded-xl" />
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h4 class="font-bold text-sm text-gray-700 mb-3">🏆 Penilaian & Catatan Guru</h4>
                    <p class="text-xs text-gray-400 mb-2">
                        Nilai / Predikat <span class="text-red-500">*</span>
                    </p>
                    <div class="grid grid-cols-2 gap-2 mb-1">
                        <button
                            v-for="opt in pilihanNilai"
                            :key="opt.value"
                            @click="nilai = opt.value; errorNilai = ''"
                            class="text-xs font-medium px-3 py-2.5 rounded-xl border transition text-left"
                            :class="nilai === opt.value
                                ? 'bg-[#1B7F5A] text-white border-[#1B7F5A]'
                                : 'border-gray-200 text-gray-600 hover:border-[#1B7F5A]'"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                    <p v-if="errorNilai" class="text-xs text-red-500 mt-1 mb-2">
                        {{ errorNilai }}
                    </p>
                    <p class="text-xs text-gray-400 mt-4 mb-2">
                        Catatan untuk Siswa & Orang Tua
                    </p>
                    <textarea
                        v-model="evaluasi"
                        rows="4"
                        placeholder="Tulis catatan evaluasi (opsional)..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#1B7F5A] resize-none"
                    />
                    <p v-if="savedMessage" class="text-xs text-green-600 mt-2">
                        {{ savedMessage }}
                    </p>
                    <div class="flex gap-2 mt-4">
                        <Link
                            href="/guru/monitoring"
                            class="flex-1 text-center border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition"
                        >
                            Batal
                        </Link>
                        <button
                            @click="simpanEvaluasi"
                            class="flex-1 bg-[#1B7F5A] hover:bg-[#166347] text-white py-2.5 rounded-xl text-sm font-semibold transition"
                        >
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</GuruLayout>
</template>