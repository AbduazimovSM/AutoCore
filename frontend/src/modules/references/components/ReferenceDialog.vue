<template>
    <Dialog v-model:visible="dialogStore.visible" maximizable :style="{ width: '450px' }"
        :header="t('references.model.dialog.title_reference')" modal class="p-fluid">
        <div>
            <FloatLabel variant="on" class="mt-2">
                <InputText id="name" v-model.trim="reference.name" autofocus :invalid="submitted && !reference.name"
                    fluid />
                <label for="name">{{ t('references.model.table.name') }}</label>
            </FloatLabel>
            <small v-if="submitted && !reference.name" class="p-error">{{ t('global.messages.required') }}</small>
        </div>

        <div v-if="dialogStore.type === 'category'" class="field">
            <FloatLabel variant="on" class="mt-4">

                <AppTreeSelect v-model="reference.parent_id" :loader="loadCategories" />
                <label for="parent_id">
                    {{ t('references.model.table.parent_category') }}
                </label>
            </FloatLabel>
        </div>

        <div v-if="dialogStore.type === 'unit'" class="field">
            <FloatLabel variant="on" class="mt-4">
                <InputText id="short_name" v-model.trim="reference.short_name" fluid />
                <label for="short_name">{{ t('references.model.table.short_name') }}</label>
            </FloatLabel>
        </div>

        <div class="field">
            <FloatLabel variant="on" class="mt-4">
                <Textarea id="description" v-model="reference.description" rows="3" fluid />
                <label for="description">{{ t('references.model.table.description') }}</label>
            </FloatLabel>
        </div>

        <div class="field">
            <FloatLabel variant="on" class="mt-4">
                <Select id="status" v-model="reference.status" :options="statuses" option-label="label"
                    option-value="value" fluid />
                <label for="status"> {{ t('references.model.table.status') }}</label>
            </FloatLabel>
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
import AppTreeSelect from '@/components/AppTreeSelect.vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

import { getReferences, createReference, updateReference } from '@/modules/references/api/reference.api';
import { useReferenceDialogStore } from '@/modules/references/stores/referenceDialog.store';

const dialogStore = useReferenceDialogStore();
const toast = useToast();
const reference = ref({});
const submitted = ref(false);
const saving = ref(false);

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
        description: '',
        status: true
    };
}

async function loadCategories(search = '') {
    const response = await getReferences(
        'category',
        1,
        100,
        'name',
        'asc',
        search
    );

    return response.data.data.data;
}

watch(
    () => dialogStore.visible,
    async (visible) => {
        if (!visible) {
            return;
        }

        submitted.value = false;

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
        if (reference.value.id) {
            await updateReference(reference.value.id, reference.value);
            toast.add({
                severity: 'success',
                summary: t('global.toast.success'),
                detail: t('global.success.updated'),
                life: 3000
            });
        } else {
            await createReference(reference.value);
            toast.add({
                severity: 'success',
                summary: t('global.toast.success'),
                detail: t('global.success.created'),
                life: 3000
            });
        }



        dialogStore.saved();

    } catch (error) {
        console.error(error.response?.data);
        
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