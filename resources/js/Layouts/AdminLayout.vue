<script setup>
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">

        <!-- Sidebar -->
        <aside class="w-64 bg-blue-900 text-white p-5">
            <h1 class="text-2xl font-bold mb-8">
                Admin Panel
            </h1>

            <nav class="space-y-4">
                <a href="/admin/dashboard" class="block hover:text-gray-300">
                    Dashboard
                </a>

                <a href="#" class="block hover:text-gray-300">
                    Kelola User
                </a>

                <a href="#" class="block hover:text-gray-300">
                    Monitoring
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <slot />
        </main>

    </div>
</template>