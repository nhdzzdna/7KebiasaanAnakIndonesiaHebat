<script setup>
import GuruLayout from '@/Layouts/GuruLayout.vue'
import BarChartCard from '@/Components/BarChartCard.vue'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    rekapSiswa:        Array,
    stats:             Object,
    kebiasaanTerlemah: Array,
    trenKepatuhan:     Array,
    filters:           Object,
})

const filterBulan = ref(props.filters?.bulan ?? new Date().getMonth() + 1)
const filterTahun = ref(props.filters?.tahun ?? new Date().getFullYear())

const daftarBulan = [
    { value: 1,  label: 'Januari'   }, { value: 2,  label: 'Februari'  },
    { value: 3,  label: 'Maret'     }, { value: 4,  label: 'April'     },
    { value: 5,  label: 'Mei'       }, { value: 6,  label: 'Juni'      },
    { value: 7,  label: 'Juli'      }, { value: 8,  label: 'Agustus'   },
    { value: 9,  label: 'September' }, { value: 10, label: 'Oktober'   },
    { value: 11, label: 'November'  }, { value: 12, label: 'Desember'  },
]

function applyFilter() {
    router.get(
        route('guru.rekap.index'),
        { bulan: filterBulan.value, tahun: filterTahun.value },
        { preserveState: true }
    )
}

// ── Data untuk BarChartCard ──────────────────────────────────────────────────
const chartLabels = computed(() =>
    (props.trenKepatuhan ?? []).map(t => t.bulan.split(' ')[0])
)
const chartValues = computed(() =>
    (props.trenKepatuhan ?? []).map(t => t.rata_rata)
)
// Highlight bar bulan aktif (index terakhir dari array)
const chartHighlight = computed(() => [
    (props.trenKepatuhan?.length ?? 1) - 1
])

// ── Search & pagination ──────────────────────────────────────────────────────
const search       = ref('')
const halamanAktif = ref(1)
const perHalaman   = 5

// ── Filter kebiasaan (dropdown "Semua Kebiasaan") ────────────────────────────
const filterKebiasaan = ref('semua')
const daftarKebiasaanKolom = [
    { value: 'bangun',   label: 'Bangun Pagi'   },
    { value: 'olahraga', label: 'Olahraga'      },
    { value: 'makan',    label: 'Makan Sehat'   },
    { value: 'belajar',  label: 'Belajar Mandiri' },
    { value: 'ibadah',   label: 'Ibadah'        },
    { value: 'tidur',    label: 'Tidur Cukup'   },
    { value: 'sosial',   label: 'Sosial'        },
]

// kolom yang harus ditampilkan di tabel, sesuai filter aktif
const kolomTampil = computed(() => {
    if (filterKebiasaan.value === 'semua') {
        return daftarKebiasaanKolom
    }
    return daftarKebiasaanKolom.filter(k => k.value === filterKebiasaan.value)
})

// ── Filter nilai & rentang kepatuhan (tombol "🔽 Filter" di search bar) ──────
const showFilterPanel = ref(false)
const filterNilai = ref('')
const filterKepatuhan = ref('')

const daftarPilihanNilai = [
    { value: 'A', label: 'A — Baik Sekali' },
    { value: 'B', label: 'B — Baik' },
    { value: 'C', label: 'C — Cukup' },
    { value: 'D', label: 'D — Perlu Bimbingan' },
]
const daftarPilihanKepatuhan = [
    { value: 'tinggi', label: '≥ 90% (Berprestasi)' },
    { value: 'sedang', label: '60% – 89%' },
    { value: 'rendah', label: '< 60% (Perlu Bimbingan)' },
]

function cocokKepatuhan(persen, rentang) {
    if (rentang === 'tinggi') return persen >= 90
    if (rentang === 'sedang') return persen >= 60 && persen < 90
    if (rentang === 'rendah') return persen < 60
    return true
}

function resetFilter() {
    filterNilai.value = ''
    filterKepatuhan.value = ''
    showFilterPanel.value = false
    halamanAktif.value = 1
}

const filtered = computed(() =>
    (props.rekapSiswa ?? []).filter(s => {
        const cocokNama = s.nama.toLowerCase().includes(search.value.toLowerCase())
        const cocokNilai = !filterNilai.value || s.nilai_akhir === filterNilai.value
        const cocokKep = !filterKepatuhan.value || cocokKepatuhan(s.rata_rata_kepatuhan, filterKepatuhan.value)
        return cocokNama && cocokNilai && cocokKep
    })
)
const totalHalaman = computed(() =>
    Math.max(1, Math.ceil(filtered.value.length / perHalaman))
)
const paginatedData = computed(() => {
    const start = (halamanAktif.value - 1) * perHalaman
    return filtered.value.slice(start, start + perHalaman)
})
function onSearch() { halamanAktif.value = 1 }
function onFilterChange() { halamanAktif.value = 1 }

