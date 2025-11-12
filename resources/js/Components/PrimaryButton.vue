<template>
  <button
    class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32"
    :disabled="processing"
  >
    <span v-if="processing">
      <span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>
      <span class="ms-2 text-white">Processando...</span>
    </span>
    <slot v-else />
  </button>
</template>

<script setup>
defineProps({
  processing: Boolean
});
</script>
