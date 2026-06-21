<script setup>
// Import ref untuk membuat data reaktif, computed untuk data turunan,
// onMounted untuk menjalankan kode setelah komponen siap di browser
import { ref, computed, onMounted } from 'vue'

// Import Link untuk navigasi Inertia tanpa reload halaman penuh
import { Link } from '@inertiajs/vue3'

// Import layout khusus guru
import GuruLayout from '@/Layouts/GuruLayout.vue'

// Daftar 7 kebiasaan dengan key, label, dan ikon
// key dipakai sebagai identifier, label untuk tampilan, icon untuk visual
const habits = [
    { key: 'bangun_pagi', label: 'Bangun Pagi', icon: '🛏' },
    { key: 'ibadah', label: 'Ibadah & Doa', icon: '🙏' },
    { key: 'makan', label: 'Makan Sehat & Bergizi', icon: '🍎' },
    { key: 'olahraga', label: 'Olahraga', icon: '🏃' },
    { key: 'belajar', label: 'Belajar Mandiri', icon: '📚' },
    { key: 'sosial', label: 'Aktivitas Sosial', icon: '🤝' },
    { key: 'tidur', label: 'Tidur Cepat', icon: '😴' },
]

// ====================================================================
// BAGIAN 1: DATA HARI INI (real-time, TIDAK ikut filter bulan/tahun)
// habitsToday = kondisi 7 kebiasaan hari ini (true/false per kebiasaan)
// status = ringkasan kondisi: Lengkap / Sebagian / Perlu Perhatian
// ====================================================================
const studentsToday = ref([
    // id dipakai sebagai key unik dan untuk navigasi ke halaman detail
    { id: 1, name: 'Nadia Putri',     habitsToday: [true,  true,  true,  true,  true,  true,  true],  status: 'Lengkap' },
    { id: 2, name: 'Budi Santoso',    habitsToday: [true,  true,  false, true,  true,  false, true],  status: 'Sebagian' },
    { id: 3, name: 'Ahmad Fauzi',     habitsToday: [true,  false, true,  false, false, false, true],  status: 'Perlu Perhatian' },
    { id: 4, name: 'Rini Handayani',  habitsToday: [true,  true,  true,  true,  true,  true,  false], status: 'Lengkap' },
    { id: 5, name: 'Dewi Kartika',    habitsToday: [true,  false, true,  false, true,  false, false], status: 'Perlu Perhatian' },
])

// ====================================================================
// BAGIAN 2: DATA PERIODE (streak & % kepatuhan)
// Ini yang BERUBAH saat filter bulan/tahun diganti
// Key format: 'tahun-bulan', contoh '2025-1' = Januari 2025
// weeklyChart = array 7 angka untuk grafik mini di panel kanan
// ====================================================================
const periodStatsPerPeriode = {
    '2025-1': {
        // Key angka 1-5 = id siswa
        1: { streak: 14, compliance: 98, weeklyChart: [80, 85, 90, 95, 88, 92, 98] },
        2: { streak: 7,  compliance: 67, weeklyChart: [60, 65, 70, 68, 72, 65, 67] },
        3: { streak: 2,  compliance: 72, weeklyChart: [50, 45, 55, 60, 58, 62, 72] },
        4: { streak: 10, compliance: 91, weeklyChart: [85, 88, 90, 92, 89, 94, 91] },
        5: { streak: 2,  compliance: 53, weeklyChart: [40, 45, 50, 48, 52, 55, 53] },
    },
    '2025-2': {
        1: { streak: 9,  compliance: 89, weeklyChart: [82, 80, 85, 88, 90, 86, 89] },
        2: { streak: 3,  compliance: 58, weeklyChart: [55, 58, 60, 57, 59, 56, 58] },
        3: { streak: 18, compliance: 95, weeklyChart: [88, 90, 92, 94, 91, 96, 95] },
        4: { streak: 5,  compliance: 75, weeklyChart: [70, 72, 74, 71, 76, 73, 75] },
        5: { streak: 4,  compliance: 70, weeklyChart: [65, 68, 66, 70, 69, 71, 70] },
    },
    '2025-3': {
        1: { streak: 22, compliance: 99, weeklyChart: [95, 96, 98, 97, 99, 98, 99] },
        2: { streak: 1,  compliance: 40, weeklyChart: [35, 38, 42, 40, 39, 41, 40] },
        3: { streak: 6,  compliance: 80, weeklyChart: [75, 78, 76, 80, 79, 81, 80] },
        4: { streak: 16, compliance: 96, weeklyChart: [90, 92, 94, 93, 95, 97, 96] },
        5: { streak: 1,  compliance: 45, weeklyChart: [42, 44, 43, 46, 45, 47, 45] },
    },
}

