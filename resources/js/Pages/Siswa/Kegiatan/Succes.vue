<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    kegiatanHariIni: { type: Object, required: true },
})

const daftarKebiasaan = [
    { field: 'waktu_bangun', icon: '🌅', nama: 'Bangun Pagi' },
    { field: 'detail_ibadah_centang', icon: '🙏', nama: 'Ibadah & Doa' },
    { field: 'menu_makan', icon: '🥗', nama: 'Makan Sehat & Bergizi' },
    { field: 'jenis_olahraga', icon: '🏃', nama: 'Olahraga' },
    { field: 'belajar_mandiri', icon: '📚', nama: 'Belajar Mandiri' },
    { field: 'aktivitas_sosial', icon: '🤝', nama: 'Aktivitas Sosial' },
    { field: 'waktu_tidur', icon: '😴', nama: 'Tidur Cepat' },
]

function sudahTerisi(field) {
    const nilai = props.kegiatanHariIni[field]
    return !(nilai === null || nilai === undefined || nilai === '' || (Array.isArray(nilai) && nilai.length === 0))
}

const tanggalFormatted = new Date(props.kegiatanHariIni.tanggal).toLocaleDateString('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
})

const jamKirim = props.kegiatanHariIni.submitted_at
    ? new Date(props.kegiatanHariIni.submitted_at).toLocaleTimeString('id-ID', {
          hour: '2-digit', minute: '2-digit'
      })
    : '-'
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- ===== HEADER ===== -->
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Catat Kegiatan Harian</h1>
        <p class="text-gray-400 text-sm mt-0.5">{{ tanggalFormatted }}</p>
      </div>

      <!-- ===== STEP INDICATOR ===== -->
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">✓</div>
          <span class="text-sm font-semibold text-[#1B7F5A]">Isi Kegiatan</span>
        </div>
        <div class="flex-1 h-px bg-[#1B7F5A]"></div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">✓</div>
          <span class="text-sm font-semibold text-[#1B7F5A]">Selfie Validasi</span>
        </div>
      </div>

      <!-- ===== KARTU SUKSES ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center text-3xl mx-auto mb-4">
          ✅
        </div>
        <h2 class="text-lg font-bold text-gray-800">Kegiatan Hari Ini Sudah Terkirim!</h2>
        <p class="text-xs text-gray-400 mt-1 mb-6">
          {{ tanggalFormatted }} · Dikirim pukul {{ jamKirim }}
        </p>

        <div class="max-w-sm mx-auto text-left space-y-3 mb-6">
          <div
            v-for="k in daftarKebiasaan"
            :key="k.field"
            class="flex items-center justify-between py-1">
            <div class="flex items-center gap-2">
              <span>{{ k.icon }}</span>
              <span class="text-sm text-gray-700">{{ k.nama }}</span>
            </div>
            <span
              class="font-bold"
              :class="sudahTerisi(k.field) ? 'text-[#1B7F5A]' : 'text-gray-300'">
              {{ sudahTerisi(k.field) ? '✓' : '-' }}
            </span>
          </div>
        </div>

        <p class="text-xs text-gray-400">
          Kegiatan sudah terkunci. Sampai jumpa besok! 👋
        </p>
      </div>

      <div class="flex justify-center pb-6">
        <Link
          href="/siswa/dashboard"
          class="bg-[#1B7F5A] text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-[#155f44] transition">
          Kembali ke Dashboard
        </Link>
      </div>

    </div>
  </SiswaLayout>
</template>