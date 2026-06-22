<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    kegiatanHariIni: { type: Object, default: null },
    religion: { type: String, default: null },
})

// MAPPING JENIS IBADAH PER AGAMA (UNTUK CHECKLIST DI BLOK IBADAH & DOA)
const checklistIbadahPerAgama = {
    'Islam': ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'],
    'Kristen': ['Doa Pagi', 'Doa Malam'],
    'Katolik': ['Doa Pagi', 'Doa Malam'],
    'Hindu': ['Doa Pagi', 'Doa Malam'],
    'Buddha': ['Doa Pagi', 'Doa Malam'],
    'Konghucu': ['Doa Pagi', 'Doa Malam'],
}

const placeholderIbadahLainnya = {
    'Islam': 'contoh: Mengaji, sholat sunnah...',
    'Kristen': 'contoh: Membaca Alkitab, renungan...',
    'Katolik': 'contoh: Membaca Alkitab, rosario...',
    'Hindu': 'contoh: Tri Sandhya, Puja Bakti, Sembahyang...',
    'Buddha': 'contoh: Meditasi, membaca paritta...',
    'Konghucu': 'contoh: Sembahyang, membaca kitab...',
}

const daftarChecklistIbadah = checklistIbadahPerAgama[props.religion] ?? []
const placeholderLainnya = placeholderIbadahLainnya[props.religion] ?? 'contoh: ibadah lainnya...'
const hariIniFormatted = new Date().toLocaleDateString('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
})

// CEK APAKAH FOTO BUKTI UNTUK FIELD INI SUDAH ADA DI DRAFT TERSIMPAN
function fotoSudahAda(field) {
    return !!props.kegiatanHariIni?.[field]
}

const form = useForm({
    waktu_bangun: props.kegiatanHariIni?.waktu_bangun ?? '',
    keterangan_bangun: props.kegiatanHariIni?.keterangan_bangun ?? '',
    detail_ibadah_centang: props.kegiatanHariIni?.detail_ibadah_centang ?? [],
    detail_ibadah_lain: props.kegiatanHariIni?.detail_ibadah_lain ?? '',
    menu_makan: props.kegiatanHariIni?.menu_makan ?? '',
    jumlah_air: props.kegiatanHariIni?.jumlah_air ?? '',
    jenis_olahraga: props.kegiatanHariIni?.jenis_olahraga ?? '',
    durasi_olahraga: props.kegiatanHariIni?.durasi_olahraga ?? '',
    belajar_mandiri: props.kegiatanHariIni?.belajar_mandiri ?? '',
    durasi_belajar: props.kegiatanHariIni?.durasi_belajar ?? '',
    aktivitas_sosial: props.kegiatanHariIni?.aktivitas_sosial ?? '',
    waktu_tidur: props.kegiatanHariIni?.waktu_tidur ?? '',
    keterangan_tidur: props.kegiatanHariIni?.keterangan_tidur ?? '',
    bukti_foto_bangun: null,
    bukti_foto_ibadah: null,
    bukti_foto_makan: null,
    bukti_foto_olahraga: null,
    bukti_foto_belajar: null,
    bukti_foto_sosial: null,
    bukti_foto: null,
    status: 'draft',
})

function simpanDraft() {
    form.status = 'draft'
    form.post('/siswa/kegiatan', { forceFormData: true })
}

