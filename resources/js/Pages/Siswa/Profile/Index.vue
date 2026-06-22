<script setup>
import SiswaLayout from '@/Layouts/SiswaLayout.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  profile: Object,
})

const page = usePage()
const user = computed(() => page.props.auth?.user)

const form = useForm({
  religion: props.profile?.religion ?? '',
  hobby: props.profile?.hobby ?? '',
  weight: props.profile?.weight ?? '',
  blood_type: props.profile?.blood_type ?? '',
  favorite_food: props.profile?.favorite_food ?? '',
  favorite_subject: props.profile?.favorite_subject ?? '',
  favorite_sport: props.profile?.favorite_sport ?? '',
  strength: props.profile?.strength ?? '',
  weakness: props.profile?.weakness ?? '',
})

function simpan() {
  form.patch('/siswa/profile', { preserveScroll: true })
}

const correctionForm = useForm({ keterangan: '' })
const showCorrectionBox = ref(false)

function kirimLaporan() {
  correctionForm.post('/siswa/profile/report-correction', {
    preserveScroll: true,
    onSuccess: () => {
      correctionForm.reset()
      showCorrectionBox.value = false
    },
  })
}

const fotoForm = useForm({ foto: null })

function onFotoChange(e) {
  const file = e.target.files[0]
  if (!file) return
  fotoForm.foto = file
  fotoForm.post('/siswa/profile/foto', { preserveScroll: true, forceFormData: true })
}

