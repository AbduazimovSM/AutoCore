<template>
    <div class="card1 universal-page-card">
        <ProductMenu class="mb-8" />

        <Toolbar style="border-radius: 0">
            <template #start>
                <Button :label="t('global.buttons.add')" icon="pi pi-plus" class="mr-2"
                    @click="dialogStore.openNew(type)" />

                <Button icon="pi pi-trash" severity="danger" :disabled="!selectedReferences.length"
                    @click="confirmDeleteReferences">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-trash" viewBox="0 0 16 16">
                        <path
                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                        <path
                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                    </svg>
                </Button>
            </template>

            <template #end>
                <div class="flex gap-2">
                    <IconField iconPosition="left">
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>

                        <InputText v-model="search" :placeholder="t('global.buttons.search')" @input="onSearch" />
                    </IconField>

                    <Button v-if="search" icon="pi pi-times" severity="secondary" outlined @click="clearSearch"
                        v-tooltip.top="t('global.buttons.clear')" />
                </div>
            </template>
        </Toolbar>

        <div class="table-wrapper">
            <DataTable class="base-table" v-model:selection="selectedReferences" :value="references" :loading="loading"
                showGridlines data-key="id" resizableColumns columnResizeMode="fit" scrollable scrollHeight="flex"
                :tableStyle="{ minWidth: '760px' }" lazy :sortField="sortField"
                :sortOrder="sortOrder === 'asc' ? 1 : -1" @sort="onSort">
                <template #empty>
                    <p class="text-center">
                        {{ t('global.messages.no_data') }}
                    </p>
                </template>

                <Column selection-mode="multiple" style="width: 3rem" :exportable="false" />

<Column
    v-if="currentSettings.name"
    field="name"
    :header="t('references.model.table.name')"
    sortable
>
    <template #body="{ data }">
        <div class="reference-name-cell">

            <img
                v-if="
                    type === 'category' &&
                    currentSettings.image
                "
                :src="`${imageUrl}/images/references/${data.image}`"
                class="reference-name-image"
            />

            <span
                class="reference-name-link"
                @click="dialogStore.openEdit(data, type)"
            >
                {{ data.name }}
            </span>

        </div>
    </template>
</Column>

                <Column v-if="type === 'category' && currentSettings.parent_category" field="parent_id"
                    :header="t('references.model.table.parent_category')" sortable>
                    <template #body="{ data }">
                        {{ getParentCategoryName(data.parent_id) }}
                    </template>
                </Column>

                <Column v-if="
                    type === 'unit' &&
                    currentSettings.short_name
                " field="short_name" :header="t('references.model.table.short_name')" sortable>
                    <template #body="{ data }">
                        {{ data.short_name || '' }}
                    </template>
                </Column>

                <Column v-if="currentSettings.description" field="description"
                    :header="t('references.model.table.description')" sortable>
                    <template #body="{ data }">
                        {{ data.description || '' }}
                    </template>
                </Column>

                <Column v-if="currentSettings.status" field="status" :header="t('references.model.table.status')"
                    sortable>
                    <template #body="{ data }">
                        <Tag :value="data.status
                            ? t('global.status.active')
                            : t('global.status.inactive')
                            " :severity="data.status
                                ? 'success'
                                : 'danger'
                                " />
                    </template>
                </Column>

                <Column style="width: 4.1rem" :exportable="false">
                    <template #header>
                        <Button icon="pi pi-cog" rounded text severity="secondary"
                            @click="referenceSettingsDialog = true" />
                    </template>

                    <template #body="{ data }">
                        <Button icon="pi pi-ellipsis-h" rounded text severity="secondary"
                            @click="openActionsMenu($event, data)" />
                    </template>
                </Column>

                <Menu ref="actionsMenu" :model="actionItems" :popup="true" />
            </DataTable>

            <Paginator class="isp-paginator" style="border: 1px solid var(--surface-border)" :rows="rows" :first="first"
                :totalRecords="total" :rowsPerPageOptions="[5, 10, 25, 50, 100]"
                template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown JumpToPageInput"
                @page="onPage">
                <template #start>
                    <span v-if="total === 0">
                        {{ t('global.pagination.empty') }}
                    </span>

                    <span v-else>
                        {{
                            t('global.pagination.report', {
                                first: first + 1,
                                last: Math.min(first + rows, total),
                                totalRecords: total
                            })
                        }}
                    </span>
                </template>

                <template #end>
                    <Button type="button" icon="pi pi-refresh" @click="loadReferences" text />

                    <Button type="button" icon="pi pi-download" text />
                </template>
            </Paginator>
        </div>

        <ReferenceDialog />

        <ReferenceSettingsDialog v-model="referenceSettingsDialog" :type="type" />

        <DeleteReferenceDialog v-model="deleteReferenceDialog" :item-name="reference?.name"
            :loading="loadingDeleteReference" @confirm="destroyReference" />

        <DeleteReferencesDialog v-model="deleteReferencesDialog" :count="selectedReferences.length"
            :loading="loadingDeleteReferences" @confirm="destroyReferences" />
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';
import { useToast } from 'primevue/usetoast';

