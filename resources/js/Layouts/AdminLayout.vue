<template>
    <div class="app-wrapper">
        <!-- Header -->
        <AppHeader @toggle-sidebar="toggleSidebar" />

        <!-- Main Container -->
        <div class="main-container">
            <!-- Sidebar -->
            <AppSidebar :is-open="sidebarOpen" @toggle="toggleSidebar" />

            <main class="main-content">
                <slot />
            </main>
            <!-- Content + Footer -->

        </div>
        <AppFooter />
    </div>
</template>

<script>
import { ref, provide, onMounted } from 'vue'
import AppHeader from './../Components/Admin/Header.vue'
import AppSidebar from './../Components/Admin/Sidebar.vue'
import AppFooter from './../Components/Admin/Footer.vue'

export default {
    name: 'AppLayout',
    components: { AppHeader, AppSidebar, AppFooter },
    setup() {
        const sidebarOpen = ref(true)
        const currentPage = ref('/admin/dashboard')

        // Provide sidebar state para o footer
        provide('sidebarExpanded', sidebarOpen)

        // Toggle sidebar
        const toggleSidebar = () => {
            sidebarOpen.value = !sidebarOpen.value
        }

        // Ajusta sidebar automaticamente em telas pequenas
        const checkScreenSize = () => {
            sidebarOpen.value = window.innerWidth > 768
        }

        onMounted(() => {
            checkScreenSize()
            window.addEventListener('resize', checkScreenSize)
        })

        return { sidebarOpen, currentPage, toggleSidebar }
    }
}
</script>

<style scoped>
/* Wrapper principal */
.app-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: #f8f9fa;
}

/* Container principal */
.main-container {
    display: flex;
    flex: 1;
    min-height: calc(100vh - 64px);
    /* considerando header */
}

/* Content + Footer */
.content-area {
    display: flex;
    flex-direction: column;
    flex: 1;
    transition: margin-left 0.3s;
    margin-left: 240px;
    /* default sidebar expanded */
}

.content-area.sidebar-collapsed {
    margin-left: 64px;
    /* sidebar recolhida */
}


/* Main content */
.main-content {
    flex: 1;
    padding: 20px;
}

/* Menor padding em mobile */
@media (max-width: 768px) {
    .main-content {
        padding: 12px;
    }
}
</style>
