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

const siswa = computed(() => {
    const s = props.siswaSummary ?? {}
    return {
        id: k.id,
        nama: k.user?.name ?? '-',
        kelas: k.user?.studentProfile?.schoolClass?.name ?? '-',
        tanggal: k.tanggal,
        kepatuhanBulanIni: s.compliance_bulan_ini ?? 0,
        streak: s.streak ?? 0,
        totalHari: s.total_hari_bulan_ini ?? 0,
        statusAkun: s.status_akun ?? 'aktif',
    }
})

// Format tanggal jadi "Selasa, 18 Januari 2026"
const hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
const bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
const tanggalFormatted = computed(() => {
    if (!k.tanggal) return '-'
    const d = new Date(k.tanggal)
    return `${hariList[d.getDay()]}, ${d.getDate()} ${bulanList[d.getMonth()]} ${d.getFullYear()}`
})

const grafik12Hari = computed(() => ({
    labels: (props.chart12Hari ?? []).map(c => c.tanggal?.slice(8, 10) ?? ''),
    values: (props.chart12Hari ?? []).map(c => c.compliance ?? 0),
}))

// Warna bar grafik 3 tingkat (hijau tua / hijau sage / oranye)
function barColor(val) {
    if (val >= 85) return 'bg-[#1B7F5A]'
    if (val >= 60) return 'bg-[#A9D6BD]'
    return 'bg-[#F5A623]'
}

// Daftar 5 waktu sholat utk checklist "Ibadah & Doa"
const sholatList = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya']

// Background icon kotak per kebiasaan
const habitColors = ['bg-orange-100', 'bg-pink-100', 'bg-green-100', 'bg-sky-100', 'bg-emerald-100', 'bg-yellow-100', 'bg-amber-100']

const kebiasaan = computed(() => [
    {
        icon: '🌅', nama: 'Bangun Pagi',
        status: !!k.waktu_bangun,
        foto: k.bukti_foto_bangun ? `/storage/${k.bukti_foto_bangun}` : null,
        fields: [
            { label: 'Waktu Bangun', value: k.waktu_bangun || null },
            { label: 'Keterangan', value: k.keterangan_bangun || null },
        ],
    },
    {
        icon: '🙏', nama: 'Ibadah & Doa',
        status: (Array.isArray(k.detail_ibadah_centang) && k.detail_ibadah_centang.length > 0) || !!k.detail_ibadah_lain,
        foto: k.bukti_foto_ibadah ? `/storage/${k.bukti_foto_ibadah}` : null,
        checklist: sholatList.map(s => ({
            label: s,
            checked: Array.isArray(k.detail_ibadah_centang) && k.detail_ibadah_centang.includes(s)
        })),
        fields: [{ label: 'Lainnya', value: k.detail_ibadah_lain || null }],
    },
    {
        icon: '🥗', nama: 'Makan Sehat & Bergizi',
        status: !!k.menu_makan,
        foto: k.bukti_foto_makan ? `/storage/${k.bukti_foto_makan}` : null,
        fields: [
            { label: 'Menu Makan', value: k.menu_makan || null },
            { label: 'Jumlah Air', value: k.jumlah_air ? `${k.jumlah_air} gelas` : null },
        ],
    },
    {
        icon: '🏃', nama: 'Olahraga',
        status: !!k.jenis_olahraga,
        foto: k.bukti_foto_olahraga ? `/storage/${k.bukti_foto_olahraga}` : null,
        fields: [
            { label: 'Jenis Olahraga', value: k.jenis_olahraga || null },
            { label: 'Durasi', value: k.durasi_olahraga ? `${k.durasi_olahraga} menit` : null },
        ],
    },
    {
        icon: '📚', nama: 'Belajar Mandiri',
        status: !!k.belajar_mandiri,
        foto: k.bukti_foto_belajar ? `/storage/${k.bukti_foto_belajar}` : null,
        fields: [
            { label: 'Materi', value: k.belajar_mandiri || null },
            { label: 'Durasi', value: k.durasi_belajar ? `${k.durasi_belajar} menit` : null },
        ],
    },
    {
        icon: '🤝', nama: 'Aktivitas Sosial',
        status: !!k.aktivitas_sosial,
        foto: k.bukti_foto_sosial ? `/storage/${k.bukti_foto_sosial}` : null,
        fields: [{ label: 'Kegiatan', value: k.aktivitas_sosial || null }],
    },
    {
        icon: '😴', nama: 'Tidur Cepat',
        status: !!k.waktu_tidur,
        foto: k.bukti_foto ? `/storage/${k.bukti_foto}` : null,
        fields: [
            { label: 'Waktu Tidur', value: k.waktu_tidur || null },
            { label: 'Keterangan', value: k.keterangan_tidur || null },
        ],
    },
])

