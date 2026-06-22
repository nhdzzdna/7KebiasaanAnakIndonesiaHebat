<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    kegiatanHariIni: { type: Object, default: null },
})

const fotoSelfie = ref(null)
const previewUrl = ref(null)

const form = useForm({
    selfie_validasi: null,
    status: 'submitted',
})

function pilihFoto(e) {
    const file = e.target.files[0]

    if (!file) return

    fotoSelfie.value = file
    form.selfie_validasi = file
    previewUrl.value = URL.createObjectURL(file)
}

function ulangiFoto() {
    fotoSelfie.value = null
    form.selfie_validasi = null
    previewUrl.value = null
}

function kirimKegiatan() {
    form.status = 'submitted'
    form.post('/siswa/kegiatan', {
        forceFormData: true,
        onSuccess: () => {
            window.location.href = '/siswa/kegiatan/success'
        },
    })
}

const tanggalFormatted = props.kegiatanHariIni
    ? new Date(props.kegiatanHariIni.tanggal).toLocaleDateString('id-ID', {
          weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
      })
    : ''
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <!-- ===== HEADER ===== -->
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Catat Kegiatan Harian</h1>
        <p class="text-gray-400 text-sm mt-0.5">{{ tanggalFormatted }}</p>
      </div>

      <!-- ===== STEP INDICATOR ===== -->
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">✓</div>
          <span class="text-sm font-semibold text-[#1B7F5A]">Isi Kegiatan</span>
        </div>
        <div class="flex-1 h-px bg-[#1B7F5A]"></div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-[#1B7F5A] text-white flex items-center justify-center text-sm font-bold">2</div>
          <span class="text-sm font-semibold text-[#1B7F5A]">Selfie Validasi</span>
        </div>
      </div>

      <!-- ===== KARTU SELFIE ===== -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 text-sm mb-1">🤳 Selfie Validasi (Wajib)</h3>
        <p class="text-xs text-gray-400 mb-5">
          Foto selfie bersama orang tua sebagai validasi bahwa kegiatan benar-benar dilakukan. Ini wajib dilakukan.
        </p>

        <!-- STATE: BELUM AMBIL FOTO -->
        <div v-if="!fotoSelfie" class="flex flex-col items-center justify-center gap-3 py-10 border-2 border-dashed border-gray-200 rounded-2xl">
          <div class="text-5xl">📷</div>
          <label class="bg-[#1B7F5A] text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-[#155f44] transition cursor-pointer">
            Buka Kamera
            <input type="file" accept="image/*" capture="user" class="hidden" @change="pilihFoto" />
          </label>
          <p class="text-xs text-gray-400">Selfie bersama Ayah/Ibu/Wali</p>
        </div>

        <!-- STATE: SUDAH AMBIL FOTO -->
        <div v-else class="flex flex-col items-center justify-center gap-3 py-6 border-2 border-[#1B7F5A] rounded-2xl bg-green-50">
          <img :src="previewUrl" alt="Preview selfie" class="w-32 h-32 object-cover rounded-xl" />
          <p class="text-sm font-semibold text-[#1B7F5A]">✅ Selfie berhasil diambil</p>
          <button @click="ulangiFoto" class="text-xs text-gray-500 underline hover:text-gray-700">
            Klik untuk ulangi lagi
          </button>
        </div>

        <p v-if="form.errors.selfie_validasi" class="text-xs text-red-500 mt-3 text-center">
          {{ form.errors.selfie_validasi }}
        </p>

        <!-- CHECKLIST PANDUAN -->
        <div class="flex items-center justify-center gap-4 mt-5 text-xs text-gray-500">
          <span>✅ Wajah terlihat jelas</span>
          <span>✅ Bersama orang tua/wali</span>
          <span>✅ Pencahayaan cukup</span>
        </div>
      </div>

      <!-- ===== TOMBOL AKSI BAWAH ===== -->
      <div class="flex justify-between items-center pb-6">
        <Link
          href="/siswa/kegiatan"
          class="flex items-center gap-2 border border-gray-200 text-gray-600 text-sm px-5 py-2.5 rounded-xl hover:bg-gray-50 transition">
          ← Kembali
        </Link>

        <button
          @click="kirimKegiatan"
          :disabled="!fotoSelfie || form.processing"
          class="flex items-center gap-2 bg-[#1B7F5A] text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-[#155f44] transition disabled:opacity-40 disabled:cursor-not-allowed">
          ✅ Kirim Kegiatan
        </button>
      </div>

    </div>
  </SiswaLayout>
</template>