<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue'; // tambah computed
import { router, useForm } from '@inertiajs/vue3';

const showModal     = ref(false);
const modalMode     = ref('tambah');
const editingUserId = ref(null);
const activeTab     = ref('guru');

const props = defineProps({
    users:            Object,
    classes:          Array,
    availableClasses: Array,
    roleCounts:       Object,
    filters:          Object,
});

const search       = ref(props.filters?.search || '');
const activeFilter = ref(props.filters?.role   || 'semua');

// ─── STATE MODAL HAPUS ────────────────────────────────────────────────────────
const showDeleteModal   = ref(false);  // toggle tampil/sembunyi modal hapus
const deleteTargetId    = ref(null);   // ID user yang akan dihapus
const deleteTargetName  = ref('');     // Nama user untuk ditampilkan di modal

// ─── PAGINATION ───────────────────────────────────────────────────────────────
function goToPage(page) {
    // Jangan proses kalau halaman tidak valid
    if (page < 1 || page > props.users.last_page) return;

    router.get(
        route('admin.users.index'),
        {
            page:   page,
            search: search.value,
            role:   activeFilter.value === 'semua' ? '' : activeFilter.value,
        },
        {
            preserveState:  true,
            preserveScroll: false, // scroll ke atas saat pindah halaman
            replace:        true,
            only:           ['users'],
        }
    );
}

// Computed: generate array nomor halaman dengan titik-titik jika banyak halaman
const paginationPages = computed(() => {
    const current = props.users.current_page;
    const last    = props.users.last_page;
    const pages   = [];

    if (last <= 7) {
        // Kalau total halaman sedikit, tampilkan semua tanpa titik
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        // Selalu tampilkan halaman 1
        pages.push(1);

        // Titik kiri jika current jauh dari awal
        if (current > 3) pages.push('...');

        // Halaman di sekitar current (current-1, current, current+1)
        const start = Math.max(2, current - 1);
        const end   = Math.min(last - 1, current + 1);
        for (let i = start; i <= end; i++) pages.push(i);

        // Titik kanan jika current jauh dari akhir
        if (current < last - 2) pages.push('...');

        // Selalu tampilkan halaman terakhir
        pages.push(last);
    }

    return pages;
});

// ─── COMPUTED: KELAS TERSEDIA UNTUK GURU (SAAT EDIT) ─────────────────────────
const classesForGuruEdit = computed(() => {
    if (modalMode.value !== 'edit') return props.availableClasses;

    const currentClassId = form.school_class_id; // kelas yg sedang dipegang guru ini

    // Gabungkan availableClasses + kelas yg sedang dipegang guru ini
    // filter(Boolean) menghindari duplikat null/undefined
    const available = props.availableClasses || [];

    // Cek apakah kelas yang sedang dipegang sudah ada di availableClasses
    // Kalau belum ada (karena dia sudah punya guru = dirinya sendiri), tambahkan
    const alreadyIncluded = available.some(k => k.id == currentClassId);

    if (currentClassId && !alreadyIncluded) {
        // Cari data kelas lengkap dari props.classes (semua kelas)
        const currentClass = props.classes.find(k => k.id == currentClassId);
        if (currentClass) {
            // Return array baru: kelas guru ini + semua kelas available
            return [currentClass, ...available];
        }
    }

    return available; // kalau guru belum punya kelas, cukup return availableClasses
});

let timeout = null;

function filterByRole(role) {
    activeFilter.value = role;
    router.get(
        route('admin.users.index'),
        {
            search: search.value,
            role: role === 'semua' ? '' : role,
        },
        {
            preserveState:  true,
            preserveScroll: true,
            replace:        true,
            only:           ['users'],
        }
    );
}

function searchUsers() {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route('admin.users.index'),
            {
                search: search.value,
                role: activeFilter.value === 'semua' ? '' : activeFilter.value,
            },
            {
                preserveState:  true,
                preserveScroll: true,
                replace:        true,
                only:           ['users'],
            }
        );
    }, 500);
}

const inputClass  = 'w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none';
const selectClass = 'w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1B7F5A] focus:outline-none';

const form = useForm({
    name:            '',
    email:           '',
    password:        '',
    role:            'guru',
    status_akun:     'aktif',
    school_class_id: '',
    birth_date:      '',
    address:         '',
    parent_name:     '',
    parent_phone:    '',
    _current_role:   '',
    _current_search: '',
});

function openTambahModal() {
    modalMode.value      = 'tambah';
    editingUserId.value  = null;
    activeTab.value      = 'guru';
    form.reset();
    form.role            = 'guru';
    form.status_akun     = 'aktif';
    showModal.value      = true;
}