function bukaFoto(url) {
    window.open(url, '_blank')
}

const selfieValidasi = computed(() => ({
    foto: k.selfie_validasi ? `/storage/${k.selfie_validasi}` : null,
    valid: !!k.selfie_validasi,
}))

const bukaAccordion = ref(null)
function toggleAccordion(index) {
    bukaAccordion.value = bukaAccordion.value === index ? null : index
}

const pilihanNilai = [
    { value: 'A', label: 'A – Baik Sekali' },
    { value: 'B', label: 'B – Baik' },
    { value: 'C', label: 'C – Cukup' },
    { value: 'D', label: 'D – Perlu Bimbingan' },
]

const isLocked = computed(() => k.status === 'draft' || k.status === 'evaluated')
const lockedMessage = computed(() => {
    if (k.status === 'draft') return 'Laporan masih draft, belum bisa dievaluasi.'
    if (k.status === 'evaluated') return 'Laporan ini sudah dievaluasi sebelumnya.'
    return ''
})

// Pre-fill kalau laporan ini sudah pernah dievaluasi sebelumnya
const nilai = ref(k.nilai_guru ?? null)
const evaluasi = ref(k.catatan_guru ?? '')
const errorNilai = ref('')
const savedMessage = ref('')

function simpanEvaluasi() {
    if (isLocked.value) {
        errorNilai.value = lockedMessage.value
        return
    }
    if (!nilai.value) {
        errorNilai.value = 'Nilai predikat wajib dipilih'
        savedMessage.value = ''
        return
    }
    errorNilai.value = ''
    savedMessage.value = ''
    // BUG FIX: backend butuh key "nilai_guru" & "catatan_guru" (bukan "nilai"/"catatan")
    router.patch(`/guru/monitoring/${k.id}/evaluasi`, {
        nilai_guru: nilai.value,
        catatan_guru: evaluasi.value,
    }, {
        onSuccess: () => {
            savedMessage.value = 'Evaluasi berhasil disimpan'
        },
        onError: (errors) => {
            errorNilai.value = errors.kegiatan ?? errors.nilai_guru ?? 'Gagal menyimpan, coba lagi'
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
                <div class="flex items-center gap-2 text-sm">
                    <Link href="/guru/monitoring" class="text-gray-400 hover:text-gray-600">
                        Monitoring Kelas
                    </Link>
                    <span class="text-gray-300">›</span>
                    <span class="text-gray-600 font-medium">Detail Evaluasi</span>
                </div>
                <h1 class="text-xl font-bold text-gray-800 mt-1">Detail Kegiatan & Evaluasi</h1>
                <p class="text-sm text-gray-500 mt-0.5">Aktivitas Kegiatan — {{ tanggalFormatted }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Link
                    href="/guru/monitoring"
                    class="border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-1.5"
                >
                    ← Kembali
                </Link>
                <button
                    @click="simpanEvaluasi"
                    :disabled="isLocked"
                    class="bg-[#1B7F5A] hover:bg-[#166347] disabled:opacity-40 disabled:cursor-not-allowed text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition flex items-center gap-1.5"
                >
                    💾 Simpan Evaluasi
                </button>
            </div>
        </div>

        <!-- ====================================================================
             SATU grid 4-kolom yang sama dipakai utk 2 "baris" sekaligus, BUKAN
             3 kolom sejajar tinggi. Urutan elemen + col-span-nya:
             [profil col-span-1][grafik col-span-3]  -> pas 4 kolom, jadi baris 1
             [kebiasaan col-span-3][selfie+nilai col-span-1] -> jadi baris 2
             Default align-items grid itu "stretch", jadi tinggi tiap sel di
             SATU baris otomatis nyamain ke yang paling tinggi di baris itu —
             ini yang bikin rata, tanpa perlu hack flex h-full lagi.
             ==================================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">

            <!-- BARIS 1, kolom 1: PROFIL SISWA -->
            <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center flex flex-col">
                <div class="w-20 h-20 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center font-bold text-2xl mx-auto mb-3">
                    {{ siswa.nama?.[0] ?? '?' }}
                </div>
                <h3 class="font-bold text-gray-800">{{ siswa.nama }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Kelas {{ siswa.kelas }}</p>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 mb-1">Kepatuhan bulan ini</p>
                    <p class="text-2xl font-bold text-[#1B7F5A]">{{ siswa.kepatuhanBulanIni }}%</p>
                    <div class="w-12 h-1 bg-[#1B7F5A] rounded-full mx-auto mt-1.5" />
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="bg-gray-100 rounded-xl py-3">
                        <p class="font-bold text-gray-800">{{ siswa.streak }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Streak 🔥</p>
                    </div>
                    <div class="bg-gray-100 rounded-xl py-3">
                        <p class="font-bold text-gray-800">{{ siswa.totalHari }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Total hari</p>
                    </div>
                </div>

                <div class="mt-auto pt-4 flex justify-center">
                    <span class="inline-block text-xs font-medium px-3 py-1.5 rounded-full bg-green-100 text-green-700 capitalize">
                        • {{ siswa.statusAkun }}
                    </span>
                </div>
            </div>

            <!-- BARIS 1, kolom 2-4: GRAFIK PERKEMBANGAN -->
            <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-700 text-sm mb-4">
                    📊 Grafik Perkembangan — 12 Hari Terakhir
                </h3>
                <div class="bg-green-50/60 rounded-xl p-4">
                    <div v-if="grafik12Hari.values.length === 0" class="h-40 flex items-center justify-center text-xs text-gray-400">
                        Belum ada data grafik
                    </div>
                    <div v-else class="flex items-end justify-between gap-2 h-40">
                        <div v-for="(val, i) in grafik12Hari.values"
                            :key="i"
                            class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end"
                        >
                            <div
                                class="w-full rounded-t-md"
                                :class="barColor(val)"
                                :style="{ height: val + '%' }"
                                :title="val + '%'"
                            />
                            <span class="text-[10px] text-gray-400">{{ grafik12Hari.labels[i] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BARIS 2, kolom 1-3: DETAIL 7 KEBIASAAN -->
            <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <h3 class="font-bold text-gray-700 text-sm mb-3 px-1">✅ Detail 7 Kebiasaan — Hari Ini</h3>

                <div v-for="(item, i) in kebiasaan" :key="i" class="rounded-xl bg-green-50/70 mb-2 overflow-hidden">
                    <div
                        class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-green-50"
                        @click="toggleAccordion(i)"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0" :class="habitColors[i]">
                                {{ item.icon }}
                            </div>
                            <span class="font-medium text-gray-700 text-sm">{{ i + 1 }}. {{ item.nama }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="text-xs font-medium w-[78px] shrink-0"
                                :class="item.status ? 'text-[#1B7F5A]' : 'text-gray-400'"
                            >
                                {{ item.status ? '✓ Lengkap' : '— Kosong' }}
                            </span>
                            <button
                                :disabled="!item.foto"
                                @click.stop="item.foto && bukaFoto(item.foto)"
                                class="text-xs font-medium px-3 py-1.5 rounded-lg transition w-[100px] shrink-0"
                                :class="item.foto ? 'bg-[#1B7F5A] text-white hover:bg-[#166347]' : 'bg-gray-200/70 text-gray-400 cursor-not-allowed'"
                            >
                                🖼 Lihat Foto
                            </button>
                            <span
                                class="text-xs text-gray-400 transition-transform w-3 shrink-0 text-center"
                                :class="bukaAccordion === i ? 'rotate-180' : ''"
                            >▼</span>
                        </div>
                    </div>
                    <div v-if="bukaAccordion === i" class="px-4 pb-3 space-y-2">
                        <!-- Checklist khusus utk Ibadah & Doa -->
                        <div v-if="item.checklist" class="flex flex-wrap gap-3 mb-1">
                            <span
                                v-for="c in item.checklist"
                                :key="c.label"
                                class="flex items-center gap-1.5 text-xs"
                                :class="c.checked ? 'text-gray-700' : 'text-gray-300'"
                            >
                                {{ c.checked ? '✅' : '⬜' }} {{ c.label }}
                            </span>
                        </div>
                        <p v-for="f in item.fields.filter(f => f.value)" :key="f.label" class="text-sm text-gray-500">
                            <span class="text-gray-400">{{ f.label }}:</span> {{ f.value }}
                        </p>
                        <p
                            v-if="!item.checklist && item.fields.every(f => !f.value)"
                            class="text-xs text-gray-400"
                        >
                            Tidak ada detail tambahan dicatat
                        </p>
                    </div>
                </div>
            </div>

            <!-- BARIS 2, kolom 4: SELFIE + PENILAIAN (ditumpuk vertikal) -->
            <div class="lg:col-span-1 flex flex-col gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b flex items-center justify-between">
                        <h4 class="font-bold text-sm text-gray-700">📸 Selfie Validasi</h4>
                        <span
                            class="text-xs px-2 py-0.5 rounded-full"
                            :class="selfieValidasi.valid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                        >
                            {{ selfieValidasi.valid ? '✓ Valid' : '✗ Tidak Valid' }}
                        </span>
                    </div>
                    <div class="p-3">
                        <img v-if="selfieValidasi.foto" :src="selfieValidasi.foto" class="w-full h-40 object-cover rounded-xl" />
                        <div v-else class="w-full h-40 rounded-xl bg-gray-50 flex items-center justify-center text-xs text-gray-400">
                            Belum ada selfie validasi
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex-1 flex flex-col">
                    <h4 class="font-bold text-sm text-gray-700 mb-3">🏅 Penilaian & Catatan Guru</h4>
                    <p v-if="isLocked" class="text-xs text-orange-600 bg-orange-50 rounded-lg px-3 py-2 mb-3">
                        ⚠ {{ lockedMessage }}
                    </p>
                    <p class="text-xs text-gray-400 mb-2">
                        Nilai / Predikat <span class="text-red-500">*</span>
                    </p>
                    <div class="grid grid-cols-2 gap-2 mb-1">
                        <button
                            v-for="opt in pilihanNilai"
                            :key="opt.value"
                            @click="nilai = opt.value; errorNilai = ''"
                            class="text-xs font-medium px-2.5 py-2 rounded-lg border-2 transition text-left"
                            :class="nilai === opt.value
                                ? 'border-[#1B7F5A] text-[#1B7F5A] bg-white'
                                : 'border-gray-200 text-gray-500 hover:border-[#1B7F5A]/50'"
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
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-[#1B7F5A] resize-none"
                    />
                    <p v-if="savedMessage" class="text-xs text-green-600 mt-2">
                        {{ savedMessage }}
                    </p>
                    <div class="flex gap-2 mt-auto pt-4">
                        <Link
                            href="/guru/monitoring"
                            class="flex-1 text-center border border-gray-200 text-gray-600 py-2 rounded-xl text-sm font-medium hover:bg-gray-50 transition"
                        >
                            Batal
                        </Link>
                        <button
                            @click="simpanEvaluasi"
                            :disabled="isLocked"
                            class="flex-1 bg-[#1B7F5A] hover:bg-[#166347] disabled:opacity-40 disabled:cursor-not-allowed text-white py-2 rounded-xl text-sm font-semibold transition"
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