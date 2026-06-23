<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

// ─── PROPS ───────────────────────────────────────────────
// Menerima data kelas dan guru dari controller via Inertia
defineProps({
    classes: Array,
    teachers: Array
});

// ─── TAMBAH KELAS ─────────────────────────────────────────
// State untuk buka/tutup modal tambah
const showModal = ref(false);

// Form tambah kelas menggunakan useForm Inertia
const form = useForm({
    name: '',
    school_year: ''
})

// Fungsi submit form tambah kelas
function submitClass() {
    form.post(route('admin.classes.store'), {
        onSuccess: () => {
            showModal.value = false  // Tutup modal setelah berhasil
            form.reset()             // Reset isi form
        }
    })
}

// ─── EDIT KELAS ───────────────────────────────────────────
// State untuk buka/tutup modal edit
const showEditModal = ref(false)

// Menyimpan data kelas yang sedang diedit
const selectedClass = ref(null)
const editId = ref(null)

// Form edit kelas
const editForm = useForm({
    name: '',
    school_year: '',
    teacher_id: ''
})

// Buka modal edit dan isi form dengan data kelas yang dipilih
function openEditModal(kelas) {
    selectedClass.value = kelas
    editId.value = kelas.id
    editForm.name = kelas.name
    editForm.school_year = kelas.school_year
    editForm.teacher_id = kelas.teacher_id ?? ''
    showEditModal.value = true
}

// Kirim request PUT untuk update kelas
function updateClass() {
    editForm.put(
        route('admin.classes.update', editId.value),
        {
            // FIX: onSuccess sekarang menutup modal dengan benar
            // Karena controller sudah redirect ke index, Inertia
            // akan re-render halaman dengan data terbaru
            onSuccess: () => {
                showEditModal.value = false
                editForm.reset()
            },
            // Tambahan: tangkap error jika ada
            onError: (errors) => {
                console.error('Update error:', errors)
            }
        }
    )
}

// ─── HAPUS KELAS ──────────────────────────────────────────
// State untuk modal konfirmasi hapus (ganti confirm() native)
const showDeleteModal = ref(false)
const deleteTargetId = ref(null)      // ID kelas yang akan dihapus
const deleteTargetName = ref('')      // Nama kelas untuk ditampilkan di modal

// Buka modal konfirmasi hapus
function confirmDelete(kelas) {
    deleteTargetId.value = kelas.id
    deleteTargetName.value = kelas.name
    showDeleteModal.value = true
}

// Eksekusi hapus setelah user konfirmasi
function deleteClass() {
    router.delete(route('admin.classes.destroy', deleteTargetId.value), {
        // FIX: onSuccess menutup modal dan reset state
        onSuccess: () => {
            showDeleteModal.value = false
            deleteTargetId.value = null
            deleteTargetName.value = ''
        },
        onError: (errors) => {
            console.error('Delete error:', errors)
            showDeleteModal.value = false
        }
    })
}

// Batal hapus — tutup modal tanpa aksi
function cancelDelete() {
    showDeleteModal.value = false
    deleteTargetId.value = null
    deleteTargetName.value = ''
}
</script>

<template>
    <AdminLayout>
        <!-- ─── HEADER ─────────────────────────────────────── -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Kelola Kelas</h1>
                <p class="text-gray-500 mt-1">Kelola data kelas</p>
            </div>
            <button
                @click="showModal = true"
                class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-5 py-3 rounded-2xl font-medium transition"
            >
                + Tambah Kelas
            </button>
        </div>

        <!-- ─── TABEL KELAS ────────────────────────────────── -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-[#F6F8F6]">
                    <tr class="text-left text-sm text-gray-500">
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">Tahun Ajaran</th>
                        <th class="px-6 py-4">Wali Kelas</th>
                        <th class="px-6 py-4">Jumlah Siswa</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="kelas in classes"
                        :key="kelas.id"
                        class="border-t border-gray-100"
                    >
                        <td class="px-6 py-5 font-medium">{{ kelas.name }}</td>
                        <td class="px-6 py-5">{{ kelas.school_year }}</td>
                        <td class="px-6 py-5">{{ kelas.teacher ?? 'Belum ada wali kelas' }}</td>
                        <td class="px-6 py-5">{{ kelas.total_students }}</td>
                        <td class="px-6 py-5">
                            <div class="flex gap-3">
                                <button
                                    @click="openEditModal(kelas)"
                                    class="text-blue-600 hover:underline"
                                >
                                    Edit
                                </button>
                                <!-- FIX: Panggil confirmDelete, bukan deleteClass langsung -->
                                <button
                                    @click="confirmDelete(kelas)"
                                    class="text-red-600 hover:underline"
                                >
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!classes || classes.length === 0">
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">
                            Belum ada data kelas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ─── MODAL TAMBAH KELAS ─────────────────────────── -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
            @click.self="showModal = false"
        >
            <div class="bg-white rounded-3xl p-8 w-full max-w-md shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Tambah Kelas</h2>
                    <button
                        @click="showModal = false"
                        class="w-10 h-10 rounded-full hover:bg-gray-100 text-xl"
                    >
                        ✕
                    </button>
                </div>
                <div class="flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: 7A"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                        <input
                            v-model="form.school_year"
                            type="text"
                            placeholder="Contoh: 2025/2026"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                        >
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button
                        @click="showModal = false"
                        class="px-5 py-3 border border-gray-200 rounded-2xl text-gray-600 hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button
                        @click="submitClass"
                        :disabled="form.processing"
                        class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-6 py-3 rounded-2xl font-medium transition"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- ─── MODAL EDIT KELAS ───────────────────────────── -->
        <div
            v-if="showEditModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
            @click.self="showEditModal = false"
        >
            <div class="bg-white rounded-3xl p-8 w-full max-w-md shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Edit Kelas</h2>
                    <button
                        @click="showEditModal = false"
                        class="w-10 h-10 rounded-full hover:bg-gray-100 text-xl"
                    >
                        ✕
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
                        <input
                            v-model="editForm.name"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                        <input
                            v-model="editForm.school_year"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas</label>
                        <select
                            v-model="editForm.teacher_id"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B7F5A]"
                        >
                            <option value="">Belum ada wali kelas</option>
                            <option
                                v-for="teacher in teachers"
                                :key="teacher.id"
                                :value="teacher.id"
                            >
                                {{ teacher.name }}
                            </option>
                        </select>
                        <p
                            v-if="editForm.errors.teacher_id"
                            class="text-red-500 text-xs mt-1"
                        >{{ editForm.errors.teacher_id }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button
                        @click="showEditModal = false"
                        class="px-5 py-3 border border-gray-200 rounded-2xl text-gray-600 hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button
                        @click="updateClass"
                        :disabled="editForm.processing"
                        class="bg-[#1B7F5A] hover:bg-[#166347] text-white px-6 py-3 rounded-2xl font-medium transition"
                    >
                        {{ editForm.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- ─── MODAL KONFIRMASI HAPUS ─────────────────────── -->
        <!-- FIX: Ganti confirm() browser dengan modal Vue yang proper -->
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
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Hapus Kelas?</h2>
                    <p class="text-gray-500 text-sm mb-6">
                        Kamu yakin ingin menghapus kelas
                        <span class="font-semibold text-gray-700">{{ deleteTargetName }}</span>?
                        Tindakan ini tidak bisa dibatalkan.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="cancelDelete"
                        class="flex-1 px-5 py-3 border border-gray-200 rounded-2xl text-gray-600 hover:bg-gray-50 transition font-medium"
                    >
                        Batal
                    </button>
                    <button
                        @click="deleteClass"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-2xl font-medium transition"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>