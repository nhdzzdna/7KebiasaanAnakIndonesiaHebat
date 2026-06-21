<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
import { ref } from 'vue'

// Data dummy kegiatan (nanti diganti dari API)
const kegiatan = [
  {
    tanggal: '31 Jan 2025',
    // 7 kebiasaan direpresentasikan sebagai array true/false
    // urutan: bangun, ibadah, makan, olahraga, belajar, sosial, tidur
    kebiasaan: [true, true, true, true, true, true, true],
    selfie: false,       // apakah sudah selfie validasi
    status: 'Draft',     // Draft / Terkirim / Tidak Terkirim
    nilai: '-'           // nilai dari guru, '-' jika belum dievaluasi
  },
  {
    tanggal: '20 Jan 2025',
    kebiasaan: [true, true, true, true, true, true, true],
    selfie: true,
    status: 'Terkirim',
    nilai: 'B'
  },
  {
    tanggal: '19 Jan 2025',
    kebiasaan: [true, true, true, true, true, true, true],
    selfie: true,
    status: 'Terkirim',
    nilai: 'A'
  },
  {
    tanggal: '18 Jan 2025',
    kebiasaan: [true, true, true, true, true, true, true],
    selfie: true,
    status: 'Terkirim',
    nilai: 'A'
  },
  {
    tanggal: '17 Jan 2025',
    // beberapa false = kebiasaan tidak dilakukan
    kebiasaan: [false, false, false, false, false, false, false],
    selfie: false,
    status: 'Tidak Terkirim',
    nilai: '-'
  },
  {
    tanggal: '16 Jan 2025',
    kebiasaan: [true, true, true, false, false, true, false],
    selfie: true,
    status: 'Terkirim',
    nilai: 'C'
  },
]

// Warna titik per kebiasaan (sesuai mockup, warna berbeda tiap kebiasaan)
// Urutan: bangun, ibadah, makan, olahraga, belajar, sosial, tidur
const warnaTitik = [
  'bg-orange-400',   // bangun pagi
  'bg-purple-400',   // ibadah
  'bg-green-500',    // makan sehat
  'bg-blue-400',     // olahraga
  'bg-yellow-400',   // belajar mandiri
  'bg-pink-400',     // aktivitas sosial
  'bg-indigo-400',   // tidur cepat
]

// State untuk menyimpan baris yang sedang dipilih di panel detail kanan
const selectedItem = ref(kegiatan[1]) // default pilih baris kedua

// Fungsi untuk memilih baris dan menampilkan detailnya di panel kanan
function pilihItem(item) {
  selectedItem.value = item
}

// Fungsi untuk menentukan warna badge status
// Terkirim = hijau, Draft = abu, Tidak Terkirim = merah
function warnaBadgeStatus(status) {
  if (status === 'Terkirim') return 'bg-green-100 text-green-700'
  if (status === 'Draft') return 'bg-gray-100 text-gray-500'
  return 'bg-red-100 text-red-600'
}

