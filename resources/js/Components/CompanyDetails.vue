<template>
  <div class="card shadow-sm border-0">
    <!-- Cabeçalho -->
    <div class="card-header bg-white border-0 py-3">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <i class="pi pi-briefcase text-primary"></i>
          <h5 class="mb-0">Detalhes da Empresa</h5>
        </div>
        <button
          v-if="company"
          class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2"
          @click="$emit('edit')"
        >
          <i class="pi pi-pencil"></i>
          Editar
        </button>
      </div>
    </div>

    <div class="card-body pt-0">
      <div v-if="company" class="d-flex flex-column gap-4">
        <!-- Seção: Informação Legal -->
        <section class="border rounded-3 p-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="pi pi-id-card text-secondary"></i>
            <h6 class="mb-0 text-muted text-uppercase small fw-semibold">Informação Legal</h6>
          </div>

          <div class="row g-3">
            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-id-card" label="Nome">
              <span class="fw-semibold">{{ company.legal_name || '—' }}</span>
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-shield" label="NUIT">
              {{ company.nuit || '—' }}
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-calendar" label="Constituição">
              {{ formatDate(company.established_date) }}
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-sitemap" label="Dimensão">
              <span v-if="company.dimention" class="badge text-bg-info-subtle border border-info-subtle rounded-pill px-3 py-2">
                {{ company.dimention }}
              </span>
              <span v-else>—</span>
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-objects-column" label="Tipo de Sociedade">
              {{ company.type_of_company || '—' }}
            </FieldRow>
          </div>
        </section>

        <!-- Seção: Localização & Contactos -->
        <section class="border rounded-3 p-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="pi pi-map text-secondary"></i>
            <h6 class="mb-0 text-muted text-uppercase small fw-semibold">Localização & Contactos</h6>
          </div>

          <div class="row g-3">
            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-globe" label="País">
              {{ countryName }}
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-map" label="Província">
              {{ provinceName }}
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-compass" label="Rua / Avenida">
              {{ company.address || '—' }}
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-hashtag" label="Número">
              {{ company.number_street || '—' }}
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-phone" label="Contacto Principal">
              <span v-if="company.phone_number" class="d-inline-flex align-items-center gap-2">
                <i class="pi pi-check-circle text-success"></i>{{ company.phone_number }}
              </span>
              <span v-else>—</span>
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-phone" label="Contacto Alternativo">
              {{ company.phone_number_alternative || '—' }}
            </FieldRow>

            <FieldRow class="col-12 col-md-6 col-xl-4" icon="pi pi-envelope" label="Email">
              {{ company.email || '—' }}
            </FieldRow>
          </div>
        </section>

        <!-- Seção: Presença Online -->
        <section class="border rounded-3 p-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="pi pi-globe text-secondary"></i>
            <h6 class="mb-0 text-muted text-uppercase small fw-semibold">Presença Online</h6>
          </div>

          <div class="row g-3">
            <FieldRow
              v-if="company.facebook"
              class="col-12 col-md-6 col-xl-4"
              icon="pi pi-facebook"
              label="Facebook"
            >
              <a :href="company.facebook" target="_blank" class="link-primary d-inline-flex align-items-center gap-2 text-decoration-underline">
                <i class="pi pi-external-link"></i>
                <span class="text-truncate" style="max-width: 20rem">{{ company.facebook }}</span>
              </a>
            </FieldRow>

            <FieldRow
              v-if="company.linkedin"
              class="col-12 col-md-6 col-xl-4"
              icon="pi pi-linkedin"
              label="LinkedIn"
            >
              <a :href="company.linkedin" target="_blank" class="link-primary d-inline-flex align-items-center gap-2 text-decoration-underline">
                <i class="pi pi-external-link"></i>
                <span class="text-truncate" style="max-width: 20rem">{{ company.linkedin }}</span>
              </a>
            </FieldRow>

            <FieldRow
              v-if="company.website"
              class="col-12 col-md-6 col-xl-4"
              icon="pi pi-globe"
              label="Website"
            >
              <a :href="company.website" target="_blank" class="link-primary d-inline-flex align-items-center gap-2 text-decoration-underline">
                <i class="pi pi-external-link"></i>
                <span class="text-truncate" style="max-width: 20rem">{{ company.website }}</span>
              </a>
            </FieldRow>
          </div>
        </section>
      </div>

      <div v-else class="alert alert-warning mt-3 mb-0">
        Dados da empresa indisponíveis.
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, defineComponent, h } from 'vue'

const props = defineProps({
  company: Object,
  countries: { type: Array, default: () => [] }
})

defineEmits(['edit'])

/* Computeds originais */
const countryName = computed(() => {
  if (props.company?.country?.name) return props.company.country.name
  const c = props.countries.find(c => c.id === props.company?.country_id)
  return c?.name || '—'
})

const provinceName = computed(() => {
  if (props.company?.province?.name) return props.company.province.name
  if (props.company?.province && typeof props.company.province === 'string') {
    return props.company.province
  }
  return '—'
})

function formatDate (d) {
  if (!d) return '—'
  const dt = typeof d === 'string' ? new Date(d) : new Date(d)
  if (Number.isNaN(dt.getTime())) return d
  return dt.toLocaleDateString('pt-PT')
}

/**
 * Componente local para uma linha (label + valor) dentro de uma coluna Bootstrap.
 * Mantém alinhamento vertical centralizado e boa legibilidade.
 */
const FieldRow = defineComponent({
  name: 'FieldRow',
  props: {
    label: { type: String, required: true },
    icon: { type: String, default: '' }
  },
  setup (p, { slots, attrs }) {
    return () =>
      h(
        'div',
        { class: ['d-flex flex-column gap-1', attrs.class] },
        [
          h('div', { class: 'text-muted small d-flex align-items-center gap-2' }, [
            p.icon ? h('i', { class: `${p.icon} text-secondary` }) : null,
            h('strong', `${p.label}:`)
          ]),
          h('div', { class: 'text-body' }, slots.default ? slots.default() : '—')
        ]
      )
  }
})
</script>

<style scoped>
/* pequenos toques visuais */
section + section { margin-top: .25rem; }
.text-bg-info-subtle {
  background-color: rgba(13,202,240,.15);
  color: #055160;
}
.border-info-subtle { border-color: rgba(13,202,240,.25) !important; }
</style>
