<template>
    <Toast />
    <form @submit.prevent="submitForm">
        <!-- Card: Identificação da Empresa -->
        <Card class="mb-4">
            <template #title>Identificação da Empresa</template>
            <template #content>
                <!-- Nome da empresa -->
                <div class="mb-3">
                    <label class="form-label">Nome da empresa <span class="text-danger">*</span></label>
                    <InputText type="text" v-model="form.legal_name" class="form-control"
                        :class="{ 'p-invalid': form.errors.legal_name }" />
                    <small v-if="form.errors.legal_name" class="text-danger">{{ form.errors.legal_name }}</small>
                </div>

                <!-- Nuit e Ano -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nuit <span class="text-danger">*</span></label>
                        <InputText type="text" v-model="form.nuit" class="form-control"
                            :class="{ 'p-invalid': form.errors.nuit }" />
                        <small v-if="form.errors.nuit" class="text-danger">{{ form.errors.nuit }}</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Data de constituição <span class="text-danger">*</span></label>
                        <Calendar v-model="form.established_date" dateFormat="yy-mm-dd" showIcon class="w-100" />
                        <small v-if="form.errors.established_date" class="text-danger">{{ form.errors.established_date
                            }}</small>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Card: Classificação -->
        <Card class="mb-4">
            <template #title>Classificação</template>
            <template #content>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Dimensão da empresa <span class="text-danger">*</span></label>
                        <Dropdown v-model="form.dimention" :options="dimentionOptions" optionLabel="label"
                            optionValue="value" placeholder="Selecione" class="w-100"
                            :class="{ 'p-invalid': form.errors.dimention }" />
                        <small v-if="form.errors.dimention" class="text-danger">{{ form.errors.dimention }}</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tipo de sociedade <span class="text-danger">*</span></label>
                        <Dropdown v-model="form.type_of_company" :options="companyTypeOptions" optionLabel="label"
                            optionValue="value" placeholder="Selecione" class="w-100"
                            :class="{ 'p-invalid': form.errors.type_of_company }" />
                        <small v-if="form.errors.type_of_company" class="text-danger">{{ form.errors.type_of_company
                            }}</small>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Card: Localização -->
        <Card class="mb-4">
            <template #title>Localização</template>
            <template #content>
                <div class="row g-3">
                    <!-- País -->
                    <div class="col-md-4">
                        <label class="form-label">País <span class="text-danger">*</span></label>
                        <Dropdown v-model="form.country_id" :options="countries" optionLabel="name" optionValue="id"
                            filter showClear placeholder="Selecione o país" class="w-100"
                            :class="{ 'p-invalid': form.errors.country_id }" />
                        <small v-if="form.errors.country_id" class="text-danger">{{ form.errors.country_id }}</small>
                    </div>

                    <!-- Província -->
                    <div class="col-md-4">
                        <label class="form-label">
                            Província
                            <span v-if="isMozambique" class="text-danger">*</span>
                        </label>
                        <Dropdown v-model="form.province_id" :options="provinceOptions" optionLabel="name"
                            optionValue="id" :disabled="!isMozambique || provinceLoading"
                            :placeholder="isMozambique ? (provinceLoading ? 'A carregar…' : 'Selecione a província') : 'Não aplicável'"
                            class="w-100" :class="{ 'p-invalid': form.errors.province_id }" filter showClear />
                        <small v-if="form.errors.province_id" class="text-danger">{{ form.errors.province_id }}</small>
                    </div>

                    <!-- Número -->
                    <div class="col-md-4">
                        <label class="form-label">Número <span class="text-danger">*</span></label>
                        <InputText type="text" v-model="form.number_street" class="w-100"
                            :class="{ 'p-invalid': form.errors.number_street }" />
                        <small v-if="form.errors.number_street" class="text-danger">{{ form.errors.number_street
                            }}</small>
                    </div>
                </div>

                <!-- Rua -->
                <div class="mt-3">
                    <label class="form-label">Rua/Avenida <span class="text-danger">*</span></label>
                    <InputText type="text" v-model="form.address" class="form-control"
                        :class="{ 'p-invalid': form.errors.address }" />
                    <small v-if="form.errors.address" class="text-danger">{{ form.errors.address }}</small>
                </div>
            </template>
        </Card>

        <!-- Card: Contactos -->
        <Card class="mb-4">
            <template #title>Contactos</template>
            <template #content>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Contacto principal <span class="text-danger">*</span></label>
                        <InputText type="text" v-model="form.phone_number" class="form-control"
                            :class="{ 'p-invalid': form.errors.phone_number }" />
                        <small v-if="form.errors.phone_number" class="text-danger">{{ form.errors.phone_number
                            }}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contacto alternativo</label>
                        <InputText type="text" v-model="form.phone_number_alternative" class="form-control"
                            :class="{ 'p-invalid': form.errors.phone_number_alternative }" />
                        <small v-if="form.errors.phone_number_alternative" class="text-danger">
                            {{ form.errors.phone_number_alternative }}
                        </small>
                    </div>
                </div>

                <!-- Email -->
                <div class="mt-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <InputText type="email" v-model="form.email" class="form-control"
                        :class="{ 'p-invalid': form.errors.email }" />
                    <small v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</small>
                </div>
            </template>
        </Card>

        <!-- Card: Redes Sociais -->
        <Card class="mb-4">
            <template #title>Redes Sociais</template>
            <template #content>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Facebook</label>
                        <InputText type="url" v-model="form.facebook" class="form-control"
                            :class="{ 'p-invalid': form.errors.facebook }" />
                        <small v-if="form.errors.facebook" class="text-danger">{{ form.errors.facebook }}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">LinkedIn</label>
                        <InputText type="url" v-model="form.linkedin" class="form-control"
                            :class="{ 'p-invalid': form.errors.linkedin }" />
                        <small v-if="form.errors.linkedin" class="text-danger">{{ form.errors.linkedin }}</small>
                    </div>
                </div>

                <!-- Website -->
                <div class="mt-3">
                    <label class="form-label">Website</label>
                    <InputText type="url" v-model="form.website" class="form-control"
                        :class="{ 'p-invalid': form.errors.website }" />
                    <small v-if="form.errors.website" class="text-danger">{{ form.errors.website }}</small>
                </div>
            </template>
        </Card>

        <!-- Botão -->
        <div class="d-flex justify-content-end">
            <Button type="submit" :label="loading ? 'Processando...' : submitLabel" icon="pi pi-check"
                :loading="loading" />
        </div>
    </form>
