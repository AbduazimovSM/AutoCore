import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useReferenceDialogStore = defineStore('referenceDialog', () => {
    const visible = ref(false);
    const reference = ref(null);
    const type = ref(null);
    const changed = ref(false);

    function openNew(referenceType) {
        type.value = referenceType;
        reference.value = null;
        visible.value = true;
    }

    function openEdit(item, referenceType) {
        type.value = referenceType;
        reference.value = item;
        visible.value = true;
    }

    function close() {
        visible.value = false;
        reference.value = null;
    }

    function saved() {
        changed.value = true;
        visible.value = false;
        reference.value = null;
    }

    function resetChanged() {
        changed.value = false;
    }

    return {
        visible,
        reference,
        type,
        changed,
        openNew,
        openEdit,
        close,
        saved,
        resetChanged
    };
});