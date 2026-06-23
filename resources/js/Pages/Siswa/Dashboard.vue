<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'

const props = defineProps({
    stats: { type: Object, required: true },
    todayReport: { type: Object, default: null },
    todayStatus: { type: String, required: true },
    isTodaySubmitted: { type: Boolean, required: true },
    isTodayEvaluated: { type: Boolean, required: true },
    todayCompliance: { type: Number, required: true },
    profileWarning: { type: Boolean, required: true },
    latestEvaluation: { type: Object, default: null },
    latestFeedback: { type: String, default: null },
    namaWaliKelas: { type: String, default: null },
    recentReports: { type: Array, default: () => [] },
    weeklyChart: { type: Array, default: () => [] },
})

// DAFTAR 7 KEBIASAAN, DIPAKAI UNTUK BACA FIELD DARI todayReport SECARA DINAMIS
const daftarKebiasaan = [
    { field: 'waktu_bangun', icon: '🌅', nama: 'Bangun Pagi' },
    { field: 'detail_ibadah_centang', icon: '🕌', nama: 'Ibadah & Doa' },
    { field: 'menu_makan', icon: '🥗', nama: 'Makan Sehat & Bergizi' },
    { field: 'jenis_olahraga', icon: '🏃', nama: 'Olahraga' },
    { field: 'belajar_mandiri', icon: '📚', nama: 'Belajar Mandiri' },
    { field: 'aktivitas_sosial', icon: '🤝', nama: 'Aktivitas Sosial' },
    { field: 'waktu_tidur', icon: '😴', nama: 'Tidur Cepat' },
]

// CEK APAKAH SUATU FIELD KEBIASAAN SUDAH TERISI DI todayReport
function sudahTerisi(field) {
    if (!props.todayReport) return false
    const nilai = props.todayReport[field]
    return !(nilai === null || nilai === undefined || nilai === '' || (Array.isArray(nilai) && nilai.length === 0))
}

// AMBIL TEKS RINGKASAN UNTUK DITAMPILKAN DI BAWAH NAMA KEBIASAAN
function ringkasanField(field) {
    if (!props.todayReport) return 'Belum dicatat'
    const nilai = props.todayReport[field]
    if (!sudahTerisi(field)) return 'Belum dicatat'
    if (Array.isArray(nilai)) return nilai.join(', ') + ' ✓'
    return nilai + ' ✓'
}

// LABEL BADGE NILAI: "A — Sangat Baik", "B — Baik", dst
const labelNilai = {
    A: 'Sangat Baik',
    B: 'Baik',
    C: 'Cukup',
    D: 'Perlu Bimbingan',
}

// TANGGAL HARI INI, FORMAT INDONESIA
const hariIniFormatted = new Date().toLocaleDateString('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
})

