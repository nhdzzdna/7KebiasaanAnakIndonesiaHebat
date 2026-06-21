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
      <!-- Step 1 sudah selesai (centang), Step 2 aktif sekarang -->
      <div class="flex items-center gap-3">

        <!-- Step 1 sudah selesai: ikon centang hijau -->
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">
            ✓
          </div>
          <span class="text-sm text-[#1B7F5A] font-semibold">Isi Kegiatan</span>
        </div>

        <!-- Garis pemisah -->
        <div class="flex-1 h-px bg-[#1B7F5A]"></div>

        <!-- Step 2 sekarang aktif -->
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">
            2
          </div>
          <span class="text-sm font-semibold text-[#1B7F5A]">Selfie Validasi</span>
        </div>

      </div>

      <!-- ===== JUDUL SECTION SELFIE ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <div class="flex items-center gap-2 mb-1">
          <span>📸</span>
          <!-- Tanda wajib berarti tidak bisa dilewati -->
          <h3 class="font-bold text-gray-800">Selfie Validasi (Wajib)</h3>
        </div>
        <p class="text-sm text-gray-400 mb-5">
          Foto selfie bersama orang tua sebagai validasi bahwa kegiatan benar-benar dilakukan. Ini wajib dilakukan.
        </p>

        <!-- ===== AREA KAMERA ===== -->
        <!-- Latar hitam meniru tampilan preview kamera perangkat -->
        <div class="bg-black rounded-2xl aspect-video flex flex-col items-center justify-center gap-3">
          <!-- Ikon kamera berwarna putih di tengah area hitam -->
          <div class="text-white text-5xl">📷</div>
          <p class="text-white font-semibold">Buka Kamera</p>
          <!-- Sub-teks panduan -->
          <p class="text-gray-400 text-sm">Selfie bersama Ayah/Ibu/Wali</p>
        </div>

        <!-- ===== 3 SYARAT FOTO ===== -->
        <!-- Sesuai mockup: wajah jelas, bersama ortu, pencahayaan cukup -->
        <div class="flex items-center gap-6 mt-4">
          <div class="flex items-center gap-1 text-sm text-gray-600">
            <span class="text-green-500 font-bold">✓</span> Wajah terlihat jelas
          </div>
          <div class="flex items-center gap-1 text-sm text-gray-600">
            <span class="text-green-500 font-bold">✓</span> Bersama orang tua/wali
          </div>
          <div class="flex items-center gap-1 text-sm text-gray-600">
            <span class="text-green-500 font-bold">✓</span> Pencahayaan cukup
          </div>
        </div>

      </div>

      <!-- ===== TOMBOL AKSI BAWAH ===== -->
      <div class="flex justify-between items-center pb-6">

        <!-- Tombol kembali ke halaman isi kegiatan -->
        <Link
          href="/siswa/kegiatan"
          class="border border-gray-200 text-gray-600 text-sm px-5 py-2.5 rounded-xl hover:bg-gray-50 transition"
        >
          ← Kembali
        </Link>

        <!-- Tombol kirim kegiatan: navigasi ke halaman sukses -->
        <Link
          href="/siswa/kegiatan/succes"
          class="flex items-center gap-2 bg-[#1B7F5A] text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-[#155f44] transition"
        >
          ✓ Kirim Kegiatan
        </Link>

      </div>

    </div>
  </SiswaLayout>
</template>