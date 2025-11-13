<template>
    <AdminLayout>
        <Toast ref="toast" />

        <div class="card">
            <div class="p-6 space-y-6">
                <!-- Header -->
                <div class="card-header">
                    <h2 class="text-2xl font-semibold"><i>Novo Medicamento</i></h2>
                    <p class="text-gray-600 mt-1">Adicione um novo medicamento ao inventário</p>
                </div>

                <!-- Steps com Design Melhorado -->
                <div class="steps-container mb-8">
                    <div class="flex justify-between items-center relative">
                        <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 -translate-y-1/2 -z-10"></div>
                        <div class="absolute top-1/2 left-0 h-1 bg-primary-500 -translate-y-1/2 -z-10 transition-all duration-500"
                             :style="{ width: progressWidth }"></div>

                        <div v-for="(step, index) in steps" :key="index"
                             class="flex flex-col items-center relative z-10">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center border-2 transition-all duration-300"
                                 :class="getStepClasses(index)">
                                <i :class="step.icon" class="text-lg"></i>
                            </div>
                            <span class="text-sm font-medium mt-2 transition-colors duration-300"
                                  :class="getStepLabelClasses(index)">
                                {{ step.label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progresso</span>
                        <span>{{ Math.round((activeStep / (steps.length - 1)) * 100) }}%</span>
                    </div>
                    <ProgressBar :value="(activeStep / (steps.length - 1)) * 100"
                               class="h-2" showValue="false" />
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm">
                    <!-- Step 1: Informações Básicas -->
                    <div v-if="activeStep === 0" class="space-y-6 animate-fade-in">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div class="field">
                                <label for="name" class="font-medium text-gray-700 block mb-2">
                                    Nome do Medicamento *
                                </label>
                                <InputText id="name" v-model="form.name"
                                         class="w-full" :class="{ 'p-invalid': form.errors.name }"
                                         placeholder="Ex: Paracetamol 500mg" />
                                <small v-if="form.errors.name" class="p-error mt-1 block">
                                    {{ form.errors.name }}
                                </small>
                            </div>

                            <!-- Nome Genérico -->
                            <div class="field">
                                <label for="generic_name" class="font-medium text-gray-700 block mb-2">
                                    Nome Genérico
                                </label>
                                <InputText id="generic_name" v-model="form.generic_name"
                                         class="w-full" :class="{ 'p-invalid': form.errors.generic_name }"
                                         placeholder="Ex: Paracetamol" />
                                <small v-if="form.errors.generic_name" class="p-error mt-1 block">
                                    {{ form.errors.generic_name }}
                                </small>
                            </div>

                            <!-- Marca -->
                            <div class="field">
                                <label for="brand" class="font-medium text-gray-700 block mb-2">
                                    Marca
                                </label>
                                <InputText id="brand" v-model="form.brand"
                                         class="w-full" :class="{ 'p-invalid': form.errors.brand }"
                                         placeholder="Ex: Bayer" />
                                <small v-if="form.errors.brand" class="p-error mt-1 block">
                                    {{ form.errors.brand }}
                                </small>
                            </div>

                            <!-- SKU -->
                            <div class="field">
                                <label for="sku" class="font-medium text-gray-700 block mb-2">
                                    SKU *
                                </label>
                                <InputText id="sku" v-model="form.sku"
                                         class="w-full" :class="{ 'p-invalid': form.errors.sku }"
                                         placeholder="Ex: MED-001" />
                                <small v-if="form.errors.sku" class="p-error mt-1 block">
                                    {{ form.errors.sku }}
                                </small>
                            </div>

                            <!-- Categoria -->
                            <div class="field">
                                <label for="category_id" class="font-medium text-gray-700 block mb-2">
                                    Categoria *
                                </label>
                                <Dropdown id="category_id" v-model="form.category_id"
                                        :options="categories" optionLabel="name" optionValue="id"
                                        :filter="true" filterPlaceholder="Pesquisar categoria..."
                                        placeholder="Selecione uma categoria"
                                        class="w-full" :class="{ 'p-invalid': form.errors.category_id }">
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value" class="flex items-center">
                                            <span>{{ getCategoryName(slotProps.value) }}</span>
                                        </div>
                                        <span v-else class="text-gray-400">
                                            Selecione uma categoria
                                        </span>
                                    </template>
                                </Dropdown>
                                <small v-if="form.errors.category_id" class="p-error mt-1 block">
                                    {{ form.errors.category_id }}
                                </small>
                            </div>

                            <!-- Fornecedor -->
                            <div class="field">
                                <label for="supplier_id" class="font-medium text-gray-700 block mb-2">
                                    Fornecedor
                                </label>
                                <Dropdown id="supplier_id" v-model="form.supplier_id"
                                        :options="suppliers" optionLabel="name" optionValue="id"
                                        :filter="true" filterPlaceholder="Pesquisar fornecedor..."
                                        placeholder="Selecione um fornecedor"
                                        class="w-full" :class="{ 'p-invalid': form.errors.supplier_id }">
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value" class="flex items-center">
                                            <span>{{ getSupplierName(slotProps.value) }}</span>
                                        </div>
                                        <span v-else class="text-gray-400">
                                            Selecione um fornecedor
                                        </span>
                                    </template>
                                </Dropdown>
                                <small v-if="form.errors.supplier_id" class="p-error mt-1 block">
                                    {{ form.errors.supplier_id }}
                                </small>
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="field">
                            <label for="description" class="font-medium text-gray-700 block mb-2">
                                Descrição
                            </label>
                            <Textarea id="description" v-model="form.description" rows="3"
                                    class="w-full resize-none" autoResize
                                    :class="{ 'p-invalid': form.errors.description }"
                                    placeholder="Descrição detalhada do medicamento..." />
                            <small v-if="form.errors.description" class="p-error mt-1 block">
                                {{ form.errors.description }}
                            </small>
                        </div>
                    </div>

                    <!-- Step 2: Detalhes Técnicos -->
                    <div v-if="activeStep === 1" class="space-y-6 animate-fade-in">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Forma Farmacêutica -->
                            <div class="field">
                                <label for="dosage_form" class="font-medium text-gray-700 block mb-2">
                                    Forma Farmacêutica
                                </label>
                                <Dropdown id="dosage_form" v-model="form.dosage_form"
                                        :options="dosageForms" optionLabel="label" optionValue="value"
                                        :filter="true" filterPlaceholder="Pesquisar forma..."
                                        placeholder="Selecione a forma"
                                        class="w-full" :class="{ 'p-invalid': form.errors.dosage_form }">
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value" class="flex items-center">
                                            <span>{{ getDosageFormLabel(slotProps.value) }}</span>
                                        </div>
                                        <span v-else class="text-gray-400">
                                            Selecione a forma
                                        </span>
                                    </template>
                                </Dropdown>
                                <small v-if="form.errors.dosage_form" class="p-error mt-1 block">
                                    {{ form.errors.dosage_form }}
                                </small>
                            </div>

                            <!-- Concentração -->
                            <div class="field">
                                <label for="strength" class="font-medium text-gray-700 block mb-2">
                                    Concentração/Força
                                </label>
                                <InputText id="strength" v-model="form.strength"
                                         class="w-full" :class="{ 'p-invalid': form.errors.strength }"
                                         placeholder="Ex: 500mg, 10mg/mL" />
                                <small v-if="form.errors.strength" class="p-error mt-1 block">
                                    {{ form.errors.strength }}
                                </small>
                            </div>

                            <!-- Lote -->
                            <div class="field">
                                <label for="batch_number" class="font-medium text-gray-700 block mb-2">
                                    Número do Lote
                                </label>
                                <InputText id="batch_number" v-model="form.batch_number"
                                         class="w-full" :class="{ 'p-invalid': form.errors.batch_number }"
                                         placeholder="Ex: LOTE-2024-001" />
                                <small v-if="form.errors.batch_number" class="p-error mt-1 block">
                                    {{ form.errors.batch_number }}
                                </small>
                            </div>

                            <!-- Data de Validade -->
                            <div class="field">
                                <label for="expiry_date" class="font-medium text-gray-700 block mb-2">
                                    Data de Validade
                                </label>
                                <Calendar id="expiry_date" v-model="form.expiry_date"
                                         dateFormat="dd/mm/yy" showIcon
                                         class="w-full" :class="{ 'p-invalid': form.errors.expiry_date }"
                                         placeholder="Selecione a data" />
                                <small v-if="form.errors.expiry_date" class="p-error mt-1 block">
                                    {{ form.errors.expiry_date }}
                                </small>
                            </div>
                        </div>

                        <!-- Checkboxes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="flex items-center">
                                <Checkbox id="requires_prescription" v-model="form.requires_prescription"
                                         binary :class="{ 'p-invalid': form.errors.requires_prescription }" />
                                <label for="requires_prescription" class="ml-2 font-medium text-gray-700">
                                    Requer Prescrição Médica
                                </label>
                            </div>
                            <div class="flex items-center">
                                <Checkbox id="is_active" v-model="form.is_active"
                                         binary :class="{ 'p-invalid': form.errors.is_active }" />
                                <label for="is_active" class="ml-2 font-medium text-gray-700">
                                    Medicamento Ativo
                                </label>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <small v-if="form.errors.requires_prescription" class="p-error block">
                                {{ form.errors.requires_prescription }}
                            </small>
                            <small v-if="form.errors.is_active" class="p-error block">
                                {{ form.errors.is_active }}
                            </small>
                        </div>
                    </div>

                    <!-- Step 3: Stock e Preços -->
                    <div v-if="activeStep === 2" class="space-y-6 animate-fade-in">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Preço de Venda -->
                            <div class="field">
                                <label for="price" class="font-medium text-gray-700 block mb-2">
                                    Preço de Venda *
                                </label>
                                <InputNumber id="price" v-model="form.price"
                                           mode="currency" currency="MZN" locale="pt-MZ"
                                           class="w-full" :class="{ 'p-invalid': form.errors.price }"
                                           placeholder="0,00" />
                                <small v-if="form.errors.price" class="p-error mt-1 block">
                                    {{ form.errors.price }}
                                </small>
                            </div>

                            <!-- Preço de Custo -->
                            <div class="field">
                                <label for="cost_price" class="font-medium text-gray-700 block mb-2">
                                    Preço de Custo *
                                </label>
                                <InputNumber id="cost_price" v-model="form.cost_price"
                                           mode="currency" currency="MZN" locale="pt-MZ"
                                           class="w-full" :class="{ 'p-invalid': form.errors.cost_price }"
                                           placeholder="0,00" />
                                <small v-if="form.errors.cost_price" class="p-error mt-1 block">
                                    {{ form.errors.cost_price }}
                                </small>
                            </div>

                            <!-- Quantidade em Stock -->
                            <div class="field">
                                <label for="stock_quantity" class="font-medium text-gray-700 block mb-2">
                                    Quantidade em Stock *
                                </label>
                                <InputNumber id="stock_quantity" v-model="form.stock_quantity"
                                           class="w-full" :class="{ 'p-invalid': form.errors.stock_quantity }"
                                           placeholder="0" />
                                <small v-if="form.errors.stock_quantity" class="p-error mt-1 block">
                                    {{ form.errors.stock_quantity }}
                                </small>
                            </div>

                            <!-- Stock Mínimo -->
                            <div class="field">
                                <label for="min_stock" class="font-medium text-gray-700 block mb-2">
                                    Stock Mínimo
                                </label>
                                <InputNumber id="min_stock" v-model="form.min_stock"
                                           class="w-full" :class="{ 'p-invalid': form.errors.min_stock }"
                                           placeholder="0" />
                                <small v-if="form.errors.min_stock" class="p-error mt-1 block">
                                    {{ form.errors.min_stock }}
                                </small>
                            </div>

                            <!-- Stock Máximo -->
                            <div class="field">
                                <label for="max_stock" class="font-medium text-gray-700 block mb-2">
                                    Stock Máximo
                                </label>
                                <InputNumber id="max_stock" v-model="form.max_stock"
                                           class="w-full" :class="{ 'p-invalid': form.errors.max_stock }"
                                           placeholder="0" />
                                <small v-if="form.errors.max_stock" class="p-error mt-1 block">
                                    {{ form.errors.max_stock }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Imagens do Medicamento -->
                    <div v-if="activeStep === 3" class="space-y-6 animate-fade-in">
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Imagens do Medicamento</h3>
                            <p class="text-gray-600">Adicione 3 a 4 imagens do medicamento</p>
                        </div>

                        <!-- Upload Area -->
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center bg-gray-50 hover:bg-gray-100 transition cursor-pointer"
                             @drop.prevent="handleImageDrop" @dragover.prevent @click="$refs.imageInput.click()">
                            <i class="pi pi-cloud-upload text-4xl text-gray-500 mb-3"></i>
                            <p class="text-gray-600 font-medium">
                                Arraste e solte as imagens aqui ou clique para selecionar
                            </p>
                            <p class="text-gray-500 text-sm mt-1">
                                Formatos: JPG, PNG, WEBP (Máx. 2MB cada)
                            </p>
                            <input ref="imageInput" type="file" class="hidden" multiple
                                   accept=".jpg,.jpeg,.png,.webp" @change="onImageChange" />
                        </div>

                        <!-- Preview das Imagens -->
                        <div v-if="images.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                            <div v-for="(image, index) in images" :key="index"
                                 class="relative group border rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
                                <img :src="image.preview" :alt="`Imagem ${index + 1}`"
                                     class="w-full h-32 object-cover" />
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <Button icon="pi pi-times" severity="danger"
                                            class="p-button-rounded p-button-sm"
                                            @click="removeImage(index)" />
                                </div>
                                <div class="p-2 text-center text-xs text-gray-600 border-t">
                                    Imagem {{ index + 1 }}
                                </div>
                            </div>
                        </div>

                        <!-- Mensagem de validação -->
                        <div v-if="imageError" class="p-error text-center mt-4 p-3 bg-red-50 rounded-lg">
                            <i class="pi pi-exclamation-triangle mr-2"></i>
                            {{ imageError }}
                        </div>
                    </div>

                    <!-- Step 5: Anexos e Documentos -->
                    <div v-if="activeStep === 4" class="space-y-6 animate-fade-in">
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Anexos e Documentos</h3>
                            <p class="text-gray-600">Adicione fichas técnicas, manuais ou outros documentos</p>
                        </div>

                        <!-- Lista de Anexos -->
                        <div class="space-y-4">
                            <div v-for="(attachment, index) in attachments" :key="index"
                                 class="border rounded-lg p-4 bg-white shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <InputText v-model="attachment.name"
                                                   placeholder="Nome do documento (ex: Ficha Técnica)"
                                                   class="w-full mb-3" />
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1">
                                                <input type="file"
                                                       :ref="`fileInput-${index}`"
                                                       class="hidden"
                                                       accept=".pdf,.doc,.docx,.xls,.xlsx"
                                                       @change="(e) => onFileChange(e, index)" />
                                                <Button :label="attachment.file ? 'Alterar Arquivo' : 'Selecionar Arquivo'"
                                                        icon="pi pi-paperclip"
                                                        severity="secondary"
                                                        class="p-button-outlined w-full"
                                                        @click="$refs[`fileInput-${index}`][0].click()" />
                                            </div>
                                            <Button v-if="attachments.length > 1"
                                                    icon="pi pi-trash"
                                                    severity="danger"
                                                    class="p-button-text p-button-sm"
                                                    @click="removeAttachment(index)" />
                                        </div>
                                        <div v-if="attachment.file" class="mt-2 p-2 bg-green-50 rounded border border-green-200">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-green-700 font-medium">
                                                    <i class="pi pi-file mr-1"></i>
                                                    {{ attachment.file.name }}
                                                </span>
                                                <span class="text-green-600">
                                                    {{ formatFileSize(attachment.file.size) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botão para adicionar mais anexos -->
                        <div class="flex justify-center">
                            <Button label="Adicionar Outro Documento"
                                    icon="pi pi-plus"
                                    class="p-button-outlined p-button-secondary"
                                    @click="addAttachment" />
                        </div>

                        <!-- Informações -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="pi pi-info-circle text-blue-500 mt-1 mr-3"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">Tipos de arquivo suportados:</p>
                                    <p>PDF, DOC, DOCX, XLS, XLSX (Máx. 5MB cada)</p>
                                    <p class="mt-1">Exemplos: Ficha técnica, manual de instruções, certificado de qualidade, etc.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between pt-6 border-t mt-8">
                        <Button label="Voltar" icon="pi pi-arrow-left"
                                :disabled="activeStep === 0 || form.processing"
                                class="p-button-text"
                                @click="prevStep" />

                        <div class="flex gap-2">
                            <Button label="Cancelar" icon="pi pi-times" severity="secondary"
                                    :disabled="form.processing"
                                    class="p-button-text"
                                    @click="cancelForm" />

                            <Button v-if="activeStep < steps.length - 1"
                                    label="Próximo"
                                    icon="pi pi-arrow-right"
                                    iconPos="right"
                                    :loading="form.processing"
                                    @click="nextStep" />

                            <Button v-else
                                    label="Criar Medicamento"
                                    icon="pi pi-check"
                                    type="submit"
                                    severity="success"
                                    :loading="form.processing" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Components PrimeVue
import Toast from 'primevue/toast'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Dropdown from 'primevue/dropdown'
import InputNumber from 'primevue/inputnumber'
import Calendar from 'primevue/calendar'
import Checkbox from 'primevue/checkbox'
import ProgressBar from 'primevue/progressbar'

const props = defineProps({
    categories: Array,
    suppliers: Array,
})

const toast = ref(null)
const activeStep = ref(0)
const images = ref([])
const attachments = ref([{ name: '', file: null }])
const imageError = ref('')
const imageInput = ref(null)

// Steps configuration atualizada
const steps = ref([
    { label: 'Informações', icon: 'pi pi-info-circle' },
    { label: 'Detalhes', icon: 'pi pi-cog' },
    { label: 'Stock & Preços', icon: 'pi pi-dollar' },
    { label: 'Imagens', icon: 'pi pi-images' },
    { label: 'Documentos', icon: 'pi pi-paperclip' }
])

// Computed para progress bar
const progressWidth = computed(() => {
    const progress = (activeStep.value / (steps.value.length - 1)) * 100
    return `${progress}%`
})

// Forma farmacêutica options
const dosageForms = ref([
    { label: 'Comprimido', value: 'tablet' },
    { label: 'Cápsula', value: 'capsule' },
    { label: 'Xarope', value: 'syrup' },
    { label: 'Injetável', value: 'injectable' },
    { label: 'Pomada', value: 'ointment' },
    { label: 'Creme', value: 'cream' },
    { label: 'Gotas', value: 'drops' },
    { label: 'Spray', value: 'spray' },
    { label: 'Suspensão', value: 'suspension' },
    { label: 'Pó', value: 'powder' }
])

// Form data
const form = useForm({
    name: '',
    generic_name: '',
    brand: '',
    description: '',
    sku: '',
    price: 0,
    cost_price: 0,
    stock_quantity: 0,
    min_stock: null,
    max_stock: null,
    expiry_date: null,
    batch_number: '',
    dosage_form: '',
    strength: '',
    requires_prescription: false,
    is_active: true,
    category_id: null,
    supplier_id: null,
})

// Helper functions
const getCategoryName = (id) => {
    const category = props.categories.find(cat => cat.id === id)
    return category ? category.name : ''
}

const getSupplierName = (id) => {
    const supplier = props.suppliers.find(sup => sup.id === id)
    return supplier ? supplier.name : ''
}

const getDosageFormLabel = (value) => {
    const form = dosageForms.value.find(df => df.value === value)
    return form ? form.label : ''
}

// Step classes
const getStepClasses = (index) => {
    if (index === activeStep.value) {
        return 'bg-primary-500 border-primary-500 text-white shadow-lg scale-110'
    } else if (index < activeStep.value) {
        return 'bg-green-500 border-green-500 text-white'
    } else {
        return 'bg-white border-gray-300 text-gray-400'
    }
}

const getStepLabelClasses = (index) => {
    if (index <= activeStep.value) {
        return 'text-gray-800 font-semibold'
    } else {
        return 'text-gray-500'
    }
}

// Step navigation
const nextStep = () => {
    if (validateStep(activeStep.value)) {
        activeStep.value++
    }
}

const prevStep = () => {
    activeStep.value--
}

const validateStep = (step) => {
    let isValid = true

    switch (step) {
        case 0: // Informações básicas
            if (!form.name) {
                form.setError('name', 'O nome é obrigatório')
                isValid = false
            }
            if (!form.sku) {
                form.setError('sku', 'O SKU é obrigatório')
                isValid = false
            }
            if (!form.category_id) {
                form.setError('category_id', 'A categoria é obrigatória')
                isValid = false
            }
            break

        case 2: // Stock e preços
            if (!form.price || form.price <= 0) {
                form.setError('price', 'O preço de venda é obrigatório')
                isValid = false
            }
            if (!form.cost_price || form.cost_price <= 0) {
                form.setError('cost_price', 'O preço de custo é obrigatório')
                isValid = false
            }
            if (!form.stock_quantity && form.stock_quantity !== 0) {
                form.setError('stock_quantity', 'A quantidade em stock é obrigatória')
                isValid = false
            }
            break

        case 3: // Imagens
            if (images.value.length < 3) {
                imageError.value = 'É necessário adicionar pelo menos 3 imagens'
                isValid = false
            } else {
                imageError.value = ''
            }
            break
    }

    return isValid
}

// Image handling
const onImageChange = (e) => {
    handleImageFiles(e.target.files)
}

const handleImageDrop = (e) => {
    handleImageFiles(e.dataTransfer.files)
}

const handleImageFiles = (files) => {
    imageError.value = ''

    // Validar número de imagens
    if (images.value.length + files.length > 4) {
        imageError.value = 'Máximo de 4 imagens permitidas'
        return
    }

    Array.from(files).forEach(file => {
        // Validar tipo de arquivo
        if (!file.type.startsWith('image/')) {
            imageError.value = 'Apenas arquivos de imagem são permitidos'
            return
        }

        // Validar tamanho (2MB)
        if (file.size > 2 * 1024 * 1024) {
            imageError.value = 'Cada imagem deve ter no máximo 2MB'
            return
        }

        const reader = new FileReader()
        reader.onload = (e) => {
            images.value.push({
                file: file,
                preview: e.target.result
            })
        }
        reader.readAsDataURL(file)
    })

    // Limpar input
    if (imageInput.value) {
        imageInput.value.value = ''
    }
}

const removeImage = (index) => {
    images.value.splice(index, 1)
}

// Attachment handling
const addAttachment = () => {
    attachments.value.push({ name: '', file: null })
}

const removeAttachment = (index) => {
    if (attachments.value.length > 1) {
        attachments.value.splice(index, 1)
    }
}

const onFileChange = (event, index) => {
    const file = event.target.files[0]
    if (file) {
        // Validar tamanho (5MB)
        if (file.size > 5 * 1024 * 1024) {
            showMessage('error', 'Erro', 'O arquivo não pode exceder 5MB')
            return
        }

        // Validar tipo
        const allowedTypes = ['.pdf', '.doc', '.docx', '.xls', '.xlsx']
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase()
        if (!allowedTypes.includes(fileExtension)) {
            showMessage('error', 'Erro', 'Tipo de arquivo não permitido')
            return
        }

        attachments.value[index].file = file

        // Preencher nome automaticamente se estiver vazio
        if (!attachments.value[index].name) {
            const fileName = file.name.replace(/\.[^/.]+$/, "") // Remove extensão
            attachments.value[index].name = fileName
        }
    }
}

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Form submission
const submitForm = async () => {
    // Validar step final
    if (!validateStep(activeStep.value)) {
        return
    }

    // Preparar dados do formulário
    const formData = new FormData()

    // Adicionar campos do formulário
    Object.keys(form.data()).forEach(key => {
        if (form[key] !== null && form[key] !== undefined) {
            if (key === 'expiry_date' && form[key]) {
                // Formatar data para YYYY-MM-DD
                const date = new Date(form[key])
                formData.append(key, date.toISOString().split('T')[0])
            } else {
                formData.append(key, form[key])
            }
        }
    })

    // Adicionar imagens
    images.value.forEach((image, index) => {
        formData.append(`images[${index}]`, image.file)
    })

    // Adicionar anexos
    attachments.value.forEach((attachment, index) => {
        if (attachment.file) {
            formData.append(`attachments[${index}][name]`, attachment.name)
            formData.append(`attachments[${index}][file]`, attachment.file)
        }
    })

    // Enviar formulário
    form.processing = true

    try {
        await router.post(route('medicines.store'), formData, {
            preserveScroll: true,
            onSuccess: () => {
                showMessage('success', 'Sucesso', 'Medicamento criado com sucesso!')
                router.visit(route('medicines.index'))
            },
            onError: (errors) => {
                form.processing = false
                showMessage('error', 'Erro', 'Erro ao criar medicamento')

                // Scroll para o primeiro erro
                if (Object.keys(errors).length > 0) {
                    const firstErrorField = Object.keys(errors)[0]
                    const element = document.getElementById(firstErrorField)
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' })
                    }
                }
            }
        })
    } catch (error) {
        form.processing = false
        showMessage('error', 'Erro', 'Erro ao processar a requisição')
    }
}

const cancelForm = () => {
    router.visit(route('medicines.index'))
}

const showMessage = (severity, summary, detail) => {
    if (toast.value) {
        toast.value.add({ severity, summary, detail, life: 3000 })
    }
}

onMounted(() => {
    // Inicializar valores padrão
    form.is_active = true
    form.requires_prescription = false
})
</script>

<style scoped>
.steps-container {
    max-width: 700px;
    margin: 0 auto;
}

.field {
    margin-bottom: 1.5rem;
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
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

@media (max-width: 768px) {
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }

    .steps-container {
        padding: 0 1rem;
    }

    .steps-container .flex > div {
        transform: scale(0.9);
    }

    .steps-container .flex > div span {
        font-size: 0.7rem;
    }
}

/* Custom colors para consistência */
:deep(.p-progressbar .p-progressbar-value) {
    background: linear-gradient(90deg, #263244, #1D4ED8);
}

:deep(.p-button.p-button-success) {
    background: linear-gradient(90deg, #10B981, #059669);
    border-color: #059669;
}
</style>