// ── Helpers ──────────────────────────────────────────────────────────────────
function warnaPersentase(val) {
    if (val >= 90) return 'text-green-600'
    if (val >= 70) return 'text-yellow-500'
    return 'text-red-500'
}
function warnaNilai(val) {
    if (val === 'A') return 'text-green-600'
    if (val === 'B') return 'text-yellow-500'
    if (val === 'C') return 'text-orange-500'
    if (val === 'D') return 'text-red-500'
    return 'text-gray-400'
}
function warnaBar(persen) {
    if (persen < 70) return 'bg-red-400'
    if (persen < 80) return 'bg-yellow-400'
    return 'bg-blue-400'
}
function warnaBarText(persen) {
    if (persen < 70) return 'text-red-400'
    if (persen < 80) return 'text-yellow-400'
    return 'text-blue-400'
}
function inisial(nama) {
    return nama.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
}
const warnaAvatar = ['bg-blue-400','bg-green-400','bg-yellow-400','bg-pink-400','bg-purple-400']
function warnaAvatarByIndex(i) { return warnaAvatar[i % warnaAvatar.length] }

const urlPdf = computed(() =>
    `/guru/rekap/pdf?bulan=${filterBulan.value}&tahun=${filterTahun.value}`
)
const urlExcel = computed(() =>
    `/guru/rekap/excel?bulan=${filterBulan.value}&tahun=${filterTahun.value}`
)
</script>

