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
<style scoped>
.app-wrapper {
    min-height: 100vh;
    background: #f8fafc;
}

.main-container {
    display: flex;
    min-height: calc(100vh - 64px);
    margin-top: 64px;
}

.main-content {
    flex: 1;
    margin-left: 240px;
    padding: 24px;
    transition: margin-left 0.3s ease;
    background: #f8fafc;
    min-height: calc(100vh - 64px);
}

.main-content.sidebar-collapsed {
    margin-left: 64px;
}

.content-wrapper {
    max-width: 100%;
    height: 100%;
}

/* Responsive */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0 !important;
        padding: 16px;
    }

    .main-content.sidebar-collapsed {
        margin-left: 0 !important;
    }
}
</style>