// Label nama kebiasaan untuk ditampilkan di panel detail
const namaKebiasaan = [
  { icon: '🌅', nama: 'Bangun Pagi' },
  { icon: '🕌', nama: 'Ibadah & Doa' },
  { icon: '🥗', nama: 'Makan Sehat & Bergizi' },
  { icon: '🏃', nama: 'Olahraga' },
  { icon: '📚', nama: 'Belajar Mandiri' },
  { icon: '🤝', nama: 'Aktivitas Sosial' },
  { icon: '😴', nama: 'Tidur Cepat' },
]
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- ===== HEADER ===== -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Riwayat Kegiatan</h1>
          <p class="text-gray-400 text-sm mt-0.5">Semua kegiatan yang sudah kamu catat</p>
        </div>
        <!-- Tombol Catat Baru di kanan header -->
        <a
          href="/siswa/kegiatan"
          class="bg-[#1B7F5A] text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-[#155f44] transition"
        >
          + Catat Baru
        </a>
      </div>

      <!-- ===== LAYOUT 2 KOLOM: TABEL KIRI + DETAIL KANAN ===== -->
      <div class="grid grid-cols-3 gap-5 items-start">

        <!-- ===== KOLOM KIRI: TABEL RIWAYAT (2/3 lebar) ===== -->
        <div class="col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

          <!-- Filter bulan dan tahun -->
          <div class="p-4 border-b border-gray-100 flex items-center gap-3">

            <!-- Dropdown pilih bulan -->
            <select class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
              <option>Januari</option>
              <option>Februari</option>
              <option>Maret</option>
              <option>April</option>
              <option>Mei</option>
              <option>Juni</option>
              <option>Juli</option>
              <option>Agustus</option>
              <option>September</option>
              <option>Oktober</option>
              <option>November</option>
              <option>Desember</option>
            </select>

            <!-- Dropdown pilih tahun -->
            <select class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
              <option>2025</option>
              <option>2026</option>
            </select>

            <!-- Tombol filter -->
            <button class="border border-gray-200 text-gray-600 text-sm px-3 py-2 rounded-xl hover:bg-gray-50 transition">
              🔍 Filter
            </button>

          </div>

          <!-- ===== TABEL DATA ===== -->
          <div class="overflow-x-auto">
            <table class="w-full text-sm">

              <!-- Header tabel -->
              <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                  <th class="text-left px-4 py-3">Tanggal</th>
                  <!-- Header kebiasaan: 7 kolom titik warna -->
                  <th class="text-center px-2 py-3">Kebiasaan</th>
                  <th class="text-center px-3 py-3">Selfie</th>
                  <th class="text-center px-3 py-3">Status</th>
                  <th class="text-center px-3 py-3">Nilai Guru</th>
                  <th class="text-center px-3 py-3">Aksi</th>
                </tr>
              </thead>

              <tbody>
                <!-- Loop tiap baris kegiatan -->
                <tr
                  v-for="item in kegiatan"
                  :key="item.tanggal"
                  class="border-t border-gray-50 hover:bg-gray-50 cursor-pointer transition"
                  :class="selectedItem === item ? 'bg-green-50' : ''"
                  @click="pilihItem(item)"
                >

                  <!-- Kolom tanggal -->
                  <td class="px-4 py-3 font-medium text-gray-700">{{ item.tanggal }}</td>

                  <!-- Kolom titiktapi kebiasaan: loop 7 titik berwarna -->
                  <td class="px-2 py-3">
                    <div class="flex items-center justify-center gap-1">
                      <!-- Tiap titik: warna sesuai kebiasaan, opacity berkurang jika false -->
                      <div
                        v-for="(done, i) in item.kebiasaan"
                        :key="i"
                        class="w-2.5 h-2.5 rounded-full"
                        :class="done ? warnaTitik[i] : 'bg-gray-200'"
                      ></div>
                    </div>
                  </td>

                  <!-- Kolom selfie: centang atau strip -->
                  <td class="text-center py-3">
                    <span v-if="item.selfie" class="text-green-500 font-bold text-base">✓</span>
                    <span v-else class="text-gray-300">—</span>
                  </td>

                  <!-- Kolom status dengan badge warna berbeda -->
                  <td class="text-center py-3">
                    <span
                      class="px-2 py-1 rounded-full text-xs font-medium"
                      :class="warnaBadgeStatus(item.status)"
                    >
                      {{ item.status }}
                    </span>
                  </td>

                  <!-- Kolom nilai guru -->
                  <td class="text-center py-3 font-semibold text-[#1B7F5A]">
                    {{ item.nilai }}
                  </td>

                  <!-- Kolom tombol detail -->
                  <td class="text-center py-3 px-3">
                    <a
                      href="/siswa/riwayat/show"
                      class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg transition"
                      @click.stop
                    >
                      Detail
                    </a>
                  </td>

                </tr>
              </tbody>

            </table>
          </div>

          <!-- Pagination sederhana -->
          <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-400">
            <span>1–6 dari 31 siswa</span>
            <div class="flex gap-1">
              <button class="w-7 h-7 rounded-lg bg-[#1B7F5A] text-white text-xs">1</button>
              <button class="w-7 h-7 rounded-lg hover:bg-gray-100 text-xs">2</button>
              <button class="w-7 h-7 rounded-lg hover:bg-gray-100 text-xs">3</button>
              <span class="w-7 h-7 flex items-center justify-center text-xs">...</span>
            </div>
          </div>

        </div>

        <!-- ===== KOLOM KANAN: PANEL DETAIL (1/3 lebar) ===== -->
        <div class="col-span-1 space-y-4">

          <!-- Card info tanggal yang dipilih -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">

            <!-- Tanggal yang dipilih -->
            <h3 class="font-bold text-gray-800 text-base mb-1">📅 {{ selectedItem.tanggal }}</h3>

            <!-- Statistik ringkas: kepatuhan dan jumlah kebiasaan -->
            <div class="flex gap-4 mt-3">
              <div class="text-center">
                <!-- Hitung persentase: jumlah true dibagi 7 dikali 100 -->
                <p class="text-2xl font-bold text-[#1B7F5A]">
                  {{ Math.round(selectedItem.kebiasaan.filter(Boolean).length / 7 * 100) }}%
                </p>
                <p class="text-xs text-gray-400">Kepatuhan</p>
              </div>
              <div class="text-center">
                <!-- Jumlah kebiasaan yang dilakukan dari total 7 -->
                <p class="text-2xl font-bold text-gray-800">
                  {{ selectedItem.kebiasaan.filter(Boolean).length }}/7
                </p>
                <p class="text-xs text-gray-400">Kebiasaan</p>
              </div>
            </div>

          </div>

          <!-- Card detail per kebiasaan -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">

            <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">Detail Kebiasaan</h4>

            <div class="space-y-2">
              <!-- Loop 7 kebiasaan dengan ikon dan status centang/strip -->
              <div
                v-for="(done, i) in selectedItem.kebiasaan"
                :key="i"
                class="flex items-center justify-between"
              >
                <!-- Ikon dan nama kebiasaan -->
                <div class="flex items-center gap-2">
                  <span class="text-sm">{{ namaKebiasaan[i].icon }}</span>
                  <span class="text-sm text-gray-700">{{ namaKebiasaan[i].nama }}</span>
                </div>
                <!-- Status: centang hijau jika dilakukan, strip abu jika tidak -->
                <span v-if="done" class="text-green-500 font-bold text-sm">✓</span>
                <span v-else class="text-gray-300 text-sm">—</span>
              </div>
            </div>

          </div>

          <!-- Card catatan evaluasi guru -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">

            <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">Catatan Evaluasi Guru</h4>

            <!-- Avatar guru dan nama -->
            <div class="flex items-center gap-2 mb-3">
              <div class="w-8 h-8 rounded-full bg-[#1B7F5A] flex items-center justify-center text-white text-xs font-bold">
                SA
              </div>
              <div>
                <p class="text-xs font-bold text-[#1B7F5A]">Sari Rahayu, S.Pd</p>
                <p class="text-xs text-gray-400">{{ selectedItem.tanggal }}</p>
              </div>
            </div>

            <!-- Isi catatan guru -->
            <p class="text-xs text-gray-600 italic leading-relaxed">
              "Budi menunjukkan perkembangan yang bagus! Pertahankan kebiasaan belajar mandirinya ya."
            </p>

            <!-- Badge nilai predikat -->
            <div class="mt-3">
              <span class="inline-flex items-center gap-1 bg-[#1B7F5A] text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                ↑ Nilai: {{ selectedItem.nilai }} — Baik
              </span>
            </div>

          </div>

        </div>

      </div>

    </div>
  </SiswaLayout>
</template>