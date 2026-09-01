<template>
    <Dialog v-model:visible="visible" :header="t('global.settings.title')" :style="{ width: '650px' }"
        :breakpoints="{ '960px': '95vw' }" modal dismissableMask class="product-settings-dialog">
        <div class="settings-table">
            <!-- Заголовок -->
            <div class="settings-head">
                <div>{{ t('global.settings.column_name') }}</div>
                <div>{{ t('global.settings.visibility') }}</div>
                <div>{{ t('global.settings.column_name') }}</div>
                <div>{{ t('global.settings.visibility') }}</div>
            </div>

            <!-- Поля -->
            <div v-for="(row, index) in fieldRows" :key="index" class="settings-row">
                <template v-for="item in row" :key="item?.key">
                    <template v-if="item">
                        <div class="settings-name">
                            {{ item.label }}
                        </div>

                        <div class="settings-switch">
                            <ToggleSwitch v-model="draft[item.key]" :disabled="[
                                'name',
                                'unit',
                                'category',
                                'status',
                                'barcode'
                            ].includes(item.key)" />
                        </div>
                    </template>

                    <template v-else>
                        <div class="settings-empty"></div>
                        <div class="settings-empty"></div>
                    </template>
                </template>
            </div>
        </div>

        <template #footer>
            <div class="dialog-footer">
                <Button :label="t('global.buttons.cancel')" severity="secondary" outlined class="footer-button"
                    @click="cancel" />

                <Button :label="t('global.buttons.save')" icon="pi pi-check" class="footer-button" @click="save" />
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

import { useProductSettingsStore } from '@/modules/directories/stores/productSettings.store';

const settings = useProductSettingsStore();

import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const visible = defineModel({
    type: Boolean,
    default: false
});

const draft = ref({});

const fields = computed(() => [
    {
        key: 'name',
        label: t('directories.products.table.name')
    },
    {
        key: 'barcode',
        label: t('directories.products.table.barcode')
    },
    {
        key: 'sku',
        label: t('directories.products.table.sku')
    },
    {
        key: 'category',
        label: t('directories.products.table.category')
    },
    {
        key: 'unit',
        label: t('directories.products.table.unit')
    },
    {
        key: 'brand',
        label: t('directories.products.table.brand')
    },
    {
        key: 'min_quantity',
        label: t('directories.products.table.min_quantity')
    },
    {
        key: 'image',
        label: t('directories.products.table.image')
    },
    {
        key: 'description',
        label: t('directories.products.table.description')
    },
    {
        key: 'status',
        label: t('directories.products.table.status')
    }
]);

const fieldRows = computed(() => {
    const rows = [];

    for (let i = 0; i < fields.value.length; i += 2) {
        rows.push([
            fields.value[i],
            fields.value[i + 1] ?? null
        ]);
    }

    return rows;
});

watch(
    visible,
    (isVisible) => {
        if (isVisible) {
            draft.value = {
                ...settings.fields
            };
        }
    }
);

function save() {
    settings.save({
        ...draft.value,
        name: true
    });
    visible.value = false;
}

function cancel() {
    visible.value = false;
}
</script>

<style scoped>
.settings-table {
    overflow: hidden;
    border: 1px solid var(--surface-border);
    border-radius: 12px;
    background: var(--surface-card);
}

.settings-head,
.settings-row {
    display: grid;
    grid-template-columns:
        minmax(180px, 1fr) 120px minmax(180px, 1fr) 120px;
}

.settings-head {
    background: var(--surface-100);
    font-weight: 600;
    border-bottom: 1px solid var(--surface-border);
}

.settings-head>div,
.settings-row>div {
    min-height: 24px;
    display: flex;
    align-items: center;
    padding: 0.5rem 0.8rem;
    border-right: 1px solid var(--surface-border);
}

.settings-head>div:last-child,
.settings-row>div:last-child {
    border-right: none;
}

.settings-row:not(:last-child) {
    border-bottom: 1px solid var(--surface-border);
}

.settings-name {
    font-size: 0.95rem;
    font-weight: 500;
}

.settings-switch {
    justify-content: center;
}

.settings-empty {
    background: var(--surface-50);
}

.dialog-footer {
    width: 100%;
    display: flex;
    justify-content: center;
    gap: 0.75rem;
}

.footer-button {
    min-width: 160px;
}

/* Планшет / телефон */
@media (max-width: 768px) {
    .settings-table {
        overflow-x: auto;
    }

    .settings-head,
    .settings-row {
        min-width: 650px;
    }

    .dialog-footer {
        flex-direction: column;
    }

    .footer-button {
        width: 100%;
    }
}
</style>