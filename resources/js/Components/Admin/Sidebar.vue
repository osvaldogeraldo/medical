<template>
    <aside :class="[
        'sidebar',
        { 'sidebar-open': isOpen, 'sidebar-collapsed': !isOpen }
    ]">

        <!-- User Profile -->
        <div class="sidebar-profile" v-if="isOpen">
            <div class="profile-avatar">
                <div class="avatar-placeholder">
                    {{ userInitials }}
                </div>
            </div>
            <div class="profile-info">
                <h5 class="profile-name">{{ $page.props.auth.user.name }}</h5>
                <span class="profile-role">{{ $page.props.auth.user.roles[0] }}</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li v-for="item in menuItems" :key="item.label" class="nav-item">
                    <Link v-if="item.route" :href="route(item.route)" class="nav-link" :class="{ 'active': isActive(item) }">
                        <span class="nav-icon">
                            <font-awesome-icon :icon="item.icon" />
                        </span>
                        <span class="nav-label" v-if="isOpen">{{ item.label }}</span>
                        <span v-if="item.children && isOpen" class="nav-arrow" :class="{ 'rotated': item.expanded }">
                            <font-awesome-icon :icon="['fas', 'chevron-down']" />
                        </span>
                    </Link>
                    <a v-else :href="item.link" class="nav-link" :class="{ 'active': isActive(item) }"
                       @click.prevent="handleNavigation(item)">
                        <span class="nav-icon">
                            <font-awesome-icon :icon="item.icon" />
                        </span>
                        <span class="nav-label" v-if="isOpen">{{ item.label }}</span>
                        <span v-if="item.children && isOpen" class="nav-arrow" :class="{ 'rotated': item.expanded }">
                            <font-awesome-icon :icon="['fas', 'chevron-down']" />
                        </span>
                    </a>

                    <!-- Submenu -->
                    <transition name="slide-down">
                        <ul v-if="item.children && item.expanded && isOpen" class="submenu">
                            <li v-for="child in item.children" :key="child.label">
                                <Link v-if="child.route" :href="route(child.route)" class="submenu-link"
                                    :class="{ 'active': isChildActive(child) }">
                                    <span class="submenu-icon">
                                        <font-awesome-icon :icon="child.icon" />
                                    </span>
                                    <span class="submenu-label">{{ child.label }}</span>
                                </Link>
                                <a v-else :href="child.link" class="submenu-link"
                                    :class="{ 'active': isChildActive(child) }"
                                    @click.prevent="handleNavigation(child)">
                                    <span class="submenu-icon">
                                        <font-awesome-icon :icon="child.icon" />
                                    </span>
                                    <span class="submenu-label">{{ child.label }}</span>
                                </a>
                            </li>
                        </ul>
                    </transition>
                </li>
            </ul>
        </nav>
    </aside>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'

