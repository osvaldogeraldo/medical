<template>
  <div class="col-xxl-4 col-xl-4 col-sm-6">
    <div
      :class="[
        'px-20 py-16 shadow-none radius-8 h-100',
        gradientClass,
        'left-line',
        lineColor,
        'position-relative overflow-hidden'
      ]"
    >
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
        <div>
          <span class="mb-2 fw-medium text-secondary-light text-md">{{ title }}</span>
          <h6 class="fw-semibold mb-1">{{ formattedValue }}</h6>
        </div>
        <span
          :class="[
            'w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12',
            bgClass,
            iconColor
          ]"
        >
          <i :class="icon"></i>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const {
  title,
  value,
  icon,
  bgClass,
  iconColor,
  lineColor,
  gradientClass
} = defineProps({
  title: { type: String, required: true },
  value: { type: [Number, String], default: 0 },
  icon: { type: String, required: true },
  bgClass: { type: String, default: 'bg-primary-100' },
  iconColor: { type: String, default: 'text-primary-600' },
  lineColor: { type: String, default: 'line-bg-primary' },
  gradientClass: { type: String, default: 'gradient-deep-1' },
})

const formattedValue = computed(() => {
  if (!value && value !== 0) return '0'
  return typeof value === 'number' ? value.toLocaleString() : value
})
</script>
