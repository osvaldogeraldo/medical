<template>
    <footer class="app-footer" :class="sidebarClass">
        <div class="footer-content">
            <div class="footer-left">
                <span>&copy; {{ currentYear }} Medflex Admin. All rights reserved.</span>
            </div>
            <div class="footer-right">
                <span class="footer-version">v1.0.0</span>
                <span class="footer-status" :class="systemStatus">
                    <span class="status-dot"></span>
                    System {{ systemStatus }}
                </span>
            </div>
        </div>
    </footer>
</template>

<script>
import { computed, inject, ref, onMounted } from 'vue'

export default {
    name: 'AppFooter',
    setup() {
        // Para pegar o estado da sidebar
        const sidebarExpanded = inject('sidebarExpanded', ref(true))
        const systemStatus = ref('online')

        const currentYear = computed(() => new Date().getFullYear())

        const sidebarClass = computed(() => {
            return sidebarExpanded.value ? 'sidebar-expanded' : 'sidebar-collapsed'
        })

        // Simula status do sistema
        onMounted(() => {
            setInterval(() => {
                systemStatus.value = Math.random() > 0.1 ? 'online' : 'offline'
            }, 30000)
        })

        return {
            currentYear,
            sidebarClass,
            systemStatus
        }
    }
}
</script>

<style scoped>
.app-footer {
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 16px 24px;
    margin-left: 240px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 99;
}

.app-footer.sidebar-collapsed {
    margin-left: 64px;
}

/* Footer responsivo: sumir em telas ≤ 768px */
@media (max-width: 768px) {
    .app-footer {
        display: none;
    }
}

/* Footer interno */
.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-left span {
    font-size: 14px;
    color: #64748b;
}

.footer-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.footer-version {
    font-size: 13px;
    color: #94a3b8;
}

.footer-status {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #10b981;
    /* online verde */
}

.footer-status.offline {
    color: #ef4444;
    /* offline vermelho */
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: currentColor;
    display: inline-block;
    margin-right: 6px;
}
</style>
