<template>
  <div class="app-footer bg-white">
    <span>© Medflex admin {{ currentYear }}</span>
  </div>
</template>

<script>
export default {
  name: 'AppFooter',
  computed: {
    currentYear() {
      return new Date().getFullYear()
    }
  }
}
</script>
