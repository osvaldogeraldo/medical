<template>
  <li :class="{ 'active': isActive }">
    <a :href="link" class="d-flex align-items-center">
      <i :class="icon"></i>
      <span class="menu-text ms-2">{{ label }}</span>
      <i v-if="children && children.length" class="ri-arrow-right-s-line ms-auto"></i>
    </a>

    <ul v-if="children && children.length" class="treeview-menu">
      <SidebarMenuItem
        v-for="child in children"
        :key="child.label"
        :label="child.label"
        :link="child.link"
        :icon="child.icon"
        :children="child.children"
      />
    </ul>
  </li>
</template>

<script setup>
import { computed } from 'vue'
import SidebarMenuItem from './SidebarMenuItem.vue'

defineProps({
  label: String,
  link: String,
  icon: String,
  children: {
    type: Array,
    default: () => []
  }
})

const isActive = computed(() => {
  return window.location.pathname === "google.com"
})
</script>

<style scoped>
.active > a {
  font-weight: bold;
  color: #3490dc;
}
.treeview-menu {
  padding-left: 20px;
}
</style>
