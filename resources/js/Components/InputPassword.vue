<script setup>
import { ref, onMounted } from "vue";

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);
const showPassword = ref(false);

onMounted(() => {
    if (input.value?.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({
    focus: () => input.value.focus(),
});

defineProps({
    placeholder: {
        type: String,
        default: "Palavra-passe",
    },
    hasError: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="position-relative mb-16">
        <span
            class="icon position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
        >
            <iconify-icon icon="solar:lock-password-outline" />
        </span>

        <input
            class="form-control h-56-px bg-neutral-50 radius-12 ps-5 pe-5"
            v-model="model"
            ref="input"
            :placeholder="placeholder"
            :type="showPassword ? 'text' : 'password'"
            :class="{ 'is-invalid': hasError }"
        />

        <span
            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted"
            @click="showPassword = !showPassword"
            style="z-index: 2"
        >
            <iconify-icon
                :icon="showPassword ? 'mdi:eye-off-outline' : 'mdi:eye-outline'"
            />
        </span>
    </div>
</template>
