<template>
    <div class="card p-4 shadow-sm mb-4">
        <h5 class="mb-3">Itens da Cotação</h5>

        <!-- Formulário de novo item -->
        <div v-if="!readonly" class="row gy-3">
            <div class="col-md-6">
                <label class="form-label">Item</label>
                <select v-model="item.item_id" class="form-select" :class="{ 'is-invalid': errors.item_id }">
                    <option :value="null">Selecione...</option>
                    <option v-for="i in items" :key="i.id" :value="i.id">{{ i.name }}</option>
                </select>
                <div class="invalid-feedback" v-if="errors.item_id">{{ errors.item_id }}</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Quantidade</label>
                <input type="number" v-model="item.quantity" class="form-control" min="1"
                    :class="{ 'is-invalid': errors.quantity }" />
                <div class="invalid-feedback" v-if="errors.quantity">{{ errors.quantity }}</div>
            </div>

            <div class="col-md-12">
                <label class="form-label">Descrição</label>
                <textarea v-model="item.description" class="form-control" rows="2"
                    :class="{ 'is-invalid': errors.description }"></textarea>
                <div class="invalid-feedback" v-if="errors.description">{{ errors.description }}</div>
            </div>

            <div class="col-md-12 d-flex justify-content-end">
                <button class="btn btn-primary" type="button" @click="addItem">+ Adicionar Item</button>
            </div>
        </div>

        <!-- Tabela de itens adicionados -->
        <BasicDataTable title="Itens Adicionados" :columns="columns" :items="modelItens" :pagination="null">
            <template #item_id="{ value }">
                {{ getItemName(value) }}
            </template>

            <template #actions="{ index }">
                <button v-if="!readonly" class="btn btn-sm btn-danger" @click="removeItem(index)">Remover</button>
            </template>
        </BasicDataTable>
    </div>
</template>

<script setup>
import { reactive, computed } from 'vue';
import BasicDataTable from '@/components/BaseTable.vue';

// Props e emit
const props = defineProps({
    items: Array,
    itens: Array,
    readonly: Boolean
});
const emit = defineEmits(['update:itens']);

// Bind do v-model:itens
const modelItens = computed({
    get: () => props.itens,
    set: (val) => emit('update:itens', val)
});

// Estado do novo item
const item = reactive({
    item_id: null,
    quantity: 1,
    description: ''
});

// Erros do formulário de novo item
const errors = reactive({
    item_id: null,
    quantity: null,
    description: null
});

// Colunas da tabela
const columns = [
    { label: 'Item', field: 'item_id', slot: 'item_id' },
    { label: 'Quantidade', field: 'quantity' },
    { label: 'Descrição', field: 'description' }
];

// Nome do item baseado no id
function getItemName(id) {
    const found = props.items.find(i => i.id === id);
    return found ? found.name : '—';
}

// Validação do formulário de novo item
function validateNewItem() {
    errors.item_id = !item.item_id ? 'Item obrigatório.' : null;
    errors.quantity = !item.quantity || item.quantity < 1 ? 'Quantidade inválida.' : null;
    errors.description = !item.description ? 'Descrição obrigatória.' : null;
    return !errors.item_id && !errors.quantity && !errors.description;
}

// Adicionar item à lista
function addItem() {
    if (!validateNewItem()) return;
    const newItens = [...modelItens.value];
    newItens.push({ ...item });
    modelItens.value = newItens;
    Object.assign(item, { item_id: null, quantity: 1, description: '' });
}

// Remover item da lista
function removeItem(index) {
    const newItens = [...modelItens.value];
    newItens.splice(index, 1);
    modelItens.value = newItens;
}

// Expor função para validação do step
defineExpose({ validateStep });

function validateStep() {
    if (props.readonly) return true;
    if (!modelItens.value.length) {
        window.toast?.error?.('Adicione pelo menos um item à cotação.');
        return false;
    }
    return true;
}
</script>
