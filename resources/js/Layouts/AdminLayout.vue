<template>
  <div class="page-wrapper">
    <!-- Header -->
    <AppHeader :user="user" />

    <!-- Main Container -->
    <div class="main-container">
      <!-- Sidebar -->
      <AppSidebar :user="user" />

      <!-- App Container -->
      <div class="app-container">
        <!-- Page Content -->
        <slot />

        <!-- Footer -->
        <AppFooter />
      </div>
    </div>
  </div>
</template>

<script>
import AppHeader from '@/Components/Admin/Header.vue'
import AppSidebar from '@/Components/Admin/Sidebar.vue'
import AppFooter from '@/Components/Admin/Footer.vue'

export default {
  name: 'AppLayout',
  components: {
    AppHeader,
    AppSidebar,
    AppFooter
  },
  props: {
    user: {
      type: Object,
      required: true
    }
  }
}
</script>
