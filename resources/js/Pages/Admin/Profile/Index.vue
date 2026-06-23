<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    settings: Object,
});

// State edit mode untuk data akun
const isEditing = ref(false);

// FORM DATA AKUN
const accountForm = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
});

const submitAccount = () => {
    accountForm.patch(route('admin.profile.update'), {
        preserveScroll: true,
        onSuccess: () => { isEditing.value = false; },
    });
};

const cancelEdit = () => {
    accountForm.name = props.user?.name ?? '';
    accountForm.email = props.user?.email ?? '';
    accountForm.clearErrors();
    isEditing.value = false;
};

// FORM PASSWORD
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submitPassword = () => {
    passwordForm.patch(route('admin.profile.password'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

// FORM PENGATURAN SISTEM
const settingsForm = useForm({
    notifikasi_email: props.settings?.notifikasi_email ?? true,
    laporan_otomatis: props.settings?.laporan_otomatis ?? false,
    alert_pengguna_baru: props.settings?.alert_pengguna_baru ?? true,
});

const submitSettings = () => {
    settingsForm.transform((data) => ({
        notifikasi_email: data.notifikasi_email ? 1 : 0,
        laporan_otomatis: data.laporan_otomatis ? 1 : 0,
        alert_pengguna_baru: data.alert_pengguna_baru ? 1 : 0,
    })).patch(route('admin.profile.settings'), {
        preserveScroll: true,
    });
};

// DANGER ZONE: RESET KEGIATAN
const showResetModal = ref(false);
const resetForm = useForm({
    password: '',
    tanggal_mulai: '',
    tanggal_akhir: '',
});

const submitReset = () => {
    resetForm.delete(route('admin.profile.reset-kegiatan'), {
        preserveScroll: true,
        onSuccess: () => {
            resetForm.reset();
            showResetModal.value = false;
        },
    });
};

const closeResetModal = () => {
    resetForm.reset();
    resetForm.clearErrors();
    showResetModal.value = false;
};

// DANGER ZONE: NONAKTIFKAN SISWA
const showDeactivateModal = ref(false);
const deactivateForm = useForm({
    password: '',
});

const submitDeactivate = () => {
    deactivateForm.patch(route('admin.profile.deactivate-students'), {
        preserveScroll: true,
        onSuccess: () => {
            deactivateForm.reset();
            showDeactivateModal.value = false;
        },
    });
};

const closeDeactivateModal = () => {
    deactivateForm.reset();
    deactivateForm.clearErrors();
    showDeactivateModal.value = false;
};
</script>

<template>
    <AdminLayout>
        <div class="space-y-5">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Profil & Pengaturan
                </h1>
                <p class="text-gray-500 mt-1">
                    Kelola data akun dan preferensi sistem
                </p>
            </div>

            <!-- Profile Banner -->
            <div class="bg-[#0F3D2E] rounded-3xl overflow-hidden relative px-8 py-6">
                <div class="flex items-center justify-between relative z-10">
                    <!-- Left -->
                    <div class="flex items-center gap-4">
                        <!-- Info -->
                        <div>
                            <h2 class="text-white text-3xl font-bold">
                                {{ user?.name ?? 'Administrator' }}
                            </h2>
                            <p class="text-green-100 mt-1">
                                {{ user?.email }}
                            </p>
                            <div class="flex items-center gap-2 mt-4">
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                    Aktif
                                </span>
                                <span class="bg-pink-100 text-pink-700 px-3 py-1 rounded-full text-sm font-medium">
                                    ADMIN
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-start">
                <!-- LEFT -->
                <div class="xl:col-span-3 space-y-5">
                    <!-- Data Akun -->
                    <div class="bg-white rounded-3xl shadow-md border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                <span class="text-purple-500">👤</span>
                                Data Akun Admin
                            </h3>
                            <button
                                v-if="!isEditing"
                                @click="isEditing = true"
                                class="bg-[#FFF3E6] text-[#F59E0B] px-4 py-2 rounded-xl text-sm font-medium"
                            >
                                ✏️ Edit
                            </button>
                        </div>

                        <div class="space-y-5">
                            <!-- Nama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                                <input
                                    v-model="accountForm.name"
                                    type="text"
                                    :disabled="!isEditing"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A] disabled:bg-gray-50"
                                />
                                <p v-if="accountForm.errors.name" class="text-red-500 text-sm mt-1">
                                    {{ accountForm.errors.name }}
                                </p>
                            </div>
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input
                                    v-model="accountForm.email"
                                    type="email"
                                    :disabled="!isEditing"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A] disabled:bg-gray-50"
                                />
                                <p v-if="accountForm.errors.email" class="text-red-500 text-sm mt-1">
                                    {{ accountForm.errors.email }}
                                </p>
                            </div>
                        </div>

                        <div v-if="isEditing" class="flex justify-end gap-3 mt-4">
                            <button
                                @click="cancelEdit"
                                class="px-5 py-3 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all duration-200"
                            >
                                Batal
                            </button>
                            <button
                                @click="submitAccount"
                                :disabled="accountForm.processing"
                                class="bg-[#1B7F5A] hover:bg-[#166347] transition-all duration-200 text-white px-5 py-3 rounded-xl font-medium disabled:opacity-50"
                            >
                                {{ accountForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </button>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="bg-white rounded-3xl shadow-md border border-gray-100 p-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2 mb-6">
                            <span class="text-yellow-500">🔒</span>
                            Ganti Password
                        </h3>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                                <input
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    placeholder="Masukkan password lama"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                                />
                                <p v-if="passwordForm.errors.current_password" class="text-red-500 text-sm mt-1">
                                    {{ passwordForm.errors.current_password }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                <input
                                    v-model="passwordForm.password"
                                    type="password"
                                    placeholder="Minimal 8 karakter"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                                />
                                <p v-if="passwordForm.errors.password" class="text-red-500 text-sm mt-1">
                                    {{ passwordForm.errors.password }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                <input
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    placeholder="Ulangi password baru"
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button
                                @click="submitPassword"
                                :disabled="passwordForm.processing"
                                class="bg-[#1B7F5A] hover:bg-[#166347] transition-all duration-200 text-white px-5 py-3 rounded-xl font-medium disabled:opacity-50"
                            >
                                {{ passwordForm.processing ? 'Menyimpan...' : 'Simpan Password' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="xl:col-span-2 space-y-5 min-w-[350px]">
                    <!-- Pengaturan Sistem -->
                    <div class="bg-white rounded-3xl shadow-md border border-gray-100 p-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2 mb-1">
                            <span class="text-gray-400">⚙️</span>
                            Pengaturan Sistem
                        </h3>
                        <p class="text-xs text-gray-400 mb-6">Fitur notifikasi sedang dalam pengembangan</p>

                        <div class="space-y-5">
                            <!-- Notifikasi Email -->
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-gray-700">Notifikasi Email</h4>
                                        <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded-full">Segera Hadir</span>
                                    </div>
                                    <p class="text-[15px] text-gray-400 mt-1">Terima ringkasan aktivitas harian</p>
                                </div>
                                <button
                                    @click="settingsForm.notifikasi_email = !settingsForm.notifikasi_email"
                                    :class="settingsForm.notifikasi_email ? 'bg-[#1B7F5A]' : 'bg-gray-200'"
                                    class="w-12 h-7 shrink-0 rounded-full relative transition-colors duration-200 flex items-center px-1 opacity-60 cursor-not-allowed"
                                    disabled
                                >
                                    <div
                                        :style="{ transform: settingsForm.notifikasi_email ? 'translateX(0px)' : 'translateX(20px)' }"
                                        class="w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                                    ></div>
                                </button>
                            </div>

                            <!-- Laporan Otomatis -->
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-gray-700">Laporan Otomatis</h4>
                                        <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded-full">Segera Hadir</span>
                                    </div>
                                    <p class="text-[15px] text-gray-400 mt-1">Kirim laporan mingguan ke email</p>
                                </div>
                                <button
                                    @click="settingsForm.laporan_otomatis = !settingsForm.laporan_otomatis"
                                    :class="settingsForm.laporan_otomatis ? 'bg-[#1B7F5A]' : 'bg-gray-200'"
                                    class="w-12 h-7 shrink-0 rounded-full relative transition-colors duration-200 flex items-center px-1 opacity-60 cursor-not-allowed"
                                    disabled
                                >
                                    <div
                                        :style="{ transform: settingsForm.laporan_otomatis ? 'translateX(0px)' : 'translateX(20px)' }"
                                        class="w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                                    ></div>
                                </button>
                            </div>

                            <!-- Alert Pengguna Baru -->
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-gray-700">Alert Pengguna Baru</h4>
                                        <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded-full">Segera Hadir</span>
                                    </div>
                                    <p class="text-[15px] text-gray-400 mt-1">Notifikasi saat ada akun baru dibuat</p>
                                </div>
                                <button
                                    @click="settingsForm.alert_pengguna_baru = !settingsForm.alert_pengguna_baru"
                                    :class="settingsForm.alert_pengguna_baru ? 'bg-[#1B7F5A]' : 'bg-gray-200'"
                                    class="w-12 h-7 shrink-0 rounded-full relative transition-colors duration-200 flex items-center px-1 opacity-60 cursor-not-allowed"
                                    disabled
                                >
                                    <div
                                        :style="{ transform: settingsForm.alert_pengguna_baru ? 'translateX(0px)' : 'translateX(20px)' }"
                                        class="w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                                    ></div>
                                </button>
                            </div>
                        </div>

                        <!-- Tombol simpan juga di-disable -->
                        <div class="flex justify-end mt-8">
                            <button
                                disabled
                                class="bg-gray-200 text-gray-400 px-5 py-3 rounded-xl font-medium cursor-not-allowed"
                            >
                                Segera Hadir
                            </button>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="bg-white rounded-3xl shadow-md border border-red-200 p-6">
                        <h3 class="text-xl font-bold text-red-500 flex items-center gap-2 mb-6">
                            <span class="text-yellow-500">⚠️</span>
                            Zona Berbahaya
                        </h3>
                        <div class="space-y-5">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-700">Reset semua data kegiatan</h4>
                                    <p class="text-[15px] text-gray-500 mt-1">Hapus data kegiatan pada rentang tanggal</p>
                                </div>
                                <button
                                    @click="showResetModal = true"
                                    class="border border-red-300 text-red-500 hover:bg-red-50 transition-all duration-200 px-4 py-3 rounded-xl text-sm font-medium"
                                >
                                    Reset
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-700">Nonaktifkan semua siswa</h4>
                                    <p class="text-[15px] text-gray-500 mt-1">Nonaktifkan seluruh akun siswa sekaligus</p>
                                </div>
                                <button
                                    @click="showDeactivateModal = true"
                                    class="border border-red-300 text-red-500 hover:bg-red-50 transition-all duration-200 px-4 py-3 rounded-xl text-sm font-medium"
                                >
                                    Nonaktifkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL RESET KEGIATAN -->
        <div v-if="showResetModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 w-full max-w-md space-y-4">
                <h3 class="text-xl font-bold text-red-500">Reset Data Kegiatan</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input v-model="resetForm.tanggal_mulai" type="date" class="w-full border border-gray-200 rounded-2xl px-4 py-3" />
                    <p v-if="resetForm.errors.tanggal_mulai" class="text-red-500 text-sm mt-1">{{ resetForm.errors.tanggal_mulai }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input v-model="resetForm.tanggal_akhir" type="date" class="w-full border border-gray-200 rounded-2xl px-4 py-3" />
                    <p v-if="resetForm.errors.tanggal_akhir" class="text-red-500 text-sm mt-1">{{ resetForm.errors.tanggal_akhir }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input v-model="resetForm.password" type="password" placeholder="Konfirmasi password" class="w-full border border-gray-200 rounded-2xl px-4 py-3" />
                    <p v-if="resetForm.errors.password" class="text-red-500 text-sm mt-1">{{ resetForm.errors.password }}</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button @click="closeResetModal" class="px-5 py-3 rounded-xl border border-gray-200 text-gray-600">Batal</button>
                    <button @click="submitReset" :disabled="resetForm.processing" class="bg-red-500 hover:bg-red-600 text-white px-5 py-3 rounded-xl font-medium disabled:opacity-50">
                        {{ resetForm.processing ? 'Memproses...' : 'Hapus Data' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL NONAKTIFKAN SISWA -->
        <div v-if="showDeactivateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 w-full max-w-md space-y-4">
                <h3 class="text-xl font-bold text-red-500">Nonaktifkan Semua Siswa</h3>
                <p class="text-gray-500">Tindakan ini akan menonaktifkan seluruh akun siswa. Masukkan password untuk konfirmasi.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input v-model="deactivateForm.password" type="password" placeholder="Konfirmasi password" class="w-full border border-gray-200 rounded-2xl px-4 py-3" />
                    <p v-if="deactivateForm.errors.password" class="text-red-500 text-sm mt-1">{{ deactivateForm.errors.password }}</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button @click="closeDeactivateModal" class="px-5 py-3 rounded-xl border border-gray-200 text-gray-600">Batal</button>
                    <button @click="submitDeactivate" :disabled="deactivateForm.processing" class="bg-red-500 hover:bg-red-600 text-white px-5 py-3 rounded-xl font-medium disabled:opacity-50">
                        {{ deactivateForm.processing ? 'Memproses...' : 'Nonaktifkan' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>