function formatTanggal(tgl) {
  if (!tgl) return '-'
  return new Date(tgl).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const fotoUrl = computed(() =>
  user.value?.foto ? `/storage/${user.value.foto}` : 'https://i.pinimg.com/originals/22/02/f1/2202f1513fa534d5e3698ae8619d9474.jpg?nii=t'
)

const namaKelas = computed(() => props.profile?.school_class?.name ?? '-')
const namaWaliKelas = computed(() => props.profile?.school_class?.teacher?.name ?? '-')

const fieldStatus = computed(() => [
  { label: 'Hobi', filled: !!form.hobby },
  { label: 'Agama', filled: !!form.religion },
  { label: 'Berat Badan', filled: !!form.weight },
  { label: 'Mak. Favorit', filled: !!form.favorite_food },
  { label: 'Pel. Favorit', filled: !!form.favorite_subject },
  { label: 'Olah. Favorit', filled: !!form.favorite_sport },
  { label: 'Gol. Darah', filled: !!form.blood_type },
  { label: 'Hambatan & Kelebihan', filled: !!(form.strength && form.weakness) },
])
</script>

<template>
  <SiswaLayout>
    <div class="space-y-5">

      <div>
        <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
        <p class="text-gray-400 text-sm mt-0.5">Data akun dan informasi personal</p>
      </div>

      <div class="bg-gradient-to-r from-[#0F3D2E] to-[#1B7F5A] rounded-2xl p-6 text-white flex items-center justify-between">
        <div class="flex items-center gap-5">
          <div class="w-20 h-20 rounded-full bg-white/20 overflow-hidden flex-shrink-0">
            <img :src="fotoUrl" alt="Foto Profil" class="w-full h-full object-cover" />
          </div>
          <div>
            <h2 class="text-2xl font-bold">{{ user?.name }}</h2>
            <p class="text-green-200 text-sm mt-0.5">
              Kelas {{ namaKelas }} — Wali Kelas: {{ namaWaliKelas }}
            </p>
            <div class="mt-2">
              <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full">✓ Aktif</span>
            </div>
          </div>
        </div>
        <label class="flex items-center gap-1 bg-white/20 hover:bg-white/30 text-white text-sm px-4 py-2 rounded-xl transition cursor-pointer">
          ✏️ Edit Foto
          <input type="file" accept="image/*" class="hidden" @change="onFotoChange" />
        </label>
      </div>

      <div class="grid grid-cols-2 gap-5 items-start">

        <!-- KOLOM KIRI: DATA AKUN (read-only) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
              <span>👤</span>
              <h3 class="font-bold text-gray-800">Data Akun</h3>
            </div>
            <span class="bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1 rounded-lg">Diisi Admin</span>
          </div>

          <div class="space-y-4">
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Nama Lengkap</span>
              <span class="text-sm font-semibold text-gray-800">{{ user?.name }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Email</span>
              <span class="text-sm font-semibold text-gray-800">{{ user?.email }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Kelas</span>
              <span class="text-sm font-semibold text-gray-800">{{ namaKelas }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Tanggal Lahir</span>
              <span class="text-sm font-semibold text-gray-800">{{ formatTanggal(profile?.birth_date) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-400">Alamat</span>
              <span class="text-sm font-semibold text-gray-800 text-right">{{ profile?.address ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-center py-2">
              <span class="text-sm text-gray-400">Nama Orang Tua</span>
              <span class="text-sm font-semibold text-gray-800">{{ profile?.parent_name ?? '-' }}</span>
            </div>
          </div>

          <div v-if="!showCorrectionBox">
            <button
              @click="showCorrectionBox = true"
              class="mt-5 w-full border border-gray-200 text-gray-500 text-sm py-2.5 rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2">
              ✏️ Laporkan Kesalahan Data
            </button>
          </div>
          <div v-else class="mt-5">
            <textarea
              v-model="correctionForm.keterangan"
              rows="3"
              placeholder="Jelaskan data mana yang salah..."
              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#1B7F5A] resize-none">
            </textarea>
            <p v-if="correctionForm.errors.keterangan" class="text-red-500 text-xs mt-1">{{ correctionForm.errors.keterangan }}</p>
            <div class="flex gap-2 mt-2">
              <button
                @click="kirimLaporan"
                :disabled="correctionForm.processing"
                class="bg-[#1B7F5A] text-white text-xs font-semibold px-4 py-2 rounded-xl disabled:opacity-50">
                Kirim Laporan
              </button>
              <button @click="showCorrectionBox = false" class="text-xs text-gray-500 px-4 py-2">Batal</button>
            </div>
          </div>
        </div>

        <!-- KOLOM KANAN: DATA PERSONAL -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <span>✏️</span>
              <h3 class="font-bold text-gray-800">Data Personal (Diisi Sendiri)</h3>
            </div>
            <span class="bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-lg">Bisa diedit</span>
          </div>

          <div class="mb-5">
            <p class="text-xs text-gray-400 mb-2">Status kelengkapan field:</p>
            <div class="grid grid-cols-4 gap-2">
              <div
                v-for="f in fieldStatus" :key="f.label"
                class="flex items-center gap-1 rounded-lg px-2 py-1.5"
                :class="f.filled ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200'">
                <span v-if="f.filled" class="text-green-600 text-xs">✓</span>
                <span
                  class="text-xs font-medium"
                  :class="f.filled ? 'text-green-700' : 'text-gray-400'">
                  {{ f.label }}
                </span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Hobi</label>
                <input type="text" v-model="form.hobby"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Agama</label>
                <select v-model="form.religion" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]">
                  <option value="">-- Pilih --</option>
                  <option>Islam</option>
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
                <input type="number" v-model="form.weight"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Golongan Darah</label>
                <select v-model="form.blood_type" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]">
                  <option value="">-- Pilih --</option>
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
                <input type="text" v-model="form.favorite_food" placeholder="contoh: Nasi goreng"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Pelajaran Favorit</label>
                <input type="text" v-model="form.favorite_subject" placeholder="contoh: Matematika"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
              </div>
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Olahraga Favorit</label>
              <input type="text" v-model="form.favorite_sport" placeholder="contoh: Sepak bola"
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A]" />
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Kelebihanku</label>
              <textarea rows="2" v-model="form.strength" placeholder="Ceritakan kelebihan dirimu..."
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A] resize-none"></textarea>
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Hambatanku</label>
              <textarea rows="2" v-model="form.weakness" placeholder="Ceritakan hambatan yang kamu hadapi..."
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B7F5A] resize-none"></textarea>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-5">
            <button
              @click="form.reset()"
              class="px-4 py-2 text-sm text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
              Batal
            </button>
            <button
              @click="simpan"
              :disabled="form.processing"
              class="px-4 py-2 text-sm font-semibold bg-[#1B7F5A] text-white rounded-xl hover:bg-[#155f44] transition flex items-center gap-1 disabled:opacity-50">
              💾 Simpan Data Personal
            </button>
          </div>
        </div>

      </div>
    </div>
  </SiswaLayout>
</template>