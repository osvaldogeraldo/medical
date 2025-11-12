<template>
  <span>{{ formatted }}</span>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  date: {
    type: [String, Date],
    required: true,
  },
});

const formatted = computed(() => {
  const d = new Date(props.date);
  if (isNaN(d)) return "";
  const day = String(d.getDate()).padStart(2, "0");
  const month = String(d.getMonth() + 1).padStart(2, "0");
  const year = d.getFullYear();
  return `${day}-${month}-${year}`;
});
</script>
