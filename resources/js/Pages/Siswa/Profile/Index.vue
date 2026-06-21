<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- HEADER -->
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
        <p class="text-gray-400 text-sm mt-0.5">Data akun dan informasi personal</p>
      </div>

      <!-- BANNER -->
      <div class="bg-gradient-to-r from-[#0F3D2E] to-[#1B7F5A] rounded-2xl p-6 text-white flex items-center justify-between">
        <div class="flex items-center gap-5">

          <!-- Foto profil -->
          <div class="w-20 h-20 rounded-full bg-white/20 overflow-hidden flex-shrink-0">
            <img
              src="https://i.pravatar.cc/80"
              alt="Foto Profil"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Info nama -->
          <div>
            <h2 class="text-2xl font-bold">Budi Santoso</h2>
            <p class="text-green-200 text-sm mt-0.5">
              Kelas 7A — Wali Kelas: Sari Rahayu, S.Pd
            </p>
            <div class="mt-2">
              <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full">
                ✓ Aktif
              </span>
            </div>
          </div>

        </div>

        <!-- Tombol edit foto -->
        <button class="flex items-center gap-1 bg-white/20 hover:bg-white/30 text-white text-sm px-4 py-2 rounded-xl transition">
          ✏️ Edit Foto
        </button>
      </div>

      <!-- KONTEN 2 KOLOM -->
      <div class="grid grid-cols-2 gap-5 items-start">

        <!-- KOLOM KIRI: DATA AKUN (read-only) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

          <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
              <span>👤</span>
              <h3 class="font-bold text-gray-800">Data Akun</h3>
            </div>
            <span class="bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1 rounded-lg">
              Diisi Admin
            </span>
          </div>

          <!-- Baris data -->
          <div class="space-y-4">

            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Nama Lengkap</span>
              <span class="text-sm font-semibold text-gray-800">Budi Santoso</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Email</span>
              <span class="text-sm font-semibold text-gray-800">budisantoso@student.ac.id</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Kelas</span>
              <span class="text-sm font-semibold text-gray-800">7A</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Tanggal Lahir</span>
              <span class="text-sm font-semibold text-gray-800">10 Maret 2013</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Alamat</span>
              <span class="text-sm font-semibold text-gray-800 text-right">Jl. Mawar No. 5, Surakarta</span>
            </div>

            <div class="flex justify-between items-center py-2">
              <span class="text-sm text-gray-400">Nama Orang Tua</span>
              <span class="text-sm font-semibold text-gray-800">Bapak Santoso</span>
            </div>

          </div>

          <!-- Laporkan Kesalahan -->
          <button class="mt-5 w-full border border-gray-200 text-gray-500 text-sm py-2.5 rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2">
            ✏️ Laporkan Kesalahan Data
          </button>

        </div>

        <!-- KOLOM KANAN: DATA PERSONAL (bisa diedit siswa) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <span>✏️</span>
              <h3 class="font-bold text-gray-800">Data Personal (Diisi Sendiri)</h3>
            </div>
            <span class="bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-lg">
              Bisa diedit
            </span>
          </div>

          <!-- Status kelengkapan field -->
          <div class="mb-5">
            <p class="text-xs text-gray-400 mb-2">Status kelengkapan field:</p>
            <div class="grid grid-cols-4 gap-2">
              <div class="flex items-center gap-1 bg-green-50 border border-green-200 rounded-lg px-2 py-1.5">
                <span class="text-green-600 text-xs">✓</span>
                <span class="text-xs text-green-700 font-medium">Hobi</span>
              </div>
              <div class="flex items-center gap-1 bg-green-50 border border-green-200 rounded-lg px-2 py-1.5">
                <span class="text-green-600 text-xs">✓</span>
                <span class="text-xs text-green-700 font-medium">Agama</span>
              </div>
              <div class="flex items-center gap-1 bg-green-50 border border-green-200 rounded-lg px-2 py-1.5">
                <span class="text-green-600 text-xs">✓</span>
                <span class="text-xs text-green-700 font-medium">Berat Badan</span>
              </div>
              <div class="flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5">
                <span class="text-xs text-gray-400 font-medium">Mak. Favorit</span>
              </div>
              <div class="flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5">
                <span class="text-xs text-gray-400 font-medium">Pel. Favorit</span>
              </div>
              <div class="flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5">
                <span class="text-xs text-gray-400 font-medium">Olah. Favorit</span>
              </div>
              <div class="flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5">
                <span class="text-xs text-gray-400 font-medium">Gol. Darah</span>
              </div>
              <div class="flex items-center gap-1 bg-green-50 border border-green-200 rounded-lg px-2 py-1.5">
                <span class="text-green-600 text-xs">✓</span>
                <span class="text-xs text-green-700 font-medium">Hambatan & Kelebihan</span>
              </div>
            </div>
          </div>

          <!-- Form input -->
          <div class="space-y-4">

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Hobi</label>
                <input type="text" value="Sepak bola, membaca"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Agama</label>
                <select class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]">
                  <option selected>Islam</option>
                  <option>Kristen</option>
                  <option>Katolik</option>
                  <option>Hindu</option>
                  <option>Buddha</option>
                  <option>Konghucu</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Berat Badan (kg)</label>
                <input type="number" value="38"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Golongan Darah</label>
                <select class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]">
                  <option>O</option>
                  <option>A</option>
                  <option>B</option>
                  <option>AB</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Makanan Favorit</label>
                <input type="text" placeholder="contoh: Nasi goreng"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Pelajaran Favorit</label>
                <input type="text" placeholder="contoh: Matematika"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Olahraga Favorit</label>
              <input type="text" placeholder="contoh: Sepak bola"
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Kelebihanku</label>
              <textarea rows="2" placeholder="Ceritakan kelebihan dirimu..."
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A] resize-none"></textarea>
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Hambatanku</label>
              <textarea rows="2" placeholder="Ceritakan hambatan yang kamu hadapi..."
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A] resize-none"></textarea>
            </div>

          </div>

          <!-- Tombol aksi -->
          <div class="flex justify-end gap-3 mt-5">
            <button class="px-4 py-2 text-sm text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
              Batal
            </button>
            <button class="px-4 py-2 text-sm font-semibold bg-[#1B7F5A] text-white rounded-xl hover:bg-[#155f44] transition flex items-center gap-1">
              💾 Simpan Data Personal
            </button>
          </div>

        </div>

      </div>

    </div>
  </SiswaLayout>
</template>