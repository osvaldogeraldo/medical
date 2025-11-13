<template>
    <AdminLayout>
        <Toast ref="toast" />
        <div class="card shadow-lg border-none">

            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-sm" @click="goBack">
                        <i class="pi pi-arrow-left"></i>
                    </button>
                    <h2 class="h5 mb-0 fw-bold text-dark">
                        {{ isView ? 'Visualizar' : isEdit ? 'Editar' : 'Adicionar ' }} Medicamento
                    </h2>
                </div>
            </div>

            <!-- Conteúdo principal -->
            <div v-if="isEdit || isView" class="bg-white rounded-lg shadow-sm p-6">
                <!-- Modo Edit/Show - Formulário simples -->
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="space-y-4">
                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Nome *</label>
                                <InputText v-model="form.name" :disabled="isView" placeholder="Nome do medicamento"
                                    class="w-full" :class="{ 'p-invalid': errors.name }" />
                                <small class="p-error" v-if="errors.name">{{ errors.name }}</small>
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Nome Genérico</label>
                                <InputText v-model="form.generic_name" :disabled="isView" placeholder="Nome genérico"
                                    class="w-full" />
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Marca</label>
                                <InputText v-model="form.brand" :disabled="isView" placeholder="Marca" class="w-full" />
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="space-y-4">
                            <div class="field">
                                <label class="block text-sm font-medium mb-2">SKU *</label>
                                <InputText v-model="form.sku" :disabled="isView" placeholder="Código SKU" class="w-full"
                                    :class="{ 'p-invalid': errors.sku }" />
                                <small class="p-error" v-if="errors.sku">{{ errors.sku }}</small>
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Preço *</label>
                                <InputNumber v-model="form.price" :disabled="isView" mode="currency" currency="MZN"
                                    locale="pt-MZ" class="w-full" :class="{ 'p-invalid': errors.price }" />
                                <small class="p-error" v-if="errors.price">{{ errors.price }}</small>
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Preço de Custo *</label>
                                <InputNumber v-model="form.cost_price" :disabled="isView" mode="currency" currency="MZN"
                                    locale="pt-MZ" class="w-full" :class="{ 'p-invalid': errors.cost_price }" />
                                <small class="p-error" v-if="errors.cost_price">{{ errors.cost_price }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="space-y-4">
                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Categoria *</label>
                                <Dropdown v-model="form.category_id" :options="categories" option-label="name"
                                    option-value="id" :disabled="isView" placeholder="Selecione a categoria"
                                    class="w-full" :class="{ 'p-invalid': errors.category_id }" :filter="true"
                                    filter-placeholder="Pesquisar categoria..." />
                                <small class="p-error" v-if="errors.category_id">{{ errors.category_id }}</small>
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Fornecedor</label>
                                <Dropdown v-model="form.supplier_id" :options="suppliers" option-label="name"
                                    option-value="id" :disabled="isView" placeholder="Selecione o fornecedor"
                                    class="w-full" :filter="true" filter-placeholder="Pesquisar fornecedor..." />
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Quantidade em Stock *</label>
                                <InputNumber v-model="form.stock_quantity" :disabled="isView" class="w-full"
                                    :class="{ 'p-invalid': errors.stock_quantity }" />
                                <small class="p-error" v-if="errors.stock_quantity">{{ errors.stock_quantity }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segunda linha de campos -->
                <div class="row mt-4">
                    <div class="col-12 col-md-3">
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Stock Mínimo</label>
                            <InputNumber v-model="form.min_stock" :disabled="isView" class="w-full" />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Stock Máximo</label>
                            <InputNumber v-model="form.max_stock" :disabled="isView" class="w-full" />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Data de Validade</label>
                            <Calendar v-model="form.expiry_date" :disabled="isView" date-format="dd/mm/yy"
                                class="w-full" />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Número do Lote</label>
                            <InputText v-model="form.batch_number" :disabled="isView" placeholder="Número do lote"
                                class="w-full" />
                        </div>
                    </div>
                </div>

                <!-- Terceira linha de campos -->
                <div class="row mt-4">
                    <div class="col-12 col-md-6">
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Forma Farmacêutica</label>
                            <InputText v-model="form.dosage_form" :disabled="isView"
                                placeholder="Ex: Comprimido, Xarope, etc." class="w-full" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Concentração</label>
                            <InputText v-model="form.strength" :disabled="isView" placeholder="Ex: 500mg, 10mg/ml, etc."
                                class="w-full" />
                        </div>
                    </div>
                </div>

                <!-- Checkboxes -->
                <div class="d-flex flex-wrap gap-4 mt-4">
                    <div class="field-checkbox">
                        <Checkbox v-model="form.requires_prescription" :disabled="isView"
                            inputId="requires_prescription" :binary="true" />
                        <label for="requires_prescription" class="ml-2">Requer Prescrição Médica</label>
                    </div>

                    <div class="field-checkbox">
                        <Checkbox v-model="form.is_active" :disabled="isView" inputId="is_active" :binary="true" />
                        <label for="is_active" class="ml-2">Ativo</label>
                    </div>
                </div>

                <!-- Descrição -->
                <div class="field mt-4">
                    <label class="block text-sm font-medium mb-2">Descrição</label>
                    <Textarea v-model="form.description" :disabled="isView" rows="3"
                        placeholder="Descrição do medicamento..." class="w-full" />
                </div>
            </div>

            <!-- Modo Create - Com Tabs e Steps -->
            <TabView v-else v-model:activeIndex="activeTab" class="mt-4">
                <!-- Tab 1: Criação Manual com Steps -->
                <TabPanel header="Criação Manual">
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="p-stepper p-component">
                            <div class="p-stepper-nav">
                                <div class="p-stepper-list" role="tablist">
                                    <div class="p-stepper-item" :class="{ 'p-highlight': activeStep >= 0 }">
                                        <button class="p-stepper-action" @click="activeStep = 0">
                                            <span class="p-stepper-number">1</span>
                                            <span class="p-stepper-title">Informações Básicas</span>
                                        </button>
                                    </div>
                                    <div class="p-stepper-item" :class="{ 'p-highlight': activeStep >= 1 }">
                                        <button class="p-stepper-action" @click="activeStep = 1">
                                            <span class="p-stepper-number">2</span>
                                            <span class="p-stepper-title">Preços e Stock</span>
                                        </button>
                                    </div>
                                    <div class="p-stepper-item" :class="{ 'p-highlight': activeStep >= 2 }">
                                        <button class="p-stepper-action" @click="activeStep = 2">
                                            <span class="p-stepper-number">3</span>
                                            <span class="p-stepper-title">Detalhes Adicionais</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="p-stepper-panels">
                                <!-- Step 1: Informações Básicas -->
                                <div v-show="activeStep === 0" class="p-stepper-panel">
                                    <div class="p-4 p-md-6 space-y-4">
                                        <FormGroup>
                                            <div class="row g-3">
                                                <div class="col-12 col-md-6">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Nome *</label>
                                                        <InputText v-model="form.name" placeholder="Nome do medicamento"
                                                            class="w-full" :class="{ 'p-invalid': errors.name }" />
                                                        <small class="p-error" v-if="errors.name">{{ errors.name
                                                            }}</small>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Nome
                                                            Genérico</label>
                                                        <InputText v-model="form.generic_name"
                                                            placeholder="Nome genérico" class="w-full" />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Marca</label>
                                                        <InputText v-model="form.brand" placeholder="Marca"
                                                            class="w-full" />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">SKU *</label>
                                                        <InputText v-model="form.sku" placeholder="Código SKU"
                                                            class="w-full" :class="{ 'p-invalid': errors.sku }" />
                                                        <small class="p-error" v-if="errors.sku">{{ errors.sku
                                                            }}</small>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Categoria
                                                            *</label>
                                                        <Dropdown v-model="form.category_id" :options="categories"
                                                            optionLabel="name" optionValue="id"
                                                            placeholder="Selecione a categoria" class="w-full"
                                                            :class="{ 'p-invalid': errors.category_id }" :filter="true"
                                                            filterPlaceholder="Pesquisar categoria..." />
                                                        <small class="p-error" v-if="errors.category_id">{{
                                                            errors.category_id
                                                        }}</small>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Fornecedor</label>
                                                        <Dropdown v-model="form.supplier_id" :options="suppliers"
                                                            optionLabel="name" optionValue="id"
                                                            placeholder="Selecione o fornecedor" class="w-full"
                                                            :filter="true"
                                                            filterPlaceholder="Pesquisar fornecedor..." />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="field mt-4">
                                                <label class="block text-sm font-medium mb-2">Descrição</label>
                                                <Textarea v-model="form.description" rows="3"
                                                    placeholder="Descrição do medicamento..." class="w-full" />
                                            </div>
                                        </FormGroup>
                                    </div>

                                    <div class="flex justify-end p-4 p-md-6 border-top">
                                        <Button label="Próximo" icon="pi pi-arrow-right" iconPos="right"
                                            @click="goToNextStep" />
                                    </div>
                                </div>

                                <!-- Step 2: Preços e Stock -->
                                <div v-show="activeStep === 1" class="p-stepper-panel">
                                    <div class="p-4 p-md-6 space-y-4">
                                        <FormGroup>
                                            <div class="row g-3">
                                                <div class="col-12 col-md-4">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Preço *</label>
                                                        <InputNumber v-model="form.price" mode="currency" currency="MZN"
                                                            locale="pt-MZ" class="w-full"
                                                            :class="{ 'p-invalid': errors.price }" />
                                                        <small class="p-error" v-if="errors.price">{{ errors.price
                                                        }}</small>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Preço de Custo
                                                            *</label>
                                                        <InputNumber v-model="form.cost_price" mode="currency"
                                                            currency="MZN" locale="pt-MZ" class="w-full"
                                                            :class="{ 'p-invalid': errors.cost_price }" />
                                                        <small class="p-error" v-if="errors.cost_price">{{
                                                            errors.cost_price
                                                            }}</small>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Quantidade em
                                                            Stock
                                                            *</label>
                                                        <InputNumber v-model="form.stock_quantity" class="w-full"
                                                            :class="{ 'p-invalid': errors.stock_quantity }" :min="0"
                                                            showButtons buttonLayout="horizontal"
                                                            incrementButtonIcon="pi pi-plus"
                                                            decrementButtonIcon="pi pi-minus" :step="1" />
                                                        <small class="p-error" v-if="errors.stock_quantity">{{
                                                            errors.stock_quantity }}</small>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Stock
                                                            Mínimo</label>
                                                        <InputNumber v-model="form.min_stock" class="w-full" :min="0"
                                                            showButtons />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <div class="field">
                                                        <label class="block text-sm font-medium mb-2">Stock
                                                            Máximo</label>
                                                        <InputNumber v-model="form.max_stock" class="w-full" :min="0"
                                                            showButtons />
                                                    </div>
                                                </div>
                                            </div>
                                        </FormGroup>
                                    </div>

                                    <div class="d-flex p-4 p-md-6 border-top gap-2">
                                        <Button label="Voltar" icon="pi pi-arrow-left" severity="secondary"
                                            @click="goToPreviousStep" />
                                        <Button label="Próximo" icon="pi pi-arrow-right" iconPos="right"
                                            @click="goToNextStep" />
                                    </div>

                                </div>

                                <!-- Step 3: Detalhes Adicionais -->
                                <div v-show="activeStep === 2" class="p-stepper-panel">
                                    <div class="p-4 p-md-6 space-y-4">

                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <div class="field">
                                                    <label class="block text-sm font-medium mb-2">Data de
                                                        Validade</label>
                                                    <Calendar v-model="form.expiry_date" dateFormat="dd/mm/yy"
                                                        class="w-full" />
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="field">
                                                    <label class="block text-sm font-medium mb-2">Número do Lote</label>
                                                    <InputText v-model="form.batch_number" placeholder="Número do lote"
                                                        class="w-full" />
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="field">
                                                    <label class="block text-sm font-medium mb-2">Forma
                                                        Farmacêutica</label>
                                                    <InputText v-model="form.dosage_form"
                                                        placeholder="Ex: Comprimido, Xarope, etc." class="w-full" />
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="field">
                                                    <label class="block text-sm font-medium mb-2">Concentração</label>
                                                    <InputText v-model="form.strength"
                                                        placeholder="Ex: 500mg, 10mg/ml, etc." class="w-full" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-4 mt-4">
                                            <div class="field-checkbox">
                                                <Checkbox v-model="form.requires_prescription"
                                                    inputId="requires_prescription_create" :binary="true" />
                                                <label for="requires_prescription_create" class="ml-2">Requer
                                                    Prescrição Médica</label>
                                            </div>

                                            <div class="field-checkbox">
                                                <Checkbox v-model="form.is_active" inputId="is_active_create"
                                                    :binary="true" :modelValue="true" />
                                                <label for="is_active_create" class="ml-2">Ativo</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex p-4 p-md-6 border-top gap-2">
                                        <Button label="Voltar" icon="pi pi-arrow-left" severity="secondary"
                                            @click="goToPreviousStep" />
                                        <Button label="Salvar" icon="pi pi-save" :loading="loading"
                                            @click="submitForm" />
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </TabPanel>

                <!-- Tab 2: Importar Excel -->
                <TabPanel header="Importar Excel">
                    <div class="bg-white rounded-2 shadow-2 p-4 p-md-6">
                        <div class="space-y-4">
                            <!-- Dropzone -->
                            <div class="border-2 border-dashed border-surface-300 rounded-xl p-6 p-md-8 text-center bg-surface-50 hover:bg-surface-100 transition cursor-pointer"
                                @drop.prevent="handleDrop" @dragover.prevent @click="$refs.fileInput.click()"
                                :class="{ 'border-primary-500 bg-primary-50': isDragOver }">
                                <i class="pi pi-cloud-upload text-4xl text-surface-500 mb-3"></i>
                                <p class="text-surface-600 font-medium">
                                    Arraste e solte o arquivo Excel aqui
                                </p>
                                <p class="text-surface-500 text-sm mt-1">
                                    ou clique para selecionar o arquivo
                                </p>
                                <p v-if="file" class="mt-2 text-green-600 font-semibold">
                                    <i class="pi pi-file-excel mr-2"></i>{{ file.name }}
                                </p>
                                <input ref="fileInput" type="file" class="hidden" accept=".xlsx,.xls"
                                    @change="onFileChange" />
                            </div>

                            <!-- Botões de Ação -->

                            <div
                                class="d-flex flex-wrap justify-content-center justify-content-md-end align-items-center gap-3 mt-4">
                                <button type="button" class="btn btn-success d-flex align-items-center gap-2 px-3 py-2"
                                    :disabled="!file || loading" @click="submitImport">
                                    <i class="pi pi-cloud-upload"></i>
                                    <span>Importar Dados</span>
                                </button>

                                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2"
                                    @click="downloadTemplate">
                                    <i class="pi pi-download"></i>
                                    <span>Template Excel</span>
                                </button>
                            </div>

                            <!-- Resumo da Importação -->
                            <div v-if="importSummary" class="bg-surface-50 border rounded-lg p-3 p-md-4">
                                <h4 class="font-semibold text-lg mb-3">Resumo da Importação</h4>
                                <div class="row g-3 text-sm">
                                    <div class="col-12 col-md-4">
                                        <div class="text-center p-3 bg-green-100 text-green-800 rounded">
                                            <div class="text-2xl font-bold">{{ importSummary.inserted }}</div>
                                            <div>Importados com Sucesso</div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="text-center p-3 bg-red-100 text-red-800 rounded">
                                            <div class="text-2xl font-bold">{{ importSummary.failed }}</div>
                                            <div>Falhas na Importação</div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="text-center p-3 bg-blue-100 text-blue-800 rounded">
                                            <div class="text-2xl font-bold">{{ importSummary.total }}</div>
                                            <div>Total Processados</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lista de Erros -->
                                <div v-if="importSummary.errors && importSummary.errors.length" class="mt-3">
                                    <h5 class="font-medium text-red-600 mb-2">Erros encontrados:</h5>
                                    <div class="max-h-32 overflow-y-auto">
                                        <div v-for="(error, index) in importSummary.errors" :key="index"
                                            class="text-sm text-red-600 py-1 border-b border-red-100">
                                            {{ error }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </TabPanel>
            </TabView>

            <div class="row mt-4 mx-2 mb-2">
                <div class="card border-0 shadow-sm bg-light rounded-3 p-3">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2 ms-auto" v-if="!isView">
                            <button type="button" class="btn btn-outline-danger px-3" @click="goBack">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// PrimeVue Components
import Toast from 'primevue/toast'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Textarea from 'primevue/textarea'
import Dropdown from 'primevue/dropdown'
import Calendar from 'primevue/calendar'
import Checkbox from 'primevue/checkbox'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'

const props = defineProps({
    medicine: Object,
    categories: Array,
    suppliers: Array,
    isEdit: Boolean,
    isView: Boolean,
    errors: Object
})

const toast = ref()
const loading = ref(false)
const activeTab = ref(0)
const activeStep = ref(0)
const file = ref(null)
const isDragOver = ref(false)
const importSummary = ref(null)

// Form data
const form = useForm({
    name: props.medicine?.name || '',
    generic_name: props.medicine?.generic_name || '',
    brand: props.medicine?.brand || '',
    description: props.medicine?.description || '',
    sku: props.medicine?.sku || '',
    price: props.medicine?.price || 0,
    cost_price: props.medicine?.cost_price || 0,
    stock_quantity: props.medicine?.stock_quantity || 0,
    min_stock: props.medicine?.min_stock || 10,
    max_stock: props.medicine?.max_stock || 100,
    expiry_date: props.medicine?.expiry_date || null,
    batch_number: props.medicine?.batch_number || '',
    dosage_form: props.medicine?.dosage_form || '',
    strength: props.medicine?.strength || '',
    requires_prescription: props.medicine?.requires_prescription || false,
    is_active: props.medicine?.is_active ?? true,
    category_id: props.medicine?.category_id || null,
    supplier_id: props.medicine?.supplier_id || null
})

// Computed
const isCreateMode = computed(() => !props.isEdit && !props.isView)

// Methods
const goBack = () => {
    router.get(route('medicines.index'))
}

const goToNextStep = () => {
    if (activeStep.value < 2) {
        activeStep.value++
    }
}

const goToPreviousStep = () => {
    if (activeStep.value > 0) {
        activeStep.value--
    }
}

const submitForm = () => {
    loading.value = true

    if (props.isEdit) {
        form.put(route('medicines.update', props.medicine.uuid), {
            preserveScroll: true,
            onSuccess: () => {
                toast.value.add({
                    severity: 'success',
                    summary: 'Sucesso',
                    detail: 'Medicamento atualizado com sucesso!',
                    life: 3000
                })
            },
            onError: () => {
                toast.value.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: 'Erro ao atualizar medicamento!',
                    life: 3000
                })
            },
            onFinish: () => {
                loading.value = false
            }
        })
    } else {
        form.post(route('medicines.store'), {
            preserveScroll: true,
            onSuccess: () => {
                toast.value.add({
                    severity: 'success',
                    summary: 'Sucesso',
                    detail: 'Medicamento criado com sucesso!',
                    life: 3000
                })
            },
            onError: () => {
                toast.value.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: 'Erro ao criar medicamento!',
                    life: 3000
                })
            },
            onFinish: () => {
                loading.value = false
            }
        })
    }
}

