<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import GuruLayout from '@/Layouts/GuruLayout.vue'

// ====================================================================
// PROPS — sesuai dokumentasi API Nahdia untuk GET /guru/profile.
// "user" datang dengan relasi teacherProfile.schoolClass sudah di-load.
// ====================================================================
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const isEditingAccount = ref(false)

const accountForm = useForm({
    name: props.user.name,
    email: props.user.email,
})

function submitAccount() {
    accountForm.patch(route('guru.profile.update'), {
        preserveScroll: true,
        onSuccess: () => { isEditingAccount.value = false },
    })
}

function cancelEditAccount() {
    accountForm.reset()
    isEditingAccount.value = false
}

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

function submitPassword() {
    passwordForm.patch(route('guru.profile.password'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => passwordForm.reset('current_password'),
    })
}

const fotoForm = useForm({ foto: null })
const fotoInput = ref(null)

function triggerFotoInput() {
    fotoInput.value.click()
}

function handleFotoChange(event) {
    const file = event.target.files[0]
    if (!file) return
    fotoForm.foto = file
    fotoForm.post(route('guru.profile.foto'), {
        preserveScroll: true,
        onSuccess: () => fotoForm.reset(),
    })
}
</script>

<template>

<GuruLayout>

    <div class="space-y-5">

        <!-- HEADER -->
        <div>
            <h1 class="text-xl font-bold text-gray-800">Profil Saya</h1>
            <p class="text-gray-500 mt-0.5 text-sm">Kelola data akun guru</p>
        </div>

        <!-- BANNER PROFIL — ramping, foto+teks sejajar horizontal -->
        <div class="bg-gradient-to-r from-[#0F3D2E] to-[#1B7F5A] rounded-2xl px-6 py-5 text-white flex items-center justify-between gap-4 flex-wrap">

            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/20 overflow-hidden flex items-center justify-center text-xl font-bold shrink-0">
                    <img v-if="user.foto" :src="`/storage/${user.foto}`" class="w-full h-full object-cover">
                    <span v-else>{{ user.name[0] }}</span>
                </div>

                <div>
                    <h2 class="text-lg font-bold leading-tight">{{ user.name }}</h2>
                    <p class="text-green-100 text-sm mt-0.5">
                        Wali Kelas {{ user.teacherProfile?.schoolClass?.name ?? '-' }} — Aktif sejak Januari 2025
                    </p>
                    <span class="inline-block mt-2 bg-white/15 px-2.5 py-1 rounded-full text-xs">
                        ● Aktif
                    </span>
                </div>
            </div>

            <input
                ref="fotoInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleFotoChange"
            >
            <button
                @click="triggerFotoInput"
                :disabled="fotoForm.processing"
                class="bg-white/90 hover:bg-white text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition disabled:opacity-60 shrink-0"
            >
                {{ fotoForm.processing ? 'Mengunggah...' : '✏ Edit Foto' }}
            </button>

        </div>

        <!-- 2 KOLOM SEJAJAR: DATA AKUN | GANTI PASSWORD -->
        <div class="grid lg:grid-cols-2 gap-5 items-start">

            <!-- DATA AKUN -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">

                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        👤 Data Akun
                    </h3>
                    <button
                        v-if="!isEditingAccount"
                        @click="isEditingAccount = true"
                        class="bg-[#1B7F5A] text-white px-3.5 py-1.5 rounded-lg text-xs font-medium hover:bg-[#166347] transition"
                    >
                        ✏ Edit
                    </button>
                </div>

                <form @submit.prevent="submitAccount" class="space-y-4">

                    <div>
                        <label class="block text-xs font-medium mb-1.5 text-gray-500">Nama Lengkap</label>
                        <input
                            v-model="accountForm.name"
                            type="text"
                            :disabled="!isEditingAccount"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm disabled:bg-gray-50 disabled:text-gray-500 focus:border-[#1B7F5A] focus:ring-[#1B7F5A]/30"
                        >
                        <p v-if="accountForm.errors.name" class="text-xs text-red-500 mt-1">{{ accountForm.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1.5 text-gray-500">Email</label>
                        <input
                            v-model="accountForm.email"
                            type="email"
                            :disabled="!isEditingAccount"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm disabled:bg-gray-50 disabled:text-gray-500 focus:border-[#1B7F5A] focus:ring-[#1B7F5A]/30"
                        >
                        <p v-if="accountForm.errors.email" class="text-xs text-red-500 mt-1">{{ accountForm.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1.5 text-gray-500">Kelas yang Diampu</label>
                        <input
                            :value="user.teacherProfile?.schoolClass?.name ?? '-'"
                            type="text"
                            disabled
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm bg-gray-50 text-gray-500"
                        >
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <button
                            type="button"
                            @click="cancelEditAccount"
                            class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="!isEditingAccount || accountForm.processing"
                            class="bg-[#1B7F5A] hover:bg-[#166347] disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm font-medium transition"
                        >
                            {{ accountForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>

            </div>

            <!-- GANTI PASSWORD -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">

                <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                    🔒 Ganti Password
                </h3>

                <form @submit.prevent="submitPassword" class="space-y-4">

                    <div>
                        <label class="block text-xs font-medium mb-1.5 text-gray-500">Password Lama</label>
                        <input
                            v-model="passwordForm.current_password"
                            type="password"
                            placeholder="Masukkan password lama"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#1B7F5A] focus:ring-[#1B7F5A]/30"
                        >
                        <p v-if="passwordForm.errors.current_password" class="text-xs text-red-500 mt-1">{{ passwordForm.errors.current_password }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1.5 text-gray-500">Password Baru</label>
                        <input
                            v-model="passwordForm.password"
                            type="password"
                            placeholder="Minimal 8 karakter"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#1B7F5A] focus:ring-[#1B7F5A]/30"
                        >
                        <p v-if="passwordForm.errors.password" class="text-xs text-red-500 mt-1">{{ passwordForm.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1.5 text-gray-500">Konfirmasi Password Baru</label>
                        <input
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            placeholder="Ulangi password baru"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#1B7F5A] focus:ring-[#1B7F5A]/30"
                        >
                    </div>

                    <div class="flex justify-end pt-1">
                        <button
                            type="submit"
                            :disabled="passwordForm.processing"
                            class="bg-[#1B7F5A] hover:bg-[#166347] disabled:opacity-60 text-white px-4 py-2 rounded-lg text-sm font-medium transition"
                        >
                            {{ passwordForm.processing ? 'Menyimpan...' : 'Simpan Password' }}
                        </button>
                    </div>
                </form>

            </div>

        </div>

    </div>

</GuruLayout>

</template>