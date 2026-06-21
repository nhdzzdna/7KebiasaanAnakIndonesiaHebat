<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import GuruLayout from '@/Layouts/GuruLayout.vue'

// ====================================================================
// PROPS — struktur PERSIS dari dokumentasi API Nahdia untuk
// GET /guru/monitoring. PENTING: endpoint ini punya 3 MODE BERBEDA
// (per_siswa / per_kebiasaan / kalender), dan field yang terkirim
// BEDA TOTAL tergantung mode aktif. Kita cuma boleh akses field yang
// relevan dengan mode yang sedang aktif — field mode lain kemungkinan
// tidak dikirim sama sekali (undefined), bukan array kosong.
// ====================================================================
const props = defineProps({
    mode: { type: String, required: true }, // "per_siswa" | "per_kebiasaan" | "kalender"
    // --- HANYA ADA kalau mode === "per_siswa" ---
    kegiatans: { type: Object, default: null }, // { data: [...], current_page, last_page, per_page, total, ... }
    // --- HANYA ADA kalau mode === "per_kebiasaan" ---
    perKebiasaan: { type: Array, default: () => [] },
    // --- HANYA ADA kalau mode === "kalender" ---
    perTanggal: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) }, // isi field beda tergantung mode juga
})

const habits = [
    { key: 'waktu_bangun', label: 'Bangun Pagi', icon: '🛏' },
    { key: 'detail_ibadah_centang', label: 'Ibadah & Doa', icon: '🙏' },
    { key: 'menu_makan', label: 'Makan Sehat & Bergizi', icon: '🍎' },
    { key: 'jenis_olahraga', label: 'Olahraga', icon: '🏃' },
    { key: 'belajar_mandiri', label: 'Belajar Mandiri', icon: '📚' },
    { key: 'aktivitas_sosial', label: 'Aktivitas Sosial', icon: '🤝' },
    { key: 'waktu_tidur', label: 'Tidur Cepat', icon: '😴' },
]

