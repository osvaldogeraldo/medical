<template>
    <AdminLayout>
        <Toast ref="toast" />
        <div class="card">
            <div class="p-6">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Gestão de Categorias</h1>
                        <p class="text-gray-600">Gerencie todas as categorias do sistema</p>
                    </div>
                </div>

                <!-- Filtros e ações -->
                <div class="card mb-4 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex-1">
                        <InputText v-model="searchQuery" placeholder="Pesquisar categorias..." class="w-full md:w-64"
                            @input="handleSearch" />
                    </div>
                    <div class="flex gap-2">
                        <Button label="Nova Categoria" icon="pi pi-plus" severity="success" @click="openCreateModal" />
                        <Button label="Importar Excel" icon="pi pi-upload" severity="info"
                            @click="showImportModal = true" />
                    </div>
                </div>

                <!-- Tabela de Categorias com scroll horizontal apenas -->
                <div class="table-wrapper overflow-x-auto  rounded border">
                    <DataTable :value="categories.data" :paginator="true" :rows="categories.per_page"
                        :totalRecords="categories.total" dataKey="uuid" class="p-datatable-sm min-w-[600px] pl-4"
                        @page="handlePaginate">
                        <template #header>
                            <div class="flex justify-between items-center">
                                <span class="font-semibold">Categorias ({{ categories.total }})</span>
                            </div>
                        </template>

                        <Column field="name" header="Nome" sortable>
                            <template #body="slotProps">
                                <span class="font-medium">{{ slotProps.data.name }}</span>
                            </template>
                        </Column>

                        <Column field="medicines_count" header="Medicamentos" style="width:8rem">
                            <template #body="slotProps">
                                <Tag :value="slotProps.data.medicines_count" severity="info" rounded />
                            </template>
                        </Column>

                        <Column field="is_active" header="Estado" style="width:8rem">
                            <template #body="slotProps">
                                <Tag :value="slotProps.data.is_active ? 'Activo' : 'Inactivo'"
                                    :severity="slotProps.data.is_active ? 'success' : 'danger'" rounded />
                            </template>
                        </Column>

                        <Column header="Ações" style="10rem">
                            <template #body="slotProps">
                                <div class="flex gap-1">
                                    <Button icon="pi pi-eye"
                                        class="p-button-rounded p-button-info p-button-text p-button-sm"
                                        @click="openDetailsModal(slotProps.data)" />
                                    <Button icon="pi pi-pencil"
                                        class="p-button-rounded p-button-warning p-button-text p-button-sm"
                                        @click="openEditModal(slotProps.data)" />
                                    <Button icon="pi pi-trash"
                                        class="p-button-rounded p-button-danger p-button-text p-button-sm"
                                        @click="openDeleteModal(slotProps.data)" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <!-- Dialog Detalhes -->
                <Dialog header="Detalhes da Categoria" v-model:visible="showDetailsModal" :modal="true"
                    :style="{ width: '500px' }">
                    <div class="p-4 space-y-3">
                        <div>
                            <strong>Nome:</strong> {{ detailsCategory?.name }}
                        </div>
                        <div>
                            <strong>Estado:</strong>
                            <Tag :value="detailsCategory?.is_active ? 'Activo' : 'Inactivo'"
                                :severity="detailsCategory?.is_active ? 'success' : 'danger'" rounded />
                        </div>
                        <div>
                            <strong>Medicamentos:</strong> {{ detailsCategory?.medicines_count }}
                        </div>
                    </div>
                    <template #footer>
                        <Button label="Fechar" icon="pi pi-times" class="p-button-text" @click="closeDetailsModal" />
                    </template>
                </Dialog>

                <!-- Dialog Criar/Editar -->
                <Dialog :header="editingCategory ? 'Editar Categoria' : 'Nova Categoria'" v-model:visible="showModal"
                    :modal="true" :style="{ width: '500px' }">
                    <div class="p-fluid">
                        <div class="field mb-3">
                            <label for="name">Nome *</label>
                            <InputText id="name" v-model="form.name" />
                            <small class="p-error">{{ form.errors.name }}</small>
                        </div>
                        <div class="field-checkbox mb-3">
                            <Checkbox inputId="is_active" v-model="form.is_active" />
                            <label for="is_active">Categoria Activa</label>
                        </div>
                    </div>
                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="closeModal" />
                        <Button label="Salvar" icon="pi pi-check" :loading="form.processing" @click="submitForm" />
                    </template>
                </Dialog>

                <!-- Dialog Confirm Delete -->
                <Dialog header="Confirmar Eliminação" v-model:visible="showDeleteModal" :modal="true"
                    :style="{ width: '400px' }">
                    <p>Tem certeza que deseja eliminar esta categoria?</p>
                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="closeDeleteModal" />
                        <Button label="Eliminar" icon="pi pi-trash" severity="danger" :loading="deleting"
                            @click="confirmDelete" />
                    </template>
                </Dialog>

                <!-- Dialog Import Excel -->
                <Dialog header="Importar Categorias" v-model:visible="showImportModal" :modal="true"
                    :style="{ width: '600px' }">
                    <FileUpload ref="fileUploadRef" name="file" url="#" mode="basic" chooseLabel="Selecionar Excel"
                        accept=".xlsx,.xls" auto customUpload @upload="submitImport" />
                    <small class="text-gray-500 mt-2 block">
                        Formatos suportados: .xlsx, .xls. Colunas necessárias: name, is_active, medicines_count
                    </small>
                    <template #footer>
                        <Button label="Fechar" icon="pi pi-times" class="p-button-text" @click="closeImportModal" />
                    </template>
                </Dialog>

            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onUnmounted, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import FileUpload from 'primevue/fileupload';
