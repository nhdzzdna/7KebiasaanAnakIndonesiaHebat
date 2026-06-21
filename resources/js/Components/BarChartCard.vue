<script setup>
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from 'chart.js'

// Daftarkan modul Chart.js yang dipakai untuk grafik batang.
// Ini WAJIB dilakukan sekali sebelum chart dirender, kalau tidak chart tidak akan muncul.
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

// Props yang diterima dari komponen induk (Dashboard.vue, dll).
// 'labels' dan 'values' nantinya akan dikirim dari controller Laravel lewat Inertia,
// jadi komponen ini sengaja dibuat tidak tahu-menahu soal asal datanya (reusable & presentational).
const props = defineProps({
    // Array label sumbu X, contoh: ['1 Jun', '2 Jun', '3 Jun', ...]
    labels: {
        type: Array,
        required: true,
    },
    // Array angka yang jadi tinggi tiap bar, contoh: [12, 18, 25, ...]
    values: {
        type: Array,
        required: true,
    },
    // Index bar yang ingin di-highlight warna beda (misal: hari dengan aktivitas tertinggi)
    // contoh: [3, 7] artinya bar ke-4 dan ke-8 akan diwarnai oranye
    highlightIndexes: {
        type: Array,
        default: () => [],
    },
    height: {
        type: Number,
        default: 220,
    },
})

// Computed: susun ulang props jadi format yang dimengerti Chart.js.
// Warna tiap bar otomatis oranye kalau index-nya ada di highlightIndexes,
// selain itu pakai warna hijau tema sebagai default.
const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            data: props.values,
            backgroundColor: props.values.map((_, index) =>
                props.highlightIndexes.includes(index) ? '#F59E0B' : '#1B7F5A'
            ),
            borderRadius: 6,
            barThickness: 14,
        },
    ],
}))

// Opsi tampilan: matikan legend (karena cuma 1 dataset, legend tidak perlu),
// dan rapikan grid + sumbu supaya terlihat bersih, senada dengan tema hijau aplikasi.
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#0F3D2E',
            padding: 10,
            cornerRadius: 8,
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { color: '#9CA3AF', font: { size: 11 } },
        },
        y: {
            beginAtZero: true,
            grid: { color: '#F3F4F6' },
            ticks: { color: '#9CA3AF', font: { size: 11 } },
        },
    },
}
</script>

<template>
    <div :style="{ height: height + 'px' }">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>