// ====================================================================
// BAGIAN 3: DATA KALENDER
// Rekap kepatuhan rata-rata KELAS per tanggal dalam satu bulan
// avgCompliance = rata-rata % kepatuhan seluruh siswa di hari itu
// ====================================================================
const calendarDataPerPeriode = {
    '2025-1': generateDummyCalendar(31), // Januari 31 hari
    '2025-2': generateDummyCalendar(28), // Februari 28 hari
    '2025-3': generateDummyCalendar(31), // Maret 31 hari
}

// Helper membuat data kalender dummy yang konsisten (deterministik)
// totalDays = jumlah hari dalam bulan tersebut
function generateDummyCalendar(totalDays) {
    const result = []
    for (let day = 1; day <= totalDays; day++) {
        // Rumus sederhana agar angka bervariasi tapi selalu sama tiap render
        const avgCompliance = 50 + ((day * 17) % 50)
        result.push({ date: day, avgCompliance })
    }
    return result
}

// ====================================================================
// STATE FILTER PERIODE — hanya mempengaruhi BAGIAN 2 & 3
// ====================================================================

// Daftar bulan untuk dropdown filter
const months = [
    { value: 1,  label: 'Januari'  }, { value: 2,  label: 'Februari' },
    { value: 3,  label: 'Maret'    }, { value: 4,  label: 'April'    },
    { value: 5,  label: 'Mei'      }, { value: 6,  label: 'Juni'     },
    { value: 7,  label: 'Juli'     }, { value: 8,  label: 'Agustus'  },
    { value: 9,  label: 'September'}, { value: 10, label: 'Oktober'  },
    { value: 11, label: 'November' }, { value: 12, label: 'Desember' },
]

// Daftar tahun untuk dropdown filter
const years = [2023, 2024, 2025, 2026]

// Bulan dan tahun yang sedang dipilih (default: Januari 2025)
const selectedMonth = ref(1)
const selectedYear  = ref(2025)

// periodKey = kunci gabungan tahun-bulan, dipakai untuk lookup data periode
// Otomatis berubah setiap selectedMonth atau selectedYear berubah
const periodKey = computed(() => `${selectedYear.value}-${selectedMonth.value}`)

// Gabungkan data hari ini (statis) + data periode (dinamis ikut filter)
// menjadi satu array siap pakai untuk tabel tab "Per Siswa"
const students = computed(() => {
    // Ambil data periode sesuai bulan/tahun yang dipilih
    const periodStats = periodStatsPerPeriode[periodKey.value] || {}
    return studentsToday.value.map((s) => ({
        // Spread semua properti siswa (id, name, habitsToday, status)
        ...s,
        // Ambil streak dari periode, default 0 jika tidak ada
        streak:      periodStats[s.id]?.streak      ?? 0,
        // Ambil persentase kepatuhan dari periode, default 0
        compliance:  periodStats[s.id]?.compliance  ?? 0,
        // Ambil data grafik mingguan, default array 7 nol
        weeklyChart: periodStats[s.id]?.weeklyChart ?? [0, 0, 0, 0, 0, 0, 0],
    }))
})

// Data kalender sesuai periode yang dipilih
const calendarDays = computed(() => calendarDataPerPeriode[periodKey.value] || [])

// ====================================================================
// STATE FILTER STATUS (panel filter checkbox)
// ====================================================================

// Apakah panel filter sedang terbuka atau tidak
const showFilterPanel = ref(false)

// Semua pilihan status yang tersedia
const statusOptions = ['Lengkap', 'Sebagian', 'Perlu Perhatian']

// Status yang sedang aktif difilter (default semua aktif = tampilkan semua)
const activeStatusFilters = ref([...statusOptions])

// Toggle satu status: jika sudah aktif → hapus, jika belum → tambahkan
function toggleStatusFilter(status) {
    if (activeStatusFilters.value.includes(status)) {
        // Hapus status dari filter aktif
        activeStatusFilters.value = activeStatusFilters.value.filter((s) => s !== status)
    } else {
        // Tambahkan status ke filter aktif
        activeStatusFilters.value.push(status)
    }
}