const months = [
    { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' }, { value: 3, label: 'Maret' },
    { value: 4, label: 'April' }, { value: 5, label: 'Mei' }, { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' }, { value: 8, label: 'Agustus' }, { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' }, { value: 11, label: 'November' }, { value: 12, label: 'Desember' },
]
const years = [2023, 2024, 2025, 2026]

// ====================================================================
// STATE FILTER — nilai awal diambil dari props.filters yang dikirim
// backend (supaya kalau reload halaman dengan query string, dropdown
// otomatis menunjukkan filter yang sedang aktif, bukan reset ke default)
// ====================================================================
const today = new Date()
const selectedMonth = ref(props.filters.bulan ?? today.getMonth() + 1)
const selectedYear = ref(props.filters.tahun ?? today.getFullYear())
const selectedDate = ref(props.filters.tanggal ?? today.toISOString().slice(0, 10)) // "YYYY-MM-DD"
const searchQuery = ref(props.filters.search ?? '')
const selectedNilaiFilter = ref(props.filters.nilai_guru ?? '')
const showFilterPanel = ref(false)
const showPeriodPanel = ref(false) // dropdown bulan/tahun di pojok kanan header (dekoratif utk mode selain kalender)
const selectedCalendarDay = ref(null)
const selectedStudent = ref(null)

// Auto-pilih siswa pertama di tabel supaya panel detail kanan langsung
// tampil begitu halaman dibuka — sesuai tampilan mockup.
function autoSelectFirstStudent() {
    if (props.mode === 'per_siswa' && props.kegiatans?.data?.length && !selectedStudent.value) {
        selectedStudent.value = props.kegiatans.data[0]
    }
}
onMounted(autoSelectFirstStudent)
watch(() => props.kegiatans, () => {
    selectedStudent.value = null
    autoSelectFirstStudent()
})

// ====================================================================
// NAVIGASI ANTAR MODE — pindah tab = router.get() baru ke backend dengan
// query `mode` beda, BUKAN sekadar ganti ref lokal. Inertia otomatis
// re-render komponen ini dengan props baru begitu response datang.
// ====================================================================
function gotoMode(newMode) {
    const params = { mode: newMode }
    if (newMode === 'per_siswa') {
        if (searchQuery.value) params.search = searchQuery.value
        if (selectedNilaiFilter.value) params.nilai_guru = selectedNilaiFilter.value
        if (selectedDate.value) params.tanggal = selectedDate.value
    } else if (newMode === 'per_kebiasaan') {
        if (selectedDate.value) params.tanggal = selectedDate.value
    } else if (newMode === 'kalender') {
        params.bulan = selectedMonth.value
        params.tahun = selectedYear.value
    }
    router.get(route('guru.monitoring.index'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

function handleCalendarPeriodChange() {
    selectedCalendarDay.value = null
    showPeriodPanel.value = false
    gotoMode('kalender')
}

function handleSiswaFilterChange() {
    showFilterPanel.value = false
    gotoMode('per_siswa')
}

function selectStudent(kegiatan) {
    selectedStudent.value = kegiatan
}

// ====================================================================
// HELPERS TAMPILAN
// ====================================================================
function initials(name) {
    if (!name) return '?'
    const parts = name.trim().split(' ')
    return ((parts[0]?.[0] ?? '') + (parts[1]?.[0] ?? '')).toUpperCase() || '?'
}

// Variasi warna avatar biar gak monoton kayak di mockup (NP hijau, BS oranye, dst)
const avatarPalette = [
    'bg-green-100 text-green-700',
    'bg-orange-100 text-orange-700',
    'bg-blue-100 text-blue-700',
    'bg-pink-100 text-pink-700',
    'bg-purple-100 text-purple-700',
    'bg-gray-100 text-gray-600',
]
function avatarColor(name) {
    if (!name) return avatarPalette[avatarPalette.length - 1]
    const code = name.split('').reduce((acc, c) => acc + c.charCodeAt(0), 0)
    return avatarPalette[code % avatarPalette.length]
}

function statusStyle(status) {
    if (status === 'Lengkap') return 'bg-green-100 text-green-700'
    if (status === 'Sebagian') return 'bg-yellow-100 text-yellow-700'
    return 'bg-red-100 text-red-600'
}

function calendarCellStyle(rataRata) {
    if (rataRata >= 85) return 'bg-[#1B7F5A] text-white'
    if (rataRata >= 65) return 'bg-yellow-300 text-gray-800'
    return 'bg-red-200 text-red-800'
}

// Helper: hitung jumlah kebiasaan yang terisi dari 1 object kegiatan.
function habitsFilledCount(kegiatan) {
    return habits.filter((h) => {
        const val = kegiatan[h.key]
        return val !== null && val !== undefined && val !== ''
    }).length
}

// Helper: turunkan status (Lengkap/Sebagian/Perlu Perhatian) dari 1 object
// kegiatan, karena field "status" semacam ini TIDAK eksplisit disebutkan
// ada di response per_siswa pada dokumentasi Nahdia.
// CATATAN: kalau backend ternyata SUDAH mengirim field status jadi,
// fungsi ini bisa dihapus, tinggal pakai kegiatan.status langsung.
function hitungStatusKegiatan(kegiatan) {
    const terisi = habitsFilledCount(kegiatan)
    if (terisi === habits.length) return 'Lengkap'
    if (terisi === 0) return 'Perlu Perhatian'
    return 'Sebagian'
}

// Persentase kepatuhan: pakai field dari backend kalau ada (kepatuhan /
// persentase_kepatuhan), kalau tidak ada baru dihitung manual dari jumlah
// kebiasaan terisi. SESUAIKAN nama field ini begitu lihat response asli.
function kepatuhanValue(kegiatan) {
    if (kegiatan.kepatuhan !== undefined && kegiatan.kepatuhan !== null) return Math.round(kegiatan.kepatuhan)
    if (kegiatan.persentase_kepatuhan !== undefined && kegiatan.persentase_kepatuhan !== null) {
        return Math.round(kegiatan.persentase_kepatuhan)
    }
    return Math.round((habitsFilledCount(kegiatan) / habits.length) * 100)
}

// Warna progress bar kepatuhan diikutkan ke status (bukan ke angka mentah),
// soalnya di mockup 87% bisa kuning ("Sebagian") sementara 91% hijau
// ("Lengkap") — jadi warnanya representasi status, bukan threshold angka.
function kepatuhanBarColor(kegiatan) {
    const status = hitungStatusKegiatan(kegiatan)
    if (status === 'Lengkap') return 'bg-[#1B7F5A]'
    if (status === 'Sebagian') return 'bg-yellow-400'
    return 'bg-red-400'
}

const currentMonthLabel = computed(() => {
    const m = months.find((m) => m.value === selectedMonth.value)
    return m ? `${m.label.slice(0, 3)} ${selectedYear.value}` : ''
})
</script>

<template>
<GuruLayout>
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Monitoring Kelas {{ filters.kelas ?? '' }}
                </h1>
                <p class="text-gray-500 mt-1 text-sm">
                    Pantau kegiatan dan kebiasaan setiap siswa secara detail
                </p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Quick period display — fungsional penuh kalau mode kalender,
                     di mode lain cuma indikator konteks bulan aktif -->
                <div class="relative">
                    <button
                        @click="showPeriodPanel = !showPeriodPanel"
                        class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2"
                    >
                        📅 {{ currentMonthLabel }}
                    </button>
                    <div v-if="showPeriodPanel" class="absolute right-0 top-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg p-3 z-20 w-52 flex gap-2">
                        <select v-model.number="selectedMonth" class="flex-1 border border-gray-200 rounded-lg px-2 py-1.5 text-sm">
                            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                        </select>
                        <select v-model.number="selectedYear" class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                        <button
                            v-if="mode === 'kalender'"
                            @click="handleCalendarPeriodChange"
                            class="bg-[#1B7F5A] text-white text-xs px-2.5 rounded-lg shrink-0"
                        >OK</button>
                    </div>
                </div>
                <Link
                    :href="route('guru.rekap.index')"
                    class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2"
                >
                    📋 Rekap Kelas
                </Link>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6 items-start">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <!-- TAB SWITCHER — underline style, setiap klik manggil gotoMode() -->
                    <div class="flex items-center gap-6 px-5 pt-4 border-b border-gray-100">
                        <button
                            @click="gotoMode('per_siswa')"
                            class="pb-3 text-sm font-medium transition border-b-2"
                            :class="mode === 'per_siswa' ? 'border-[#1B7F5A] text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        >Per Siswa</button>
                        <button
                            @click="gotoMode('per_kebiasaan')"
                            class="pb-3 text-sm font-medium transition border-b-2"
                            :class="mode === 'per_kebiasaan' ? 'border-[#1B7F5A] text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        >Per Kebiasaan</button>
                        <button
                            @click="gotoMode('kalender')"
                            class="pb-3 text-sm font-medium transition border-b-2"
                            :class="mode === 'kalender' ? 'border-[#1B7F5A] text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        >Kalender</button>
                    </div>

                    <!-- ============ MODE: PER SISWA ============ -->
                    <div v-if="mode === 'per_siswa'">
                        <div class="p-4 flex items-center gap-3 relative">
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Cari nama siswa"
                                    @keyup.enter="handleSiswaFilterChange"
                                    class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A] focus:ring-1 focus:ring-[#1B7F5A]/30"
                                >
                            </div>
                            <button
                                @click="showFilterPanel = !showFilterPanel"
                                class="border rounded-xl px-4 py-2.5 text-sm transition shrink-0 flex items-center gap-1.5"
                                :class="showFilterPanel ? 'border-[#1B7F5A] text-[#1B7F5A] bg-green-50' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                            >
                                🎛 Filter
                            </button>
                            <button
                                @click="selectedStudent && router.visit(route('guru.monitoring.show', selectedStudent.id))"
                                :disabled="!selectedStudent"
                                class="bg-[#1B7F5A] hover:bg-[#166347] disabled:opacity-40 disabled:cursor-not-allowed text-white px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-1.5 shrink-0"
                            >
                                📣 Evaluasi
                            </button>

                            <!-- Filter nilai_guru, sesuai query param yang didukung backend -->
                            <div v-if="showFilterPanel" class="absolute right-4 top-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg p-4 z-10 w-56">
                                <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Filter Nilai</p>
                                <select
                                    v-model="selectedNilaiFilter"
                                    @change="handleSiswaFilterChange"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-3"
                                >
                                    <option value="">Semua Nilai</option>
                                    <option value="A">A — Baik Sekali</option>
                                    <option value="B">B — Baik</option>
                                    <option value="C">C — Cukup</option>
                                    <option value="D">D — Perlu Bimbingan</option>
                                </select>
                                <input
                                    v-model="selectedDate"
                                    type="date"
                                    @change="handleSiswaFilterChange"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                >
                            </div>
                        </div>

                        <!-- kegiatans bisa null kalau backend belum kirim (misal request awal) -->
                        <div v-if="!kegiatans || kegiatans.data.length === 0" class="text-center py-12 text-sm text-gray-400">
                            Belum ada data kegiatan pada tanggal/filter yang dipilih
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-gray-400 text-xs uppercase tracking-wide">
                                        <th class="text-left px-5 py-2.5 font-medium w-10">#</th>
                                        <th class="text-left px-2 py-2.5 font-medium">Nama Siswa</th>
                                        <th class="text-center px-2 py-2.5 font-medium">Kebiasaan Hari Ini</th>
                                        <th class="text-center px-2 py-2.5 font-medium">Streak</th>
                                        <th class="text-center px-2 py-2.5 font-medium">Kepatuhan</th>
                                        <th class="text-center px-2 py-2.5 font-medium">Status</th>
                                        <th class="text-center px-2 py-2.5 font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- "kegiatan" di sini = 1 baris laporan siswa pada tanggal terpilih,
                                         BUKAN object siswa murni. Nama siswa kemungkinan ada di
                                         kegiatan.user.name atau kegiatan.nama_siswa —
                                         sesuaikan begitu lihat bentuk response asli -->
                                    <tr
                                        v-for="(kegiatan, idx) in kegiatans.data"
                                        :key="kegiatan.id"
                                        class="border-t border-gray-100 hover:bg-gray-50/60 cursor-pointer transition"
                                        :class="selectedStudent && selectedStudent.id === kegiatan.id ? 'bg-green-50/50' : ''"
                                        @click="selectStudent(kegiatan)"
                                    >
                                        <td class="px-5 py-3 text-gray-400">
                                            {{ String((kegiatans.current_page - 1) * (kegiatans.per_page ?? kegiatans.data.length) + idx + 1).padStart(2, '0') }}
                                        </td>
                                        <td class="px-2 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div
                                                    class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs shrink-0"
                                                    :class="avatarColor(kegiatan.user?.name ?? kegiatan.nama_siswa)"
                                                >
                                                    {{ initials(kegiatan.user?.name ?? kegiatan.nama_siswa) }}
                                                </div>
                                                <p class="font-medium text-gray-700">{{ kegiatan.user?.name ?? kegiatan.nama_siswa }}</p>
                                            </div>
                                        </td>
                                        <td class="px-2 py-3">
                                            <div class="flex items-center justify-center gap-1">
                                                <span
                                                    v-for="h in habits"
                                                    :key="h.key"
                                                    class="w-2.5 h-2.5 rounded-[3px]"
                                                    :class="kegiatan[h.key] ? 'bg-[#1B7F5A]' : 'bg-gray-200'"
                                                    :title="h.label"
                                                />
                                            </div>
                                        </td>
                                        <td class="px-2 py-3 text-center font-medium text-gray-600">
                                            🔥 {{ kegiatan.streak ?? 0 }}
                                        </td>
                                        <td class="px-2 py-3">
                                            <div class="flex items-center gap-2 justify-center">
                                                <div class="w-16 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                                    <div
                                                        class="h-1.5 rounded-full"
                                                        :class="kepatuhanBarColor(kegiatan)"
                                                        :style="{ width: kepatuhanValue(kegiatan) + '%' }"
                                                    />
                                                </div>
                                                <span class="text-gray-500 text-xs w-9">{{ kepatuhanValue(kegiatan) }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-2 py-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium" :class="statusStyle(hitungStatusKegiatan(kegiatan))">
                                                • {{ hitungStatusKegiatan(kegiatan) }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-3 text-center">
                                            <Link
                                                :href="route('guru.monitoring.show', kegiatan.id)"
                                                class="border border-gray-200 hover:bg-gray-50 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-medium transition"
                                                @click.stop
                                            >
                                                Detail
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- PAGINASI — kegiatans adalah hasil paginate() Laravel -->
                            <div v-if="kegiatans.last_page > 1" class="flex items-center justify-between p-4 border-t border-gray-100">
                                <p class="text-xs text-gray-400">
                                    {{ (kegiatans.current_page - 1) * (kegiatans.per_page ?? kegiatans.data.length) + 1 }}-{{ Math.min(kegiatans.current_page * (kegiatans.per_page ?? kegiatans.data.length), kegiatans.total ?? kegiatans.data.length) }}
                                    dari {{ kegiatans.total ?? kegiatans.data.length }} siswa
                                </p>
                                <div class="flex items-center gap-1">
                                    <Link
                                        v-if="kegiatans.current_page > 1"
                                        :href="route('guru.monitoring.index', { mode: 'per_siswa', page: kegiatans.current_page - 1 })"
                                        preserve-scroll
                                        class="w-8 h-8 rounded-lg text-sm flex items-center justify-center text-gray-400 hover:bg-gray-100"
                                    >‹</Link>
                                    <Link
                                        v-for="page in kegiatans.last_page"
                                        :key="page"
                                        :href="route('guru.monitoring.index', { mode: 'per_siswa', page })"
                                        preserve-scroll
                                        class="w-8 h-8 rounded-lg text-sm flex items-center justify-center transition"
                                        :class="page === kegiatans.current_page ? 'bg-[#1B7F5A] text-white' : 'text-gray-500 hover:bg-gray-100'"
                                    >
                                        {{ page }}
                                    </Link>
                                    <Link
                                        v-if="kegiatans.current_page < kegiatans.last_page"
                                        :href="route('guru.monitoring.index', { mode: 'per_siswa', page: kegiatans.current_page + 1 })"
                                        preserve-scroll
                                        class="w-8 h-8 rounded-lg text-sm flex items-center justify-center text-gray-400 hover:bg-gray-100"
                                    >›</Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ MODE: PER KEBIASAAN ============ -->
                    <div v-else-if="mode === 'per_kebiasaan'" class="p-5 space-y-3">
                        <input
                            v-model="selectedDate"
                            type="date"
                            @change="gotoMode('per_kebiasaan')"
                            class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm mb-2"
                        >
                        <div v-if="perKebiasaan.length === 0" class="text-center py-12 text-sm text-gray-400">
                            Belum ada data kegiatan pada tanggal ini
                        </div>
                        <div
                            v-for="item in perKebiasaan"
                            :key="item.field"
                            class="bg-gray-50 rounded-xl border border-gray-100 p-4"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <p class="font-medium text-gray-700 text-sm">{{ item.label }}</p>
                                <span class="text-sm text-gray-400">{{ item.total_terisi }} / {{ item.total_siswa }} siswa</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-gray-200 mb-3">
                                <div
                                    class="h-2 rounded-full bg-[#1B7F5A]"
                                    :style="{ width: (item.total_terisi / item.total_siswa * 100) + '%' }"
                                />
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="s in item.siswa"
                                    :key="s.id"
                                    class="text-xs px-2 py-1 rounded-full"
                                    :class="s.terisi ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'"
                                    :title="s.nilai ?? ''"
                                >
                                    {{ s.nama_siswa }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ============ MODE: KALENDER ============ -->
                    <div v-else class="p-6">
                        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                            <h3 class="font-semibold text-gray-700 text-sm">Rata-rata Kepatuhan Kelas</h3>
                            <div class="flex items-center gap-2">
                                <select v-model.number="selectedMonth" @change="handleCalendarPeriodChange" class="text-sm border border-gray-200 rounded-lg px-3 py-2">
                                    <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                                </select>
                                <select v-model.number="selectedYear" @change="handleCalendarPeriodChange" class="text-sm border border-gray-200 rounded-lg px-3 py-2">
                                    <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                                </select>
                            </div>
                        </div>
                        <!-- perTanggal cuma berisi tanggal yang ADA datanya, jadi grid ini
                             bukan grid kalender 7-kolom sungguhan; ditampilkan sebagai
                             daftar kartu per tanggal (versi sederhana). -->
                        <div v-if="perTanggal.length === 0" class="text-center py-12 text-sm text-gray-400">
                            Belum ada data untuk bulan ini
                        </div>
                        <div v-else class="grid grid-cols-7 gap-2">
                            <div
                                v-for="day in perTanggal"
                                :key="day.tanggal"
                                @click="selectedCalendarDay = day"
                                class="aspect-square rounded-lg flex flex-col items-center justify-center cursor-pointer transition hover:scale-105"
                                :class="calendarCellStyle(day.rata_rata_kepatuhan)"
                            >
                                <span class="text-sm font-semibold">{{ day.tanggal.split('-')[2] }}</span>
                                <span class="text-[10px] opacity-80">{{ day.rata_rata_kepatuhan }}%</span>
                            </div>
                        </div>
                        <div v-if="selectedCalendarDay" class="mt-5 pt-5 border-t border-gray-100">
                            <p class="text-sm text-gray-600">
                                📅 <strong>{{ selectedCalendarDay.tanggal }}</strong> —
                                {{ selectedCalendarDay.total_siswa_lapor }} siswa lapor,
                                rata-rata kepatuhan <strong class="text-[#1B7F5A]">{{ selectedCalendarDay.rata_rata_kepatuhan }}%</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL KANAN: detail siswa — hanya relevan untuk mode per_siswa -->
            <div v-if="selectedStudent && mode === 'per_siswa'" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-6 space-y-5">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center font-bold text-xl mx-auto mb-3">
                        {{ initials(selectedStudent.user?.name ?? selectedStudent.nama_siswa) }}
                    </div>
                    <h3 class="font-bold text-gray-800">{{ selectedStudent.user?.name ?? selectedStudent.nama_siswa }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ selectedStudent.kelas ?? filters.kelas ?? '-' }} • Siswa Aktif</p>
                    <span class="inline-block mt-2 px-2.5 py-1 rounded-full text-xs font-medium" :class="statusStyle(hitungStatusKegiatan(selectedStudent))">
                        • {{ hitungStatusKegiatan(selectedStudent) }} Hari Ini
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xl font-bold text-gray-800">{{ kepatuhanValue(selectedStudent) }}%</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Kepatuhan</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xl font-bold text-gray-800">{{ selectedStudent.streak ?? 0 }} 🔥</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Streak</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xl font-bold text-gray-800">{{ habitsFilledCount(selectedStudent) }}/{{ habits.length }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Hari ini</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-xl font-bold text-gray-800">{{ selectedStudent.total_hari ?? '-' }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Total hari</p>
                    </div>
                </div>

                <!-- Grafik mingguan — tampil cuma kalau backend kirim array grafik_minggu
                     (0-100 per hari). Sesuaikan nama field kalau beda. -->
                <div v-if="selectedStudent.grafik_minggu?.length">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-3 border-l-2 border-[#1B7F5A] pl-2">Grafik Minggu Ini</p>
                    <div class="flex items-end gap-1.5 h-20 bg-gray-50 rounded-xl p-3">
                        <div
                            v-for="(val, i) in selectedStudent.grafik_minggu"
                            :key="i"
                            class="flex-1 rounded-md bg-[#1B7F5A]"
                            :style="{ height: Math.max(val, 8) + '%', opacity: 0.5 + (val / 100) * 0.5 }"
                        />
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-3 border-l-2 border-[#1B7F5A] pl-2">Kebiasaan Hari Ini</p>
                    <div class="space-y-2.5">
                        <div v-for="h in habits" :key="h.key" class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ h.icon }} {{ h.label }}</span>
                            <span :class="selectedStudent[h.key] ? 'text-[#1B7F5A]' : 'text-gray-300'">
                                {{ selectedStudent[h.key] ? '✓' : '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('guru.monitoring.show', selectedStudent.id)"
                    class="block text-center w-full bg-[#1B7F5A] hover:bg-[#166347] text-white py-2.5 rounded-xl text-sm font-medium transition"
                >
                    ✏ Beri Evaluasi
                </Link>
            </div>
        </div>
    </div>
</GuruLayout>
</template>