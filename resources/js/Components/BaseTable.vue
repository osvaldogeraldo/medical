<template>
    <div class="card basic-data-table">
        <!-- Cabeçalho com título e botão -->
        <div class="card-header">
            <div
                class="d-flex flex-wrap align-items-center justify-content-between gap-3"
            >
                <p class="fw-semibold mb-0">{{ title }}</p>

                <div class="d-flex align-items-center gap-2">
                    <!-- Slot para ações adicionais (filtros, etc) -->
                    <slot name="header-actions"></slot>

                    <!-- Botão de criação -->
                    <div v-if="link && linkText">
                        <Link
                            :href="route(link)"
                            class="fw-semibold text-primary-light py-16 px-24 border radius-12 text-md d-flex align-items-center justify-content-center gap-12 bg-hover-primary-50 text-decoration-none"
                            role="button"
                        >
                            <iconify-icon
                                icon="lucide:plus"
                                class="text-primary-600 text-xl"
                            />
                            {{ linkText }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Corpo da tabela -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th
                                v-for="(column, i) in columns"
                                :key="i"
                                class="text-center text-muted"
                            >
                                {{ column.label }}
                            </th>
                            <!-- <th>Acções</th> -->
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="!items || !items.length">
                            <td
                                :colspan="columns.length + 2"
                                class="text-center text-muted"
                            >
                                Sem dados disponíveis
                            </td>
                        </tr>

                        <tr
                            v-for="(item, index) in items"
                            :key="index"
                            class="text-center text-muted"
                        >
                            <!-- Índice -->
                            <td>
                                {{ (index + 1).toString().padStart(2, "0") }}
                            </td>

                            <!-- Colunas dinamicamente -->
                            <td v-for="(column, i) in columns" :key="i">
                                <slot
                                    v-if="
                                        column.field !== 'status' &&
                                        column.field !== 'estimated_value'
                                    "
                                    :name="column.slot"
                                    :value="getNestedValue(item, column.field)"
                                    :item="item"
                                    :column="column"
                                >
                                    {{ getNestedValue(item, column.field) }}
                                </slot>
                                <slot
                                    v-if="column.field == 'estimated_value'"
                                    :name="column.slot"
                                    :value="getNestedValue(item, column.field)"
                                    :item="item"
                                    :column="column"
                                >
                                    {{ getNestedValue(item, column.field) }}
                                </slot>
                                <span
                                    v-if="
                                        column.field == 'status' &&
                                        getNestedValue(item, column.field) ===
                                            contestStatus.UNPUBLISHED
                                    "
                                    class="bg-warning-focus text-warning-main px-32 py-1 rounded-pill fw-medium text-sm"
                                    >{{
                                        getNestedValue(item, column.field)
                                    }}</span
                                >
                                <span
                                    v-if="
                                        column.field == 'status' &&
                                        getNestedValue(item, column.field) ===
                                            contestStatus.PUBLISHED
                                    "
                                    class="bg-success-focus text-success-main px-32 py-1 rounded-pill fw-medium text-sm"
                                    >{{
                                        getNestedValue(item, column.field)
                                    }}</span
                                >
                                <span
                                    v-if="
                                        column.field == 'status' &&
                                        getNestedValue(item, column.field) ===
                                            contestStatus.CLOSED
                                    "
                                    class="bg-danger-focus text-danger-main px-32 py-1 rounded-pill fw-medium text-sm"
                                    >{{
                                        getNestedValue(item, column.field)
                                    }}</span
                                >
                            </td>

                            <!-- Ações -->
                            <!-- <td>
                                <slot name="actions" :item="item" />
                            </td> -->
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <nav v-if="pagination && pagination.last_page > 1" class="mt-3">
                <ul class="pagination justify-content-end mb-0">
                    <li
                        v-for="(link, index) in pagination.links"
                        :key="index"
                        :class="[
                            'page-item',
                            { active: link.active, disabled: !link.url },
                        ]"
                    >
                        <a
                            class="page-link"
                            href="#"
                            @click.prevent="
                                $emit('paginate', getPageNumber(link.url))
                            "
                            v-html="link.label"
                        ></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script setup>
// import { contestStatus } from "@/Enums/ContestEnum";
import { Link } from "@inertiajs/vue3";

// Props
const props = defineProps({
    title: String,
    columns: {
        type: Array,
        default: () => [],
    },
    items: {
        type: Array,
        default: () => [],
    },
    pagination: {
        type: Object,
        default: null,
    },
    link: String,
    linkText: String,
});

// Função utilitária para acessar valores aninhados
function getNestedValue(obj, path) {
    try {
        if (!path) return undefined;

        const keys = path
            .replace(/\[(\w+)\]/g, ".$1")
            .replace(/^\./, "")
            .split(".");

        let result = obj;
        for (let key of keys) {
            if (result == null) return undefined;
            result = result[key];
        }
        return result;
    } catch {
        return undefined;
    }
}

// Extrai número da página de uma URL
function getPageNumber(url) {
    if (!url) return null;
    const query = new URLSearchParams(url.split("?")[1]);
    return query.get("page");
}
</script>
