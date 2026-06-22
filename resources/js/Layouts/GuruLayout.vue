<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const page = usePage()
const sidebarOpen = ref(true)
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}
</script>

<template>
<div class="flex min-h-screen bg-[#F4F7F6]">
    <!-- SIDEBAR -->
    <aside
        class="w-64 bg-[#0F3D2E] text-white flex flex-col fixed h-full z-30 transition-transform duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <!-- TOMBOL PANAH, DI DALAM SIDEBAR -->
        <button
            v-if="sidebarOpen"
            @click="toggleSidebar"
            class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition z-40">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6" />
            </svg>
        </button>

        <!-- PROFILE -->
        <div class="p-5 border-b border-white/10">
            <div class="w-14 h-14 rounded-full overflow-hidden bg-white/20 mb-3">
                <img
                    src="https://i.pinimg.com/originals/22/02/f1/2202f1513fa534d5e3698ae8619d9474.jpg?nii=t"
                    class="w-full h-full object-cover"/>
            </div>

            <h2 class="font-bold text-base leading-tight">
                Sari Rahayu, S.Pd
            </h2>

            <p class="text-green-200 text-xs mt-0.5">
                Wali Kelas • 7A
            </p>
        </div>

        <!-- MENU -->
        <nav class="flex-1 p-4 space-y-1">
            <Link
                href="/guru/dashboard"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition text-sm"
                :class="$page.url.startsWith('/guru/dashboard') ? 'bg-white/15 font-semibold' : ''"
            >Dashboard
            </Link>

            <Link
                href="/guru/monitoring"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition text-sm"
                :class="$page.url.startsWith('/guru/monitoring') ? 'bg-white/15 font-semibold' : ''"
            >Monitoring Kelas
            </Link>

            <Link
                href="/guru/rekap"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition text-sm"
                :class="$page.url.startsWith('/guru/rekap') ? 'bg-white/15 font-semibold' : ''"
            >Rekap & Laporan
            </Link>

            <Link
                href="/guru/profile"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition text-sm"
                :class="$page.url.startsWith('/guru/profile') ? 'bg-white/15 font-semibold' : ''"
            >Profil Saya
            </Link>
        </nav>

        <!-- LOGOUT -->
        <div class="p-4 border-t border-white/10">
            <Link
                href="/logout"
                method="post"
                as="button"
                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm transition"
            >→ Keluar
            </Link>
        </div>
    </aside>

    <!-- OVERLAY untuk layar kecil -->
    <div
        v-if="sidebarOpen"
        @click="toggleSidebar"
        class="fixed inset-0 bg-black/20 z-20 lg:hidden">
    </div>

    <!-- CONTENT -->
    <div
        class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
        :class="sidebarOpen ? 'ml-64' : 'ml-0'">

        <!-- TOPBAR -->
        <header class="bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <button
                    v-if="!sidebarOpen"
                    @click="toggleSidebar"
                    class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6h16" />
                        <path d="M4 12h16" />
                        <path d="M4 18h16" />
                    </svg>
                </button>

                <div class="flex items-center gap-2.5">
                    <img
                        src="./logo_horizontal.png"
                        alt="Logo"
                        class="h-9 w-auto object-contain"/>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-200">
                    <img
                        @click="$inertia.visit('/guru/profile')"
                        src="https://i.pinimg.com/originals/22/02/f1/2202f1513fa534d5e3698ae8619d9474.jpg?nii=t"
                        class="w-full h-full object-cover"/>
                </div>
            </div>
        </header>

        <main class="p-6 flex-1">
            <slot />
        </main>
    </div>
</div>
</template>