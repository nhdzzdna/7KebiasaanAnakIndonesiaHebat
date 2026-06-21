<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
import { ref } from 'vue'

// State untuk accordion: menyimpan index kebiasaan yang sedang dibuka
// null berarti semua tertutup
const bukaAccordion = ref(null)

// Fungsi toggle accordion: buka jika tertutup, tutup jika sudah terbuka
function toggleAccordion(index) {
  bukaAccordion.value = bukaAccordion.value === index ? null : index
}

// Data 7 kebiasaan dengan detail per kebiasaan
const kebiasaan = [
  {
    icon: '🌅',
    nama: 'Bangun Pagi',
    status: true,
    detail: 'Bangun jam 04.45, langsung mandi dan siap-siap sholat subuh.'
  },
  {
    icon: '🕌',
    nama: 'Ibadah & Doa',
    status: true,
    detail: 'Sholat 5 waktu lengkap, mengaji setelah maghrib.'
  },
  {
    icon: '🥗',
    nama: 'Makan Sehat & Bergizi',
    status: true,
    detail: 'Nasi, sayur bayam, ayam goreng, dan buah apel.'
  },
  {
    icon: '🏃',
    nama: 'Olahraga',
    status: true,
    detail: 'Lari pagi 30 menit di sekitar perumahan.'
  },
  {
    icon: '📚',
    nama: 'Belajar Mandiri',
    status: true,
    detail: 'Belajar matematika selama 60 menit, mengerjakan PR.'
  },
  {
    icon: '🤝',
    nama: 'Aktivitas Sosial',
    // false berarti tidak dilakukan hari ini
    status: false,
    detail: '-'
  },
  {
    icon: '😴',
    nama: 'Tidur Cepat',
    status: true,
    detail: 'Tidur jam 21.00, tidak main HP sebelum tidur.'
  },
]
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- ===== HEADER ===== -->
      <div class="flex items-center gap-3">
        <!-- Tombol kembali ke halaman riwayat -->
        <a
          href="/siswa/riwayat"
          class="text-gray-400 hover:text-gray-600 text-sm flex items-center gap-1"
        >
          ← Kembali
        </a>
        <span class="text-gray-300">/</span>
        <h1 class="text-xl font-bold text-gray-800">Detail Kegiatan & Evaluasi</h1>
      </div>

      <!-- Info sub-judul -->
      <p class="text-gray-400 text-sm">Kurnia Ardiningrum — Kelas 7A — 19 Januari 2025</p>

      <!-- ===== LAYOUT 3 KOLOM ===== -->
      <div class="grid grid-cols-3 gap-5 items-start">
        <!-- ===== KOLOM KIRI: GRAFIK + DETAIL KEBIASAAN ===== -->
        <div class="col-span-2 space-y-4">
          <!-- Card detail 7 kebiasaan dengan accordion -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
              <span>📋</span>
              <h4 class="text-sm font-bold text-gray-700">Detail 7 Kebiasaan — Hari Ini</h4>
            </div>

            <!-- Loop tiap kebiasaan sebagai accordion -->
            <div
              v-for="(k, i) in kebiasaan"
              :key="i"
              class="border-b border-gray-50 last:border-0">

              <!-- Baris header accordion: klik untuk buka/tutup detail -->
              <div
                class="flex items-center justify-between px-5 py-3 cursor-pointer hover:bg-gray-50 transition"
                @click="toggleAccordion(i)">

                <!-- Ikon dan nama kebiasaan -->
                <div class="flex items-center gap-3">
                  <span class="text-base">{{ k.icon }}</span>
                  <span class="text-sm font-medium text-gray-700">{{ k.nama }}</span>
                </div>

                <!-- Status dan ikon toggle di kanan -->
                <div class="flex items-center gap-3">
                  <!-- Badge status lengkap/tidak -->
                  <span
                    class="text-xs px-2 py-0.5 rounded-full"
                    :class="k.status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'">
                    {{ k.status ? '✓ Lengkap' : '— Tidak' }}
                  </span>
                  <!-- Tombol lihat foto (placeholder) -->
                  <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-500 px-2 py-1 rounded-lg transition">
                    📷 Lihat Foto
                  </button>
                  <!-- Ikon panah accordion: rotasi saat terbuka -->
                  <span
                    class="text-gray-400 text-xs transition-transform"
                    :class="bukaAccordion === i ? 'rotate-180' : ''"
                  >▼</span>
                </div>
              </div>

              <!-- Konten detail accordion: tampil jika index sama dengan yang dipilih -->
              <div
                v-if="bukaAccordion === i"
                class="px-5 pb-3 text-sm text-gray-500 bg-gray-50">
                {{ k.detail }}
              </div>
            </div>
          </div>
        </div>

        <!-- ===== KOLOM KANAN: SELFIE, NILAI DAN EVALUASI ===== -->
        <div class="col-span-1 space-y-4">
            <!-- SELFIE VALIDASI -->
            <div
                class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div
                    class="px-5 py-3 border-b border-gray-100">
                    <h4
                        class="font-bold text-sm text-gray-700">
                        📸 Selfie Validasi
                    </h4>
                </div>

                <div class="p-2 flex justify center">
                    <img
                        src="https://picsum.photos/400/500"
                        class="w-full h-36 rounded-xl object-cover border border-gray-200">
                </div>
            </div>

            <!-- EVALUASI GURU -->
            <div
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3"
            >
                <h4
                    class="font-bold text-sm text-gray-700 mb-3"
                >
                    👨‍🏫 Evaluasi Guru
                </h4>

                <!-- Profil Guru -->
                <div
                    class="flex items-center gap-2 mb-3"
                >
                    <div
                        class="w-8 h-8 rounded-full
                        bg-[#1B7F5A]
                        text-white
                        flex items-center justify-center
                        text-sm font-bold"
                    >
                        SR
                    </div>

                    <div>
                        <p
                            class="font-semibold text-sm text-[#1B7F5A]"
                        >
                            Sari Rahayu, S.Pd
                        </p>

                        <p
                            class="text-xs text-gray-400"
                        >
                            19 Januari 2025
                        </p>
                    </div>
                </div>

                <!-- Catatan -->
                <p
                    class="text-sm text-gray-600 italic leading-relaxed"
                >
                    "Budi menunjukkan perkembangan yang bagus.
                    Pertahankan kebiasaan belajar mandirinya ya."
                </p>

                <!-- Nilai -->
                <div class="mt-3">
                    <span
                        class="inline-flex items-center gap-2
                        bg-[#1B7F5A]
                        text-white
                        text-sm
                        px-3 py-2
                        rounded-xl
                        font-medium"
                    >
                        🏅 Nilai: B — Baik
                    </span>
                </div>
            </div>
        </div>
      </div>
    </div>
  </SiswaLayout>
</template>