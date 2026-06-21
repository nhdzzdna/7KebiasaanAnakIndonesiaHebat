<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
// Link digunakan untuk navigasi ke halaman selfie tanpa reload
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- ===== HEADER ===== -->
      <div>
        <!-- Judul halaman -->
        <h1 class="text-2xl font-bold text-gray-800">Catat Kegiatan Harian</h1>
        <!-- Sub-judul dengan tanggal -->
        <p class="text-gray-400 text-sm mt-0.5">Selasa, 21 Januari 2025</p>
      </div>

      <!-- ===== STEP INDICATOR ===== -->
      <!-- Menampilkan 2 langkah: Isi Kegiatan (aktif) dan Selfie Validasi -->
      <div class="flex items-center gap-3">

        <!-- Step 1 aktif (warna hijau) -->
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">
            1
          </div>
          <span class="text-sm font-semibold text-[#1B7F5A]">Isi Kegiatan</span>
        </div>

        <!-- Garis pemisah antar step -->
        <div class="flex-1 h-px bg-gray-200"></div>

        <!-- Step 2 belum aktif (warna abu) -->
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-sm font-bold">
            2
          </div>
          <span class="text-sm text-gray-400">Selfie Validasi</span>
        </div>

      </div>

      <!-- ===== BLOK KEBIASAAN 1: BANGUN PAGI ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <!-- Header blok dengan warna aksen -->
        <div class="flex items-center gap-3 px-5 py-3 bg-orange-50 border-b border-orange-100">
          <span class="text-lg">🌅</span>
          <!-- Nomor dan nama kebiasaan -->
          <span class="font-bold text-gray-800 text-sm">1 Bangun Pagi</span>
        </div>

        <!-- Isi form kebiasaan -->
        <div class="p-5 space-y-3">

          <!-- Input jam bangun menggunakan time picker -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">
              Jam Bangun
            </label>
            <input
              type="time"
              value="04:45"
              class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-40 focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>

          <!-- Input keterangan opsional -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">
              Keterangan (opsional)
            </label>
            <input
              type="text"
              placeholder="contoh: Bangun jam 04.45, langsung mandi dan siap-siap..."
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>

          <!-- Tombol upload foto bukti (opsional) -->
          <div class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600">
            <span>📎</span>
            <span>Lampirkan foto bukti bangun pagi</span>
          </div>

        </div>
      </div>

      <!-- ===== BLOK KEBIASAAN 2: IBADAH & DOA ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 py-3 bg-purple-50 border-b border-purple-100">
          <span class="text-lg">🕌</span>
          <span class="font-bold text-gray-800 text-sm">2 Ibadah & Doa</span>
        </div>

        <div class="p-5 space-y-3">

          <!-- Checkbox sholat wajib (5 waktu) -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-2">
              Sholat Wajib
            </label>
            <!-- Setiap checkbox mewakili satu waktu sholat -->
            <div class="space-y-2">
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" checked class="w-4 h-4 accent-[#1B7F5A]" />
                Subuh
              </label>
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" checked class="w-4 h-4 accent-[#1B7F5A]" />
                Dzuhur
              </label>
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" class="w-4 h-4 accent-[#1B7F5A]" />
                Ashar
              </label>
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" class="w-4 h-4 accent-[#1B7F5A]" />
                Maghrib
              </label>
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" class="w-4 h-4 accent-[#1B7F5A]" />
                Isya
              </label>
            </div>
          </div>

          <!-- Input ibadah tambahan selain sholat wajib -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">
              Lainnya (opsional)
            </label>
            <input
              type="text"
              placeholder="contoh: Mengaji, sholat sunnah..."
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>

          <div class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600">
            <span>📎</span>
            <span>Lampirkan foto bukti ibadah</span>
          </div>

        </div>
      </div>

      <!-- ===== BLOK KEBIASAAN 3: MAKAN SEHAT & BERGIZI ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 py-3 bg-green-50 border-b border-green-100">
          <span class="text-lg">🥗</span>
          <span class="font-bold text-gray-800 text-sm">3 Makan Sehat & Bergizi</span>
        </div>

        <div class="p-5 space-y-3">

          <!-- Input menu makan hari ini -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">
              Menu Makan Hari Ini
            </label>
            <input
              type="text"
              placeholder="Nasi, sayur, ayam, buah"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>

          <!-- Input jumlah air putih dalam gelas -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">
              Air Putih (gelas)
            </label>
            <input
              type="number"
              value="8"
              min="0"
              class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-24 focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>

          <div class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600">
            <span>📎</span>
            <span>Lampirkan foto makanan sehat & bergizi hari ini</span>
          </div>

        </div>
      </div>

      <!-- ===== BLOK KEBIASAAN 4: OLAHRAGA ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 py-3 bg-blue-50 border-b border-blue-100">
          <span class="text-lg">🏃</span>
          <span class="font-bold text-gray-800 text-sm">4 Olahraga</span>
        </div>

        <div class="p-5 space-y-3">

          <!-- 2 kolom: jenis olahraga dan durasi -->
          <div class="grid grid-cols-2 gap-4">

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">
                Jenis Olahraga
              </label>
              <input
                type="text"
                placeholder="Lari Pagi"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
              />
            </div>

            <!-- Durasi dalam menit -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">
                Durasi Olahraga (menit)
              </label>
              <input
                type="number"
                value="30"
                min="0"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
              />
            </div>

          </div>

          <div class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600">
            <span>📎</span>
            <span>Lampirkan foto bukti olahraga</span>
          </div>

        </div>
      </div>

      <!-- ===== BLOK KEBIASAAN 5: BELAJAR MANDIRI ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 py-3 bg-yellow-50 border-b border-yellow-100">
          <span class="text-lg">📚</span>
          <span class="font-bold text-gray-800 text-sm">5 Belajar Mandiri</span>
        </div>

        <div class="p-5 space-y-3">

          <!-- 2 kolom: materi dan durasi belajar -->
          <div class="grid grid-cols-2 gap-4">

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">
                Materi/Pelajaran
              </label>
              <input
                type="text"
                placeholder="Matematika"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
              />
            </div>

            <!-- Durasi belajar dalam menit -->
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">
                Durasi Belajar (menit)
              </label>
              <input
                type="number"
                value="60"
                min="0"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
              />
            </div>

          </div>

          <div class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600">
            <span>📎</span>
            <span>Lampirkan suasana belajar / buku / tugas</span>
          </div>

        </div>
      </div>

      <!-- ===== BLOK KEBIASAAN 6: AKTIVITAS SOSIAL ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 py-3 bg-pink-50 border-b border-pink-100">
          <span class="text-lg">🤝</span>
          <span class="font-bold text-gray-800 text-sm">6 Aktivitas Sosial</span>
        </div>

        <div class="p-5 space-y-3">

          <!-- Input deskripsi bebas aktivitas sosial hari ini -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">
              Kegiatan Sosial Hari Ini
            </label>
            <input
              type="text"
              placeholder="Bantu ibu memasak, bermain dengan teman di halaman"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>

          <div class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600">
            <span>📎</span>
            <span>Lampirkan foto aktivitas sosial</span>
          </div>

        </div>
      </div>

      <!-- ===== BLOK KEBIASAAN 7: TIDUR CEPAT ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-5 py-3 bg-indigo-50 border-b border-indigo-100">
          <span class="text-lg">😴</span>
          <span class="font-bold text-gray-800 text-sm">7 Tidur Cepat</span>
        </div>

        <div class="p-5 space-y-3">

          <!-- Input jam tidur malam menggunakan time picker -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">
              Jam Tidur Malam
            </label>
            <input
              type="time"
              value="21:00"
              class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-40 focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>

          <!-- Input keterangan opsional sebelum tidur -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">
              Keterangan (opsional)
            </label>
            <input
              type="text"
              placeholder="contoh: Tidur jam 21.00, tidak main HP sebelum tidur..."
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>

          <div class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600">
            <span>📎</span>
            <span>Lampirkan foto bukti tidur malam</span>
          </div>

        </div>
      </div>

      <!-- ===== TOMBOL AKSI BAWAH ===== -->
      <!-- Simpan Draft di kiri, Lanjut ke selfie di kanan -->
      <div class="flex justify-between items-center pb-6">

        <!-- Tombol simpan draft: menyimpan progress tanpa mengirim -->
        <button class="flex items-center gap-2 border border-gray-200 text-gray-600 text-sm px-5 py-2.5 rounded-xl hover:bg-gray-50 transition">
          💾 Simpan Draft
        </button>

        <!-- Tombol lanjut: navigasi ke halaman selfie validasi -->
        <Link
          href="/siswa/kegiatan/selfie"
          class="flex items-center gap-2 bg-[#1B7F5A] text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-[#155f44] transition"
        >
          Lanjut → Selfie Validasi
        </Link>

      </div>

    </div>
  </SiswaLayout>
</template>