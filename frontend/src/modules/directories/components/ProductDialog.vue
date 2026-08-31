<template>
    <Dialog v-model:visible="dialogStore.visible" maximizable :style="{ width: '800px' }"
        :header="t('directories.products.dialog.title_product')" modal class="p-fluid">

        <div class="grid grid-cols-2 gap-2 mt-4">
            <div v-if="productSettings.fields.name" class="field">
                <FloatLabel variant="on">
                    <InputText id="name" v-model.trim="product.name" autofocus :invalid="submitted && !product.name"
                        fluid />
                    <label for="name">{{ t('directories.products.table.name') }}</label>
                </FloatLabel>
                <small v-if="submitted && !product.name" class="p-error"> {{ t('global.messages.required') }} </small>
            </div>

            <div v-if="productSettings.fields.barcode" class="field">
                <FloatLabel variant="on">
                    <InputText id="barcode" v-model.trim="product.barcode" fluid />
                    <label for="barcode">{{ t('directories.products.table.barcode') }}</label>
                </FloatLabel>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 mt-8">
            <div v-if="productSettings.fields.sku" class="field">
                <FloatLabel variant="on">
                    <InputText id="sku" v-model.trim="product.sku" fluid />
                    <label for="sku">{{ t('directories.products.table.sku') }}</label>
                </FloatLabel>
            </div>

            <div v-if="productSettings.fields.category" class="field">
                <FloatLabel variant="on">
                    <AppTreeSelect v-model="product.category_id" :loader="loadCategories" />
                    <label>{{ t('directories.products.table.category') }}</label>
                </FloatLabel>
                <small v-if="submitted && !product.category_id" class="p-error">{{ t('global.messages.required')
                }}</small>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 mt-8">
            <div v-if="productSettings.fields.unit" class="field">
                <FloatLabel variant="on">
                    <AppSelect v-model="product.unit_id" :loader="loadUnits" :show-add="false" />
                    <label>{{ t('directories.products.table.unit') }}</label>
                </FloatLabel>
                <small v-if="submitted && !product.unit_id" class="p-error"> {{ t('global.messages.required') }}
                </small>
            </div>
            <div v-if="productSettings.fields.brand" class="field">
                <FloatLabel variant="on">
                    <AppSelect v-model="product.brand_id" :loader="loadBrands" :show-add="false" />
                    <label>{{ t('directories.products.table.brand') }}</label>
                </FloatLabel>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 mt-8">
            <div v-if="productSettings.fields.min_quantity" class="field">
                <FloatLabel variant="on">
                    <InputNumber id="min_quantity" v-model="product.min_quantity" :min="0" :minFractionDigits="0"
                        :maxFractionDigits="3" fluid />
                    <label for="min_quantity">{{ t('directories.products.table.min_quantity') }}</label>
                </FloatLabel>
            </div>
            <div v-if="productSettings.fields.image" class="font-medium">
                <FileUpload mode="basic" name="image" accept="image/*" :maxFileSize="2000000" :auto="false" customUpload
                    @select="onSelect" :chooseLabel="t('global.buttons.select')" />
            </div>
        </div>

        <div v-if="productSettings.fields.description" class="field">
            <FloatLabel variant="on" class="mt-8">
                <Textarea id="description" v-model="product.description" rows="3" fluid />
                <label for="description">{{ t('directories.products.table.description') }}</label>
            </FloatLabel>
        </div>

        <div v-if="productSettings.fields.status" class="field">
            <FloatLabel variant="on" class="mt-8">
                <Select id="status" v-model="product.status" :options="statuses" option-label="label"
                    option-value="value" fluid />
                <label for="status">{{ t('directories.products.table.status') }}</label>
            </FloatLabel>
        </div>

        <div class="reference-dialog-actions">
            <Button :label="t('global.buttons.cancel')" icon="pi pi-times" text :disabled="saving"
                @click="dialogStore.close" />

            <Button :label="t('global.buttons.save')" icon="pi pi-check" text :loading="saving" :disabled="saving"
                @click="saveProduct" />
        </div>
    </Dialog>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

import AppSelect from '@/components/AppSelect.vue';
import AppTreeSelect from '@/components/AppTreeSelect.vue';

import {
    createProduct,
    updateProduct
} from '@/modules/directories/api/product.api';

import {
    getReferences
} from '@/modules/references/api/reference.api';

import {
    useProductDialogStore
} from '@/modules/directories/stores/productDialog.store';

import {
    useProductSettingsStore
} from '@/modules/directories/stores/productSettings.store';

const dialogStore = useProductDialogStore();
const productSettings = useProductSettingsStore();
const toast = useToast();

const product = ref({});
const submitted = ref(false);
const saving = ref(false);
const imageFile = ref(null);

function onSelect(event) {
    imageFile.value = event.files[0];
}

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

function emptyProduct() {
    return {
        name: '',
        barcode: '',
        sku: '',
        category_id: null,
        unit_id: null,
        brand_id: null,
        image: 'default.png',
        min_quantity: 0,
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

async function loadUnits(search = '') {
    const response = await getReferences(
        'unit',
        1,
        100,
        'name',
        'asc',
        search
    );

    return response.data.data.data;
}

async function loadBrands(search = '') {
    const response = await getReferences(
        'brand',
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
    (visible) => {
        if (!visible) {
            return;
        }

        submitted.value = false;
        imageFile.value = null;

        if (dialogStore.product) {
            product.value = {
                ...dialogStore.product
            };
        } else {
            product.value = emptyProduct();
        }
    }
);

async function saveProduct() {
    if (saving.value) {
        return;
    }

    submitted.value = true;

    if (
        !product.value.name?.trim() ||
        !product.value.category_id ||
        !product.value.unit_id
    ) {
        return;
    }

    saving.value = true;

    try {
        const formData = new FormData();

        formData.append('name', product.value.name);
        formData.append('barcode', product.value.barcode || '');
        formData.append('sku', product.value.sku || '');
        formData.append('category_id', product.value.category_id);
        formData.append('unit_id', product.value.unit_id);

        if (product.value.brand_id) {
            formData.append('brand_id', product.value.brand_id);
        }

        formData.append(
            'min_quantity',
            product.value.min_quantity ?? 0
        );

        formData.append(
            'description',
            product.value.description || ''
        );

        formData.append(
            'status',
            product.value.status ? 1 : 0
        );

        if (imageFile.value) {
            formData.append('image', imageFile.value);
        }

        if (product.value.id) {
            await updateProduct(product.value.id, formData);
            toast.add({
                severity: 'success',
                summary: t('global.toast.success'),
                detail: t('global.success.updated'),
                life: 3000
            });
        } else {
            await createProduct(formData);
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