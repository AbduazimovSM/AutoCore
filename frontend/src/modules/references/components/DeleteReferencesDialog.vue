<template>
    <Dialog v-model:visible="visible" :style="{ width: '450px' }" :header="t('global.toast.confirmation')" modal>
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl text-yellow-500" />
            <span>{{ t('global.messages.confirm_delete_selected', { count }) }}</span>
        </div>
        
        <template #footer>
            <Button :label="t('global.buttons.no')" icon="pi pi-times" text :disabled="loading" @click="visible = false" />
            <Button :label="t('global.buttons.yes')" icon="pi pi-check" text :loading="loading" :disabled="loading" @click="emit('confirm')" />
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
    count: {
        type: Number,
        default: 0
    },

    loading: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['confirm']);
</script>