<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- ===== HEADER ===== -->
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Catat Kegiatan Harian</h1>
        <p class="text-gray-400 text-sm mt-0.5">Selasa, 21 Januari 2025</p>
      </div>

      <!-- ===== STEP INDICATOR ===== -->
      <!-- Kedua step sudah selesai: keduanya centang hijau -->
      <div class="flex items-center gap-3">

        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">
            ✓
          </div>
          <span class="text-sm text-[#1B7F5A] font-semibold">Isi Kegiatan</span>
        </div>

        <div class="flex-1 h-px bg-[#1B7F5A]"></div>

        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">
            ✓
          </div>
          <span class="text-sm text-[#1B7F5A] font-semibold">Selfie Validasi</span>
        </div>

      </div>

      <!-- ===== KONTEN SUKSES ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">

        <!-- Ikon centang besar sebagai konfirmasi berhasil -->
        <div class="w-16 h-16 mx-auto rounded-full bg-green-100 flex items-center justify-center text-3xl">
          ✅
        </div>

        <!-- Judul konfirmasi -->
        <h2 class="text-xl font-bold text-gray-800 mt-4">Kegiatan Hari Ini Sudah Terkirim!</h2>

        <!-- Info tanggal dan waktu kirim -->
        <p class="text-sm text-gray-400 mt-1">
          Selasa, 21 Januari 2025 · Dikirim pukul 20.54
        </p>

        <!-- ===== RINGKASAN 7 KEBIASAAN ===== -->
        <!-- Sesuai mockup: list 7 kebiasaan dengan status centang per item -->
        <div class="mt-6 text-left space-y-2 max-w-sm mx-auto">

          <!-- Setiap baris: ikon warna + nama kebiasaan + status centang -->
          <div class="flex items-center justify-between py-1.5">
            <div class="flex items-center gap-2">
              <span class="text-sm">🌅</span>
              <span class="text-sm text-gray-700">Bangun Pagi</span>
            </div>
            <!-- Centang hijau berarti sudah dicatat -->
            <span class="text-green-500 font-bold">✓</span>
          </div>

          <div class="flex items-center justify-between py-1.5">
            <div class="flex items-center gap-2">
              <span class="text-sm">🕌</span>
              <span class="text-sm text-gray-700">Ibadah & Doa</span>
            </div>
            <span class="text-green-500 font-bold">✓</span>
          </div>

          <div class="flex items-center justify-between py-1.5">
            <div class="flex items-center gap-2">
              <span class="text-sm">🥗</span>
              <span class="text-sm text-gray-700">Makan Sehat & Bergizi</span>
            </div>
            <span class="text-green-500 font-bold">✓</span>
          </div>

          <div class="flex items-center justify-between py-1.5">
            <div class="flex items-center gap-2">
              <span class="text-sm">🏃</span>
              <span class="text-sm text-gray-700">Olahraga</span>
            </div>
            <span class="text-green-500 font-bold">✓</span>
          </div>

          <div class="flex items-center justify-between py-1.5">
            <div class="flex items-center gap-2">
              <span class="text-sm">📚</span>
              <span class="text-sm text-gray-700">Belajar Mandiri</span>
            </div>
            <span class="text-green-500 font-bold">✓</span>
          </div>

          <!-- Aktivitas sosial tidak diisi: tanda strip -->
          <div class="flex items-center justify-between py-1.5">
            <div class="flex items-center gap-2">
              <span class="text-sm">🤝</span>
              <span class="text-sm text-gray-400">Aktivitas Sosial</span>
            </div>
            <!-- Tanda strip berarti tidak dicatat -->
            <span class="text-gray-300">—</span>
          </div>

          <div class="flex items-center justify-between py-1.5">
            <div class="flex items-center gap-2">
              <span class="text-sm">😴</span>
              <span class="text-sm text-gray-700">Tidur Cepat</span>
            </div>
            <span class="text-green-500 font-bold">✓</span>
          </div>

        </div>

        <!-- Keterangan tambahan di bawah ringkasan -->
        <p class="text-xs text-gray-400 mt-4">
          Kegiatan sudah terkirim. Sampai jumpa besok! 👋
        </p>

      </div>

      <!-- ===== TOMBOL NAVIGASI ===== -->
      <div class="flex justify-center pb-6">
        <!-- Tombol kembali ke dashboard -->
        <Link
          href="/siswa/dashboard"
          class="bg-[#1B7F5A] text-white text-sm font-semibold px-6 py-3 rounded-xl hover:bg-[#155f44] transition"
        >
          Kembali ke Dashboard
        </Link>
      </div>

    </div>
  </SiswaLayout>
</template>