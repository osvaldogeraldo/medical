<template>
    <AdminLayout>
        <div class="dashboard-container">
            <div class="page-header">
                <h1>Dashboard</h1>
                <p>Welcome to your admin dashboard</p>
            </div>

            <div class="content-area">
                <!-- Seu conteúdo aqui -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards, gráficos, etc -->
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
</script>

<style scoped>
.dashboard-container {
    max-width: 100%;
}

.page-header {
    margin-bottom: 24px;
}

.page-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}

.page-header p {
    color: #64748b;
}
</style>
