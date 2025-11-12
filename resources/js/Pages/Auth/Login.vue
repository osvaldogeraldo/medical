<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const passwordVisible = ref(false);

const togglePasswordVisibility = () => {
    passwordVisible.value = !passwordVisible.value;
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
<GuestLayout>
    <Head title="Login" />

    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
            <div class="text-center mb-6">
                <img src="/assets/images/logo-dark.svg" alt="Logo" class="h-10 mx-auto mb-2">
                <h2 class="text-2xl font-semibold text-gray-700">Welcome Back</h2>
                <p class="text-gray-500 text-sm">Sign in to your account</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        placeholder="Enter your email"
                        class="mt-1 w-full"
                        v-model="form.email"
                        required
                        autofocus
                    />
                    <InputError :message="form.errors.email" class="mt-1" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" />
                    <div class="relative">
                        <TextInput
                            id="password"
                            :type="passwordVisible ? 'text' : 'password'"
                            placeholder="Enter your password"
                            class="mt-1 w-full pr-10"
                            v-model="form.password"
                            required
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600"
                            @click="togglePasswordVisibility"
                        >
                            <i :class="passwordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                        </button>
                    </div>
                    <InputError :message="form.errors.password" class="mt-1" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2">
                        <Checkbox v-model:checked="form.remember" />
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-blue-600 hover:underline"
                    >
                        Forgot password?
                    </Link>
                </div>

                <div class="space-y-3">
                    <PrimaryButton
                        type="submit"
                        :disabled="form.processing"
                        class="w-full"
                    >
                        Login
                    </PrimaryButton>

                    <Link
                        :href="route('register')"
                        class="block w-full text-center py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100"
                    >
                        Not registered? Sign Up
                    </Link>
                </div>
            </form>

            <div v-if="status" class="mt-4 text-green-600 text-sm text-center">
                {{ status }}
            </div>
        </div>
    </div>
</GuestLayout>
</template>

<style scoped>
/* Apenas ajustes de sombra e hover */
.shadow-md {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
</style>
