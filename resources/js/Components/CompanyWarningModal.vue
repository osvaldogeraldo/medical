<template>
  <Dialog
    v-model:visible="visible"
    modal
    :closable="false"
    :style="{ width: '80vw', maxWidth: '1100px' }"
    class="company-register-dialog"
    contentClass="!pt-0"
  >
    <!-- Header -->
    <div class="flex items-center gap-3 py-4 px-3 border-0 border-b surface-border">
      <div class="flex-1 min-w-0">
        <div class="text-xl font-bold truncate">
          <Avatar icon="pi pi-building" size="large" class="bg-primary text-white" shape="circle" />
          Progresso do Cadastro
        </div>
        <div class="text-color-secondary text-sm">
          Complete o cadastro para liberar todas as funcionalidades.
        </div>
      </div>
    </div>

    <div class="p-4 flex flex-column gap-4">
      <!-- Barra de progresso (25% por step) -->
      <div class="flex items-center gap-3">
        <div class="flex-1">
          <div
            class="progress-track"
            role="progressbar"
            :aria-valuenow="progress"
            aria-valuemin="0"
            aria-valuemax="100"
          >
            <div class="progress-fill" :style="{ width: progress + '%' }"></div>
          </div>
        </div>
        <div class="text-right font-semibold w-16">{{ progress.toFixed(2) }}%</div>
      </div>

      <!-- Status strip -->
      <div class="status-strip mb-3">
        <i class="pi pi-spin pi-spinner mr-2" aria-hidden="true"></i>
        <span>{{ statusMessage }}</span>
      </div>

      <!-- Steps (4 originais) no estilo tabs -->
      <div class="steps-container">
        <TabMenu
          :model="tabItems"
          :activeIndex="currentStepIndex"
          @tab-change="onTabChange"
          class="tabmenu-steps"
        >
          <template #item="{ item, index, props }">
            <a
              v-bind="props.action"
              class="step-tab"
              :class="{ 'is-disabled': item.disabled }"
              @click.prevent="selectTab(index)"
              :aria-disabled="item.disabled"
            >
              <i :class="['step-icon', item.icon]"></i>
              <span class="step-label">{{ item.label }}</span>
              <Badge class="step-badge" :value="''" />
            </a>
          </template>
        </TabMenu>
      </div>

      <!-- === Card SOMENTE da etapa atual === -->
      <div class="grid">
        <div class="col-12 md:col-8 mx-auto">
          <Transition name="list" mode="out-in">
            <Card v-if="currentStep" :key="currentStep.key" class="mb-3 step-card">
              <template #header>
                <div class="flex items-center gap-2 p-3">
                  <i :class="currentStep.icon" class="text-primary"></i>
                  <div class="font-bold">{{ currentStep.label }}</div>
                  <Tag
                    class="ml-auto"
                    :value="currentStep.completed ? 'Completo' : 'Pendente'"
                    :severity="currentStep.completed ? 'success' : 'warning'"
                    :icon="currentStep.completed ? 'pi pi-check' : 'pi pi-exclamation-triangle'"
                  />
                </div>
              </template>
              <template #content>
                <p class="m-0 text-color-secondary">{{ currentStep.description }}</p>
                <div
                  v-if="currentStep.hint"
                  class="mt-3 text-sm text-color-secondary flex items-center gap-2"
                >
                  <i class="pi pi-info-circle"></i> {{ currentStep.hint }}
                </div>
              </template>
              <template #footer>
                <div class="flex gap-2">
                  <Button
                    v-if="!currentStep.completed"
                    :label="'Completar ' + currentStep.label"
                    icon="pi pi-check-circle"
                    class="p-button-sm flex-1"
                    @click="navigateToStep(currentStep.route)"
                  />
                  <Button
                    v-else
                    label="Revisar"
                    icon="pi pi-eye"
                    class="p-button-outlined p-button-sm flex-1"
                    @click="navigateToStep(currentStep.route)"
                  />
                </div>
              </template>
            </Card>
          </Transition>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-content-center gap-3 w-full">
        <Button
          label="Continuar Cadastro"
          icon="pi pi-arrow-right"
          class="p-button-primary"
          @click="redirectToNextRequiredStep"
        />
      </div>
    </template>

    <Toast />
  </Dialog>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