export default {
    name: 'AppSidebar',
    components: {
        Link
    },
    props: {
        isOpen: {
            type: Boolean,
            default: true
        }
    },
    emits: ['toggle', 'navigate'],
    setup(props, { emit }) {
        const currentRouteName = ref(route().current())

        const menuItems = reactive([
            {
                label: 'Dashboard',
                route: 'dashboard',
                icon: ['fas', 'home'],
                expanded: false
            },
            {
                label: 'Categorias',
                link: '#',
                icon: ['fas', 'tags'],
                expanded: false,
                children: [
                    { label: 'Listar Categorias', route: 'categories.index', icon: ['fas', 'list'] },
                    { label: 'Adicionar Categoria', route: 'categories.create', icon: ['fas', 'plus'] },
                    { label: 'Importar Categorias', route: 'categories.import', icon: ['fas', 'file-import'] }
                ]
            },
            {
                label: 'Doctors',
                link: '#',
                icon: ['fas', 'user-md'],
                expanded: false,
                children: [
                    { label: 'Doctors Dashboard', link: '#', icon: ['fas', 'tachometer-alt'] },
                    { label: 'Doctors List', link: '#', icon: ['fas', 'list'] },
                    { label: 'Add Doctor', link: '#', icon: ['fas', 'user-plus'] }
                ]
            },
            {
                label: 'Patients',
                link: '#',
                icon: ['fas', 'heartbeat'],
                expanded: false,
                children: [
                    { label: 'Patients Dashboard', link: '#', icon: ['fas', 'tachometer-alt'] },
                    { label: 'Patients List', link: '#', icon: ['fas', 'list'] },
                    { label: 'Add Patient', link: '#', icon: ['fas', 'user-plus'] }
                ]
            },
            {
                label: 'Appointments',
                link: '#',
                icon: ['fas', 'calendar-check'],
                expanded: false
            },
            {
                label: 'Departments',
                link: '#',
                icon: ['fas', 'building'],
                expanded: false
            },
            {
                label: 'Staff',
                link: '#',
                icon: ['fas', 'user-nurse'],
                expanded: false
            },
            {
                label: 'Accounts',
                link: '#',
                icon: ['fas', 'wallet'],
                expanded: false
            },
            {
                label: 'Reports',
                link: '#',
                icon: ['fas', 'chart-line'],
                expanded: false
            }
        ])

        // Computed para as iniciais do usuário
        const userInitials = computed(() => {
            const name = window?.$page?.props?.auth?.user?.name || 'NG'
            return name
                .split(' ')
                .map(part => part.charAt(0))
                .join('')
                .toUpperCase()
                .substring(0, 2)
        })

        const isActive = (item) => {
            if (item.children) {
                // Para menus com children, verifica se algum filho está ativo
                return item.children.some(child => isChildActive(child))
            }
            if (item.route) {
                return route().current() === item.route
            }
            return false
        }

        const isChildActive = (child) => {
            if (child.route) {
                return route().current() === child.route
            }
            return false
        }

        const handleNavigation = (item) => {
            console.log('Navigation clicked:', item)

            if (item.children) {
                // Toggle submenu apenas para itens com children
                item.expanded = !item.expanded
                console.log('Toggled submenu for:', item.label, 'expanded:', item.expanded)
            } else if (item.route) {
                // Navega usando Inertia apenas se tiver uma rota definida
                console.log('Navigating to route:', item.route)
                router.visit(route(item.route))

                // Atualiza a rota atual
                currentRouteName.value = route().current()

                // Fecha todos os outros menus expandidos
                menuItems.forEach(menuItem => {
                    if (menuItem.label !== item.label && menuItem.children) {
                        menuItem.expanded = false
                    }
                })
            } else {
                console.log('No route defined for:', item.label)
            }
        }

        // Define o menu ativo baseado na rota atual
        onMounted(() => {
            currentRouteName.value = route().current()
            console.log('Current route:', currentRouteName.value)

            // Expande o menu pai se algum filho estiver ativo
            menuItems.forEach(item => {
                if (item.children) {
                    item.expanded = item.children.some(child => {
                        if (child.route) {
                            const isActive = route().current() === child.route
                            if (isActive) {
                                console.log('Auto-expanding menu for active child:', child.route)
                            }
                            return isActive
                        }
                        return false
                    })
                }
            })
        })

        return {
            menuItems,
            currentRouteName,
            userInitials,
            isActive,
            isChildActive,
            handleNavigation,
            route
        }
    }
}
</script>
<style scoped>
.sidebar {
    position: fixed;
    top: 64px;
    left: 0;
    width: 240px;
    height: calc(100vh - 64px);
    background: #fff;
    border-right: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    z-index: 1000;
    overflow-y: auto;
}

.sidebar-collapsed {
    width: 64px;
}

.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
}

.sidebar-brand h3 {
    margin: 0;
    color: #3b82f6;
    font-weight: 700;
}

.sidebar-toggle {
    background: none;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s;
}

.sidebar-toggle:hover {
    background: #f1f5f9;
    color: #334155;
}

.sidebar-profile {
    padding: 20px 16px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.profile-avatar {
    width: 40px;
    height: 40px;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.profile-info {
    flex: 1;
}

.profile-name {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.profile-role {
    font-size: 12px;
    color: #64748b;
}

.sidebar-nav {
    padding: 16px 0;
}

.nav-menu {
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-item {
    position: relative;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    color: #475569;
    text-decoration: none;
    transition: all 0.2s;
    border-left: 3px solid transparent;
    cursor: pointer;
}

.nav-link:hover {
    background: #f8fafc;
    color: #3b82f6;
}

.nav-link.active {
    background: #eff6ff;
    color: #3b82f6;
    border-left-color: #3b82f6;
}

.nav-icon {
    width: 20px;
    text-align: center;
    margin-right: 12px;
    font-size: 14px;
}

.nav-label {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
}

.nav-arrow {
    transition: transform 0.2s;
    font-size: 12px;
}

.nav-arrow.rotated {
    transform: rotate(180deg);
}

.submenu {
    list-style: none;
    margin: 0;
    padding: 0;
    background: #f8fafc;
    overflow: hidden;
}

.submenu-link {
    display: flex;
    align-items: center;
    padding: 10px 16px 10px 44px;
    color: #64748b;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.2s;
    border-left: 3px solid transparent;
    cursor: pointer;
}

.submenu-link:hover {
    background: #f1f5f9;
    color: #3b82f6;
}

.submenu-link.active {
    background: #eff6ff;
    color: #3b82f6;
    border-left-color: #3b82f6;
}

.submenu-icon {
    width: 16px;
    text-align: center;
    margin-right: 8px;
    font-size: 12px;
}

/* Transitions */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
    max-height: 200px;
}

.slide-down-enter-from,
.slide-down-leave-to {
    opacity: 0;
    max-height: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar-open {
        transform: translateX(0);
    }

    .sidebar-collapsed {
        transform: translateX(-100%);
    }
}

/* Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* No CSS da sidebar */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        z-index: 1000;
    }

    .sidebar-open {
        transform: translateX(0);
    }

    /* Overlay para quando sidebar está aberta em mobile */
    .sidebar-open::before {
        content: '';
        position: fixed;
        top: 0;
        left: 240px;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: -1;
    }
}
</style>
