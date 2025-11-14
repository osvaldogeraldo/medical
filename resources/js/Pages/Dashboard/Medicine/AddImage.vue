<template>
    <AdminLayout>
        <Toast ref="toast" />
        <ConfirmDialog />

        <div class="card shadow-lg border-none p-4 md:p-6">

            <!-- Header -->
            <div class="card-header bg-white p-4 rounded-md">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div class="flex items-center gap-4">
                        <!-- Botão voltar -->
                        <button @click="goBack"
                            class="btn btn-sm rounded-lg p-2 bg-gray-100 hover:bg-gray-200 transition">
                            <i class="pi pi-arrow-left text-gray-600"></i>
                        </button>

                        <!-- Info medicamento -->
                        <div class="flex flex-col">
                            <h1 class="text-xl md:text-2xl font-bold text-gray-800">
                                {{ medicine.name }}
                            </h1>
                        </div>
                        <div class="row">
                            <p class="text-gray-500 text-sm flex items-center gap-2">
                                <i class="pi pi-medicine text-blue-500"></i>
                                <span>{{ medicine.laboratory || 'Laboratório não informado' }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Botão adicionar -->
                    <div v-if="!isView">
                        <Button label="Adicionar Anexo" icon="pi pi-plus" class="p-button-sm p-button-primary"
                            @click="openUploadModal" />
                    </div>

                </div>
            </div>

            <!-- Galeria -->
            <div class="mb-8 mt-4">
                <div v-if="images.length > 0"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <div v-for="attachment in images" :key="attachment.uuid" class="relative group cursor-pointer"
                        @click="viewAttachment(attachment)">
                        <div class="flex flex-col items-center">

                            <div
                                class="w-14 h-14 md:w-16 md:h-16 rounded-full overflow-hidden border-2 border-gray-200 group-hover:border-blue-400 transition-all duration-300 shadow-sm group-hover:shadow-md">
                                <img :src="getAttachmentUrl(attachment)" :alt="attachment.name"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
                    <i class="pi pi-image text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Nenhuma imagem encontrada</p>
                </div>
            </div>

            <!-- Lista PDFs -->
            <div class="mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="pi pi-file-pdf text-red-500"></i>
                        Documentos
                    </h3>
                    <span class="text-sm text-gray-500">{{ pdfs.length }} documentos</span>
                </div>

                <div v-if="pdfs.length > 0" class="space-y-3">

                    <div v-for="attachment in pdfs" :key="attachment.uuid"
                        class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200">

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="pi pi-file-pdf text-red-500"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ attachment.name || 'Documento sem nome' }}
                                    </p>
                                    <p class="text-xs text-gray-500 flex items-center gap-2 mt-1">
                                        <span>{{ getFileExtension(attachment.name) }}</span>
                                        <span>•</span>
                                        <span>{{ formatFileSize(attachment.size) }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <Button icon="pi pi-download" class="p-button-text p-button-sm p-button-info"
                                    @click="downloadAttachment(attachment)" />
                                <Button v-if="!isView" icon="pi pi-pencil"
                                    class="p-button-text p-button-sm p-button-warning"
                                    @click="editAttachment(attachment)" />
                                <Button v-if="!isView" icon="pi pi-trash"
                                    class="p-button-text p-button-sm p-button-danger"
                                    @click="confirmDelete(attachment)" />
                            </div>
                        </div>

                    </div>
                </div>

                <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
                    <i class="pi pi-file text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Nenhum documento encontrado</p>
                </div>
            </div>

            <!-- Modal Upload/Edição -->
            <Dialog v-model:visible="uploadModal" :header="editingAttachment ? 'Editar Anexo' : 'Adicionar Anexo'"
                :modal="true" :closable="true" :style="{ width: '90vw', maxWidth: '500px' }">

                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Nome do arquivo <span class="text-gray-400 text-xs">(opcional)</span>
                        </label>
                        <InputText v-model="attachmentName" placeholder="Digite um nome para o arquivo..."
                            class="w-full" />
                    </div>

                    <div class="border border-2 rounded-4 p-4 text-center mt-2 dropzone" @drop.prevent="handleDrop"
                        @dragover.prevent @dragenter.prevent="isDragOver = true" @dragleave.prevent="isDragOver = false"
                        :class="{ 'dropzone-hover': isDragOver }" @click="$refs.fileInput.click()">

                        <i class="pi pi-cloud-upload text-4xl text-blue-400 mb-3"></i>
                        <p class="text-gray-600 font-medium">Arraste e solte o arquivo aqui</p>
                        <p class="text-gray-500 text-sm mt-1">ou clique para selecionar</p>

                        <input ref="fileInput" type="file" class="hidden" @change="onFileChange" />

                        <Button label="Selecionar Arquivo" icon="pi pi-folder-open"
                            class="p-button-outlined p-button-secondary mt-3" @click.stop="$refs.fileInput.click()" />

                        <div v-if="file" class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="pi pi-file text-lg text-blue-500"></i>
                                <div class="flex-1 text-left">
                                    <p class="font-medium text-gray-800 text-sm">{{ file.name }}</p>
                                    <p class="text-gray-500 text-xs">{{ formatFileSize(file.size) }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <template #footer>
                    <div class="flex justify-end gap-2">
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-text p-button-secondary"
                            @click="closeUploadModal" />
                        <Button :label="editingAttachment ? 'Actualizar' : 'Upload'" icon="pi pi-upload"
                            :loading="loading" @click="uploadAttachment" :disabled="!file" class="p-button-primary" />
                    </div>
                </template>
            </Dialog>

            <!-- Modal Visualização -->
            <Dialog v-model:visible="viewModal" :modal="true" :closable="false"
                :style="{ width: '60vw', maxWidth: '420px' }">

                <template #header>
                    <div class="flex justify-end items-center w-full">
                        <Button icon="pi pi-times" class="p-button-text p-button-sm" @click="viewModal = false" />
                    </div>
                </template>

                <div class="text-center">

                    <img v-if="selectedAttachment && isImage(selectedAttachment)"
                        :src="getAttachmentUrl(selectedAttachment)" :alt="selectedAttachment.name"
                        class="max-w-full max-h-[60vh] object-contain mx-auto rounded-lg" />

                    <div v-else-if="selectedAttachment" class="py-8">
                        <i class="pi pi-file text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Visualização não disponível</p>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ selectedAttachment?.name || 'Sem nome' }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ selectedAttachment ? getFileExtension(selectedAttachment.name) : '' }}
                    </p>
                </div>

                <hr class="my-3" />

                <div class="flex mb-2">
                    <Button label="Baixar" icon="pi pi-download" class="p-button-text p-button-sm"
                        @click="downloadAttachment(selectedAttachment)" />

                    <Button label="Editar" icon="pi pi-pencil" class="p-button-text p-button-sm text-blue-600"
                        @click="openEditAttachment(selectedAttachment)" />

                    <Button label="Deletar" icon="pi pi-trash" class="p-button-text p-button-sm text-red-600"
                        @click="confirmDelete(selectedAttachment)" />
                </div>

            </Dialog>

        </div>
    </AdminLayout>
</template>
<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Toast from 'primevue/toast'
import ConfirmDialog from 'primevue/confirmdialog'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import axios from 'axios'
import { useConfirm } from 'primevue/useconfirm'

const confirm = useConfirm()

const props = defineProps({
    medicine: Object,
    isEdit: Boolean,
    isView: Boolean
})

/* STATES */
const toast = ref()
const file = ref(null)
const attachmentName = ref('')
const isDragOver = ref(false)
const uploadModal = ref(false)
const loading = ref(false)
const viewModal = ref(false)
const selectedAttachment = ref(null)
const editingAttachment = ref(null)

const attachments = ref(props.medicine.attachments ?? [])

/* COMPUTED */
const images = computed(() =>
    attachments.value.filter(a => isImage(a))
)

const pdfs = computed(() =>
    attachments.value.filter(a => {
        if (!a.name) return false
        const ext = getFileExtension(a.name).toLowerCase()
        return ['pdf', 'doc', 'docx', 'txt'].includes(ext)
    })
)

/* HELPERS */
const goBack = () => router.get(route('medicines.index'))

const getAttachmentUrl = (attachment) => `/storage/${attachment.path}`

const getFileExtension = (filename) =>
    filename?.split('.').pop() || ''

const isImage = (attachment) => {
    if (!attachment?.name) return false
    const ext = getFileExtension(attachment.name).toLowerCase()
    return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)
}

const formatFileSize = (bytes) => {
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return (bytes / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i]
}

/* OPEN/CLOSE MODAL */
const openUploadModal = () => {
    editingAttachment.value = null
    attachmentName.value = ''
    file.value = null
    uploadModal.value = true
}

const closeUploadModal = () => {
    uploadModal.value = false
    editingAttachment.value = null
    attachmentName.value = ''
    file.value = null
}

const onFileChange = (e) => {
    file.value = e.target.files[0]
}

const handleDrop = (e) => {
    isDragOver.value = false
    file.value = e.dataTransfer.files[0]
}


const uploadAttachment = async () => {
    // Se não houver arquivo, nome e não estiver editando, sai
    if (!file.value && !attachmentName.value.trim() && !editingAttachment.value) return;

    loading.value = true;

    const formData = new FormData();

    // Nome do arquivo, se informado
    if (attachmentName.value.trim()) {
        formData.append('name', attachmentName.value.trim());
    }

    // Arquivo selecionado
    if (file.value) {
        formData.append('file', file.value);
    }

    try {
        let response;

        if (editingAttachment.value) {
            // Atualizar anexo existente - rota com dois parâmetros

            console.log('Editing attachment UUID:', editingAttachment.value.uuid);
            response = await axios.post(
                route('attachments.update', [props.medicine.uuid, editingAttachment.value.uuid]),
                formData,
                { headers: { 'Content-Type': 'multipart/form-data' } }
            );
            console.log('Nott attachment UUID:', editingAttachment.value.uuid);

            // Atualiza o anexo na lista
            const index = attachments.value.findIndex(a => a.uuid === editingAttachment.value.uuid);
            if (index !== -1) attachments.value[index] = response.data;

        } else {
            // Novo anexo
            response = await axios.post(
                route('attachments.store', props.medicine.uuid),
                formData,
                { headers: { 'Content-Type': 'multipart/form-data' } }
            );
            attachments.value.push(response.data);
        }

        toast.value.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: editingAttachment.value ? 'Anexo Actualizado' : 'Anexo adicionado',
            life: 3000
        });

        closeUploadModal();

    } catch (error) {
        toast.value.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Não foi possível salvar o anexo',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};


/* DOWNLOAD */
const downloadAttachment = (attachment) =>
    window.open(route('attachments.download', [props.medicine.uuid, attachment.uuid]), '_blank')

/* DELETE */
const confirmDelete = (attachment) => {
    confirm.require({
        message: `Deseja realmente remover "${attachment.name}"?`,
        header: 'Confirmar Remoção',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Remover',
        accept: () => removeAttachment(attachment)
    })
}

const removeAttachment = async (attachment) => {
    loading.value = true

    try {
        await axios.delete(
            route('attachments.destroy', [props.medicine.uuid, attachment.uuid])
        )

        attachments.value = attachments.value.filter(a => a.uuid !== attachment.uuid)
        viewModal.value = false

        toast.value.add({
            severity: 'success',
            summary: 'Removido',
            detail: 'O anexo foi excluído.',
            life: 3000
        })

    } catch {
        toast.value.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Não foi possível excluir.',
            life: 3000
        })
    } finally {
        loading.value = false
    }
}

/* VIEW / EDIT */
const viewAttachment = (attachment) => {
    selectedAttachment.value = attachment
    viewModal.value = true
}

const editAttachment = (attachment) => {
    editingAttachment.value = attachment
    attachmentName.value = attachment.name || ''
    file.value = null
    uploadModal.value = true
}

const openEditAttachment = (attachment) => {
    viewModal.value = false
    editAttachment(attachment)
}
</script>

<style scoped>
.dropzone-hover {
    border-color: #60a5fa !important;
    background: #f0f8ff;
}

.rounded-full {
    border-radius: 80%;
}

.transition-all {
    transition: all 0.3s ease;
}
</style>
