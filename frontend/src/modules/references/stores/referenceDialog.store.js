import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useReferenceDialogStore = defineStore('referenceDialog', () => {
    const visible = ref(false);
    const reference = ref(null);
    const type = ref(null);
    const changed = ref(false);

    // Последняя созданная / изменённая запись
    const savedReference = ref(null);

    function openNew(referenceType) {
        type.value = referenceType;
        reference.value = null;
        savedReference.value = null;
        visible.value = true;
    }

    function openEdit(item, referenceType) {
        type.value = referenceType;
        reference.value = item;
        savedReference.value = null;
        visible.value = true;
    }

    function close() {
        visible.value = false;
        reference.value = null;
    }

    function saved(item) {
        savedReference.value = item;
        changed.value = true;

        visible.value = false;
        reference.value = null;
    }

    function resetChanged() {
        changed.value = false;
        savedReference.value = null;
    }

    return {
        visible,
        reference,
        type,
        changed,
        savedReference,

        openNew,
        openEdit,
        close,
        saved,
        resetChanged
    };
});