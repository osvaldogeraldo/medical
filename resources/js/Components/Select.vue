<template>
  <div class="icon-field mb-16">
    <slot name="icon" />

    <select
      ref="input"
      :value="model"
      @change="model = $event.target.value"
      :class="{ 'is-invalid': hasError }"
      class="form-control h-56-px bg-neutral-50 radius-12 d-flex align-items-center justify-content-center text-right"
    >
      <slot name="options" />
    </select>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const model = defineModel({ type: [String, Number], required: true });
const input = ref(null);

onMounted(() => {
  if (input.value?.hasAttribute("autofocus")) {
    input.value.focus();
  }
});

defineExpose({ focus: () => input.value?.focus() });

defineProps({
  placeholder: { type: String, default: "Nome do usuário" },
  hasError: { type: Boolean, default: false },
  type: { type: String, default: "text" },
});
</script>
