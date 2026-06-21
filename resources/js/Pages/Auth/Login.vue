<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login" />
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#0F3D2E] via-[#1B7F5A] to-[#A8D5C2] px-6 py-12">
        <div class="w-full max-w-sm">

            <!-- CARD LOGIN -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                <!-- LOGO + JUDUL SEJAJAR KIRI -->
                <div class="flex items-center gap-4 mb-6">
                    <img
                        src="./logo_vertikal.png"
                        class="w-16 shrink-0" />
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">
                            Selamat Datang!
                        </h1>
                        <p class="text-gray-500 mt-1 text-sm">
                            Sistem Monitoring 7 Kebiasaan Anak Indonesia Hebat
                        </p>
                    </div>
                </div>

                <div v-if="status" class="mb-4 text-sm font-medium text-[#1B7F5A] bg-[#1B7F5A]/10 rounded-lg px-4 py-3">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel
                            for="email"
                            value="Email"/>
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1.5 block w-full rounded-lg border-gray-200 focus:border-[#1B7F5A] focus:ring-[#1B7F5A]/30"
                            v-model="form.email"
                            placeholder="nama@email.com"
                            required
                            autofocus
                            autocomplete="username" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel
                            for="password"
                            value="Password" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1.5 block w-full rounded-lg border-gray-200 focus:border-[#1B7F5A] focus:ring-[#1B7F5A]/30"
                            v-model="form.password"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer select-none">
                            <Checkbox
                                name="remember"
                                v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600">
                                Ingat Saya
                            </span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm font-medium text-[#1B7F5A] hover:underline">
                            Lupa password?
                        </Link>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full mt-2 bg-[#1B7F5A] hover:bg-[#166347] disabled:opacity-60
                               text-white py-2.5 rounded-lg font-medium transition">
                        {{ form.processing ? 'Memproses...' : 'Masuk' }}
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-white/70 mt-6">
                &copy; {{ new Date().getFullYear() }} 7 Kebiasaan Anak Indonesia Hebat
            </p>
        </div>
    </div>
</template>