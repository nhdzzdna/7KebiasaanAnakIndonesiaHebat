<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  kegiatan: Object,
})

const k = props.kegiatan

const bukaAccordion = ref(null)

function toggleAccordion(index) {
  bukaAccordion.value = bukaAccordion.value === index ? null : index
}

const daftarKebiasaan = [
  { icon: '🌅', nama: 'Bangun Pagi', field: 'waktu_bangun',
    detail: () => k.waktu_bangun ? `Bangun jam ${k.waktu_bangun}${k.keterangan_bangun ? '. ' + k.keterangan_bangun : ''}` : '-',
    foto: 'bukti_foto_bangun' },
  { icon: '🕌', nama: 'Ibadah & Doa', field: 'detail_ibadah_centang',
    detail: () => {
      const parts = []
      if (k.detail_ibadah_centang?.length) parts.push(k.detail_ibadah_centang.join(', '))
      if (k.detail_ibadah_lain) parts.push(k.detail_ibadah_lain)
      return parts.join('. ') || '-'
    },
    foto: 'bukti_foto_ibadah' },
  { icon: '🥗', nama: 'Makan Sehat & Bergizi', field: 'menu_makan',
    detail: () => [k.menu_makan, k.jumlah_air ? `${k.jumlah_air} gelas air` : ''].filter(Boolean).join(', ') || '-',
    foto: 'bukti_foto_makan' },
  { icon: '🏃', nama: 'Olahraga', field: 'jenis_olahraga',
    detail: () => k.jenis_olahraga ? `${k.jenis_olahraga}${k.durasi_olahraga ? ', ' + k.durasi_olahraga + ' menit' : ''}` : '-',
    foto: 'bukti_foto_olahraga' },
  { icon: '📚', nama: 'Belajar Mandiri', field: 'belajar_mandiri',
    detail: () => k.belajar_mandiri ? `${k.belajar_mandiri}${k.durasi_belajar ? ', ' + k.durasi_belajar + ' menit' : ''}` : '-',
    foto: 'bukti_foto_belajar' },
  { icon: '🤝', nama: 'Aktivitas Sosial', field: 'aktivitas_sosial',
    detail: () => k.aktivitas_sosial || '-',
    foto: 'bukti_foto_sosial' },
  { icon: '😴', nama: 'Tidur Cepat', field: 'waktu_tidur',
    detail: () => k.waktu_tidur ? `Tidur jam ${k.waktu_tidur}${k.keterangan_tidur ? '. ' + k.keterangan_tidur : ''}` : '-',
    foto: 'bukti_foto' },
]

function terisi(field) {
  const v = k?.[field]
  return !(v === null || v === undefined || v === '' || (Array.isArray(v) && v.length === 0))
}

function formatTanggal(tgl) {
  return new Date(tgl).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const nilaiLabel = { A: 'Sangat Baik', B: 'Baik', C: 'Cukup', D: 'Perlu Bimbingan' }
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <div class="flex items-center gap-3">
        <a href="/siswa/riwayat" class="text-gray-400 hover:text-gray-600 text-sm flex items-center gap-1">← Kembali</a>
        <span class="text-gray-300">/</span>
        <h1 class="text-xl font-bold text-gray-800">Detail Kegiatan & Evaluasi</h1>
      </div>

      <p class="text-gray-400 text-sm">{{ formatTanggal(k.tanggal) }}</p>

      <div class="grid grid-cols-3 gap-5 items-start">

        <div class="col-span-2 space-y-4">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
              <span>📋</span>
              <h4 class="text-sm font-bold text-gray-700">Detail 7 Kebiasaan — {{ formatTanggal(k.tanggal) }}</h4>
            </div>

            <div v-for="(kb, i) in daftarKebiasaan" :key="i" class="border-b border-gray-50 last:border-0">
              <div class="flex items-center justify-between px-5 py-3 cursor-pointer hover:bg-gray-50 transition" @click="toggleAccordion(i)">
                <div class="flex items-center gap-3">
                  <span class="text-base">{{ kb.icon }}</span>
                  <span class="text-sm font-medium text-gray-700">{{ kb.nama }}</span>
                </div>
                <div class="flex items-center gap-3">
                  <span class="text-xs px-2 py-0.5 rounded-full" :class="terisi(kb.field) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'">
                    {{ terisi(kb.field) ? '✓ Lengkap' : '— Tidak' }}
                  </span>
                  <button v-if="k[kb.foto]" @click.stop="window.open('/storage/' + k[kb.foto])" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-500 px-2 py-1 rounded-lg transition">
                    📷 Lihat Foto
                  </button>
                  <span class="text-gray-400 text-xs transition-transform" :class="bukaAccordion === i ? 'rotate-180' : ''">▼</span>
                </div>
              </div>
              <div v-if="bukaAccordion === i" class="px-5 pb-3 text-sm text-gray-500 bg-gray-50">
                {{ kb.detail() }}
              </div>
            </div>
          </div>
        </div>

        <div class="col-span-1 space-y-4">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100">
              <h4 class="font-bold text-sm text-gray-700">📸 Selfie Validasi</h4>
            </div>
            <div class="p-2">
              <img v-if="k.selfie_validasi" :src="`/storage/${k.selfie_validasi}`" class="w-full h-36 rounded-xl object-cover border border-gray-200" />
              <div v-else class="w-full h-36 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                Tidak ada selfie
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3">
            <h4 class="font-bold text-sm text-gray-700 mb-3">👨‍🏫 Evaluasi Guru</h4>

            <div v-if="!k.nilai_guru" class="text-xs text-gray-400 text-center py-4">
              Belum ada evaluasi dari guru
            </div>
            <div v-else>
              <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">G</div>
                <div>
                  <p class="font-semibold text-sm text-[#1B7F5A]">Wali Kelas</p>
                  <p class="text-xs text-gray-400">{{ formatTanggal(k.tanggal) }}</p>
                </div>
              </div>
              <p v-if="k.catatan_guru" class="text-sm text-gray-600 italic leading-relaxed">"{{ k.catatan_guru }}"</p>
              <div class="mt-3">
                <span class="inline-flex items-center gap-2 bg-[#1B7F5A] text-white text-sm px-3 py-2 rounded-xl font-medium">
                  🏅 Nilai: {{ k.nilai_guru }} — {{ nilaiLabel[k.nilai_guru] }}
                </span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </SiswaLayout>
</template>