function openEditModal(user) {
    modalMode.value      = 'edit';
    editingUserId.value  = user.id;
    form.name            = user.name;
    form.email           = user.email;
    form.password        = '';
    form.role            = user.role;
    form.status_akun     = user.status_akun;

    if (user.role === 'guru') {
        // school_class_id dari teacher_profile — ini yang dipakai classesForGuruEdit
        form.school_class_id = user.teacher_profile?.school_class_id || '';
        form.birth_date      = '';
        form.address         = '';
        form.parent_name     = '';
        form.parent_phone    = '';
    } else if (user.role === 'siswa') {
        form.school_class_id = user.student_profile?.school_class_id || '';
        form.birth_date      = user.student_profile?.birth_date      || '';
        form.address         = user.student_profile?.address         || '';
        form.parent_name     = user.student_profile?.parent_name     || '';
        form.parent_phone    = user.student_profile?.parent_phone    || '';
    }

    showModal.value = true;
}

function closeModal() {
    showModal.value      = false;
    editingUserId.value  = null;
    modalMode.value      = 'tambah';
    activeTab.value      = 'guru';
    form.reset();
}

function submitUser() {
    form._current_role   = activeFilter.value === 'semua' ? '' : activeFilter.value;
    form._current_search = search.value;

    if (modalMode.value === 'tambah') {
        form.post(route('admin.users.store'), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.put(route('admin.users.update', editingUserId.value), {
            onSuccess: () => closeModal(),
        });
    }
}

// ─── HAPUS USER ───────────────────────────────────────────────────────────────
// Buka modal konfirmasi hapus, simpan ID dan nama user yang dipilih
function confirmDeleteUser(user) {
    deleteTargetId.value   = user.id;
    deleteTargetName.value = user.name;
    showDeleteModal.value  = true;
}

// Eksekusi hapus setelah user klik "Ya, Hapus" di modal
function executeDeleteUser() {
    router.delete(route('admin.users.destroy', deleteTargetId.value), {
        data: {
            _current_role:   activeFilter.value === 'semua' ? '' : activeFilter.value,
            _current_search: search.value,
        },
        onSuccess: () => {
            // Tutup modal dan reset state setelah berhasil hapus
            showDeleteModal.value  = false;
            deleteTargetId.value   = null;
            deleteTargetName.value = '';
        },
        onError: () => {
            // Tetap tutup modal meski error, biar user bisa lihat flash error
            showDeleteModal.value = false;
        }
    });
}

// Batal hapus — tutup modal tanpa melakukan apapun
function cancelDeleteUser() {
    showDeleteModal.value  = false;
    deleteTargetId.value   = null;
    deleteTargetName.value = '';
}
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- HEADER -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Kelola Pengguna</h1>
                    <p class="text-gray-500 mt-1">Kelola akun guru dan siswa</p>
                </div>
                <button
                    @click="openTambahModal"
                    class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-5 py-3 rounded-2xl font-medium transition"
                >
                    + Tambah Pengguna
                </button>
            </div>

            <!-- FILTER TAB + SEARCH -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="flex border-b border-gray-100 px-2">
                    <button
                        @click="filterByRole('semua')"
                        :class="[
                            'px-5 py-4 text-sm font-medium transition border-b-2 -mb-px',
                            activeFilter === 'semua'
                                ? 'border-[#1B7F5A] text-[#1B7F5A]'
                                : 'border-transparent text-gray-400 hover:text-gray-600'
                        ]"
                    >
                        Semua
                        <span :class="['ml-2 px-2 py-0.5 rounded-full text-xs', activeFilter === 'semua' ? 'bg-[#E7F3EE] text-[#1B7F5A]' : 'bg-gray-100 text-gray-400']">
                            {{ roleCounts?.semua ?? 0 }}
                        </span>
                    </button>
                    <button
                        @click="filterByRole('guru')"
                        :class="[
                            'px-5 py-4 text-sm font-medium transition border-b-2 -mb-px',
                            activeFilter === 'guru'
                                ? 'border-[#1B7F5A] text-[#1B7F5A]'
                                : 'border-transparent text-gray-400 hover:text-gray-600'
                        ]"
                    >
                        Guru/Wali Kelas
                        <span :class="['ml-2 px-2 py-0.5 rounded-full text-xs', activeFilter === 'guru' ? 'bg-[#E7F3EE] text-[#1B7F5A]' : 'bg-gray-100 text-gray-400']">
                            {{ roleCounts?.guru ?? 0 }}
                        </span>
                    </button>
                    <button
                        @click="filterByRole('siswa')"
                        :class="[
                            'px-5 py-4 text-sm font-medium transition border-b-2 -mb-px',
                            activeFilter === 'siswa'
                                ? 'border-[#1B7F5A] text-[#1B7F5A]'
                                : 'border-transparent text-gray-400 hover:text-gray-600'
                        ]"
                    >
                        Siswa
                        <span :class="['ml-2 px-2 py-0.5 rounded-full text-xs', activeFilter === 'siswa' ? 'bg-[#E7F3EE] text-[#1B7F5A]' : 'bg-gray-100 text-gray-400']">
                            {{ roleCounts?.siswa ?? 0 }}
                        </span>
                    </button>
                </div>
                <div class="p-4">
                    <input
                        v-model="search"
                        @input="searchUsers"
                        type="text"
                        placeholder="Cari pengguna..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                    />
                </div>
            </div>

            <!-- TABEL USER -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-[#F6F8F6]">
                        <tr class="text-left text-sm text-gray-500">
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id" class="border-t border-gray-100">
                            <td class="px-6 py-4 font-medium text-gray-700">{{ user.name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ user.email }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-[#E7F3EE] text-[#1B7F5A] px-3 py-1 rounded-full text-sm">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['px-3 py-1 rounded-full text-sm', user.status_akun === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                                    {{ user.status_akun === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <button @click="openEditModal(user)" class="text-blue-600 hover:underline">
                                        Edit
                                    </button>
                                    <!-- FIX: Panggil confirmDeleteUser, bukan deleteUser langsung -->
                                    <button @click="confirmDeleteUser(user)" class="text-red-600 hover:underline">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                Tidak ada pengguna ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tambahkan ini SETELAH div tabel (setelah </div> penutup tabel) -->
        <!-- PAGINATION -->
        <div v-if="users.last_page > 1" class="flex items-center justify-between px-6 py-4 bg-white rounded-2xl shadow-sm border border-gray-100">
            <!-- Info: "Menampilkan 1-10 dari 35" -->
            <p class="text-sm text-gray-500">
                Menampilkan {{ users.from }}–{{ users.to }} dari {{ users.total }} pengguna
            </p>

            <!-- Tombol navigasi halaman -->
            <div class="flex items-center gap-2">
                <!-- Tombol Sebelumnya -->
                <button
                    @click="goToPage(users.current_page - 1)"
                    :disabled="users.current_page === 1"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium transition
                        disabled:opacity-40 disabled:cursor-not-allowed
                        hover:bg-gray-50 text-gray-600"
                >
                    ← Sebelumnya
                </button>

                <!-- Nomor halaman -->
                <template v-for="page in paginationPages" :key="page">
                    <!-- Titik-titik jika ada gap -->
                    <span v-if="page === '...'" class="px-2 text-gray-400">...</span>
                    <!-- Tombol nomor halaman -->
                    <button
                        v-else
                        @click="goToPage(page)"
                        :class="[
                            'w-9 h-9 rounded-xl text-sm font-medium transition',
                            page === users.current_page
                                ? 'bg-[#1B7F5A] text-white'
                                : 'border border-gray-200 text-gray-600 hover:bg-gray-50'
                        ]"
                    >
                        {{ page }}
                    </button>
                </template>

                <!-- Tombol Selanjutnya -->
                <button
                    @click="goToPage(users.current_page + 1)"
                    :disabled="users.current_page === users.last_page"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium transition
                        disabled:opacity-40 disabled:cursor-not-allowed
                        hover:bg-gray-50 text-gray-600"
                >
                    Selanjutnya →
                </button>
            </div>
        </div>
        <!-- MODAL TAMBAH / EDIT                                                -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        >
            <div class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <!-- HEADER MODAL -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">
                            {{ modalMode === 'tambah' ? 'Tambah Pengguna Baru' : 'Edit Pengguna' }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ modalMode === 'tambah' ? 'Tambahkan akun guru atau siswa' : 'Ubah data pengguna' }}
                        </p>
                    </div>
                    <button @click="closeModal" class="w-10 h-10 rounded-full hover:bg-gray-100 text-xl">✕</button>
                </div>

                <!-- TAB ROLE — hanya muncul saat mode tambah -->
                <div v-if="modalMode === 'tambah'" class="flex gap-2 px-6 pt-4">
                    <button
                        @click="activeTab = 'guru'; form.role = 'guru';"
                        :class="['flex-1 py-3 font-medium rounded-xl transition', activeTab === 'guru' ? 'bg-[#E7F3EE] text-[#1B7F5A] border border-[#1B7F5A]' : 'text-gray-400 hover:bg-gray-50 border border-gray-200']"
                    >
                        👨‍🏫 Guru / Wali Kelas
                    </button>
                    <button
                        @click="activeTab = 'siswa'; form.role = 'siswa';"
                        :class="['flex-1 py-3 font-medium rounded-xl transition', activeTab === 'siswa' ? 'bg-[#E7F3EE] text-[#1B7F5A] border border-[#1B7F5A]' : 'text-gray-400 hover:bg-gray-50 border border-gray-200']"
                    >
                        🧑‍🎓 Siswa
                    </button>
                </div>

                <!-- Badge role saat mode edit -->
                <div v-if="modalMode === 'edit'" class="px-6 pt-4">
                    <span class="inline-flex items-center gap-2 bg-[#E7F3EE] text-[#1B7F5A] px-4 py-2 rounded-xl text-sm font-medium border border-[#1B7F5A]">
                        {{ form.role === 'guru' ? '👨‍🏫 Guru / Wali Kelas' : '🧑‍🎓 Siswa' }}
                    </span>
                </div>

                <!-- ISI FORM -->
                <div class="p-6 overflow-y-auto flex-1 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input v-model="form.name" type="text" placeholder="Masukkan nama lengkap" :class="inputClass" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input v-model="form.email" type="email" placeholder="email@contoh.com" :class="inputClass" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                                <span v-if="modalMode === 'edit'" class="text-xs text-gray-400 font-normal ml-1">(kosongkan jika tidak diganti)</span>
                            </label>
                            <input v-model="form.password" type="password" placeholder="Minimal 8 karakter" :class="inputClass" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Akun</label>
                        <select v-model="form.status_akun" :class="selectClass">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <!-- FIELD KHUSUS GURU -->
                    <template v-if="(modalMode === 'tambah' && activeTab === 'guru') || (modalMode === 'edit' && form.role === 'guru')">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kelas Diampu</label>
                            <!--
                                FIX BUG KELAS GURU:
                                - Saat tambah  → pakai availableClasses (kelas yg belum punya guru)
                                - Saat edit    → pakai classesForGuruEdit (computed):
                                  berisi availableClasses + kelas milik guru ini sendiri
                                  sehingga kelas dia sendiri tetap muncul, tapi kelas orang lain tidak
                            -->
                            <select
                                v-model="form.school_class_id"
                                :class="selectClass"
                            >
                                <option value="">Tidak ada / Pilih Kelas</option>
                                <option
                                    v-for="kelas in (modalMode === 'tambah' ? availableClasses : classesForGuruEdit)"
                                    :key="kelas.id"
                                    :value="kelas.id"
                                >
                                    {{ kelas.name }}
                                </option>
                            </select>
                        </div>
                    </template>

                    <!-- FIELD KHUSUS SISWA -->
                    <template v-if="(modalMode === 'tambah' && activeTab === 'siswa') || (modalMode === 'edit' && form.role === 'siswa')">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                                <select v-model="form.school_class_id" :class="selectClass">
                                    <option value="">Pilih Kelas</option>
                                    <option v-for="kelas in classes" :key="kelas.id" :value="kelas.id">
                                        {{ kelas.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                <input v-model="form.birth_date" type="date" :class="inputClass" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea v-model="form.address" rows="3" placeholder="Masukkan alamat lengkap" :class="inputClass"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Orang Tua</label>
                                <input v-model="form.parent_name" type="text" placeholder="Nama orang tua" :class="inputClass" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No HP Orang Tua</label>
                                <input v-model="form.parent_phone" type="text" placeholder="08xxxxxxxx" :class="inputClass" />
                            </div>
                        </div>
                    </template>
                </div>

                <!-- FOOTER MODAL -->
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 shrink-0">
                    <button @click="closeModal" class="px-5 py-3 border border-gray-200 rounded-2xl text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button
                        @click="submitUser"
                        :disabled="form.processing"
                        class="bg-[#1B7F5A] hover:bg-[#166347] disabled:opacity-50 text-white px-6 py-3 rounded-2xl font-medium transition"
                    >
                        {{ form.processing ? 'Menyimpan...' : (modalMode === 'tambah' ? 'Simpan' : 'Update') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- MODAL KONFIRMASI HAPUS USER                                        -->
        <!-- ================================================================= -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-3xl p-8 w-full max-w-sm shadow-xl">
                <div class="text-center">
                    <!-- Icon peringatan -->
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🗑️</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Hapus Pengguna?</h2>
                    <p class="text-gray-500 text-sm mb-6">
                        Kamu yakin ingin menghapus pengguna
                        <!-- Tampilkan nama user yang akan dihapus -->
                        <span class="font-semibold text-gray-700">{{ deleteTargetName }}</span>?
                        Tindakan ini tidak bisa dibatalkan.
                    </p>
                </div>
                <div class="flex gap-3">
                    <!-- Tombol batal — tutup modal tanpa hapus -->
                    <button
                        @click="cancelDeleteUser"
                        class="flex-1 px-5 py-3 border border-gray-200 rounded-2xl text-gray-600 hover:bg-gray-50 transition font-medium"
                    >
                        Batal
                    </button>
                    <!-- Tombol hapus — eksekusi delete -->
                    <button
                        @click="executeDeleteUser"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-2xl font-medium transition"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>