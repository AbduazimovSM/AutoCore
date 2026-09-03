<template>
    <Dialog v-model:visible="dialogStore.visible" maximizable :style="{ width: '600px' }"
        :breakpoints="{ '700px': '95vw' }" :header="t('references.model.dialog.title_reference')" modal class="p-fluid">

        <div class="reference-form">

            <!-- Название -->
            <div v-if="currentSettings.name" class="field">
                <FloatLabel variant="on">
                    <InputText id="name" v-model.trim="reference.name" autofocus :invalid="submitted && !reference.name"
                        fluid />

                    <label for="name">
                        {{ t('references.model.table.name') }}
                    </label>
                </FloatLabel>

                <small v-if="submitted && !reference.name" class="p-error">
                    {{ t('global.messages.required') }}
                </small>
            </div>


            <!-- Родительская категория: только CATEGORY -->
            <div v-if="
                dialogStore.type === 'category' &&
                currentSettings.parent_category
            " class="field">
                <FloatLabel variant="on">
                    <AppTreeSelect v-model="reference.parent_id" :loader="loadCategories" />

                    <label for="parent_id">
                        {{ t('references.model.table.parent_category') }}
                    </label>
                </FloatLabel>
            </div>

            <!-- Картинка: только CATEGORY -->
            <div v-if="dialogStore.type === 'category' &&
                currentSettings.image
            " class="field">
                <FileUpload mode="basic" name="image" accept="image/*" :maxFileSize="2000000" :auto="false" customUpload
                    @select="onSelect" :chooseLabel="t('global.buttons.select')" />
            </div>


            <!-- Краткое название: только UNIT -->
            <div v-if="
                dialogStore.type === 'unit' &&
                currentSettings.short_name
            " class="field">
                <FloatLabel variant="on">
                    <InputText id="short_name" v-model.trim="reference.short_name" fluid />

                    <label for="short_name">
                        {{ t('references.model.table.short_name') }}
                    </label>
                </FloatLabel>
            </div>


            <!-- Описание -->
            <div v-if="currentSettings.description" class="field field-full">
                <FloatLabel variant="on">
                    <Textarea id="description" v-model="reference.description" rows="3" fluid />

                    <label for="description">
                        {{ t('references.model.table.description') }}
                    </label>
                </FloatLabel>
            </div>


            <!-- Статус -->
            <div v-if="currentSettings.status" class="field field-full">
                <FloatLabel variant="on">
                    <Select id="status" v-model="reference.status" :options="statuses" option-label="label"
                        option-value="value" fluid />

                    <label for="status">
                        {{ t('references.model.table.status') }}
                    </label>
                </FloatLabel>
            </div>

        </div>


        <div class="reference-dialog-actions">
            <Button :label="t('global.buttons.cancel')" icon="pi pi-times" text :disabled="saving"
                @click="dialogStore.close" />

            <Button :label="t('global.buttons.save')" icon="pi pi-check" text :loading="saving" :disabled="saving"
                @click="saveReference" />
        </div>

    </Dialog>
</template>


<script setup>
import { computed, ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useI18n } from 'vue-i18n';

import AppTreeSelect from '@/components/AppTreeSelect.vue';

import {
    getReferences,
    createReference,
    updateReference
} from '@/modules/references/api/reference.api';

import {
    useReferenceDialogStore
} from '@/modules/references/stores/referenceDialog.store';

import {
    useReferenceSettingsStore
} from '@/modules/references/stores/referenceSettings.store';


const { t } = useI18n();

const dialogStore = useReferenceDialogStore();
const referenceSettings = useReferenceSettingsStore();

const toast = useToast();

const reference = ref({});
const submitted = ref(false);
const saving = ref(false);
const imageFile = ref(null);
function onSelect(event) {
    imageFile.value = event.files[0];
}
/*
|--------------------------------------------------------------------------
| Настройки текущего type
|--------------------------------------------------------------------------
|
| category -> referenceSettings.fields.category
| unit     -> referenceSettings.fields.unit
| brand    -> referenceSettings.fields.brand
|
*/
const currentSettings = computed(() => {
    return referenceSettings.fields[dialogStore.type] || {};
});


const statuses = computed(() => [
    {
        label: t('global.status.active'),
        value: true
    },
    {
        label: t('global.status.inactive'),
        value: false
    }
]);


function emptyReference() {
    return {
        type: dialogStore.type,
        name: '',
        short_name: null,
        parent_id: null,
        image: 'default.png',
        description: '',
        status: true
    };
}


async function loadCategories(search = '') {
    const response = await getReferences(
        'category',
        1,
        100000,
        'name',
        'asc',
        search
    );

    return response.data.data.data;
}


watch(
    () => dialogStore.visible,
    (visible) => {
        if (!visible) {
            return;
        }

        submitted.value = false;
        imageFile.value = null;
        if (dialogStore.reference) {
            reference.value = {
                ...dialogStore.reference
            };
        } else {
            reference.value = emptyReference();
        }
    }
);


async function saveReference() {
    if (saving.value) {
        return;
    }

    submitted.value = true;

    if (!reference.value.name?.trim()) {
        return;
    }

    saving.value = true;

    try {
        const formData = new FormData();

        formData.append(
            'type',
            reference.value.type || dialogStore.type
        );

        formData.append(
            'name',
            reference.value.name
        );

        if (reference.value.code) {
            formData.append(
                'code',
                reference.value.code
            );
        }

        if (reference.value.short_name) {
            formData.append(
                'short_name',
                reference.value.short_name
            );
        }

        if (reference.value.parent_id) {
            formData.append(
                'parent_id',
                reference.value.parent_id
            );
        }

        formData.append(
            'description',
            reference.value.description || ''
        );

        formData.append(
            'status',
            reference.value.status ? 1 : 0
        );

        if (
            dialogStore.type === 'category' &&
            imageFile.value
        ) {
            formData.append(
                'image',
                imageFile.value
            );
        }

        let response;

        if (reference.value.id) {
            response = await updateReference(
                reference.value.id,
                formData
            );

            toast.add({
                severity: 'success',
                summary: t('global.toast.success'),
                detail: t('global.success.updated'),
                life: 3000
            });
        } else {
            response = await createReference(
                formData
            );

            toast.add({
                severity: 'success',
                summary: t('global.toast.success'),
                detail: t('global.success.created'),
                life: 3000
            });
        }

        dialogStore.saved(
            response.data.data
        );

    } catch (error) {
        console.error(
            error.response?.data
        );

        toast.add({
            severity: 'error',
            summary: t('global.toast.error'),
            detail: t('global.errors.save_failed'),
            life: 3000
        });

    } finally {
        saving.value = false;
    }
}
</script>


<style scoped>
.reference-form {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-top: 1rem;
}

.field {
    min-width: 0;
}

.field-full {
    grid-column: auto;
}

.reference-dialog-actions {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.reference-dialog-actions :deep(.p-button) {
    min-width: 130px;
}

@media (max-width: 700px) {
    .reference-dialog-actions {
        flex-direction: column;
    }

    .reference-dialog-actions :deep(.p-button) {
        width: 100%;
    }
}
</style>