import Dialog from 'primevue/dialog'
import Card from 'primevue/card'
import Tag from 'primevue/tag'
import Button from 'primevue/button'
import Avatar from 'primevue/avatar'
import Badge from 'primevue/badge'
import TabMenu from 'primevue/tabmenu'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

import Roles from '@/Enums/RolesEnum'
import { usePermissions } from '@/Composables/usePermissions'

const toast = useToast()
const visible = ref(false) // começa escondido
const page = usePage()
const { hasAnyRole } = usePermissions()

/** Acesso ao user/authenticatable vindos do Inertia */
const user = computed(() => page.props?.auth?.user ?? null)
const company = computed(() => user.value?.authenticatable ?? null)

/** Regras de existência (true = preenchido) */
const hasGeneral      = computed(() => !!company.value)
const hasBankAccounts = computed(() => (company.value?.bank_accounts?.length ?? 0) > 0)
const hasDocuments    = computed(() => (company.value?.documents?.length ?? 0) > 0)
const hasActivities   = computed(() => (company.value?.activities?.length ?? 0) > 0)

/** Steps (4 originais) — ROTAS ATUALIZADAS */
const steps = ref([
  { key:'general',     label:'Dados Gerais',   icon:'pi pi-id-card',    description:'Informações básicas da empresa', hint:'NUIT, Inicio de actividades e dados de contato, etc ...', completed:false, route:'/dashboard/company/create#general' },
  { key:'bank',        label:'Dados Bancários',icon:'pi pi-credit-card', description:'Contas bancárias da empresa',    hint:'BCI, BIM, MOZA, etc...',                completed:false, route:'/dashboard/company/bank/create' },
  { key:'attachments', label:'Anexos',         icon:'pi pi-paperclip',   description:'Documentos necessários',         hint:'Alvara, etc...',   completed:false, route:'/dashboard/company/attachments/create' },
  { key:'activities',  label:'Atividades',     icon:'pi pi-briefcase',   description:'Ramo de atuação da empresa',     hint:'Defina CNAE/serviços prestados',        completed:false, route:'/dashboard/company/activities' }
])

/** Marca completed conforme dados reais do auth */
const hydrateCompletionFromAuth = () => {
  const map = {
    general: hasGeneral.value,
    bank: hasBankAccounts.value,
    attachments: hasDocuments.value,
    activities: hasActivities.value
  }
  steps.value = steps.value.map(s => ({ ...s, completed: !!map[s.key] }))
}

/** === PROGRESSO: 25% por step === */
const progress = computed(() => {
  const total = steps.value.length || 1
  const done  = steps.value.filter(s => s.completed).length
  return (done / total) * 100
})

/** Próxima etapa obrigatória (ordem sequencial) */
const order = ['general','bank','attachments','activities']
const nextRequiredKey = computed(() => {
  if (!hasGeneral.value)      return 'general'
  if (!hasBankAccounts.value) return 'bank'
  if (!hasDocuments.value)    return 'attachments'
  if (!hasActivities.value)   return 'activities'
  return null
})

/** Só mostra modal se faltar algo */
const mustShowModal = computed(() => nextRequiredKey.value !== null)

/** Step atual (hash → ou próxima obrigatória) */
const currentStepIndex = ref(0)
const findIndexByHash = (hash) => {
  const key = (hash || '').replace('#','').trim()
  return key ? order.indexOf(key) : -1
}
const firstPendingIndex = () => {
  const k = nextRequiredKey.value
  return k ? order.indexOf(k) : 0
}
const syncCurrentFromLocation = () => {
  const idxFromHash = findIndexByHash(window.location.hash)
  currentStepIndex.value = idxFromHash >= 0 ? idxFromHash : firstPendingIndex()
}
const currentStep = computed(() => steps.value[currentStepIndex.value])