// WARNA BAR PER HARI DI GRAFIK MINGGU INI: HIJAU JIKA >=80%, ORANGE JIKA ADA DATA TAPI DI BAWAH 80%, ABU JIKA BELUM ADA DATA
function warnaBarMingguan(hari) {
    if (hari.compliance === 0) return null // ditangani terpisah di template (tampil "—")
    if (hari.compliance >= 80) return 'bg-[#1B7F5A]'
    return 'bg-orange-400'
}
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- HEADER -->
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Dashboard Siswa</h1>
          <p class="text-gray-400 text-sm mt-0.5">Pantau kebiasaanmu hari ini, {{ hariIniFormatted }}</p>
        </div>
        <button
          @click="$inertia.visit('/siswa/kegiatan')"
          class="flex items-center gap-2 bg-[#1B7F5A] text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-[#155f44] transition">
          <span>📋</span> Catat Kegiatan Hari Ini
        </button>
      </div>

      <!-- ALERT PROFIL BELUM LENGKAP -->
      <div v-if="profileWarning" class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <div class="flex items-start gap-3">
          <div class="bg-amber-400 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm flex-shrink-0">✏️</div>
          <div class="flex-1">
            <h3 class="font-bold text-amber-700 text-sm">Profil Kamu Belum Lengkap!</h3>
            <p class="text-xs text-amber-600 mt-0.5">Lengkapi data personalmu agar guru bisa mengenalmu dengan baik</p>
            <div class="mt-3 flex items-center gap-3">
              <div class="flex-1 bg-amber-200 rounded-full h-2">
                <div class="bg-amber-500 h-2 rounded-full" :style="{ width: stats.profile_completion + '%' }"></div>
              </div>
              <span class="text-xs text-amber-600 whitespace-nowrap">{{ stats.profile_completion }}% lengkap — masih ada beberapa data yang belum diisi</span>
            </div>
          </div>
          <button
            @click="$inertia.visit('/siswa/profile')"
            class="bg-amber-500 text-white text-xs font-semibold px-4 py-2 rounded-xl whitespace-nowrap hover:bg-amber-600 transition">
            Lengkapi Sekarang →
          </button>
        </div>
      </div>

      <!-- STAT CARDS (4 kolom) -->
      <div class="grid grid-cols-4 gap-4">

        <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
          <div class="text-3xl">🔥</div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.streak }}</p>
            <p class="text-xs text-gray-500 font-medium">Hari Beruntun</p>
            <p class="text-xs text-gray-400">Terus pertahankan!</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
          <div class="text-3xl">📊</div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.average_compliance }}%</p>
            <p class="text-xs text-gray-500 font-medium">Kepatuhan Bulan Ini</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
          <div class="text-3xl">✅</div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.habits_filled_today }}/{{ stats.habits_total }}</p>
            <p class="text-xs text-gray-500 font-medium">Kebiasaan Hari Ini</p>
            <p class="text-xs text-gray-400">{{ stats.habits_total - stats.habits_filled_today }} belum dicatat</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
          <div class="text-3xl">🏆</div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.total_reports }}</p>
            <p class="text-xs text-gray-500 font-medium">Total Hari Tercatat</p>
          </div>
        </div>

      </div>

      <!-- KONTEN UTAMA: 2 KOLOM -->
      <div class="grid grid-cols-3 gap-5">

        <!-- KIRI: KEBIASAAN HARI INI (2/3 lebar) -->
        <div class="col-span-2 bg-white rounded-2xl p-6 shadow-sm">

          <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
              <span class="text-red-500">🗓️</span>
              <h3 class="font-bold text-gray-800">Kebiasaan Hari Ini — {{ hariIniFormatted }}</h3>
            </div>
            <button
              v-if="!isTodaySubmitted"
              @click="$inertia.visit('/siswa/kegiatan')"
              class="flex items-center gap-1 bg-[#1B7F5A] text-white text-xs font-semibold px-3 py-2 rounded-xl hover:bg-[#155f44] transition">
              + Catat Sekarang
            </button>
          </div>

          <div class="space-y-3">
            <div
              v-for="(k, i) in daftarKebiasaan"
              :key="k.field"
              class="flex items-center justify-between py-3"
              :class="i < daftarKebiasaan.length - 1 ? 'border-b border-gray-100' : ''">
              <div class="flex items-center gap-3">
                <span class="text-xl">{{ k.icon }}</span>
                <div>
                  <p class="text-sm font-semibold text-gray-800">{{ i + 1 }}. {{ k.nama }}</p>
                  <p class="text-xs text-gray-400">{{ ringkasanField(k.field) }}</p>
                </div>
              </div>
              <div
                class="w-6 h-6 rounded-md border-2 flex items-center justify-center text-white text-xs"
                :class="sudahTerisi(k.field) ? 'border-[#1B7F5A] bg-[#1B7F5A]' : 'border-gray-300'">
                <span v-if="sudahTerisi(k.field)">✓</span>
              </div>
            </div>
          </div>
        </div>

        <!-- KANAN: CATATAN GURU + MINGGU INI (1/3 lebar) -->
        <div class="col-span-1 space-y-4">

          <!-- Catatan Guru -->
          <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
              <span class="text-gray-500">💬</span>
              <h3 class="font-bold text-gray-800 text-sm">Catatan Terbaru dari Guru</h3>
            </div>

            <div v-if="!latestEvaluation" class="text-xs text-gray-400 text-center py-4">
              Belum ada catatan dari guru
            </div>

            <div v-else class="border border-gray-100 rounded-xl p-4">
              <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-full bg-[#1B7F5A] flex items-center justify-center text-white text-xs font-bold">
                    {{ namaWaliKelas ? namaWaliKelas[0] : '-' }}
                </div>
                <div>
                    <p class="text-xs font-bold text-[#1B7F5A]">{{ namaWaliKelas ?? 'Wali Kelas' }}</p>
                    <p class="text-xs text-gray-400">{{ new Date(latestEvaluation.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
                </div>
              </div>
              <p v-if="latestFeedback" class="text-xs text-gray-600 italic leading-relaxed">
                "{{ latestFeedback }}"
              </p>
              <div class="mt-3">
                <span class="inline-flex items-center gap-1 bg-[#1B7F5A] text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                  Nilai: {{ latestEvaluation.nilai_guru }} — {{ labelNilai[latestEvaluation.nilai_guru] ?? '-' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Minggu Ini -->
          <div class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
              <span>📊</span>
              <h3 class="font-bold text-gray-800 text-sm">Minggu Ini</h3>
            </div>

            <div class="space-y-3">
              <div
                v-for="hari in weeklyChart"
                :key="hari.tanggal"
                class="flex items-center gap-3">
                <span class="text-xs text-gray-500 w-6">{{ hari.hari }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3">
                  <div
                    v-if="hari.compliance > 0"
                    class="h-3 rounded-full"
                    :class="warnaBarMingguan(hari)"
                    :style="{ width: hari.compliance + '%' }"
                  ></div>
                </div>
                <span class="text-xs w-8 text-right" :class="hari.compliance > 0 ? 'text-gray-500' : 'text-gray-400'">
                  {{ hari.compliance > 0 ? hari.compliance + '%' : '—' }}
                </span>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>
  </SiswaLayout>
</template>