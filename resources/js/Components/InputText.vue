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
    default: "Nome do usuário",
  },
  hasError: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String,
    default: "text",
  },
});
</script>

<template>
  <div class="icon-field mb-16">
    <slot />
    <input
      class="form-control h-56-px bg-neutral-50 radius-12"
      v-model="model"
      ref="input"
      :placeholder="placeholder"
      :class="{ 'is-invalid': hasError }"
      :type="type"
    />
    
  </div>
</template>
