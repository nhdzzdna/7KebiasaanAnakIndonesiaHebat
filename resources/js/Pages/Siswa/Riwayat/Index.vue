<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  kegiatans: Object,
  filters: Object,
})

const bulan = ref(props.filters?.bulan ?? new Date().getMonth() + 1)
const tahun = ref(props.filters?.tahun ?? new Date().getFullYear())
const selectedItem = ref(props.kegiatans?.data?.[0] ?? null)

const warnaTitik = [
  'bg-orange-400', 'bg-purple-400', 'bg-green-500',
  'bg-blue-400', 'bg-yellow-400', 'bg-pink-400', 'bg-indigo-400',
]

const namaKebiasaan = [
  { icon: '🌅', nama: 'Bangun Pagi', field: 'waktu_bangun' },
  { icon: '🕌', nama: 'Ibadah & Doa', field: 'detail_ibadah_centang' },
  { icon: '🥗', nama: 'Makan Sehat & Bergizi', field: 'menu_makan' },
  { icon: '🏃', nama: 'Olahraga', field: 'jenis_olahraga' },
  { icon: '📚', nama: 'Belajar Mandiri', field: 'belajar_mandiri' },
  { icon: '🤝', nama: 'Aktivitas Sosial', field: 'aktivitas_sosial' },
  { icon: '😴', nama: 'Tidur Cepat', field: 'waktu_tidur' },
]

function terisi(item, field) {
  const v = item?.[field]
  return !(v === null || v === undefined || v === '' || (Array.isArray(v) && v.length === 0))
}

function kebiasaanList(item) {
  return namaKebiasaan.map(k => terisi(item, k.field))
}

function warnaBadgeStatus(status) {
  if (status === 'submitted' || status === 'evaluated') return 'bg-green-100 text-green-700'
  if (status === 'draft') return 'bg-gray-100 text-gray-500'
  return 'bg-red-100 text-red-600'
}

function labelStatus(status) {
  if (status === 'submitted' || status === 'evaluated') return 'Terkirim'
  if (status === 'draft') return 'Draft'
  return status
}

