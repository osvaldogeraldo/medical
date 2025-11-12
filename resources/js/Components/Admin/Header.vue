<template>
    <header class="app-header">
        <div class="header-content">
            <!-- Left Section -->
            <div class="header-left">
                <button class="header-toggle" @click="$emit('toggle-sidebar')">
                    <font-awesome-icon :icon="['fas', 'bars']" />
                </button>

                <div class="header-brand">
                    <h1 class="brand-text">{{ $page.props.app.name }}</h1>
                </div>
            </div>

            <!-- Center Section - Search -->
            <div class="header-center">
                <div class="search-box">
                    <font-awesome-icon :icon="['fas', 'search']" class="search-icon" />
                    <input type="text" v-model="searchQuery" @keyup.enter="performSearch"
                        placeholder="Search patients, doctors, appointments..." class="search-input">
                </div>
            </div>

            <!-- Right Section -->
            <div class="header-right">
                <!-- Notifications -->
                <div class="header-action">
                    <button class="action-btn" @click="toggleNotifications">
                        <font-awesome-icon :icon="['fas', 'bell']" />
                        <span v-if="notificationsCount > 0" class="badge">{{ notificationsCount }}</span>
                    </button>

                    <div v-if="showNotifications" class="dropdown-panel notifications-panel">
                        <div class="dropdown-header">
                            <h3>Notifications</h3>
                            <button @click="markAllAsRead" class="text-link">Mark all as read</button>
                        </div>
                        <div class="dropdown-content">
                            <div v-for="notification in notifications" :key="notification.id" class="notification-item"
                                :class="{ 'unread': !notification.read }">
                                <div class="notification-icon">
                                    <font-awesome-icon :icon="notification.icon" />
                                </div>
                                <div class="notification-content">
                                    <p class="notification-text">{{ notification.text }}</p>
                                    <span class="notification-time">{{ notification.time }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="header-action">
                    <button class="action-btn" @click="toggleMessages">
                        <font-awesome-icon :icon="['fas', 'envelope']" />
                        <span v-if="messagesCount > 0" class="badge">{{ messagesCount }}</span>
                    </button>

                    <div v-if="showMessages" class="dropdown-panel messages-panel">
                        <div class="dropdown-header">
                            <h3>Messages</h3>
                        </div>
                        <div class="dropdown-content">
                            <div v-for="message in messages" :key="message.id" class="message-item">
                                <div class="message-avatar">
                                    <img :src="message.avatar" :alt="message.sender">
                                </div>
                                <div class="message-content">
                                    <div class="message-sender">{{ message.sender }}</div>
                                    <p class="message-preview">{{ message.preview }}</p>
                                    <span class="message-time">{{ message.time }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="header-action user-menu">
                    <!-- ░░ Botão do Usuário ░░ -->
                    <button class="user-btn" @click="toggleUserMenu">
                        <div class="user-avatar">
                            <img v-if="$page.props.auth.user.avatar" :src="$page.props.auth.user.avatar"
                                :alt="$page.props.auth.user.name" />
                            <font-awesome-icon v-else :icon="['fas', 'user-circle']" class="text-gray-400 text-xl" />
                        </div>

                        <span class="user-name">{{ $page.props.auth.user.name }}</span>
                        <font-awesome-icon :icon="['fas', 'chevron-down']" class="user-arrow" />
                    </button>

                    <!-- ░░ Dropdown do Usuário ░░ -->
                    <div v-if="showUserMenu" class="dropdown-panel user-panel">
                        <div class="user-info">
                            <div class="user-avatar-large">
                                <img v-if="$page.props.auth.user.avatar" :src="$page.props.auth.user.avatar"
                                    :alt="$page.props.auth.user.name" />
                                <font-awesome-icon v-else :icon="['fas', 'user-circle']"
                                    class="text-gray-400 text-3xl" />
                            </div>

                            <div class="user-details">
                                <h4>{{ $page.props.auth.user.username }}</h4>
                                <span class="user-role">{{ $page.props.auth.user.roles[0] }}</span>
                            </div>
                        </div>

                        <div class="dropdown-menu">
                            <a href="#" class="menu-item">
                                <font-awesome-icon :icon="['fas', 'user']" />
                                <span>Profile</span>
                            </a>
                            <a href="#" class="menu-item">
                                <font-awesome-icon :icon="['fas', 'cog']" />
                                <span>Settings</span>
                            </a>
                            <a href="#" class="menu-item">
                                <font-awesome-icon :icon="['fas', 'question-circle']" />
                                <span>Help & Support</span>
                            </a>

                            <div class="menu-divider"></div>

                            <a href="#" class="menu-item text-danger" @click="logout">
                                <font-awesome-icon :icon="['fas', 'sign-out-alt']" />
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>
</template>

<script>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'

export default {
    name: 'AppHeader',
    emits: ['toggle-sidebar'],
    setup() {
        const searchQuery = ref('')
        const showNotifications = ref(false)
        const showMessages = ref(false)
        const showUserMenu = ref(false)

        const notifications = reactive([
            { id: 1, text: 'New appointment scheduled with Dr. Smith', time: '5 min ago', read: false, icon: ['fas', 'calendar-check'] },
            { id: 2, text: 'Patient John Doe completed his checkup', time: '1 hour ago', read: false, icon: ['fas', 'user-check'] },
            { id: 3, text: 'System backup completed successfully', time: '2 hours ago', read: true, icon: ['fas', 'database'] }
        ])

        const messages = reactive([
            { id: 1, sender: 'Dr. Sarah Wilson', preview: 'Regarding the patient consultation...', time: '10 min ago', avatar: '/api/placeholder/32/32' },
            { id: 2, sender: 'Nursing Staff', preview: 'Shift schedule update for next week...', time: '1 hour ago', avatar: '/api/placeholder/32/32' }
        ])

        const notificationsCount = computed(() =>
            notifications.filter(n => !n.read).length
        )

        const messagesCount = computed(() => messages.length)

        const performSearch = () => {
            if (searchQuery.value.trim()) {
                console.log('Searching for:', searchQuery.value)
                // Implement search functionality
            }
        }

        const toggleNotifications = () => {
            showNotifications.value = !showNotifications.value
            showMessages.value = false
            showUserMenu.value = false
        }

        const toggleMessages = () => {
            showMessages.value = !showMessages.value
            showNotifications.value = false
            showUserMenu.value = false
        }

        const toggleUserMenu = () => {
            showUserMenu.value = !showUserMenu.value
            showNotifications.value = false
            showMessages.value = false
        }

        const markAllAsRead = () => {
            notifications.forEach(notification => {
                notification.read = true
            })
        }

        const logout = () => {
            console.log('Logging out...')
            // Implement logout logic
        }

        // Close dropdowns when clicking outside
        const handleClickOutside = (event) => {
            if (!event.target.closest('.header-action')) {
                showNotifications.value = false
                showMessages.value = false
                showUserMenu.value = false
            }
        }

        onMounted(() => {
            document.addEventListener('click', handleClickOutside)
        })

        onUnmounted(() => {
            document.removeEventListener('click', handleClickOutside)
        })

        return {
            searchQuery,
            showNotifications,
            showMessages,
            showUserMenu,
            notifications,
            messages,
            notificationsCount,
            messagesCount,
            performSearch,
            toggleNotifications,
            toggleMessages,
            toggleUserMenu,
            markAllAsRead,
            logout
        }
    }
}
</script>

<style scoped>
.app-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 64px;
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    z-index: 1001;
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 100%;
    padding: 0 20px;
    max-width: 100%;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.header-toggle {
    background: none;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s;
}

.header-toggle:hover {
    background: #f1f5f9;
    color: #334155;
}

.brand-text {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #3b82f6;
}

.header-center {
    flex: 1;
    max-width: 400px;
    margin: 0 40px;
}

.search-box {
    position: relative;
    width: 100%;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-input {
    width: 100%;
    padding: 10px 12px 10px 40px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
    font-size: 14px;
    transition: all 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.header-right {
    display: flex;
    align-items: center;
    gap: 8px;
}

.header-action {
    position: relative;
}

.action-btn,
.user-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: none;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.action-btn:hover,
.user-btn:hover {
    background: #f1f5f9;
    color: #334155;
}

.badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-name {
    font-size: 14px;
    font-weight: 500;
    color: #1e293b;
}

.user-arrow {
    font-size: 12px;
    transition: transform 0.2s;
}

.user-menu:hover .user-arrow {
    transform: rotate(180deg);
}

/* Dropdown Panels */
.dropdown-panel {
    position: absolute;
    top: 100%;
    right: 0;
    width: 320px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    margin-top: 8px;
    z-index: 1002;
}

.dropdown-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
}

.dropdown-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
}

.text-link {
    background: none;
    border: none;
    color: #3b82f6;
    font-size: 14px;
    cursor: pointer;
    text-decoration: none;
}

.text-link:hover {
    text-decoration: underline;
}

.dropdown-content {
    max-height: 300px;
    overflow-y: auto;
}

/* Notifications */
.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s;
}

.notification-item:hover {
    background: #f8fafc;
}

.notification-item.unread {
    background: #eff6ff;
}

.notification-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
}

