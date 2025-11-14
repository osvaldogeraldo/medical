<template>
    <AdminLayout>
        <Toast ref="toast" />

        <div class="card">
            <div class="p-6 space-y-6">
                <!-- Cards de Estatísticas -->
                <!-- Implementar no futuro -->
                <div class="card-header">
                    <h2 class="text-2xl font-semibold"><i>Medicamentos</i></h2>
                </div>
                <!-- Filtros e Ações -->
                <div
                    class="card p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-gray-50 rounded-lg border mt-4">
                    <div class="flex-1 w-full md:w-auto">
                        <InputText v-model="searchQuery" placeholder="Pesquisar medicamentos..." class="w-full md:w-1/3"
                            @input="handleSearch" />
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <Button label="Novo Medicamento" icon="pi pi-plus" severity="success" @click="goToCreate" />
                        <Button label="Importar Excel" icon="pi pi-upload" severity="info" @click="activeTab = 1" />
                    </div>
                </div>

                <!-- Tabs: Lista e Importar -->
                <TabView v-model:activeIndex="activeTab">
                    <TabPanel header="Lista de Medicamentos">
                        <div class="row border-round p-4 bg-white shadow-2">
                            <DataTable :value="medicines.data" :paginator="true" :rows="medicines.per_page"
                                :first="(medicines.current_page - 1) * medicines.per_page"
                                :totalRecords="medicines.total" dataKey="uuid" class="p-datatable-sm min-w-[600px]"
                                @page="handlePaginate">
                                <Column field="name" header="Nome" sortable />
                                <Column field="brand" header="Marca" sortable />
                                <Column field="generic_name" header="Genérico" sortable />
                                <Column field="sku" header="SKU" sortable />
                                <Column field="stock_quantity" header="Stock" sortable />
                                <Column field="price" header="Preço" sortable />
                                <Column header="Ações">
                                    <template #body="slotProps">
                                        <div class="flex gap-1">
                                            <Button icon="pi pi-eye"
                                                class="p-button-rounded p-button-info p-button-text p-button-sm"
                                                @click="openDetailsModal(slotProps.data)" />
                                            <Button icon="pi pi-pencil"
                                                class="p-button-rounded p-button-warning p-button-text p-button-sm"
                                                @click="goToEdit(slotProps.data.uuid)" />
                                            <Button icon="pi pi-image"
                                                class="p-button-rounded p-button-success p-button-text p-button-sm"
                                                @click="goToAttachments(slotProps.data.uuid)" />
                                        </div>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </TabPanel>

                    <TabPanel header="Importar Excel">
                        <div class="p-4 space-y-6">
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

                            <div class="flex flex-col md:flex-row justify-center gap-3 mt-4">
                                <Button label="Importar Dados" icon="pi pi-cloud-upload" severity="success"
                                    :disabled="!file || carregando" @click="enviarArquivo" />
                                <Button label="Baixar Template Excel" icon="pi pi-download" severity="secondary"
                                    @click="baixarTemplate" />
                            </div>

                            <div v-if="resumo" class="bg-white border rounded shadow-sm p-6 mt-4">
                                <h3 class="text-lg font-semibold mb-3">Resumo da Importação</h3>
                                <p><strong>Sucesso:</strong> {{ resumo.inserted }}</p>
                                <p><strong>Falhas:</strong> {{ resumo.failed }}</p>
                            </div>
                        </div>
                    </TabPanel>
                </TabView>

                <!-- Dialog Detalhes -->
                <Dialog header="Detalhes do Medicamento" v-model:visible="showDetailsModal" :modal="true"
                    :style="{ width: '90%', maxWidth: '500px' }">
                    <div class="p-4 space-y-3">
                        <div><strong>Nome:</strong>
                            <p>{{ detailsMedicine?.name }}</p>
                        </div>
                        <div><strong>Marca:</strong>
                            <p>{{ detailsMedicine?.brand }}</p>
                        </div>
                        <div><strong>Genérico:</strong>
                            <p>{{ detailsMedicine?.generic_name || '—' }}</p>
                        </div>
                        <div><strong>SKU:</strong>
                            <p>{{ detailsMedicine?.sku }}</p>
                        </div>
                        <div><strong>Stock:</strong>
                            <p>{{ detailsMedicine?.stock_quantity }}</p>
                        </div>
                        <div><strong>Preço:</strong>
                            <p>{{ detailsMedicine?.price }}</p>
                        </div>
                    </div>
                    <template #footer>
                        <Button label="Fechar" icon="pi pi-times" class="p-button-text" @click="closeDetailsModal" />
                    </template>
                </Dialog>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import axios from 'axios';
import { ref, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Toast from 'primevue/toast'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import StatCard from '@/Components/StatCard.vue'

const props = defineProps({
    medicines: Object,
    filters: Object,
    statistics: Object,
})

const toast = ref(null)
const activeTab = ref(0)
const searchQuery = ref(props.filters.search || '')
const searchTimeout = ref(null)
const file = ref(null)
const resumo = ref(null)
const carregando = ref(false)
const showDetailsModal = ref(false)
const detailsMedicine = ref(null)

const goToCreate = () => router.get(route('medicines.create'))
const goToEdit = (uuid) => router.get(route('medicines.edit', uuid))
const goToAttachments = (uuid) => {
    router.get(route('attachments.index', { medicine: uuid }))
}


const openDetailsModal = (medicine) => {
    detailsMedicine.value = medicine
    showDetailsModal.value = true
}
const closeDetailsModal = () => {
    showDetailsModal.value = false
    detailsMedicine.value = null
}

const handlePaginate = (event) => {
    router.get(route('medicines.index', { page: event.page + 1, search: searchQuery.value }), {}, { preserveScroll: true, preserveState: true })
}

const handleSearch = () => {
    if (searchTimeout.value) clearTimeout(searchTimeout.value)
    searchTimeout.value = setTimeout(() => {
        router.get(route('medicines.index', { search: searchQuery.value }), {}, { preserveScroll: true, preserveState: true })
    }, 500)
}

const onFileChange = (e) => (file.value = e.target.files[0])
const handleDrop = (e) => (file.value = e.dataTransfer.files[0])

const enviarArquivo = async () => {
    if (!file.value) return;

    carregando.value = true;
    const formData = new FormData();
    formData.append('file', file.value);

    try {
        const { data } = await axios.post(route('medicines.import'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        resumo.value = data;        // resumo da importação
        file.value = null;          // limpa arquivo após import
        router.reload({ only: ['medicines'] });
    } catch (error) {
        console.error('Erro ao importar arquivo:', error);
        toast.value.add({
            severity: 'error',
            summary: 'Erro',
            detail: error.response?.data?.message || 'Falha ao importar arquivo.',
            life: 3000,
        });
    } finally {
        carregando.value = false;
    }
};

const baixarTemplate = () => {
    window.open(route('medicines.template'), '_blank');
};

onUnmounted(() => searchTimeout.value && clearTimeout(searchTimeout.value))
</script>
