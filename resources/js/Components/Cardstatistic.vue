<template>
    <div class="card" :style="cardStyle">
        <div class="title">{{ label }}</div>
        <div class="icon">
            <i :class="icon"></i>
        </div>
        <div class="content">
            <p class="value">{{ formattedNumber }}</p>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    label: { type: String, required: true },
    number: { type: [Number, String], required: true },
    icon: { type: String, required: true },
    color: { type: String, default: '#0ea5e9' } // cor base
})

const formattedNumber = computed(() =>
    typeof props.number === 'number'
        ? new Intl.NumberFormat('pt-MZ').format(props.number)
        : props.number
)

const cardStyle = computed(() => ({
    '--grad': `${props.color}, ${props.color}99`
}))
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');

.card {
    padding: 2.5rem;
    background-image: linear-gradient(to bottom left, #e0e4e5, #f2f6f9);
    border-radius: 2rem;
    gap: 1.5rem;
    display: grid;
    grid-template:
        'title icon'
        'content content'
        'bar bar' / 1fr auto;
    font-family: system-ui, sans-serif;
    color: #444447;
    box-shadow:
        inset -2px 2px hsl(0 0 100% / 1),
        -20px 20px 40px hsl(0 0 0 / .25);
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.title {
    font-size: 1.2rem;
    grid-area: title;
    align-self: end;
    text-transform: uppercase;
    font-weight: 600;
    color: #333;
}

.icon {
    grid-area: icon;
    font-size: 3rem;
}

.icon>i {
    color: transparent;
    background: linear-gradient(to right, var(--grad));
    -webkit-background-clip: text;
    background-clip: text;
}

.content {
    grid-area: content;
}

.value {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}

.card::after {
    content: "";
    grid-area: bar;
    height: 3px;
    background-image: linear-gradient(90deg, var(--grad));
    border-radius: 2px;
    margin-top: 1rem;
}
</style>