function lanjutKeSelfie() {
    form.status = 'draft'
    form.post('/siswa/kegiatan', {
        forceFormData: true,
        onSuccess: () => { window.location.href = '/siswa/kegiatan/selfie' },
    })
}
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- ===== HEADER ===== -->
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Catat Kegiatan Harian</h1>
        <p class="text-gray-400 text-sm mt-0.5">{{ hariIniFormatted }}</p>
      </div>

      <!-- ===== STEP INDICATOR ===== -->
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">1</div>
          <span class="text-sm font-semibold text-[#1B7F5A]">Isi Kegiatan</span>
        </div>
        <div class="flex-1 h-px bg-gray-200"></div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-sm font-bold">2</div>
          <span class="text-sm text-gray-400">Selfie Validasi</span>
        </div>
      </div>

      <!-- ===== BLOK KEBIASAAN 1: BANGUN PAGI ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-3 bg-orange-50 border-b border-orange-100">
          <span class="text-lg">🌅</span>
          <span class="font-bold text-gray-800 text-sm">1 Bangun Pagi</span>
        </div>
        <div class="p-5 space-y-3">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Jam Bangun</label>
            <input
              type="time"
              v-model="form.waktu_bangun"
              class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-40 focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan (opsional)</label>
            <textarea
              v-model="form.keterangan_bangun"
              rows="3"
              placeholder="contoh: Bangun jam 04.45, langsung mandi dan siap-siap..."
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A] resize-none"
            ></textarea>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 flex items-center gap-2">
              📷 Foto Bukti (opsional)
              <span v-if="fotoSudahAda('bukti_foto_bangun') && !form.bukti_foto_bangun" class="text-[10px] font-semibold text-[#1B7F5A] bg-green-50 px-2 py-0.5 rounded-full">✓ Foto sudah ada</span>
            </label>
            <label class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600 border border-dashed border-gray-300 rounded-xl px-3 py-2.5">
              <span>📎</span>
              <span>{{ form.bukti_foto_bangun ? form.bukti_foto_bangun.name : (fotoSudahAda('bukti_foto_bangun') ? 'Ganti foto bukti bangun pagi' : 'Lampirkan foto bukti bangun pagi') }}</span>
              <input type="file" accept="image/*" class="hidden" @change="form.bukti_foto_bangun = $event.target.files[0]" />
            </label>
          </div>
        </div>
      </div>

      <!-- ===== BLOK KEBIASAAN 2: IBADAH & DOA ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-3 bg-purple-50 border-b border-purple-100">
          <span class="text-lg">🙏</span>
          <span class="font-bold text-gray-800 text-sm">2 Ibadah & Doa</span>
        </div>

        <!-- KONDISI: AGAMA BELUM DIISI -->
        <div v-if="!religion" class="p-6 text-center">
          <div class="text-3xl mb-2">🙏</div>
          <h4 class="font-bold text-gray-700 text-sm mb-1">Lengkapi Data Agama Dulu</h4>
          <p class="text-xs text-gray-400 mb-4">
            Untuk mengisi kegiatan Ibadah & Doa, kamu perlu mengisi agama di halaman Profil terlebih dahulu.
          </p>
          <Link href="/siswa/profile" class="text-[#1B7F5A] text-sm font-semibold hover:underline">
            Lengkapi Profil →
          </Link>
        </div>

        <!-- KONDISI: AGAMA SUDAH DIISI -->
        <div v-else class="p-5 space-y-3">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-2">
              {{ religion === 'Islam' ? 'Sholat Wajib' : 'Ibadah Wajib' }}
            </label>
            <div class="space-y-2">
              <label v-for="item in daftarChecklistIbadah" :key="item" class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" :value="item" v-model="form.detail_ibadah_centang" class="w-4 h-4 accent-[#1B7F5A]" />
                {{ item }}
              </label>
            </div>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Lainnya (opsional)</label>
            <textarea
              v-model="form.detail_ibadah_lain"
              rows="3"
              :placeholder="placeholderLainnya"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A] resize-none"
            ></textarea>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 flex items-center gap-2">
              📷 Foto Bukti (opsional)
              <span v-if="fotoSudahAda('bukti_foto_ibadah') && !form.bukti_foto_ibadah" class="text-[10px] font-semibold text-[#1B7F5A] bg-green-50 px-2 py-0.5 rounded-full">✓ Foto sudah ada</span>
            </label>
            <label class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600 border border-dashed border-gray-300 rounded-xl px-3 py-2.5">
              <span>📎</span>
              <span>{{ form.bukti_foto_ibadah ? form.bukti_foto_ibadah.name : (fotoSudahAda('bukti_foto_ibadah') ? 'Ganti foto bukti ibadah' : 'Lampirkan foto bukti ibadah') }}</span>
              <input type="file" accept="image/*" class="hidden" @change="form.bukti_foto_ibadah = $event.target.files[0]" />
            </label>
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
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Menu Makan Hari Ini</label>
            <input
              type="text"
              v-model="form.menu_makan"
              placeholder="Nasi, sayur, ayam, buah"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Air Putih (gelas)</label>
            <input
              type="number"
              v-model="form.jumlah_air"
              min="0"
              class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-24 focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1 flex items-center gap-2">
                📷 Foto Bukti (opsional)
                <span v-if="fotoSudahAda('bukti_foto_makan') && !form.bukti_foto_makan" class="text-[10px] font-semibold text-[#1B7F5A] bg-green-50 px-2 py-0.5 rounded-full">✓ Foto sudah ada</span>
              </label>
              <label class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600 border border-dashed border-gray-300 rounded-xl px-3 py-2.5">
                <span>📎</span>
                <span>{{ form.bukti_foto_makan ? form.bukti_foto_makan.name : (fotoSudahAda('bukti_foto_makan') ? 'Ganti foto makanan sehat & bergizi hari ini' : 'Lampirkan foto makanan sehat & bergizi hari ini') }}</span>
                <input type="file" accept="image/*" class="hidden" @change="form.bukti_foto_makan = $event.target.files[0]" />
              </label>
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
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Olahraga</label>
              <input
                type="text"
                v-model="form.jenis_olahraga"
                placeholder="Lari Pagi"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Durasi Olahraga (menit)</label>
              <input
                type="number"
                v-model="form.durasi_olahraga"
                min="0"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
              />
            </div>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 flex items-center gap-2">
              📷 Foto Bukti (opsional)
              <span v-if="fotoSudahAda('bukti_foto_olahraga') && !form.bukti_foto_olahraga" class="text-[10px] font-semibold text-[#1B7F5A] bg-green-50 px-2 py-0.5 rounded-full">✓ Foto sudah ada</span>
            </label>
            <label class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600 border border-dashed border-gray-300 rounded-xl px-3 py-2.5">
              <span>📎</span>
              <span>{{ form.bukti_foto_olahraga ? form.bukti_foto_olahraga.name : (fotoSudahAda('bukti_foto_olahraga') ? 'Ganti foto bukti olahraga' : 'Lampirkan foto bukti olahraga') }}</span>
              <input type="file" accept="image/*" class="hidden" @change="form.bukti_foto_olahraga = $event.target.files[0]" />
            </label>
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
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Materi/Pelajaran</label>
              <input
                type="text"
                v-model="form.belajar_mandiri"
                placeholder="Matematika"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Durasi Belajar (menit)</label>
              <input
                type="number"
                v-model="form.durasi_belajar"
                min="0"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A]"
              />
            </div>
          </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1 flex items-center gap-2">
                📷 Foto Bukti (opsional)
                <span v-if="fotoSudahAda('bukti_foto_belajar') && !form.bukti_foto_belajar" class="text-[10px] font-semibold text-[#1B7F5A] bg-green-50 px-2 py-0.5 rounded-full">✓ Foto sudah ada</span>
              </label>
              <label class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600 border border-dashed border-gray-300 rounded-xl px-3 py-2.5">
                <span>📎</span>
                <span>{{ form.bukti_foto_belajar ? form.bukti_foto_belajar.name : (fotoSudahAda('bukti_foto_belajar') ? 'Ganti suasana belajar / buku / tugas' : 'Lampirkan suasana belajar / buku / tugas') }}</span>
                <input type="file" accept="image/*" class="hidden" @change="form.bukti_foto_belajar = $event.target.files[0]" />
              </label>
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
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Kegiatan Sosial Hari Ini</label>
            <textarea
              v-model="form.aktivitas_sosial"
              rows="3"
              placeholder="Bantu ibu memasak, bermain dengan teman di halaman"
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A] resize-none"
            ></textarea>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 flex items-center gap-2">
              📷 Foto Bukti (opsional)
              <span v-if="fotoSudahAda('bukti_foto_sosial') && !form.bukti_foto_sosial" class="text-[10px] font-semibold text-[#1B7F5A] bg-green-50 px-2 py-0.5 rounded-full">✓ Foto sudah ada</span>
            </label>
            <label class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600 border border-dashed border-gray-300 rounded-xl px-3 py-2.5">
              <span>📎</span>
              <span>{{ form.bukti_foto_sosial ? form.bukti_foto_sosial.name : (fotoSudahAda('bukti_foto_sosial') ? 'Ganti foto aktivitas sosial' : 'Lampirkan foto aktivitas sosial') }}</span>
              <input type="file" accept="image/*" class="hidden" @change="form.bukti_foto_sosial = $event.target.files[0]" />
            </label>
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
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Jam Tidur Malam</label>
            <input
              type="time"
              v-model="form.waktu_tidur"
              class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-40 focus:outline-none focus:border-[#1B7F5A]"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan (opsional)</label>
            <textarea
              v-model="form.keterangan_tidur"
              rows="3"
              placeholder="contoh: Tidur jam 21.00, tidak main HP sebelum tidur..."
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A] resize-none"
            ></textarea>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1 flex items-center gap-2">
              📷 Foto Bukti (opsional)
              <span v-if="fotoSudahAda('bukti_foto') && !form.bukti_foto" class="text-[10px] font-semibold text-[#1B7F5A] bg-green-50 px-2 py-0.5 rounded-full">✓ Foto sudah ada</span>
            </label>
            <label class="flex items-center gap-2 text-gray-400 text-sm cursor-pointer hover:text-gray-600 border border-dashed border-gray-300 rounded-xl px-3 py-2.5">
              <span>📎</span>
              <span>{{ form.bukti_foto ? form.bukti_foto.name : (fotoSudahAda('bukti_foto') ? 'Ganti foto bukti tidur malam' : 'Lampirkan foto bukti tidur malam') }}</span>
              <input type="file" accept="image/*" class="hidden" @change="form.bukti_foto = $event.target.files[0]" />
            </label>
          </div>
        </div>
      </div>

      <!-- ===== TOMBOL AKSI BAWAH ===== -->
      <div class="flex justify-between items-center pb-6">
        <button
          @click="simpanDraft"
          :disabled="form.processing"
          class="flex items-center gap-2 border border-gray-200 text-gray-600 text-sm px-5 py-2.5 rounded-xl hover:bg-gray-50 transition disabled:opacity-50">
          💾 Simpan Draft
        </button>

        <button
          @click="lanjutKeSelfie"
          :disabled="form.processing"
          class="flex items-center gap-2 bg-[#1B7F5A] text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-[#155f44] transition disabled:opacity-50">
          Lanjut → Selfie Validasi
        </button>
      </div>

    </div>
  </SiswaLayout>
</template>