const onFileChange = (event) => {
    file.value = event.target.files[0]
}

const handleDrop = (event) => {
    isDragOver.value = false
    const files = event.dataTransfer.files
    if (files.length > 0) {
        const fileType = files[0].name.split('.').pop().toLowerCase()
        if (['xlsx', 'xls'].includes(fileType)) {
            file.value = files[0]
        } else {
            toast.value.add({
                severity: 'error',
                summary: 'Erro',
                detail: 'Por favor, selecione um arquivo Excel válido!',
                life: 3000
            })
        }
    }
}

const submitImport = async () => {
    if (!file.value) return

    loading.value = true

    const formData = new FormData()
    formData.append('file', file.value)

    try {
        const response = await axios.post(route('medicines.importExcel'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        importSummary.value = {
            inserted: response.data.inserted,
            failed: response.data.failed,
            total: response.data.inserted + response.data.failed,
            errors: response.data.errors
        }

        if (response.data.failed === 0) {
            toast.value.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: `${response.data.inserted} medicamentos importados com sucesso!`,
                life: 5000
            })

            // Redirecionar após 2 segundos
            setTimeout(() => {
                router.get(route('medicines.index'))
            }, 2000)
        } else {
            toast.value.add({
                severity: 'warn',
                summary: 'Importação Parcial',
                detail: `${response.data.inserted} importados, ${response.data.failed} falhas.`,
                life: 5000
            })
        }
    } catch (error) {
        toast.value.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao importar arquivo!',
            life: 3000
        })
    } finally {
        loading.value = false
    }
}

