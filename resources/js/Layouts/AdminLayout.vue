<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <div class="flex min-h-screen bg-[#F8FAF9]">

        <!-- Sidebar -->
        <aside class="w-56 bg-[#0F3D2E] text-white flex flex-col">

            <!-- Logo -->
            <div class="px-8 py-7 border-b border-white/10">

                <h1 class="text-2xl font-bold">
                    Administrator
                </h1>

                <p class="text-sm text-green-100 mt-1">
                    Sistem Monitoring Sekolah
                </p>

            </div>

            <!-- Menu -->
            <nav class="flex-1 px-5 py-6 space-y-3">

                <Link
                    href="/admin/dashboard"
                    :class="[
                        'flex items-center px-4 py-3 rounded-xl transition-all duration-200',
                        page.url.startsWith('/admin/dashboard')
                            ? 'bg-white text-[#0F3D2E] font-semibold shadow-sm'
                            : 'text-white hover:bg-white/10'
                    ]"
                >
                    Dashboard
                </Link>

                <Link
                    href="/admin/users"
                    :class="[
                        'flex items-center px-4 py-3 rounded-xl transition-all duration-200',
                        page.url.startsWith('/admin/users')
                            ? 'bg-white text-[#0F3D2E] font-semibold shadow-sm'
                            : 'text-white hover:bg-white/10'
                    ]"
                >
                    Kelola Pengguna
                </Link>

                <Link
                    href="/admin/profile"
                    :class="[
                        'flex items-center px-4 py-3 rounded-xl transition-all duration-200',
                        page.url.startsWith('/admin/profile')
                            ? 'bg-white text-[#0F3D2E] font-semibold shadow-sm'
                            : 'text-white hover:bg-white/10'
                    ]"
                >
                    Profil & Pengaturan
                </Link>

                
            </nav>

            <!-- Footer -->
            <div class="p-5 border-t border-white/10">

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full bg-[#1B7F5A] hover:bg-[#166347] transition-all duration-200 py-3 rounded-xl font-medium"
                >
                    Logout
                </Link>

            </div>

        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col">

            <!-- Navbar -->
            <header class="h-16 bg-white border-b border-gray-200 px-8 flex items-center justify-between">

                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Dashboard Admin
                    </h2>

                    <p class="text-sm text-gray-500">
                        Pantau statistik dan aktivitas sistem sekolah
                    </p>
                </div>

                <div class="flex items-center gap-4">

                    <div class="text-right">
                        <p class="font-semibold text-gray-700">
                            Admin
                        </p>

                        <p class="text-sm text-gray-500">
                            Administrator
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-full bg-[#0F3D2E] flex items-center justify-center text-white font-semibold">
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