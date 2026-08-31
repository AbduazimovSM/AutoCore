<template>
    <Dialog v-model:visible="visible" :style="{ width: '450px' }" :header="t('global.toast.confirmation')" modal>
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem"/>
            <span>{{ t('global.messages.confirm_delete', { name: itemName }) }}</span>
        </div>

        <template #footer>
            <Button :label="t('global.buttons.no')" icon="pi pi-times" text :disabled="loading" @click="visible = false"/>
            <Button :label="t('global.buttons.yes')" icon="pi pi-check" text :loading="loading" :disabled="loading" @click="emit('confirm')"/>
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

const emit = defineEmits(['confirm']);
</script>