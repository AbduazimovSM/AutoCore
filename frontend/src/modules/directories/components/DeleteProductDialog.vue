<template>
    <Dialog
        v-model:visible="visible"
        :header="t('global.toast.confirmation')"
        :style="{ width: '450px' }"
        modal
    >
        <div class="flex align-items-center gap-3">
            <i class="pi pi-exclamation-triangle" style="font-size: 2rem"/>
            <span>
                {{ t('global.messages.confirm_delete', { name: itemName }) }}
            </span>
        </div>

        <template #footer>
            <Button
                :label="t('global.buttons.no')"
                icon="pi pi-times"
                text
                :disabled="loading"
                @click="visible = false"
            />

            <Button
                :label="t('global.buttons.yes')"
                icon="pi pi-check"
                severity="danger"
                :loading="loading"
                :disabled="loading"
                @click="$emit('confirm')"
            />
        </template>
    </Dialog>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const visible = defineModel({
    type: Boolean,
    default: false
});

defineProps({
    itemName: {
        type: String,
        default: ''
    },

    loading: {
        type: Boolean,
        default: false
    }
});

defineEmits(['confirm']);
</script>