<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const page = usePage()
const sidebarOpen = ref(true)
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const user = computed(() => page.props.auth?.user ?? null)
const studentProfile = computed(() => page.props.auth?.studentProfile ?? null)

const namaKelas = computed(() =>
  studentProfile.value?.school_class?.name ?? '-'
)

const persenProfil = computed(() =>
  studentProfile.value?.profile_completion ?? 0
)

const fotoUrl = computed(() =>
  user.value?.foto
    ? `/storage/${user.value.foto}`
    : 'https://i.pinimg.com/originals/22/02/f1/2202f1513fa534d5e3698ae8619d9474.jpg?nii=t'
)
</script>

<template>
  <div class="flex min-h-screen bg-[#F4F7F6]">

    <!-- ===== SIDEBAR ===== -->
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

      <!-- ===== PROFIL SISWA DI SIDEBAR ===== -->
      <div class="p-5 border-b border-white/10">

        <!-- Foto profil bulat -->
        <div class="w-14 h-14 rounded-full overflow-hidden bg-white/20 mb-3">
          <img
            :src="fotoUrl"
            alt="Foto Profil"
            class="w-full h-full object-cover"/>
        </div>

        <h2 class="font-bold text-base leading-tight">{{ user?.name }}</h2>
        <p class="text-green-200 text-xs mt-0.5">Siswa • Kelas {{ namaKelas }}</p>
        <div class="mt-3">
          <div class="flex justify-between text-xs text-green-200 mb-1">
            <span>Kelengkapan Profil</span>
            <span>{{ persenProfil }}%</span>
          </div>
          <div class="h-1.5 rounded-full bg-white/10 overflow-hidden">
            <div class="h-full bg-yellow-400 rounded-full" :style="{ width: persenProfil + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- ===== MENU NAVIGASI ===== -->
      <nav class="flex-1 p-4 space-y-1">
        <Link
          href="/siswa/dashboard"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition text-sm"
          :class="$page.url.startsWith('/siswa/dashboard') ? 'bg-white/15 font-semibold' : ''">
          Dashboard
        </Link>

        <Link
          href="/siswa/kegiatan"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition text-sm"
          :class="$page.url.startsWith('/siswa/kegiatan') ? 'bg-white/15 font-semibold' : ''">
          Catat Kegiatan
        </Link>

        <Link
          href="/siswa/riwayat"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition text-sm"
          :class="$page.url.startsWith('/siswa/riwayat') ? 'bg-white/15 font-semibold' : ''">
          Riwayat Kegiatan
        </Link>

        <Link
          href="/siswa/profile"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition text-sm"
          :class="$page.url.startsWith('/siswa/profile') ? 'bg-white/15 font-semibold' : ''">
          Profil Saya
        </Link>
      </nav>

      <!-- ===== TOMBOL KELUAR ===== -->
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

    <div
      v-if="sidebarOpen"
      @click="toggleSidebar"
      class="fixed inset-0 bg-black/20 z-20 lg:hidden">
    </div>

    <!-- ===== AREA KONTEN UTAMA ===== -->
    <div
      class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
      :class="sidebarOpen ? 'ml-64' : 'ml-0'">

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
          <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-200 cursor-pointer">
            <img
              @click="$inertia.visit('/siswa/profile')"
              :src="fotoUrl"
              alt="Avatar" 
              class="w-full h-full object-cover"/>
          </div>
        </div>
      </header>

      <!-- ===== SLOT KONTEN HALAMAN ===== -->
      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>
  </div>
</template>