<template>
  <div class="col-xl-4 col-md-6">
    <div class="card p-3 shadow-2 radius-8 h-100">
      <div class="card-body p-0">
        <div class="d-flex align-items-center gap-3 mb-3">
          <span :class="`w-48-px h-48-px bg-${color}-100 text-white d-flex justify-content-center align-items-center rounded-circle`">
            <i :class="icon" class="fs-5" />
          </span>
          <div>
            <h6 class="fw-semibold mb-1">{{ value?.toLocaleString() ?? 0 }}</h6>
            <span class="text-muted text-sm">{{ label }}</span>
          </div>
        </div>
        <p class="text-sm mb-0">
          <span :class="`text-${color}-600 fw-medium`">{{ value?.toLocaleString() ?? 0 }}</span> {{ description }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  icon: String,
  color: String, // Exemplo: 'success', 'primary', 'lilac'
  value: Number,
  label: String,
  description: String,
});
</script>
