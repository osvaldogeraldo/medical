<template>
    <span :class="badgeClasses">{{ statusText }}</span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    }
});

const statusText = computed(() => {
    const statusMap = {
        'active': 'Ativo',
        'pending': 'Pendente',
        'expired': 'Expirado',
        'cancelled': 'Cancelado'
    };
    return statusMap[props.status] || props.status;
});

const badgeClasses = computed(() => {
    const base = 'px-16 py-0 rounded-pill fw-medium text-sm';
    const statusColors = {
        'active': 'bg-success-focus text-success-main',
        'pending': 'bg-warning-focus text-warning-main',
        'expired': 'bg-danger-focus text-danger-main',
        'cancelled': 'bg-secondary-focus text-secondary-main'
    };
    return `${base} ${statusColors[props.status] || 'bg-secondary-focus text-secondary-main'}`;
});
</script>