// ====================================================================
// STATE TAB, PENCARIAN, DAN SISWA TERPILIH
// ====================================================================

// Tab yang sedang aktif: 'siswa' / 'kebiasaan' / 'kalender'
const activeTab = ref('siswa')

// Teks pencarian nama siswa
const searchQuery = ref('')

// Siswa yang sedang dipilih untuk ditampilkan di panel kanan
const selectedStudent = ref(null)

// Tanggal kalender yang sedang dipilih (tab Kalender)
const selectedCalendarDay = ref(null)

// Siswa yang sudah difilter berdasarkan pencarian dan status
const filteredStudents = computed(() => {
    return students.value.filter((s) => {
        // Cocokkan nama siswa dengan teks pencarian (tidak case-sensitive)
        const matchSearch = s.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        // Cocokkan status siswa dengan filter status yang aktif
        const matchStatus = activeStatusFilters.value.includes(s.status)
        // Siswa ditampilkan hanya jika memenuhi KEDUA kondisi
        return matchSearch && matchStatus
    })
})

// Mengembalikan class Tailwind untuk badge status
function statusStyle(status) {
    if (status === 'Lengkap')          return 'bg-green-100 text-green-700'
    if (status === 'Sebagian')         return 'bg-yellow-100 text-yellow-700'
    return 'bg-red-100 text-red-600'   // Perlu Perhatian
}

// Mengembalikan class warna kotak kalender berdasarkan rata-rata kepatuhan
function calendarCellStyle(avgCompliance) {
    if (avgCompliance >= 85) return 'bg-[#1B7F5A] text-white'  // Tinggi
    if (avgCompliance >= 65) return 'bg-yellow-300 text-gray-800' // Sedang
    return 'bg-red-200 text-red-800'  // Rendah
}

// Set siswa yang dipilih saat baris tabel diklik
function selectStudent(student) {
    selectedStudent.value = student
}

// Dipanggil saat filter periode berubah:
// tutup panel filter dan reset tanggal kalender yang dipilih
function handlePeriodChange() {
    showFilterPanel.value    = false
    selectedCalendarDay.value = null
}

// onMounted: dijalankan setelah komponen selesai dimuat di browser
// Ini fix bug sebelumnya — selectedStudent diset di sini bukan di root script
onMounted(() => {
    // Set siswa pertama sebagai default yang ditampilkan di panel kanan
    selectedStudent.value = filteredStudents.value[0] || null
})
</script>

