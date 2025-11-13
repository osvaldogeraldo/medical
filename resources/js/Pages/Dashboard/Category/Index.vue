<template>
    <AdminLayout>
        <Toast ref="toast" />

        <div class="card">
            <div class="p-6 space-y-6">

                <!-- Header -->
                  <div class="card-header">
                    <h2 class="text-2xl font-semibold"><i>Categorias</i></h2>
                 </div>

                <!-- Tabs -->
                <TabView v-model:activeIndex="activeTab">
                    <!-- Tab 1: Lista -->
                    <TabPanel header="Lista de Categorias">
                        <!-- Filtros e Ações -->
                        <div
                            class="card p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-gray-50 rounded-lg border">
                            <div class="flex-1 w-full md:w-auto">
                                <InputText v-model="searchQuery" placeholder="Pesquisar categorias..."
                                    class="w-full md:w-64" @input="handleSearch" />
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <Button label="Nova Categoria" icon="pi pi-plus" severity="success"
                                    @click="openCreateModal" />
                                <Button label="Importar Dados" icon="pi pi-upload" severity="info"
                                    @click="activeTab = 1" />
                            </div>
                        </div>

                        <!-- Tabela -->
                        <div class="table-wrapper overflow-x-auto rounded-lg border rounded mt-4 pl-4">
                            <DataTable :value="categories.data" :paginator="true" :rows="categories.per_page"
                                :first="(categories.current_page - 1) * categories.per_page"
                                :totalRecords="categories.total" dataKey="uuid" class="p-datatable-sm min-w-[600px]"
                                @page="handlePaginate">


                                <template #header>
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold">Categorias ({{ categories.total }})</span>
                                    </div>
                                </template>

                                <Column field="name" header="Nome" sortable>
                                    <template #body="slotProps">
                                        <span class="font-medium text-gray-800">{{ slotProps.data.name }}</span>
                                    </template>
                                </Column>

                                <Column field="description" header="Descrição" sortable>
                                    <template #body="slotProps">
                                        <span class="text-gray-600 text-sm line-clamp-2">
                                            {{ slotProps.data.description || '-' }}
                                        </span>
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

                                <Column header="Ações" style="width:10rem">
                                    <template #body="slotProps">
                                        <div class="flex gap-1 justify-center">
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
                    </TabPanel>

                    <!-- Tab 2: Importar -->
                    <TabPanel header="Importar Dados">
                        <div class="p-4 space-y-6">

                            <!-- Dropzone -->
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center bg-gray-50 hover:bg-gray-100 transition cursor-pointer"
                                @drop.prevent="handleDrop" @dragover.prevent @click="$refs.fileInput.click()">
                                <i class="pi pi-upload text-4xl text-gray-500 mt-4"></i>
                                <p class="text-gray-600 mt-3">
                                    Arraste e solte o arquivo Excel aqui ou clique para selecionar
                                </p>
                                <p v-if="file" class="mt-2 text-green-600 font-semibold">{{ file.name }}</p>
                                <input ref="fileInput" type="file" class="hidden" accept=".xlsx,.xls"
                                    @change="onFileChange" />
                            </div>

                            <!-- Botões -->
                            <div class="flex flex-col md:flex-row justify-center gap-3 mt-4">
                                <Button label="Importar Dados" icon="pi pi-cloud-upload" severity="success"
                                    :disabled="!file || carregando" @click="enviarArquivo" />
                                <Button label="Baixar Template Excel" icon="pi pi-download" severity="secondary"
                                    @click="baixarTemplate" />
                            </div>

                            <!-- Resumo -->
                            <div v-if="resumo" class="bg-white border rounded shadow-sm p-6 mt-4">
                                <h3 class="text-lg font-semibold mb-3">Resumo da Importação das Categorias</h3>
                                <p><strong>Sucesso:</strong> {{ resumo.inserted }}</p>
                                <p><strong>Falhas:</strong> {{ resumo.failed }}</p>


                                <div v-if="resumo.errosDetalhes?.length" class="mt-3">
                                    <h4 class="font-medium text-red-600 mb-1">Erros Detalhados:</h4>
                                    <ul class="list-disc list-inside text-sm text-red-600">
                                        <li v-for="(erro, i) in resumo.errosDetalhes" :key="i">{{ erro }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                </TabView>

                <!-- Dialog Detalhes -->
                <Dialog header="Detalhes da Categoria" v-model:visible="showDetailsModal" :modal="true"
                    :style="{ width: '90%', maxWidth: '500px' }" class="responsive-dialog">
                    <div class="p-4 space-y-3">
                        <div><strong>Nome:</strong>
                            <p class="mt-1">{{ detailsCategory?.name }}</p>
                        </div>
                        <div><strong>Descrição:</strong>
                            <p class="mt-1">{{ detailsCategory?.description || '—' }}</p>
                        </div>
                        <div><strong>Estado:</strong>
                            <Tag :value="detailsCategory?.is_active ? 'Activo' : 'Inactivo'"
                                :severity="detailsCategory?.is_active ? 'success' : 'danger'" rounded />
                        </div>
                        <div><strong>Medicamentos:</strong>
                            <p class="mt-1">{{ detailsCategory?.medicines_count }}</p>
                        </div>
                    </div>
                    <template #footer>
                        <Button label="Fechar" icon="pi pi-times" class="p-button-text" @click="closeDetailsModal" />
                    </template>
                </Dialog>

                <!-- Dialog Criar/Editar -->
                <Dialog :header="editingCategory ? 'Editar Categoria' : 'Nova Categoria'" v-model:visible="showModal"
                    :modal="true" :style="{ width: '90%', maxWidth: '500px' }" class="responsive-dialog">
                    <div class="p-fluid">
                        <div class="field mb-4">
                            <label for="name" class="font-medium text-gray-700">Nome *</label>
                            <InputText id="name" v-model="form.name" class="w-full mt-1" />
                            <small class="p-error">{{ form.errors.name }}</small>
                        </div>

                        <div class="field mb-4">
                            <label for="description" class="font-medium text-gray-700">Descrição</label>
                            <Textarea id="description" v-model="form.description" rows="3"
                                class="w-full mt-1 resize-none" autoResize />
                            <small class="p-error">{{ form.errors.description }}</small>
                        </div>
                    </div>
                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="closeModal" />
                        <Button label="Salvar" icon="pi pi-check" :loading="form.processing" @click="submitForm" />
                    </template>
                </Dialog>

                <!-- Dialog Eliminar -->
                <Dialog header="Confirmar Eliminação" v-model:visible="showDeleteModal" :modal="true"
                    :style="{ width: '90%', maxWidth: '400px' }">
                    <p>Tem certeza que deseja eliminar esta categoria?</p>
                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="closeDeleteModal" />
                        <Button label="Eliminar" icon="pi pi-trash" severity="danger" :loading="deleting"
                            @click="confirmDelete" />
                    </template>
                </Dialog>

            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, watch, onUnmounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Tag from 'primevue/tag';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';


const props = defineProps({
    categories: Object,
    filters: Object,
});

const toast = ref(null);
const showModal = ref(false);
const showDeleteModal = ref(false);
const showDetailsModal = ref(false);
const editingCategory = ref(null);
const categoryToDelete = ref(null);
const detailsCategory = ref(null);
const deleting = ref(false);
const searchQuery = ref(props.filters.search || '');
const searchTimeout = ref(null);
const activeTab = ref(0);

const file = ref(null);
const fileInput = ref(null);
const resumo = ref(null);
const carregando = ref(false);

const form = useForm({
    name: '',
    description: '',
    is_active: true,
});

const showMessage = (severity, summary, detail) => {
    toast.value.add({ severity, summary, detail, life: 3000 });
};

// CRUD
const openCreateModal = () => {
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (category) => {
    editingCategory.value = category;
    form.clearErrors();
    form.name = category.name;
    form.description = category.description;
    form.is_active = category.is_active;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingCategory.value = null;
    form.clearErrors();
};

const openDeleteModal = (category) => {
    categoryToDelete.value = category;
    showDeleteModal.value = true;
};
const closeDeleteModal = () => {
    showDeleteModal.value = false;
    categoryToDelete.value = null;
    deleting.value = false;
};

const openDetailsModal = (category) => {
    detailsCategory.value = category;
    showDetailsModal.value = true;
};
const closeDetailsModal = () => {
    showDetailsModal.value = false;
    detailsCategory.value = null;
};

const submitForm = () => {
    if (editingCategory.value) {
        form.put(route('categories.update', editingCategory.value.uuid), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                showMessage('success', 'Sucesso', 'Categoria atualizada');
            },
            onError: () => showMessage('error', 'Erro', 'Não foi possível atualizar'),
        });
    } else {
        form.post(route('categories.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                showMessage('success', 'Sucesso', 'Categoria criada');
            },
            onError: () => showMessage('error', 'Erro', 'Não foi possível criar'),
        });
    }
};