import Paginator from 'primevue/paginator';

const props = defineProps({
    categories: Object,
    filters: Object,
});

const toast = ref(null);
const showModal = ref(false);
const showDeleteModal = ref(false);
const showImportModal = ref(false);
const showDetailsModal = ref(false);
const editingCategory = ref(null);
const categoryToDelete = ref(null);
const detailsCategory = ref(null);
const deleting = ref(false);
const searchQuery = ref(props.filters.search || '');
const fileUploadRef = ref(null);
const searchTimeout = ref(null);

const form = useForm({
    name: '',
    is_active: true,
});

// Toast
const showMessage = (severity, summary, detail) => {
    toast.value.add({ severity, summary, detail, life: 3000 });
};

// CRUD
const openCreateModal = () => { editingCategory.value = null; form.reset(); form.clearErrors(); form.is_active = true; showModal.value = true; };
const openEditModal = (category) => { editingCategory.value = category; form.clearErrors(); form.name = category.name; form.is_active = category.is_active; showModal.value = true; };
const closeModal = () => { showModal.value = false; editingCategory.value = null; form.clearErrors(); };

const openDeleteModal = (category) => { categoryToDelete.value = category; showDeleteModal.value = true; };
const closeDeleteModal = () => { showDeleteModal.value = false; categoryToDelete.value = null; deleting.value = false; };

const openDetailsModal = (category) => { detailsCategory.value = category; showDetailsModal.value = true; };
const closeDetailsModal = () => { showDetailsModal.value = false; detailsCategory.value = null; };

const submitForm = () => {
    if (editingCategory.value) {
        form.put(route('categories.update', editingCategory.value.uuid), {
            preserveScroll: true,
            onSuccess: () => { closeModal(); showMessage('success', 'Sucesso', 'Categoria atualizada'); },
            onError: () => showMessage('error', 'Erro', 'Não foi possível atualizar'),
        });
    } else {
        form.post(route('categories.store'), {
            preserveScroll: true,
            onSuccess: () => { closeModal(); showMessage('success', 'Sucesso', 'Categoria criada'); },
            onError: () => showMessage('error', 'Erro', 'Não foi possível criar'),
        });
    }
};

const confirmDelete = () => {
    deleting.value = true;
    router.delete(route('categories.destroy', categoryToDelete.value.uuid), {
        preserveScroll: true,
        onSuccess: () => { closeDeleteModal(); showMessage('success', 'Sucesso', 'Categoria eliminada'); },
        onError: () => showMessage('error', 'Erro', 'Não foi possível eliminar'),
    });
};

const handlePaginate = (event) => {
    router.get(route('categories.index', { page: event.page + 1, search: searchQuery.value }), {}, { preserveScroll: true, preserveState: true });
};

const handleSearch = () => {
    if (searchTimeout.value) clearTimeout(searchTimeout.value);
    searchTimeout.value = setTimeout(() => {
        router.get(route('categories.index', { search: searchQuery.value }), {}, { preserveScroll: true, preserveState: true });
    }, 500);
};

const submitImport = (event) => {
    if (!event.files.length) return;
    const file = event.files[0];
    const formData = new FormData();
    formData.append('file', file);
    router.post(route('categories.import'), formData, {
        preserveScroll: true,
        onSuccess: () => { showImportModal.value = false; showMessage('success', 'Sucesso', 'Categorias importadas'); },
        onError: () => showMessage('error', 'Erro', 'Não foi possível importar'),
    });
};

const closeImportModal = () => {
    showImportModal.value = false;
    if (fileUploadRef.value) fileUploadRef.value.clear();
};

onUnmounted(() => { if (searchTimeout.value) clearTimeout(searchTimeout.value); });
watch(() => props.filters.search, (newSearch) => { searchQuery.value = newSearch || ''; });
</script>

<style scoped>
.p-datatable-sm .p-datatable-tbody>tr>td {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

@media (max-width: 768px) {
    .p-datatable-sm .p-datatable-tbody>tr>td {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
}
</style>
