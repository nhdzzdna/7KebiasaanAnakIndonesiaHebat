<script setup>
import GuruLayout from '@/Layouts/GuruLayout.vue'

const dataRekap = [
    {
        nama: 'Nadia Putri',
        kepatuhan: 95,
        laporan: 30,
        nilai: 'A',
    },
    {
        nama: 'Budi Santoso',
        kepatuhan: 90,
        laporan: 29,
        nilai: 'A',
    },
    {
        nama: 'Ahmad Fauzi',
        kepatuhan: 75,
        laporan: 24,
        nilai: 'B',
    },
    {
        nama: 'Kurnia Ardiningrum',
        kepatuhan: 88,
        laporan: 28,
        nilai: 'A',
    },
]
</script>

<template>

<GuruLayout>

    <div class="space-y-6">

        <!-- HEADER -->
        <div>

            <h1
                class="text-3xl font-bold text-gray-800"
            >
                Rekap & Laporan
            </h1>

            <p
                class="text-gray-500 mt-1"
            >
                Rekap perkembangan kebiasaan siswa kelas 7A
            </p>

        </div>

        <!-- FILTER -->
        <div
            class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5"
        >

            <div
                class="flex flex-wrap gap-4 items-center"
            >

                <select
                    class="border rounded-xl px-4 py-3"
                >
                    <option>Januari 2026</option>
                    <option>Februari 2026</option>
                    <option>Maret 2026</option>
                </select>

                <button
                    class="bg-[#1B7F5A] text-white px-5 py-3 rounded-xl"
                >
                    Filter
                </button>

                <div class="flex-1"></div>

                <button
                    class="bg-red-500 text-white px-5 py-3 rounded-xl"
                >
                    Export PDF
                </button>

                <button
                    class="bg-green-500 text-white px-5 py-3 rounded-xl"
                >
                    Export Excel
                </button>

            </div>

        </div>

        <!-- SUMMARY -->
        <div
            class="grid md:grid-cols-3 gap-5"
        >

            <div
                class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5"
            >
                <p class="text-gray-500 text-sm">
                    Total Siswa
                </p>

                <h2
                    class="text-3xl font-bold mt-2"
                >
                    32
                </h2>

            </div>

            <div
                class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5"
            >
                <p class="text-gray-500 text-sm">
                    Rata-rata Kepatuhan
                </p>

                <h2
                    class="text-3xl font-bold text-green-600 mt-2"
                >
                    87%
                </h2>

            </div>

            <div
                class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5"
            >
                <p class="text-gray-500 text-sm">
                    Nilai Rata-rata
                </p>

                <h2
                    class="text-3xl font-bold text-blue-600 mt-2"
                >
                    A
                </h2>

            </div>

        </div>

        <!-- TABLE -->
        <div
            class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden"
        >

            <div
                class="p-5 border-b"
            >

                <h3
                    class="font-bold text-lg"
                >
                    Rekap Siswa
                </h3>

            </div>

            <div
                class="overflow-x-auto"
            >

                <table
                    class="w-full"
                >

                    <thead
                        class="bg-gray-50"
                    >

                        <tr>

                            <th class="text-left px-5 py-4">
                                Nama Siswa
                            </th>

                            <th class="text-center">
                                Kepatuhan
                            </th>

                            <th class="text-center">
                                Total Laporan
                            </th>

                            <th class="text-center">
                                Nilai
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr
                            v-for="item in dataRekap"
                            :key="item.nama"
                            class="border-t"
                        >

                            <td
                                class="px-5 py-4"
                            >
                                {{ item.nama }}
                            </td>

                            <td
                                class="text-center text-green-600 font-semibold"
                            >
                                {{ item.kepatuhan }}%
                            </td>

                            <td
                                class="text-center"
                            >
                                {{ item.laporan }}
                            </td>

                            <td
                                class="text-center"
                            >

                                <span
                                    class="bg-green-100 text-green-700 px-3 py-1 rounded-full"
                                >
                                    {{ item.nilai }}
                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</GuruLayout>

</template>