/** Desabilita passos depois da próxima obrigatória */
const isStepDisabled = (key) => {
  const nextKey = nextRequiredKey.value
  if (!nextKey) return false
  return order.indexOf(key) > order.indexOf(nextKey)
}

/** TabMenu items (com disabled) */
const tabItems = computed(() =>
  steps.value.map((s, i) => ({
    label: s.label,
    icon: s.icon,
    disabled: isStepDisabled(s.key),
    command: () => selectTab(i)
  }))
)

const onTabChange = (e) => selectTab(e.index)
const selectTab = (index) => {
  const key = steps.value[index].key
  if (isStepDisabled(key)) {
    toast.add({ severity:'warn', summary:'Passo bloqueado', detail:'Conclua as etapas anteriores para avançar.', life:3000 })
    return
  }
  currentStepIndex.value = index
  window.location.hash = '#' + key
}

/** Banner */
const statusMessage = computed(() => {
  const key = nextRequiredKey.value
  if (!key) return 'Cadastro concluído'
  const step = steps.value.find(s => s.key === key)
  return `Próxima etapa obrigatória: ${step?.label ?? ''}`
})

/** Mostrar/ocultar modal — evita abrir nas rotas de criação/edição */
const shouldShowModal = () => {
  const isSupplier = hasAnyRole([Roles.ADMIN_SUPPLIER, Roles.SUPPLIER_COMMERCIAL])
  const blockedPaths = [
    '/dashboard/company/create',
    '/dashboard/company/bank/create',
    '/dashboard/company/attachments/create',
    '/dashboard/company/activities'
  ]
  const notOnStepPage = blockedPaths.every(p => !window.location.pathname.startsWith(p))
  return isSupplier && mustShowModal.value && notOnStepPage
}

/** Navegar para a tela real da etapa */
const navigateToStep = (route) => {
  visible.value = false
  window.location.assign(route)
}

/** CTA do rodapé: vai direto à próxima obrigatória */
const redirectToNextRequiredStep = () => {
  const key = nextRequiredKey.value
  const idx = key ? order.indexOf(key) : firstPendingIndex()
  const step = steps.value[idx]
  currentStepIndex.value = idx
  window.location.hash = `#${step.key}`
  navigateToStep(step.route)
}

/** ======= ATIVA O PRÓXIMO STEP AO CONCLUIR O ATUAL ======= */
watch(
  () => nextRequiredKey.value,
  (newKey) => {
    if (newKey === null) {
      visible.value = false
      return
    }
    const idx = order.indexOf(newKey)
    if (idx !== -1 && idx !== currentStepIndex.value) {
      currentStepIndex.value = idx
      window.location.hash = '#' + newKey
    }
    visible.value = shouldShowModal()
  }
)

/** Sync & listeners */
const onHashChange = () => syncCurrentFromLocation()

onMounted(() => {
  hydrateCompletionFromAuth()
  syncCurrentFromLocation()
  window.addEventListener('hashchange', onHashChange)
  visible.value = shouldShowModal()
})

onBeforeUnmount(() => {
  window.removeEventListener('hashchange', onHashChange)
})

/** Se o auth mudar (ex.: após salvar no backend), rehidrata e atualiza */
watch(
  () => page.props?.auth?.user,
  () => {
    hydrateCompletionFromAuth()
    syncCurrentFromLocation()
    visible.value = shouldShowModal()
  },
  { deep: true, immediate: true }
)

/** Também vigia a URL para reabrir ao sair das páginas de criação */
watch(
  () => page.url,
  (u) => {
    hydrateCompletionFromAuth()
    syncCurrentFromLocation()
    const isStepPage = [
      '/dashboard/company/create',
      '/dashboard/company/bank/create',
      '/dashboard/company/attachments/create',
      '/dashboard/company/activities'
    ].some(p => u.startsWith(p))
    if (!isStepPage) visible.value = shouldShowModal()
  }
)
</script>