import '@/assets/app-datatable.css';

import ProductMenu from '@/modules/directories/components/ProductMenu.vue';

import {
    getReferences,
    deleteReference,
    deleteReferences
} from '@/modules/references/api/reference.api';

import ReferenceDialog from '@/modules/references/components/ReferenceDialog.vue';
import ReferenceSettingsDialog from '@/modules/references/components/ReferenceSettingsDialog.vue';
import DeleteReferenceDialog from '@/modules/references/components/DeleteReferenceDialog.vue';
import DeleteReferencesDialog from '@/modules/references/components/DeleteReferencesDialog.vue';

import {
    useReferenceDialogStore
} from '@/modules/references/stores/referenceDialog.store';

import {
    useReferenceSettingsStore
} from '@/modules/references/stores/referenceSettings.store';


const { t } = useI18n();
const imageUrl = import.meta.env.VITE_API_URL;
const route = useRoute();
const toast = useToast();

const dialogStore = useReferenceDialogStore();
const referenceSettings = useReferenceSettingsStore();


const referenceSettingsDialog = ref(false);

const reference = ref(null);
const references = ref([]);

const selectedReference = ref(null);
const selectedReferences = ref([]);

const deleteReferenceDialog = ref(false);
const deleteReferencesDialog = ref(false);

const rows = ref(10);
const first = ref(0);
const total = ref(0);

const sortField = ref('id');
const sortOrder = ref('asc');

const search = ref('');

let searchTimer = null;

const parentCategories = ref([]);

const loading = ref(false);
const loadingDeleteReference = ref(false);
const loadingDeleteReferences = ref(false);

const actionsMenu = ref(null);


const type = computed(() => {
    return route.query.type || 'category';
});


const currentSettings = computed(() => {
    return referenceSettings.fields[type.value] || {};
});


const actionItems = computed(() => [
    {
        label: t('global.buttons.update'),
        icon: 'pi pi-pencil',
        command: () => {
            dialogStore.openEdit(
                selectedReference.value,
                type.value
            );
        }
    },
    {
        label: t('global.buttons.copy'),
        icon: 'pi pi-copy',
        command: () => {
            dialogStore.openCopy(
                selectedReference.value,
                type.value
            );
        }
    },
    {
        label: t('global.buttons.delete'),
        icon: 'pi pi-trash',
        command: () => {
            confirmDeleteReference(
                selectedReference.value
            );
        }
    }
]);


function openActionsMenu(event, reference) {
    selectedReference.value = reference;
    actionsMenu.value.toggle(event);
}


function onSearch() {
    clearTimeout(searchTimer);

    searchTimer = setTimeout(async () => {
        first.value = 0;

        await loadReferences();
    }, 400);
}