<template>
<GuruLayout>
    <div class="space-y-6">

        <!-- ===== HEADER ===== -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <!-- Judul halaman monitoring -->
                <h1 class="text-2xl font-bold text-gray-800">Monitoring Kelas 7A</h1>
                <p class="text-gray-500 mt-1 text-sm">
                    Pantau kegiatan dan kebiasaan setiap siswa secara detail
                </p>
            </div>

            <!-- Dropdown filter bulan dan tahun + tombol rekap -->
            <div class="flex items-center gap-2">

                <!-- Dropdown pilih bulan, v-model.number agar value berupa angka bukan string -->
                <select
                    v-model.number="selectedMonth"
                    @change="handlePeriodChange"
                    class="text-sm bg-white border border-gray-200 px-3 py-2 rounded-lg text-gray-700"
                >
                    <!-- Loop semua bulan dari array months -->
                    <option v-for="m in months" :key="m.value" :value="m.value">
                        {{ m.label }}
                    </option>
                </select>

                <!-- Dropdown pilih tahun -->
                <select
                    v-model.number="selectedYear"
                    @change="handlePeriodChange"
                    class="text-sm bg-white border border-gray-200 px-3 py-2 rounded-lg text-gray-700"
                >
                    <!-- Loop semua tahun dari array years -->
                    <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                </select>

                <!-- Tombol navigasi ke halaman rekap kelas -->
                <Link
                    href="/guru/rekap"
                    class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-4 py-2 rounded-xl text-sm font-medium transition"
                >
                    📊 Rekap Kelas
                </Link>
            </div>
        </div>

        <!-- Keterangan singkat: mana data real-time vs data periode -->
        <p class="text-xs text-gray-400 -mt-3">
            📌 Kolom "Kebiasaan Hari Ini" & "Status" selalu menampilkan kondisi hari ini.
            Filter bulan/tahun memengaruhi <strong>Streak</strong>, <strong>% Kepatuhan</strong>, dan tampilan <strong>Kalender</strong>.
        </p>

        <!-- Layout 2 kolom: kiri tabel (2/3), kanan panel detail siswa (1/3) -->
        <div class="grid lg:grid-cols-3 gap-6 items-start">

            <!-- ===== KOLOM KIRI: TAB + KONTEN ===== -->
            <div class="lg:col-span-2 space-y-4">

                <!-- TAB SWITCHER: Per Siswa / Per Kebiasaan / Kalender -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-1.5 flex gap-1 w-fit">

                    <!-- Setiap tombol tab mengubah activeTab saat diklik -->
                    <!-- Class berubah tergantung apakah tab ini yang aktif -->
                    <button
                        @click="activeTab = 'siswa'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition"
                        :class="activeTab === 'siswa' ? 'bg-[#0F3D2E] text-white' : 'text-gray-500 hover:bg-gray-50'"
                    >Per Siswa</button>

                    <button
                        @click="activeTab = 'kebiasaan'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition"
                        :class="activeTab === 'kebiasaan' ? 'bg-[#0F3D2E] text-white' : 'text-gray-500 hover:bg-gray-50'"
                    >Per Kebiasaan</button>

                    <button
                        @click="activeTab = 'kalender'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition"
                        :class="activeTab === 'kalender' ? 'bg-[#0F3D2E] text-white' : 'text-gray-500 hover:bg-gray-50'"
                    >Kalender</button>

                </div>

                <!-- ===== TAB: PER SISWA ===== -->
                <!-- v-if: hanya render jika tab 'siswa' yang aktif -->
                <div v-if="activeTab === 'siswa'"
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
                >

                    <!-- Baris pencarian + tombol filter -->
                    <div class="p-4 border-b border-gray-100 relative">
                        <div class="flex items-center gap-3">

                            <!-- Input pencarian nama siswa, v-model terhubung ke searchQuery -->
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari nama siswa..."
                                class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#1B7F5A]"
                            />

                            <!-- Tombol buka/tutup panel filter status -->
                            <button
                                @click="showFilterPanel = !showFilterPanel"
                                class="border rounded-xl px-4 py-2.5 text-sm transition shrink-0 relative"
                                :class="showFilterPanel
                                    ? 'border-[#1B7F5A] text-[#1B7F5A] bg-green-50'
                                    : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                            >
                                ⚙ Filter
                                <!-- Badge angka: tampil hanya jika tidak semua status aktif -->
                                <span
                                    v-if="activeStatusFilters.length < statusOptions.length"
                                    class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-[#1B7F5A] text-white text-[10px] flex items-center justify-center"
                                >
                                    {{ activeStatusFilters.length }}
                                </span>
                            </button>
                        </div>

                        <!-- Panel dropdown filter status (tampil jika showFilterPanel = true) -->
                        <div
                            v-if="showFilterPanel"
                            class="absolute right-4 top-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg p-4 z-10 w-56"
                        >
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-3">
                                Filter Status (Hari Ini)
                            </p>
                            <div class="space-y-2">
                                <!-- Loop setiap pilihan status sebagai checkbox -->
                                <label
                                    v-for="status in statusOptions"
                                    :key="status"
                                    class="flex items-center gap-2.5 cursor-pointer text-sm"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="activeStatusFilters.includes(status)"
                                        @change="toggleStatusFilter(status)"
                                        class="rounded border-gray-300 text-[#1B7F5A]"
                                    />
                                    <!-- Badge status berwarna sesuai nilainya -->
                                    <span class="px-2 py-0.5 rounded-full text-xs" :class="statusStyle(status)">
                                        {{ status }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Pesan kosong jika tidak ada siswa yang cocok filter/pencarian -->
                    <div v-if="filteredStudents.length === 0"
                        class="text-center py-12 text-sm text-gray-400"
                    >
                        Tidak ada siswa yang cocok dengan pencarian/filter
                    </div>

                    <!-- Tabel daftar siswa -->
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Nama Siswa</th>
                                    <!-- Sub-label menampilkan nama bulan yang dipilih -->
                                    <th class="text-center px-2 py-3 font-medium text-gray-500">Kebiasaan Hari Ini</th>
                                    <th class="text-center px-2 py-3 font-medium text-gray-500">
                                        Streak
                                        <span class="block text-[10px] font-normal text-gray-400">
                                            {{ months.find(m => m.value === selectedMonth).label }}
                                        </span>
                                    </th>
                                    <th class="text-center px-2 py-3 font-medium text-gray-500">
                                        Kepatuhan
                                        <span class="block text-[10px] font-normal text-gray-400">
                                            {{ months.find(m => m.value === selectedMonth).label }}
                                        </span>
                                    </th>
                                    <th class="text-center px-2 py-3 font-medium text-gray-500">Status</th>
                                    <th class="text-center px-2 py-3 font-medium text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop setiap siswa yang sudah difilter -->
                                <tr
                                    v-for="student in filteredStudents"
                                    :key="student.id"
                                    class="border-t border-gray-100 hover:bg-gray-50/60 cursor-pointer transition"
                                    :class="selectedStudent && selectedStudent.id === student.id ? 'bg-green-50/50' : ''"
                                    @click="selectStudent(student)"
                                >
                                    <!-- Kolom nama: avatar inisial + nama lengkap -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <!-- Avatar inisial: huruf pertama nama siswa -->
                                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-semibold text-xs shrink-0">
                                                {{ student.name[0] }}
                                            </div>
                                            <p class="font-medium text-gray-700">{{ student.name }}</p>
                                        </div>
                                    </td>

                                    <!-- Kolom titik kebiasaan: loop 7 titik berwarna per kebiasaan -->
                                    <td class="text-center px-2 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- Titik hijau jika dilakukan, abu jika tidak -->
                                            <span
                                                v-for="(done, i) in student.habitsToday"
                                                :key="i"
                                                class="w-2 h-2 rounded-full"
                                                :class="done ? 'bg-[#1B7F5A]' : 'bg-gray-200'"
                                                :title="habits[i].label"
                                            />
                                        </div>
                                    </td>

                                    <!-- Kolom streak: ikon api + angka -->
                                    <td class="text-center px-2 py-3 text-gray-600">
                                        🔥 {{ student.streak }}
                                    </td>

                                    <!-- Kolom kepatuhan: persentase berwarna hijau -->
                                    <td class="text-center px-2 py-3 font-semibold text-[#1B7F5A]">
                                        {{ student.compliance }}%
                                    </td>

                                    <!-- Kolom status: badge warna sesuai status -->
                                    <td class="text-center px-2 py-3">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="statusStyle(student.status)"
                                        >
                                            {{ student.status }}
                                        </span>
                                    </td>

                                    <!-- Kolom aksi: Link Inertia ke halaman detail siswa -->
                                    <!-- @click.stop: mencegah klik tombol ini memicu selectStudent di baris -->
                                    <td class="text-center px-2 py-3">
                                        <Link
                                            :href="`/guru/monitoring/${student.id}`"
                                            class="text-[#1B7F5A] hover:underline font-medium text-sm"
                                            @click.stop
                                        >
                                            Detail →
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ===== TAB: PER KEBIASAAN ===== -->
                <!-- v-else-if: hanya render jika tab 'kebiasaan' yang aktif -->
                <div v-else-if="activeTab === 'kebiasaan'"
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4"
                >
                    <p class="text-xs text-gray-400 mb-2">
                        Status kebiasaan hari ini, seluruh siswa kelas 7A
                    </p>

                    <!-- Loop setiap kebiasaan, tampilkan berapa siswa yang melakukannya -->
                    <div
                        v-for="(habit, hIndex) in habits"
                        :key="habit.key"
                        class="flex items-center justify-between border border-gray-100 rounded-xl px-4 py-3"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-lg">{{ habit.icon }}</span>
                            <p class="font-medium text-gray-700 text-sm">{{ habit.label }}</p>
                        </div>
                        <!-- Hitung jumlah siswa yang habitsToday[hIndex] = true -->
                        <span class="text-sm text-gray-400">
                            {{ studentsToday.filter((s) => s.habitsToday[hIndex]).length }}
                            / {{ studentsToday.length }} siswa
                        </span>
                    </div>
                </div>

                <!-- ===== TAB: KALENDER ===== -->
                <!-- v-else: render jika bukan 'siswa' dan bukan 'kebiasaan' -->
                <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                    <!-- Judul kalender + legenda warna -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-700 text-sm">
                            Rata-rata Kepatuhan Kelas —
                            {{ months.find(m => m.value === selectedMonth).label }}
                            {{ selectedYear }}
                        </h3>
                        <!-- Legenda 3 warna: Tinggi / Sedang / Rendah -->
                        <div class="flex items-center gap-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#1B7F5A]"></span> Tinggi
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-sm bg-yellow-300"></span> Sedang
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-sm bg-red-200"></span> Rendah
                            </span>
                        </div>
                    </div>

                    <!-- Grid kalender: 7 kolom (1 minggu), loop tiap tanggal -->
                    <div class="grid grid-cols-7 gap-2">
                        <div
                            v-for="day in calendarDays"
                            :key="day.date"
                            @click="selectedCalendarDay = day"
                            class="aspect-square rounded-lg flex flex-col items-center justify-center cursor-pointer transition hover:scale-105"
                            :class="calendarCellStyle(day.avgCompliance)"
                        >
                            <!-- Angka tanggal -->
                            <span class="text-sm font-semibold">{{ day.date }}</span>
                            <!-- Persentase kepatuhan hari itu -->
                            <span class="text-[10px] opacity-80">{{ day.avgCompliance }}%</span>
                        </div>
                    </div>

                    <!-- Detail tanggal yang dipilih (tampil setelah diklik) -->
                    <div v-if="selectedCalendarDay" class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-sm text-gray-600">
                            📅 Tanggal
                            <strong>
                                {{ selectedCalendarDay.date }}
                                {{ months.find(m => m.value === selectedMonth).label }}
                            </strong>:
                            rata-rata kepatuhan kelas
                            <strong class="text-[#1B7F5A]">
                                {{ selectedCalendarDay.avgCompliance }}%
                            </strong>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Klik tanggal lain untuk melihat ringkasan hari tersebut.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ===== KOLOM KANAN: PANEL PRATINJAU SISWA ===== -->
            <!-- Hanya tampil jika ada siswa dipilih DAN tab aktif adalah 'siswa' -->
            <div
                v-if="selectedStudent && activeTab === 'siswa'"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-6"
            >
                <!-- Info nama dan kelas siswa terpilih -->
                <div class="text-center pb-4 border-b border-gray-100">
                    <!-- Avatar inisial huruf pertama nama -->
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-xl mx-auto mb-3">
                        {{ selectedStudent.name[0] }}
                    </div>
                    <h3 class="font-bold text-gray-800">{{ selectedStudent.name }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Kelas 7A • Siswa Aktif</p>
                </div>

                <!-- Statistik: kepatuhan dan streak dalam 2 kolom -->
                <div class="grid grid-cols-2 gap-3 py-4 border-b border-gray-100">
                    <div class="text-center">
                        <p class="text-xl font-bold text-[#1B7F5A]">{{ selectedStudent.compliance }}%</p>
                        <p class="text-xs text-gray-400 mt-0.5">Kepatuhan</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-bold text-gray-800">🔥 {{ selectedStudent.streak }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Streak Hari</p>
                    </div>
                </div>

                <!-- Mini grafik mingguan: 7 bar tinggi berbeda -->
                <div class="py-4 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Grafik Minggu Ini</p>
                    <div class="flex items-end justify-between gap-1.5 h-20">
                        <!-- Loop 7 nilai weeklyChart, tinggi bar = nilai % -->
                        <div
                            v-for="(val, i) in selectedStudent.weeklyChart"
                            :key="i"
                            class="flex-1 bg-[#1B7F5A] rounded-t"
                            :style="{ height: val + '%' }"
                            :title="val + '%'"
                        />
                    </div>
                </div>

                <!-- Daftar 7 kebiasaan hari ini dengan status centang/strip -->
                <div class="py-4 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Kebiasaan Hari Ini</p>
                    <div class="space-y-2">
                        <div
                            v-for="(done, i) in selectedStudent.habitsToday"
                            :key="i"
                            class="flex items-center justify-between text-sm"
                        >
                            <!-- Ikon dan nama kebiasaan -->
                            <span class="text-gray-600">
                                {{ habits[i].icon }} {{ habits[i].label }}
                            </span>
                            <!-- Centang hijau jika dilakukan, strip abu jika tidak -->
                            <span :class="done ? 'text-[#1B7F5A]' : 'text-gray-300'">
                                {{ done ? '✓' : '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tombol navigasi ke halaman detail & evaluasi siswa terpilih -->
                <!-- Pakai Link Inertia agar navigasi tanpa full reload -->
                <Link
                    :href="`/guru/monitoring/${selectedStudent.id}`"
                    class="block text-center w-full mt-4 bg-[#1B7F5A] hover:bg-[#166347] text-white py-2.5 rounded-xl text-sm font-medium transition"
                >
                    ✏ Beri Evaluasi
                </Link>
            </div>

        </div>
    </div>
</GuruLayout>
</template>