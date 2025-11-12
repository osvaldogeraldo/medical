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
                <h5 class="profile-name">Nick Gonzalez</h5>
                <span class="profile-role">Dept Admin</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li v-for="item in menuItems" :key="item.label" class="nav-item">
                    <a :href="item.link" class="nav-link" :class="{ 'active': activeRoute === item.link }"
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
                                <a :href="child.link" class="submenu-link"
                                    :class="{ 'active': activeRoute === child.link }"
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
import { ref, reactive, onMounted } from 'vue'

export default {
    name: 'AppSidebar',
    props: {
        isOpen: {
            type: Boolean,
            default: true
        }
    },
    emits: ['toggle', 'navigate'],
    setup(props, { emit }) {
        const activeRoute = ref('/admin/dashboard')

        const menuItems = reactive([
            {
                label: 'Dashboard',
                link: '/admin/dashboard',
                icon: ['fas', 'home'],
                expanded: false
            },
            {
                label: 'Doctors',
                link: '#',
                icon: ['fas', 'user-md'],
                expanded: false,
                children: [
                    { label: 'Doctors Dashboard', link: '/admin/doctors/dashboard', icon: ['fas', 'tachometer-alt'] },
                    { label: 'Doctors List', link: '/admin/doctors', icon: ['fas', 'list'] },
                    { label: 'Add Doctor', link: '/admin/doctors/create', icon: ['fas', 'user-plus'] }
                ]
            },
            {
                label: 'Patients',
                link: '#',
                icon: ['fas', 'heartbeat'],
                expanded: false,
                children: [
                    { label: 'Patients Dashboard', link: '/admin/patients/dashboard', icon: ['fas', 'tachometer-alt'] },
                    { label: 'Patients List', link: '/admin/patients', icon: ['fas', 'list'] },
                    { label: 'Add Patient', link: '/admin/patients/create', icon: ['fas', 'user-plus'] }
                ]
            },
            {
                label: 'Appointments',
                link: '/admin/appointments',
                icon: ['fas', 'calendar-check'],
                expanded: false
            },
            {
                label: 'Departments',
                link: '/admin/departments',
                icon: ['fas', 'building'],
                expanded: false
            },
            {
                label: 'Staff',
                link: '/admin/staff',
                icon: ['fas', 'user-nurse'],
                expanded: false
            },
            {
                label: 'Accounts',
                link: '/admin/accounts',
                icon: ['fas', 'wallet'],
                expanded: false
            },
            {
                label: 'Reports',
                link: '/admin/reports',
                icon: ['fas', 'chart-line'],
                expanded: false
            }
        ])

        const userInitials = 'NG'

        const handleNavigation = (item) => {
            if (item.children) {
                // Toggle submenu
                item.expanded = !item.expanded
            } else {
                // Navigate to page
                activeRoute.value = item.link
                emit('navigate', item.link)

                // Close all other expanded menus
                menuItems.forEach(menuItem => {
                    if (menuItem.label !== item.label) {
                        menuItem.expanded = false
                    }
                })
            }
        }

        // Set initial active route based on current URL
        onMounted(() => {
            const currentPath = window.location.pathname
            activeRoute.value = currentPath
        })

        return {
            menuItems,
            activeRoute,
            userInitials,
            handleNavigation
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
</style>