async function clearSearch() {
    clearTimeout(searchTimer);

    search.value = '';
    first.value = 0;

    await loadReferences();
}

async function onPage(event) {
    first.value = event.first;
    rows.value = event.rows;

    await loadReferences();
}


async function onSort(event) {
    sortField.value = event.sortField;
    sortOrder.value =
        event.sortOrder === 1
            ? 'asc'
            : 'desc';

    first.value = 0;

    await loadReferences();
}


async function loadReferences() {
    loading.value = true;

    try {
        const page =
            first.value / rows.value + 1;

        const response = await getReferences(
            type.value,
            page,
            rows.value,
            sortField.value,
            sortOrder.value,
            search.value
        );

        references.value =
            response.data.data.data;

        total.value =
            response.data.data.total;

    } catch (error) {
        console.error(
            error.response?.data
        );

        references.value = [];
        total.value = 0;

        toast.add({
            severity: 'error',
            summary: t('global.toast.error'),
            detail: t('global.errors.load_failed'),
            life: 3000
        });

    } finally {
        loading.value = false;
    }
}


async function loadParentCategories() {
    if (type.value !== 'category') {
        parentCategories.value = [];
        return;
    }

    const response = await getReferences(
        'category',
        1,
        100,
        'id',
        'asc',
        ''
    );

    parentCategories.value =
        response.data.data.data;
}


function getParentCategoryName(parentId) {
    if (!parentId) {
        return '';
    }

    const parent =
        parentCategories.value.find(
            category =>
                category.id === parentId
        );

    return parent?.name || '';
}


function confirmDeleteReference(item) {
    reference.value = item;
    deleteReferenceDialog.value = true;
}


async function destroyReference() {
    if (
        loadingDeleteReference.value ||
        !reference.value?.id
    ) {
        return;
    }

    loadingDeleteReference.value = true;

    try {
        await deleteReference(
            reference.value.id
        );

        toast.add({
            severity: 'success',
            summary: t('global.toast.success'),
            detail: t('global.success.deleted'),
            life: 3000
        });

        deleteReferenceDialog.value = false;
        reference.value = null;

        await loadReferences();

    } catch (error) {
        console.error(
            error.response?.data
        );

        toast.add({
            severity: 'error',
            summary: t('global.toast.error'),
            detail: t('global.errors.delete_failed'),
            life: 3000
        });

    } finally {
        loadingDeleteReference.value = false;
    }
}


function confirmDeleteReferences() {
    deleteReferencesDialog.value = true;
}


async function destroyReferences() {
    if (loadingDeleteReferences.value) {
        return;
    }

    const ids =
        selectedReferences.value.map(
            item => item.id
        );

    if (!ids.length) {
        return;
    }

    loadingDeleteReferences.value = true;

    try {
        await deleteReferences(ids);

        toast.add({
            severity: 'success',
            summary: t('global.toast.success'),
            detail: t('global.success.delete_selected'),
            life: 3000
        });

        deleteReferencesDialog.value = false;
        selectedReferences.value = [];

        await loadReferences();

    } catch (error) {
        console.error(
            error.response?.data
        );

        toast.add({
            severity: 'error',
            summary: t('global.toast.error'),
            detail: t('global.errors.delete_selected_failed'),
            life: 3000
        });

    } finally {
        loadingDeleteReferences.value = false;
    }
}


watch(
    () => route.query.type,
    async () => {
        selectedReferences.value = [];
        reference.value = null;

        first.value = 0;
        search.value = '';

        await loadReferences();
        await loadParentCategories();
    },
    {
        immediate: true
    }
);


watch(
    () => dialogStore.changed,
    async (changed) => {
        if (!changed) {
            return;
        }

        await loadReferences();
        await loadParentCategories();

        dialogStore.resetChanged();
    }
);
</script>