import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useProductDialogStore = defineStore('productDialog', () => {
    const visible = ref(false);
    const product = ref(null);
    const changed = ref(false);

    function openNew() {
        product.value = null;
        visible.value = true;
    }

    function openEdit(item) {
        product.value = item;
        visible.value = true;
    }

    function openCopy(item) {
    product.value = {
        ...item,
        id: null,
        barcode: '',
        sku: ''
    };

    visible.value = true;
}

    function close() {
        visible.value = false;
        product.value = null;
    }

    function saved() {
        changed.value = true;
        visible.value = false;
        product.value = null;
    }

    function resetChanged() {
        changed.value = false;
    }

    return {
        visible,
        product,
        changed,
        openNew,
        openEdit,
        openCopy,
        close,
        saved,
        resetChanged
    };
});