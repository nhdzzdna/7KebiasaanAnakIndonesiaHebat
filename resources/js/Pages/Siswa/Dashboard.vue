<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- HEADER -->
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Dashboard Siswa</h1>
          <p class="text-gray-400 text-sm mt-0.5">Pantau kebiasaanmu hari ini, Selasa, 21 Januari 2025</p>
        </div>
        <button 
          @click="$inertia.visit('/siswa/kegiatan')"
          class="flex items-center gap-2 bg-[#1B7F5A] text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-[#155f44] transition">
          <span>📋</span> Catat Kegiatan Hari Ini
        </button>
      </div>

      <!-- ALERT PROFIL BELUM LENGKAP -->
      <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <div class="flex items-start gap-3">
          <div class="bg-amber-400 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm flex-shrink-0">✏️</div>
          <div class="flex-1">
            <h3 class="font-bold text-amber-700 text-sm">Profil Kamu Belum Lengkap!</h3>
            <p class="text-xs text-amber-600 mt-0.5">Lengkapi data personalmu agar guru bisa mengenalmu dengan baik</p>
            <div class="mt-3 flex items-center gap-3">
              <div class="flex-1 bg-amber-200 rounded-full h-2">
                <div class="bg-amber-500 h-2 rounded-full" style="width: 60%"></div>
              </div>
              <span class="text-xs text-amber-600 whitespace-nowrap">60% lengkap — masih ada beberapa data yang belum diisi</span>
            </div>
          </div>
          <button class="bg-amber-500 text-white text-xs font-semibold px-4 py-2 rounded-xl whitespace-nowrap hover:bg-amber-600 transition">
            Lengkapi Sekarang →
          </button>
        </div>
      </div>

      <!-- STAT CARDS (4 kolom) -->
      <div class="grid grid-cols-4 gap-4">

        <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
          <div class="text-3xl">🔥</div>
          <div>
            <p class="text-2xl font-bold text-gray-800">7</p>
            <p class="text-xs text-gray-500 font-medium">Streak Hari Ini</p>
            <p class="text-xs text-gray-400">Terus pertahankan!</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
          <div class="text-3xl">📊</div>
          <div>
            <p class="text-2xl font-bold text-gray-800">87%</p>
            <p class="text-xs text-gray-500 font-medium">Kepatuhan Bulan Ini</p>
            <p class="text-xs text-gray-400">↑ dari 82%</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
          <div class="text-3xl">✅</div>
          <div>
            <p class="text-2xl font-bold text-gray-800">5/7</p>
            <p class="text-xs text-gray-500 font-medium">Kebiasaan Hari Ini</p>
            <p class="text-xs text-gray-400">2 belum dicatat</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
          <div class="text-3xl">🏆</div>
          <div>
            <p class="text-2xl font-bold text-gray-800">28</p>
            <p class="text-xs text-gray-500 font-medium">Total Hari Tercatat</p>
            <p class="text-xs text-gray-400">Bulan ini</p>
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
              <h3 class="font-bold text-gray-800">Kebiasaan Hari Ini — Selasa, 21 Januari 2025</h3>
            </div>
            <button 
              @click="$inertia.visit('/siswa/kegiatan')"
              class="flex items-center gap-1 bg-[#1B7F5A] text-white text-xs font-semibold px-3 py-2 rounded-xl hover:bg-[#155f44] transition">
              + Catat Sekarang
            </button>
          </div>

          <div class="space-y-3">

            <!-- Item sudah dicatat -->
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div class="flex items-center gap-3">
                <span class="text-xl">🌅</span>
                <div>
                  <p class="text-sm font-semibold text-gray-800">1. Bangun Pagi</p>
                  <p class="text-xs text-gray-400">Bangun 04.45 ✓</p>
                </div>
              </div>
              <div class="w-6 h-6 rounded-md border-2 border-[#1B7F5A] bg-[#1B7F5A] flex items-center justify-center text-white text-xs">✓</div>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">C</div>
                <div>
                  <p class="text-sm font-semibold text-gray-800">2. Ibadah & Doa</p>
                  <p class="text-xs text-gray-400">Sholat 5 Waktu ✓</p>
                </div>
              </div>
              <div class="w-6 h-6 rounded-md border-2 border-[#1B7F5A] bg-[#1B7F5A] flex items-center justify-center text-white text-xs">✓</div>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div class="flex items-center gap-3">
                <span class="text-xl">🥗</span>
                <div>
                  <p class="text-sm font-semibold text-gray-800">3. Makan Sehat & Bergizi</p>
                  <p class="text-xs text-gray-400">nasi, sayur bayam, ayam, buah apel ✓</p>
                </div>
              </div>
              <div class="w-6 h-6 rounded-md border-2 border-[#1B7F5A] bg-[#1B7F5A] flex items-center justify-center text-white text-xs">✓</div>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div class="flex items-center gap-3">
                <span class="text-xl">🏃</span>
                <div>
                  <p class="text-sm font-semibold text-gray-800">4. Olahraga</p>
                  <p class="text-xs text-gray-400">lari pagi 30 menit ✓</p>
                </div>
              </div>
              <div class="w-6 h-6 rounded-md border-2 border-[#1B7F5A] bg-[#1B7F5A] flex items-center justify-center text-white text-xs">✓</div>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div class="flex items-center gap-3">
                <span class="text-xl">📚</span>
                <div>
                  <p class="text-sm font-semibold text-gray-800">5. Belajar Mandiri</p>
                  <p class="text-xs text-gray-400">matematika — 60 menit ✓</p>
                </div>
              </div>
              <div class="w-6 h-6 rounded-md border-2 border-[#1B7F5A] bg-[#1B7F5A] flex items-center justify-center text-white text-xs">✓</div>
            </div>

            <!-- Item belum dicatat -->
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
              <div class="flex items-center gap-3">
                <span class="text-xl">🤝</span>
                <div>
                  <p class="text-sm font-semibold text-gray-800">6. Aktivitas Sosial</p>
                  <p class="text-xs text-gray-400">Belum dicatat</p>
                </div>
              </div>
              <div class="w-6 h-6 rounded-md border-2 border-gray-300 flex items-center justify-center"></div>
            </div>

            <div class="flex items-center justify-between py-3">
              <div class="flex items-center gap-3">
                <span class="text-xl">😴</span>
                <div>
                  <p class="text-sm font-semibold text-gray-800">7. Tidur Cepat</p>
                  <p class="text-xs text-gray-400">Belum dicatat</p>
                </div>
              </div>
              <div class="w-6 h-6 rounded-md border-2 border-gray-300 flex items-center justify-center"></div>
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

            <div class="border border-gray-100 rounded-xl p-4">
              <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-full bg-[#1B7F5A] flex items-center justify-center text-white text-xs font-bold">SA</div>
                <div>
                  <p class="text-xs font-bold text-[#1B7F5A]">Sari Rahayu, S.Pd</p>
                  <p class="text-xs text-gray-400">20 Jan 2025</p>
                </div>
              </div>
              <p class="text-xs text-gray-600 italic leading-relaxed">
                "Budi menunjukkan perkembangan yang bagus! Pertahankan kebiasaan belajar mandirinya ya."
              </p>
              <div class="mt-3">
                <span class="inline-flex items-center gap-1 bg-[#1B7F5A] text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                  ↑ Nilai: B — Baik
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

              <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 w-6">Sen</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3">
                  <div class="bg-[#1B7F5A] h-3 rounded-full" style="width: 86%"></div>
                </div>
                <span class="text-xs text-gray-500 w-8 text-right">86%</span>
              </div>

              <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 w-6">Sel</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3">
                  <div class="bg-orange-400 h-3 rounded-full" style="width: 71%"></div>
                </div>
                <span class="text-xs text-gray-500 w-8 text-right">71%</span>
              </div>

              <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 w-6">Rab</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3"></div>
                <span class="text-xs text-gray-400 w-8 text-right">—</span>
              </div>

            </div>
          </div>

        </div>

      </div>

    </div>
  </SiswaLayout>
</template>