function formatTanggal(tgl) {
  return new Date(tgl).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function terapkanFilter() {
  router.get('/siswa/riwayat', { bulan: bulan.value, tahun: tahun.value }, { preserveState: true, replace: true })
}

const nilaiLabel = { A: 'Sangat Baik', B: 'Baik', C: 'Cukup', D: 'Perlu Bimbingan' }

const selectedKebiasaan = computed(() => kebiasaanList(selectedItem.value))
const selectedKepatuhan = computed(() => {
  if (!selectedItem.value) return 0
  return Math.round(selectedKebiasaan.value.filter(Boolean).length / 7 * 100)
})
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Riwayat Kegiatan</h1>
          <p class="text-gray-400 text-sm mt-0.5">Semua kegiatan yang sudah kamu catat</p>
        </div>
        <a href="/siswa/kegiatan" class="bg-[#1B7F5A] text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-[#155f44] transition">
          + Catat Baru
        </a>
      </div>

      <div class="grid grid-cols-3 gap-5 items-start">

        <div class="col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

          <div class="p-4 border-b border-gray-100 flex items-center gap-3">
            <select v-model="bulan" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
              <option v-for="(n, i) in ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']" :key="i" :value="i+1">{{ n }}</option>
            </select>
            <select v-model="tahun" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
              <option>2025</option>
              <option>2026</option>
            </select>
            <button @click="terapkanFilter" class="border border-gray-200 text-gray-600 text-sm px-3 py-2 rounded-xl hover:bg-gray-50 transition">
              🔍 Filter
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                  <th class="text-left px-4 py-3">Tanggal</th>
                  <th class="text-center px-2 py-3">Kebiasaan</th>
                  <th class="text-center px-3 py-3">Selfie</th>
                  <th class="text-center px-3 py-3">Status</th>
                  <th class="text-center px-3 py-3">Nilai Guru</th>
                  <th class="text-center px-3 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!kegiatans?.data?.length">
                  <td colspan="6" class="text-center py-8 text-gray-400">Belum ada riwayat</td>
                </tr>
                <tr
                  v-for="item in kegiatans?.data"
                  :key="item.id"
                  class="border-t border-gray-50 hover:bg-gray-50 cursor-pointer transition"
                  :class="selectedItem?.id === item.id ? 'bg-green-50' : ''"
                  @click="selectedItem = item">
                  <td class="px-4 py-3 font-medium text-gray-700">{{ formatTanggal(item.tanggal) }}</td>
                  <td class="px-2 py-3">
                    <div class="flex items-center justify-center gap-1">
                      <div v-for="(done, i) in kebiasaanList(item)" :key="i"
                        class="w-2.5 h-2.5 rounded-full"
                        :class="done ? warnaTitik[i] : 'bg-gray-200'">
                      </div>
                    </div>
                  </td>
                  <td class="text-center py-3">
                    <span v-if="item.selfie_validasi" class="text-green-500 font-bold text-base">✓</span>
                    <span v-else class="text-gray-300">—</span>
                  </td>
                  <td class="text-center py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium" :class="warnaBadgeStatus(item.status)">
                      {{ labelStatus(item.status) }}
                    </span>
                  </td>
                  <td class="text-center py-3 font-semibold text-[#1B7F5A]">{{ item.nilai_guru ?? '-' }}</td>
                  <td class="text-center py-3 px-3">
                    <a :href="`/siswa/riwayat/${item.id}`"
                      class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg transition"
                      @click.stop>
                      Detail
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-400">
            <span>{{ kegiatans?.from ?? 0 }}–{{ kegiatans?.to ?? 0 }} dari {{ kegiatans?.total ?? 0 }} kegiatan</span>
            <div class="flex gap-1">
              <template v-for="(link, i) in kegiatans?.links" :key="i">
                <button
                  v-if="link.url"
                  @click="router.visit(link.url)"
                  class="w-7 h-7 rounded-lg text-xs"
                  :class="link.active ? 'bg-[#1B7F5A] text-white' : 'hover:bg-gray-100'"
                  v-html="link.label">
                </button>
              </template>
            </div>
          </div>
        </div>

        <!-- PANEL DETAIL KANAN -->
        <div class="col-span-1 space-y-4">

          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 text-base mb-1">
              📅 {{ selectedItem ? formatTanggal(selectedItem.tanggal) : '-' }}
            </h3>
            <div class="flex gap-4 mt-3">
              <div class="text-center">
                <p class="text-2xl font-bold text-[#1B7F5A]">{{ selectedKepatuhan }}%</p>
                <p class="text-xs text-gray-400">Kepatuhan</p>
              </div>
              <div class="text-center">
                <p class="text-2xl font-bold text-gray-800">{{ selectedKebiasaan.filter(Boolean).length }}/7</p>
                <p class="text-xs text-gray-400">Kebiasaan</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">Detail Kebiasaan</h4>
            <div class="space-y-2">
              <div v-for="(k, i) in namaKebiasaan" :key="i" class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="text-sm">{{ k.icon }}</span>
                  <span class="text-sm text-gray-700">{{ k.nama }}</span>
                </div>
                <span v-if="selectedKebiasaan[i]" class="text-green-500 font-bold text-sm">✓</span>
                <span v-else class="text-gray-300 text-sm">—</span>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">Catatan Evaluasi Guru</h4>

            <div v-if="!selectedItem?.nilai_guru" class="text-xs text-gray-400 text-center py-2">
              Belum ada evaluasi
            </div>
            <div v-else>
              <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-full bg-[#1B7F5A] flex items-center justify-center text-white text-xs font-bold">
                  G
                </div>
                <div>
                  <p class="text-xs font-bold text-[#1B7F5A]">Wali Kelas</p>
                  <p class="text-xs text-gray-400">{{ formatTanggal(selectedItem.tanggal) }}</p>
                </div>
              </div>
              <p v-if="selectedItem.catatan_guru" class="text-xs text-gray-600 italic leading-relaxed">
                "{{ selectedItem.catatan_guru }}"
              </p>
              <div class="mt-3">
                <span class="inline-flex items-center gap-1 bg-[#1B7F5A] text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                  Nilai: {{ selectedItem.nilai_guru }} — {{ nilaiLabel[selectedItem.nilai_guru] }}
                </span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </SiswaLayout>
</template>