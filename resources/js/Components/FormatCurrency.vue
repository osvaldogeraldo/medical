<template>
  <span>{{ formatted }}</span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: {
    type: [Number, String],
    required: true
  },
  currency: {
    type: String,
    default: 'MZN' // Moeda padrão
  }
})

// Escolher o locale ideal para visualização com vírgula como decimal e espaço como milhar
const formatted = computed(() => {
  const val = Number(props.value)
  if (isNaN(val)) return ''

  return new Intl.NumberFormat('pt-PT', {
    style: 'currency',
    currency: props.currency,
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(val)
})
</script>
