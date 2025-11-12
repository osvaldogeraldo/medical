<template>
    <div class="card p-4 shadow-sm mb-4">
        <h5 class="mb-3">Dados da Cotação</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Referência</label>
                <input v-model="model.reference" class="form-control" :readonly="readonly"
                    :class="{ 'is-invalid': errors.reference }" />
                <div class="invalid-feedback" v-if="errors.reference">{{ errors.reference }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Título</label>
                <input v-model="model.title" class="form-control" :readonly="readonly"
                    :class="{ 'is-invalid': errors.title }" />
                <div class="invalid-feedback" v-if="errors.title">{{ errors.title }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Departamento</label>
                <select v-model="model.department_id" class="form-select" :disabled="readonly"
                    :class="{ 'is-invalid': errors.department_id }">
                    <option :value="null">Selecione...</option>
                    <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
                <div class="invalid-feedback" v-if="errors.department_id">{{ errors.department_id }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Prazo</label>
                <input type="date" v-model="model.deadline" class="form-control" :readonly="readonly"
                    :class="{ 'is-invalid': errors.deadline }" />
                <div class="invalid-feedback" v-if="errors.deadline">{{ errors.deadline }}</div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <select v-model="model.status" class="form-select" :disabled="readonly"
                    :class="{ 'is-invalid': errors.status }">
                    <option value="Aberto">Aberto</option>
                    <option value="Fechado">Fechado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
                <div class="invalid-feedback" v-if="errors.status">{{ errors.status }}</div>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Descrição</label>
                <textarea v-model="model.description" class="form-control" rows="3" :readonly="readonly"
                    :class="{ 'is-invalid': errors.description }"></textarea>
                <div class="invalid-feedback" v-if="errors.description">{{ errors.description }}</div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { computed, reactive } from 'vue';

const props = defineProps({
    modelValue: Object,
    departments: Array,
    readonly: Boolean
});

const emit = defineEmits(['update:modelValue']);

// Modelo reativo vinculado ao `v-model` do pai
const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const errors = reactive({
    reference: null,
    title: null,
    department_id: null,
    deadline: null,
    status: null,
    description: null
});

// Exporta a função de validação para o pai
defineExpose({ validateStep });

function validateStep() {
    errors.reference = !model.value.reference ? 'Referência obrigatória.' : null;
    errors.title = !model.value.title ? 'Título obrigatório.' : null;
    errors.department_id = !model.value.department_id ? 'Departamento obrigatório.' : null;
    errors.deadline = !model.value.deadline ? 'Prazo obrigatório.' : null;
    errors.status = !model.value.status ? 'Status obrigatório.' : null;
    errors.description = !model.value.description ? 'Descrição obrigatória.' : null;
    return Object.values(errors).every(e => !e);
}
</script>
