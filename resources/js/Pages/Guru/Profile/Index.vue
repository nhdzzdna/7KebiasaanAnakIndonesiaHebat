<script setup>
import GuruLayout from '@/Layouts/GuruLayout.vue'
</script>

<template>

<GuruLayout>

    <div class="space-y-6">

        <!-- HEADER -->
        <div>

            <h1
                class="text-3xl font-bold text-gray-800"
            >
                Profil Guru
            </h1>

            <p
                class="text-gray-500 mt-1"
            >
                Kelola data akun dan informasi pribadi
            </p>

        </div>

        <!-- PROFILE BANNER -->
        <div
            class="bg-gradient-to-r from-[#0F3D2E] to-[#1B7F5A]
            rounded-3xl p-8 text-white"
        >

            <div
                class="flex items-center gap-6"
            >

                <div
                    class="w-24 h-24 rounded-full
                    bg-white/20 flex items-center justify-center
                    text-3xl font-bold"
                >
                    S
                </div>

                <div>

                    <h2
                        class="text-3xl font-bold"
                    >
                        Sari Rahayu, S.Pd
                    </h2>

                    <p
                        class="text-green-100 mt-1"
                    >
                        Wali Kelas 7A
                    </p>

                    <div
                        class="flex gap-2 mt-4"
                    >

                        <span
                            class="bg-white/20 px-3 py-1 rounded-full text-sm"
                        >
                            Aktif
                        </span>

                        <span
                            class="bg-yellow-400 text-black px-3 py-1 rounded-full text-sm"
                        >
                            Guru
                        </span>

                    </div>

                </div>

            </div>

        </div>

        <!-- DATA AKUN -->
        <div
            class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6"
        >

            <div
                class="flex items-center justify-between mb-6"
            >

                <h3
                    class="text-xl font-bold"
                >
                    Data Akun
                </h3>

                <button
                    class="bg-[#1B7F5A] text-white px-4 py-2 rounded-xl"
                >
                    Edit
                </button>

            </div>

            <div
                class="grid md:grid-cols-2 gap-5"
            >

                <div>

                    <label
                        class="block text-sm font-medium mb-2"
                    >
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        value="Sari Rahayu, S.Pd"
                        class="w-full border rounded-xl px-4 py-3"
                    >

                </div>

                <div>

                    <label
                        class="block text-sm font-medium mb-2"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        value="sari@sekolah.sch.id"
                        class="w-full border rounded-xl px-4 py-3"
                    >

                </div>

                <div>

                    <label
                        class="block text-sm font-medium mb-2"
                    >
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        value="081234567890"
                        class="w-full border rounded-xl px-4 py-3"
                    >

                </div>

                <div>

                    <label
                        class="block text-sm font-medium mb-2"
                    >
                        Kelas Wali
                    </label>

                    <input
                        type="text"
                        value="7A"
                        class="w-full border rounded-xl px-4 py-3"
                    >

                </div>

            </div>

            <div
                class="flex justify-end mt-6"
            >

                <button
                    class="bg-[#1B7F5A] hover:bg-[#166347]
                    text-white px-6 py-3 rounded-xl"
                >
                    Simpan Perubahan
                </button>

            </div>

        </div>

        <!-- PASSWORD -->
        <div
            class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6"
        >

            <h3
                class="text-xl font-bold mb-6"
            >
                Ganti Password
            </h3>

            <div
                class="space-y-5"
            >

                <div>

                    <label
                        class="block text-sm font-medium mb-2"
                    >
                        Password Lama
                    </label>

                    <input
                        type="password"
                        class="w-full border rounded-xl px-4 py-3"
                    >

                </div>

                <div>

                    <label
                        class="block text-sm font-medium mb-2"
                    >
                        Password Baru
                    </label>

                    <input
                        type="password"
                        class="w-full border rounded-xl px-4 py-3"
                    >

                </div>

                <div>

                    <label
                        class="block text-sm font-medium mb-2"
                    >
                        Konfirmasi Password Baru
                    </label>

                    <input
                        type="password"
                        class="w-full border rounded-xl px-4 py-3"
                    >

                </div>

            </div>

            <div
                class="flex justify-end mt-6"
            >

                <button
                    class="bg-[#1B7F5A] hover:bg-[#166347]
                    text-white px-6 py-3 rounded-xl"
                >
                    Simpan Password
                </button>

            </div>

        </div>

    </div>

</GuruLayout>

</template>