<template>
    <GuruLayout>
        <div class="space-y-5 p-6">

            <!-- ── HEADER + FILTER + EKSPOR ───────────────────────────────── -->
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Rekap & Laporan Kelas {{ filters?.kelas ?? '' }}</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Rekap otomatis kebiasaan seluruh siswa per periode</p>
                </div>

                <div class="flex flex-wrap gap-2 items-center">

                    <!-- Dropdown bulan -->
                    <div class="relative">
                        <select
                            v-model="filterBulan"
                            @change="applyFilter"
                            class="appearance-none bg-white border border-gray-200 rounded-xl pl-4 pr-9 py-2 text-sm text-gray-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#1B7F5A] shadow-sm"
                        >
                            <option v-for="b in daftarBulan" :key="b.value" :value="b.value">
                                {{ b.label }} {{ filterTahun }}
                            </option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Dropdown semua kebiasaan -->
                    <div class="relative">
                        <select
                            v-model="filterKebiasaan"
                            class="appearance-none bg-white border border-gray-200 rounded-xl pl-4 pr-9 py-2 text-sm text-gray-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#1B7F5A] shadow-sm"
                        >
                            <option value="semua">Semua Kebiasaan</option>
                            <option v-for="k in daftarKebiasaanKolom" :key="k.value" :value="k.value">
                                {{ k.label }}
                            </option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Ekspor PDF (download langsung, bukan Inertia) -->
                    <a
                        :href="urlPdf"
                        class="flex items-center gap-1.5 border border-gray-300 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-50 transition"
                    >
                        🖨️ Ekspor PDF
                    </a>

                    <!-- Ekspor Excel (download langsung, bukan Inertia) -->
                    <a
                        :href="urlExcel"
                        class="flex items-center gap-1.5 bg-[#1B7F5A] text-white px-4 py-2 rounded-xl text-sm hover:bg-[#155f44] transition"
                    >
                        📊 Ekspor Excel
                    </a>
                </div>
            </div>

            <!-- ── SUMMARY CARDS ──────────────────────────────────────────── -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-xl">📈</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ stats?.rata_rata_kelas ?? 0 }}%</p>
                        <p class="text-xs text-gray-500 leading-tight">Rata-rata Kelas</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center text-xl">🏆</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ stats?.siswa_berprestasi ?? 0 }}</p>
                        <p class="text-xs text-gray-500 leading-tight">Siswa 90%+</p>
                        <p class="text-xs text-yellow-600">Prestasi terbaik</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-xl">⚠️</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ stats?.siswa_perlu_bimbingan ?? 0 }}</p>
                        <p class="text-xs text-gray-500 leading-tight">Siswa &lt;60%</p>
                        <p class="text-xs text-orange-500">Perlu bimbingan</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center text-xl">📝</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ stats?.sudah_dievaluasi ?? 0 }}</p>
                        <p class="text-xs text-gray-500 leading-tight">Sudah Dievaluasi</p>
                        <p class="text-xs text-red-400">{{ stats?.belum_dievaluasi ?? 0 }} belum</p>
                    </div>
                </div>
            </div>

            <!-- ── TREN + KEBIASAAN TERLEMAH ──────────────────────────────── -->
            <div class="grid md:grid-cols-2 gap-4">

                <!-- Tren kepatuhan pakai BarChartCard -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">📅 Tren Kepatuhan per Periode</h3>
                    <BarChartCard
                        :labels="chartLabels"
                        :values="chartValues"
                        :highlight-indexes="chartHighlight"
                        :height="144"
                    />
                </div>

                <!-- Kebiasaan terlemah -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">📊 Kebiasaan Terlemah Kelas</h3>
                    <div class="space-y-4">
                        <div
                            v-for="k in (kebiasaanTerlemah ?? []).slice(0, 3)"
                            :key="k.kebiasaan"
                        >
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ k.kebiasaan }}</span>
                                <span class="font-semibold" :class="warnaBarText(k.persentase)">
                                    {{ k.persentase }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div
                                    class="h-2.5 rounded-full"
                                    :class="warnaBar(k.persentase)"
                                    :style="{ width: k.persentase + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── TABEL REKAP SISWA ───────────────────────────────────────── -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                <!-- Search -->
                <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 relative">
                    <div class="flex-1 flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
                        <span class="text-gray-400 text-sm">🔍</span>
                        <input
                            v-model="search"
                            @input="onSearch"
                            type="text"
                            placeholder="Cari siswa..."
                            class="bg-transparent text-sm w-full focus:outline-none text-gray-700"
                        />
                    </div>
                    <button
                        @click="showFilterPanel = !showFilterPanel"
                        class="flex items-center gap-1.5 border rounded-xl px-4 py-2 text-sm transition"
                        :class="showFilterPanel || filterNilai || filterKepatuhan
                            ? 'border-[#1B7F5A] text-[#1B7F5A] bg-green-50'
                            : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                    >
                        🔽 Filter
                    </button>

                    <div v-if="showFilterPanel" class="absolute right-5 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg p-4 z-20 w-60">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Nilai Akhir</p>
                        <select
                            v-model="filterNilai"
                            @change="onFilterChange"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-3"
                        >
                            <option value="">Semua Nilai</option>
                            <option v-for="n in daftarPilihanNilai" :key="n.value" :value="n.value">{{ n.label }}</option>
                        </select>

                        <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Kepatuhan</p>
                        <select
                            v-model="filterKepatuhan"
                            @change="onFilterChange"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-3"
                        >
                            <option value="">Semua Rentang</option>
                            <option v-for="k in daftarPilihanKepatuhan" :key="k.value" :value="k.value">{{ k.label }}</option>
                        </select>

                        <button
                            @click="resetFilter"
                            class="w-full text-center text-xs text-gray-500 hover:text-gray-700 transition"
                        >
                            Reset Filter
                        </button>
                    </div>
                </div>

                <!-- Tabel -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-4 py-3 w-8">#</th>
                                <th class="text-left px-4 py-3">Nama Siswa</th>
                                <th
                                    v-for="k in kolomTampil"
                                    :key="k.value"
                                    class="text-center px-3 py-3"
                                >{{ k.label }}</th>
                                <th class="text-center px-3 py-3">Rata-rata</th>
                                <th class="text-center px-3 py-3">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(siswa, i) in paginatedData"
                                :key="siswa.id_siswa"
                                class="border-t border-gray-50 hover:bg-gray-50 transition"
                            >
                                <td class="px-4 py-3 text-gray-400 text-xs">
                                    {{ String((halamanAktif - 1) * perHalaman + i + 1).padStart(2, '0') }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                            :class="warnaAvatarByIndex(i)"
                                        >{{ inisial(siswa.nama) }}</div>
                                        <span class="font-medium text-gray-700">{{ siswa.nama }}</span>
                                    </div>
                                </td>

                                <td
                                    v-for="k in kolomTampil"
                                    :key="k.value"
                                    class="text-center px-3 py-3 font-semibold"
                                    :class="warnaPersentase(siswa[k.value])"
                                >{{ siswa[k.value] }}%</td>

                                <td class="text-center px-3 py-3 font-bold" :class="warnaPersentase(siswa.rata_rata_kepatuhan)">
                                    {{ siswa.rata_rata_kepatuhan }}%
                                </td>

                                <td class="text-center px-3 py-3 font-bold" :class="warnaNilai(siswa.nilai_akhir)">
                                    {{ siswa.nilai_akhir }}
                                </td>
                            </tr>

                            <tr v-if="paginatedData.length === 0">
                                <td :colspan="kolomTampil.length + 4" class="text-center py-10 text-gray-400">
                                    Tidak ada siswa ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                    <span class="text-sm text-gray-400">
                        {{ (halamanAktif - 1) * perHalaman + 1 }}–{{ Math.min(halamanAktif * perHalaman, filtered.length) }}
                        dari {{ filtered.length }} siswa
                    </span>
                    <div class="flex items-center gap-1">
                        <button
                            @click="halamanAktif = Math.max(1, halamanAktif - 1)"
                            :disabled="halamanAktif === 1"
                            class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-30 transition"
                        >‹</button>
                        <button
                            v-for="p in totalHalaman"
                            :key="p"
                            @click="halamanAktif = p"
                            class="w-8 h-8 rounded-lg border text-sm font-medium transition"
                            :class="halamanAktif === p
                                ? 'bg-[#1B7F5A] text-white border-[#1B7F5A]'
                                : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                        >{{ p }}</button>
                        <button
                            @click="halamanAktif = Math.min(totalHalaman, halamanAktif + 1)"
                            :disabled="halamanAktif === totalHalaman"
                            class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-30 transition"
                        >›</button>
                    </div>
                </div>
            </div>

        </div>
    </GuruLayout>
</template>