<template>
    <Dialog
        v-model:visible="visible"
        header="Настройки таблицы"
        :style="{ width: '650px' }"
        modal
    >
        <div class="grid grid-cols-2 gap-4">

            <div class="flex justify-between items-center">
                <span>Наименование</span>
                <ToggleSwitch
                    v-model="settings.fields.name"
                />
            </div>

            <div class="flex justify-between items-center">
                <span>Штрих код</span>
                <ToggleSwitch
                    v-model="settings.fields.barcode"
                />
            </div>

            <div class="flex justify-between items-center">
                <span>Артикул</span>
                <ToggleSwitch
                    v-model="settings.fields.sku"
                />
            </div>

            <div class="flex justify-between items-center">
                <span>Категория</span>
                <ToggleSwitch
                    v-model="settings.fields.category"
                />
            </div>

            <div class="flex justify-between items-center">
                <span>Ед. изм.</span>
                <ToggleSwitch
                    v-model="settings.fields.unit"
                />
            </div>

            <div class="flex justify-between items-center">
                <span>Бренд</span>
                <ToggleSwitch
                    v-model="settings.fields.brand"
                />
            </div>

            <div class="flex justify-between items-center">
                <span>Минимальное количество</span>
                <ToggleSwitch
                    v-model="settings.fields.min_quantity"
                />
            </div>

            <div class="flex justify-between items-center">
                <span>Изображение</span>
                <ToggleSwitch
                    v-model="settings.fields.image"
                />
            </div>

            <div class="flex justify-between items-center">
                <span>Описание</span>
                <ToggleSwitch
                    v-model="settings.fields.description"
                />
            </div>

            <div class="flex justify-between items-center">
                <span>Статус</span>
                <ToggleSwitch
                    v-model="settings.fields.status"
                />
            </div>

        </div>

        <template #footer>
            <Button
                label="Закрыть"
                @click="visible = false"
            />
        </template>
    </Dialog>
</template>

<script setup>
import { useProductSettingsStore } from '@/modules/directories/stores/productSettings.store';

const settings = useProductSettingsStore();

const visible = defineModel({
    type: Boolean,
    default: false
});
</script>