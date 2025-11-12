<template>
  <aside class="sidebar-wrapper">
    <!-- Perfil do Usuário -->
    <div class="sidebar-profile text-center py-3">
      <img :src="profileImage" class="rounded-circle img-50 mb-2" alt="Admin">
      <h5 class="mb-0">{{ userName }}</h5>
      <p class="small mb-0">{{ userRole }}</p>
    </div>

    <!-- Menu -->
    <ul class="sidebar-menu">
      <SidebarMenuItem
        v-for="item in menuItems"
        :key="item.label"
        :label="item.label"
        :link="item.link"
        :icon="item.icon"
        :children="item.children"
      />
    </ul>
  </aside>
</template>

<script setup>
import SidebarMenuItem from './SidebarMenuItem.vue'

const userName = 'Nick Gonzalez'
const userRole = 'Dept Admin'
const profileImage = '/template/images/user6.png'

// Menu do Sidebar
const menuItems = [
  { label: 'Dashboard', link: '/dashboard', icon: 'ri-home-6-line' },
  {
    label: 'Doctors',
    link: '#',
    icon: 'ri-stethoscope-line',
    children: [
      { label: 'Doctors Dashboard', link: '/doctors/dashboard', icon: 'ri-home-5-line' },
      { label: 'Doctors List', link: '/doctors/list', icon: 'ri-list-unordered' },
      { label: 'Add Doctor', link: '/doctors/add', icon: 'ri-user-add-line' }
    ]
  },
  {
    label: 'Patients',
    link: '#',
    icon: 'ri-heart-pulse-line',
    children: [
      { label: 'Patients Dashboard', link: '/patients/dashboard', icon: 'ri-home-5-line' },
      { label: 'Patients List', link: '/patients/list', icon: 'ri-list-unordered' },
      { label: 'Add Patient', link: '/patients/add', icon: 'ri-user-add-line' }
    ]
  },
  { label: 'Appointments', link: '/appointments', icon: 'ri-dossier-line' },
  { label: 'Departments', link: '/departments', icon: 'ri-building-2-line' },
  { label: 'Staff', link: '/staff', icon: 'ri-nurse-line' },
  { label: 'Accounts', link: '/accounts', icon: 'ri-secure-payment-line' },
  { label: 'Reports', link: '/reports', icon: 'ri-file-chart-line' }
]
</script>

<style scoped>
.sidebar-wrapper {
  width: 250px;
  background: #fff;
  border-right: 1px solid #ddd;
  min-height: 100vh;
}
.sidebar-profile img {
  width: 60px;
  height: 60px;
}
.sidebar-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}
.sidebar-menu li {
  padding: 10px 15px;
}
.sidebar-menu li a {
  display: flex;
  align-items: center;
  color: #333;
  text-decoration: none;
}
.sidebar-menu li a:hover {
  background: #f0f0f0;
  border-radius: 5px;
}
.treeview-menu li a {
  padding-left: 25px;
}
</style>
