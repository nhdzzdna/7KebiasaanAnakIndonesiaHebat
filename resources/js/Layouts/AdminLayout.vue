<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen bg-[#F4F7FE]">

        <!-- Sidebar -->
        <aside class="w-72 bg-[#1E3A5F] text-white flex flex-col">

            <!-- Logo -->
            <div class="px-8 py-7 border-b border-white/10">

                <h1 class="text-3xl font-bold tracking-wide">
                    EduMonitor
                </h1>

                <p class="text-sm text-blue-100 mt-1">
                    Admin Dashboard
                </p>

            </div>

            <!-- Menu -->
            <nav class="flex-1 px-5 py-6 space-y-3">

                <a
                    href="/admin/dashboard"
                    class="flex items-center px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 transition"
                >
                    Dashboard
                </a>

                <a
                    href="/admin/users"
                    class="flex items-center px-4 py-3 rounded-xl hover:bg-white/10 transition"
                >
                    Kelola User
                </a>

                <a
                    href="#"
                    class="flex items-center px-4 py-3 rounded-xl hover:bg-white/10 transition"
                >
                    Monitoring
                </a>

            </nav>

            <!-- Footer -->
            <div class="p-5 border-t border-white/10">

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full bg-[#2563EB] hover:bg-[#1D4ED8] transition py-3 rounded-xl font-medium"
                >
                    Logout
                </Link>

            </div>

        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col">

            <!-- Navbar -->
            <header class="h-20 bg-white border-b border-gray-200 px-8 flex items-center justify-between">

                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Dashboard Admin
                    </h2>

                    <p class="text-sm text-gray-500">
                        Sistem Monitoring Siswa
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

                    <div class="w-12 h-12 rounded-full bg-[#1E3A5F]"></div>

                </div>

            </header>

            <!-- Content -->
            <main class="flex-1 p-8">
                <slot />
            </main>

        </div>

    </div>
</template>