<style scoped>
.company-register-dialog { border-radius: 14px; }

/* ===== Progress bar ===== */
.progress-track{
  height:8px; width:100%;
  background:var(--surface-200,#eef1f6);
  border-radius:999px; overflow:hidden; position:relative;
}
.progress-fill{
  height:100%; width:0%; border-radius:inherit;
  background:linear-gradient(90deg,#3b82f6,#60a5fa 50%,#93c5fd);
  position:relative; transition:width .5s cubic-bezier(.2,.9,.2,1);
}
.progress-fill::after{
  content:''; position:absolute; top:0; bottom:0; right:-30%; width:30%;
  background:linear-gradient(90deg,rgba(255,255,255,0),rgba(255,255,255,.35),rgba(255,255,255,0));
  animation:sheen 2.6s infinite; pointer-events:none;
}
@keyframes sheen{ 0%{ transform:translateX(-120%); opacity:0; } 40%{ opacity:.9; } 100%{ transform:translateX(0%); opacity:0; } }

/* ===== Status strip ===== */
.status-strip{
  display:flex; align-items:center; gap:.5rem; font-size:.9rem;
  padding:.65rem .85rem; border-radius:10px;
  background: color-mix(in oklab, var(--primary-color,#3b82f6) 12%, white);
  border:1px solid color-mix(in oklab, var(--primary-color,#3b82f6) 25%, transparent);
}

/* ===== TabMenu steps (estilo do exemplo) ===== */
.tabmenu-steps :deep(.p-tabmenu){ border:0; }
.tabmenu-steps :deep(.p-tabmenu-nav){
  border:0; gap:.25rem; padding:0 .25rem;
  overflow-x:auto; scrollbar-width:none;
}
.tabmenu-steps :deep(.p-tabmenu-nav::-webkit-scrollbar){ display:none; }

.step-tab{
  display:flex; align-items:center; gap:.5rem;
  padding:.35rem .6rem .6rem;
  color: var(--text-color-secondary);
  position:relative; text-decoration:none; border-radius:8px;
}
.step-tab:hover{ background: color-mix(in oklab, var(--primary-color) 6%, transparent); }
.step-tab.is-disabled{ opacity:.5; cursor:not-allowed; }
.step-icon{ font-size:1rem; opacity:.9; }
.step-label{ font-weight:600; white-space:nowrap; }

/* badge “vazio” só para manter círculo do exemplo */
.step-badge{
  width:1.15rem; height:1.15rem; border-radius:999px;
  background: color-mix(in oklab, var(--primary-color) 25%, #eaeaea);
  color:transparent;
}

/* Sublinhado do item ativo */
.tabmenu-steps :deep(.p-tabmenuitem.p-highlight) .step-tab{
  color: var(--text-color);
}
.tabmenu-steps :deep(.p-tabmenuitem.p-highlight) .step-tab::after{
  content:''; position:absolute; left:.6rem; right:.6rem; bottom:-1px; height:2px;
  background: linear-gradient(90deg, #7c4dff, #8da2fb);
  border-radius:999px;
}

/* ===== Card ===== */
.step-card{
  border-radius:12px; box-shadow:0 6px 24px rgba(0,0,0,.06);
  transition: transform .18s ease, box-shadow .18s ease;
}
.step-card:hover{ transform:translateY(-2px); box-shadow:0 10px 28px rgba(0,0,0,.08); }
:deep(.p-card-header){ border-bottom:1px solid var(--surface-border); }

/* ===== Transitions ===== */
.list-enter-active, .list-leave-active{ transition: all .22s ease; }
.list-enter-from, .list-leave-to{ opacity:0; transform: translateY(6px); }

/* Dark mode */
@media (prefers-color-scheme: dark){
  .status-strip{
    background: color-mix(in oklab, var(--primary-color,#3b82f6) 18%, #dbeafe);
    border-color: color-mix(in oklab, var(--primary-color,#3b82f6) 45%, #dbeafe);
  }
}
</style>
