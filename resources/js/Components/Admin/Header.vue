<template>
  <div class="app-header d-flex align-items-center">
    <!-- Toggle buttons -->
    <div class="d-flex">
      <button class="toggle-sidebar" @click="toggleSidebar">
        <i class="ri-menu-line"></i>
      </button>
      <button class="pin-sidebar" @click="pinSidebar">
        <i class="ri-menu-line"></i>
      </button>
    </div>

    <!-- App brand -->
    <div class="app-brand ms-3">
      <Link href="/" class="d-lg-block d-none">
        <!-- <img src="/template/images/logo.svg" class="logo" alt="Admin Logo"> -->
      </Link>
      <Link href="/" class="d-lg-none d-md-block">
        <!-- <img src="/template/images/logo-sm.svg" class="logo" alt="Admin Logo Small"> -->
      </Link>
    </div>

    <!-- Header actions -->
    <div class="header-actions ms-auto d-flex align-items-center gap-3">
      <!-- Search -->
      <div class="search-container d-lg-block d-none">
        <input
          type="text"
          class="form-control"
          placeholder="Search"
          v-model="searchQuery"
          @keyup.enter="performSearch"
        >
        <i class="ri-search-line"></i>
      </div>

      <!-- Dropdowns (opcional, podem ser mockados) -->
      <NotificationsDropdown />
      <AlertsDropdown />
      <MessagesDropdown />

      <!-- User settings mock -->
      <div class="dropdown ms-2">
        <a class="dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
          <div class="avatar-box">
            {{ userInitials }}
            <span class="status busy"></span>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end shadow-lg">
          <div class="px-3 py-2">
            <span class="small">Admin</span>
            <h6 class="m-0">Osvaldo</h6> <!-- Nome fixo enquanto não há login -->
          </div>
          <div class="mx-3 my-2 d-grid">
            <Link href="#" class="btn btn-danger">Logout</Link> <!-- Mock -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import NotificationsDropdown from './NotificationsDropdown.vue'
import AlertsDropdown from './AlertsDropdown.vue'
import MessagesDropdown from './MessagesDropdown.vue'

export default {
  name: 'AppHeader',
  components: { Link, NotificationsDropdown, AlertsDropdown, MessagesDropdown },
  data() {
    return {
      searchQuery: ''
    }
  },
  computed: {
    userInitials() {
      // placeholder fixo enquanto não tem login
      return 'OG'
    }
  },
  methods: {
    toggleSidebar() {
      document.body.classList.toggle('sidebar-collapsed')
    },
    pinSidebar() {
      document.body.classList.toggle('sidebar-pinned')
    },
    performSearch() {
      if (this.searchQuery.trim()) {
        alert(`Search for: ${this.searchQuery}`) // Mock da busca
      }
    }
  }
}
</script>