.notification-text {
    margin: 0 0 4px 0;
    font-size: 14px;
    color: #334155;
    line-height: 1.4;
}

.notification-time {
    font-size: 12px;
    color: #94a3b8;
}

/* Messages */
.message-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s;
}

.message-item:hover {
    background: #f8fafc;
}

.message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.message-content {
    flex: 1;
}

.message-sender {
    font-weight: 600;
    font-size: 14px;
    color: #1e293b;
    margin-bottom: 2px;
}

.message-preview {
    margin: 0 0 4px 0;
    font-size: 13px;
    color: #64748b;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.message-time {
    font-size: 12px;
    color: #94a3b8;
}

/* User Panel */
.user-panel {
    width: 280px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px;
    border-bottom: 1px solid #f1f5f9;
}

.user-avatar-large {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.user-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-details h4 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
}

.user-role {
    font-size: 14px;
    color: #64748b;
}

.dropdown-menu {
    padding: 8px 0;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: #475569;
    text-decoration: none;
    transition: all 0.2s;
    font-size: 14px;
}

.menu-item:hover {
    background: #f8fafc;
    color: #3b82f6;
}

.menu-item.text-danger {
    color: #ef4444;
}

.menu-item.text-danger:hover {
    background: #fef2f2;
    color: #dc2626;
}

.menu-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 8px 0;
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        padding: 0 16px;
    }

    .header-center {
        display: none;
    }

    .user-name {
        display: none;
    }

    .dropdown-panel {
        position: fixed;
        top: 64px;
        left: 0;
        right: 0;
        width: auto;
        margin: 0;
        border-radius: 0;
        border-left: none;
        border-right: none;
    }
}

@media (max-width: 480px) {
    .brand-text {
        display: none;
    }
}
</style>
