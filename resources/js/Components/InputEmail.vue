<script setup>
import { onMounted, ref } from "vue";

const model = defineModel({
  type: String,
  required: true,
});

const input = ref(null);

onMounted(() => {
  if (input.value.hasAttribute("autofocus")) {
    input.value.focus();
  }
});

defineExpose({ focus: () => input.value.focus() });
defineProps({
placeholder: {
    type: String,
    default: "voce@exemplo.com",
  },
  hasError: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});
</script>

<template>
  <div class="icon-field mb-16">
    <span class="icon top-50 translate-middle-y">
      <iconify-icon icon="mage:email"></iconify-icon>
    </span>
    <input
      class="form-control h-56-px bg-neutral-50 radius-12"
      v-model="model"
      ref="input"
      :placeholder="placeholder"
      :class="{ 'is-invalid': hasError }"
      :disabled="disabled"
    />
  </div>
</template>
