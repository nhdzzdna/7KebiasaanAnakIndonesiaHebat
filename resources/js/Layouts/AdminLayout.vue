<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue'

const page = usePage();
const sidebarOpen = ref(true)
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}
</script>

<template>
    <div class="flex min-h-screen bg-[#F8FAF9]">

        <!-- Sidebar -->
        <aside
            class="w-64 bg-[#0F3D2E] text-white flex flex-col fixed h-full z-30 transition-transform duration-300 ease-in-out"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <button
                v-if="sidebarOpen"
                @click="toggleSidebar"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition z-40">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </button>

            <!-- Identitas Panel -->
            <div class="p-5 border-b border-white/10">

                <h1 class="font-bold text-base leading-tight">
                    Administrator
                </h1>

                <p class="text-green-200 text-xs mt-0.5">
                    Sistem Monitoring Sekolah
                </p>

            </div>

            <!-- Menu -->
            <nav class="flex-1 p-4 space-y-1">

                <Link
                    href="/admin/dashboard"
                    :class="[
                        'flex items-center px-4 py-3 rounded-xl transition text-sm',
                        page.url.startsWith('/admin/dashboard')
                            ? 'bg-white/15 font-semibold'
                            : 'text-white hover:bg-white/10'
                    ]">Dashboard
                </Link>

                <Link
                    href="/admin/classes"
                    :class="[
                        'flex items-center px-4 py-3 rounded-xl transition text-sm',
                        page.url.startsWith('/admin/classes')
                            ? 'bg-white/15 font-semibold'
                            : 'text-white hover:bg-white/10'
                    ]">Kelola Kelas
                </Link>

                <Link
                    href="/admin/users"
                    :class="[
                        'flex items-center px-4 py-3 rounded-xl transition text-sm',
                        page.url.startsWith('/admin/users')
                            ? 'bg-white/15 font-semibold'
                            : 'text-white hover:bg-white/10'
                    ]">Kelola Pengguna
                </Link>

                <Link
                    href="/admin/profile"
                    :class="[
                        'flex items-center px-4 py-3 rounded-xl transition text-sm',
                        page.url.startsWith('/admin/profile')
                            ? 'bg-white/15 font-semibold'
                            : 'text-white hover:bg-white/10'
                    ]">Profil & Pengaturan
                </Link>
            </nav>

            <div class="p-4 border-t border-white/10">
                <Link
                href="/logout"
                method="post"
                as="button"
                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm transition">
                <span>→</span> Keluar
                </Link>
            </div>
        </aside>

        <!-- Main -->
        <div
            class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'ml-64' : 'ml-0'">

            <!-- Navbar -->
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

                <div class="flex items-center gap-4">
                    <div 
                        @click="$inertia.visit('/admin/profile')"
                        class="w-11 h-11 rounded-full bg-[#0F3D2E] flex items-center justify-center text-white font-semibold">
                        A
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 px-8 py-6">
                <slot />
            </main>
        </div>
    </div>
</template>