const downloadTemplate = () => {
    window.open(route('medicines.template'), '_blank')
}

// Event listeners para drag and drop
onMounted(() => {
    const handleDragOver = (e) => {
        e.preventDefault()
        isDragOver.value = true
    }

    const handleDragLeave = (e) => {
        e.preventDefault()
        isDragOver.value = false
    }

    document.addEventListener('dragover', handleDragOver)
    document.addEventListener('dragleave', handleDragLeave)

    return () => {
        document.removeEventListener('dragover', handleDragOver)
        document.removeEventListener('dragleave', handleDragLeave)
    }
})
</script>

<style scoped>
.card {
    max-width: 1200px;
    margin: auto;
}

.field {
    margin-bottom: 1rem;
}

.field-checkbox {
    display: flex;
    align-items: center;
}

/* Custom Stepper Styles */
.p-stepper {
    display: flex;
    flex-direction: column;
}

.p-stepper-nav {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 1rem;
}

.p-stepper-list {
    display: flex;
    width: 100%;
    justify-content: space-between;
}

.p-stepper-item {
    flex: 1;
    text-align: center;
}

.p-stepper-action {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    width: 100%;
    color: #6b7280;
    transition: all 0.3s ease;
}

.p-stepper-item.p-highlight .p-stepper-action {
    color: #3b82f6;
}

.p-stepper-number {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    transition: all 0.3s ease;
}

.p-stepper-item.p-highlight .p-stepper-number {
    background: #3b82f6;
    color: white;
}

.p-stepper-title {
    font-size: 0.875rem;
    font-weight: 500;
}

.p-stepper-panels {
    min-height: 400px;
}

.p-stepper-panel {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

:deep(.p-tabview-nav) {
    border-bottom: 1px solid #e5e7eb;
}

:deep(.p-tabview-nav-link) {
    padding: 1rem 1.5rem;
}

:deep(.p-dropdown) {
    width: 100%;
}

:deep(.p-inputnumber) {
    width: 100%;
}

:deep(.p-calendar) {
    width: 100%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .p-stepper-list {
        flex-direction: column;
        gap: 1rem;
    }

    .p-stepper-item {
        text-align: left;
    }

    .p-stepper-action {
        flex-direction: row;
        justify-content: flex-start;
    }

    .p-stepper-title {
        font-size: 0.8rem;
    }
}
</style>