const confirmDelete = () => {
    deleting.value = true;
    router.delete(route('categories.destroy', categoryToDelete.value.uuid), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
            showMessage('success', 'Sucesso', 'Categoria eliminada');
        },
        onError: () => showMessage('error', 'Erro', 'Não foi possível eliminar'),
    });
};

// Filtro
const handlePaginate = (event) => {
    router.get(
        route('categories.index', {
            page: event.page + 1, // PrimeVue usa zero-based, backend usa 1-based
            search: searchQuery.value
        }),
        {},
        { preserveScroll: true, preserveState: true }
    );
};


const handleSearch = () => {
    if (searchTimeout.value) clearTimeout(searchTimeout.value);
    searchTimeout.value = setTimeout(() => {
        router.get(route('categories.index', { search: searchQuery.value }), {}, { preserveScroll: true, preserveState: true });
    }, 500);
};

// Importação Excel
const onFileChange = (e) => {
    file.value = e.target.files[0];
};
const handleDrop = (e) => {
    file.value = e.dataTransfer.files[0];
};
const enviarArquivo = async () => {
    if (!file.value) return;
    carregando.value = true;
    const formData = new FormData();
    formData.append('file', file.value);

    try {
        const { data } = await axios.post(route('categories.import'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        resumo.value = data;
        showMessage('success', 'Importação Concluída', 'Categorias importadas com sucesso.');
        router.reload({ only: ['categories'] });
    } catch (error) {
        showMessage('error', 'Erro', 'Falha ao importar arquivo.');
    } finally {
        carregando.value = false;
    }
};
const baixarTemplate = () => {
    window.open(route('categories.template'), '_blank');
};

onUnmounted(() => {
    if (searchTimeout.value) clearTimeout(searchTimeout.value);
});

watch(() => props.filters.search, (newSearch) => {
    searchQuery.value = newSearch || '';
});
</script>

<style scoped>
.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.p-datatable-sm .p-datatable-tbody>tr>td {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
}

@media (max-width: 768px) {
    .p-datatable-sm .p-datatable-tbody>tr>td {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }

    .responsive-dialog {
        width: 95% !important;
    }
}
</style>