</template>

<script setup>
import axios from 'axios'
import { useForm } from '@inertiajs/vue3'
import { watch, ref, onMounted, computed } from 'vue'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Calendar from 'primevue/calendar'
import Dropdown from 'primevue/dropdown'
import Button from 'primevue/button'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

const toast = useToast()

const props = defineProps({
    company: Object,
    countries: { type: Array, default: () => [] },
    companyTypeOptions: { type: Array, default: () => [] },
    dimentionOptions: { type: Array, default: () => [] },
})

// usa diretamente as props nos Dropdowns
const companyTypeOptions = computed(() => props.companyTypeOptions)
const dimentionOptions = computed(() => props.dimentionOptions)

// detectar Moçambique SEM id
const isMozambique = computed(() => {
    const c = props.countries.find(c => c.id === form.country_id)
    if (!c?.name) return false
    return c.name.toString().trim().toLowerCase() === 'mozambique'
})
// const isMozambique = computed(() => form.country_id === mozambiqueId.value)
/* ===== Form ===== */
const form = useForm({
    legal_name: '',
    nuit: '',
    established_date: '',
    dimention: '',
    type_of_company: '',
    country_id: 152,
    province_id: null,          // <- FK correta
    number_street: '',
    address: '',
    phone_number: '',
    phone_number_alternative: '',
    email: '',
    facebook: '',
    linkedin: '',
    website: '',
    status: 'active'
})

/* Prefill quando existe company */
watch(
    () => props.company,
    (company) => {
        if (company) {
            form.defaults({
                ...company,
                established_date: company.established_date ? company.established_date.split('T')[0] : null,
                province_id: company.province_id ?? null,
                country_id: company.country_id ?? null
            })
            form.reset()
            // se já vier Moçambique, carrega as províncias e seta selecionada
            if (isMozambique.value && form.country_id) {
                loadProvinces(form.country_id).then(() => {
                    // garante coerência
                    if (!provinceOptions.value.find(p => p.id === form.province_id)) {
                        form.province_id = null
                    }
                })
            }
        }
    },
    { immediate: true }
)

/* ===== Provinces (dinâmica por país) ===== */
const provinceOptions = ref([])
const provinceLoading = ref(false)



// Descobre o ID de Moçambique pela lista vinda do backend
const mozambiqueId = computed(() => {
    const m = props.countries.find(
        c => (c.name || '').toString().trim().toLowerCase() === 'mozambique'
    )
    return m?.id ?? null
})

// País selecionado é Moçambique?

// Carrega províncias do país (só quando for Moçambique)
async function loadProvinces(countryId) {
    if (!countryId || !isMozambique.value) {
        provinceOptions.value = []
        return
    }
    provinceLoading.value = true
    try {
        // usa o método/rota por país
        const { data } = await axios.get(`/api/countries/${countryId}/provinces`)
        provinceOptions.value = Array.isArray(data) ? data : []
    } catch (e) {
        provinceOptions.value = []
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao carregar províncias.', life: 3000 })
    } finally {
        provinceLoading.value = false
    }
}

// Quando o país muda: zera a província e (se for MZ) carrega lista
watch(
    () => form.country_id,
    async () => {
        form.province_id = null
        if (isMozambique.value) {
            await loadProvinces(form.country_id)
        } else {
            provinceOptions.value = []
        }
    },
    { immediate: true }
)
/* ===== Submit ===== */
const loading = ref(false)
const submitLabel = computed(() => (props.company ? 'Atualizar' : 'Registar'))


function submitForm() {
    loading.value = true

    const url = props.company
        ? `/dashboard/company/update/${props.company.id}`
        : '/dashboard/company/store'
    form.post(url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: props.company ? 'Empresa atualizada com sucesso.' : 'Empresa criada com sucesso.',
                life: 3000
            })
            if (!props.company) form.reset()
        },
        onError: () => {
            // Erros já aparecem em form.errors; toast adicional opcional
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: 'Verifique os campos assinalados.',
                life: 3000
            })
        },
        onFinish: () => {
            loading.value = false
        }
    })
}

/* Mensagem de sucesso vinda por props (opcional) */
onMounted(() => {
    // se você estiver a enviar flash pela página pai, pode ler via page.props
})
</script>
