<script setup>
import { ref, provide, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppHeader from './../Components/Admin/Header.vue'
import AppSidebar from './../Components/Admin/Sidebar.vue'
import AppFooter from './../Components/Admin/Footer.vue'

const page = usePage()

const sidebarOpen = ref(true)

// Obter dados globais
const authUser = computed(() => page.props.auth.user)
const flash = computed(() => page.props.flash)

// Disponibiliza sidebar para os filhos
provide('sidebarExpanded', sidebarOpen)

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}
</script>

<template>
    <div class="app-wrapper">
        <AppHeader :user="authUser" @toggle-sidebar="toggleSidebar" />

        <div class="main-container">
            <AppSidebar :user="authUser" :is-open="sidebarOpen" @toggle="toggleSidebar" />

            <main class="main-content">
                <slot />
            </main>
        </div>

        <AppFooter />
    </div>

    <!-- Flash messages -->
    <div v-if="flash.success" class="alert alert-success fixed-top m-4">
        {{ flash.success }}
    </div>
    <div v-if="flash.error" class="alert alert-danger fixed-top m-4">
        {{ flash.error }}
    </div>
</template>
