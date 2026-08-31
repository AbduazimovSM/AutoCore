import { defineStore } from 'pinia';
import { ref, watch } from 'vue';

const defaultFields = {
    name: true,
    barcode: true,
    sku: true,
    category: true,
    unit: true,
    brand: true,
    image: true,
    min_quantity: true,
    description: true,
    status: true
};

export const useProductSettingsStore = defineStore('productSettings', () => {
    const saved = localStorage.getItem('productSettings');

    const fields = ref(
        saved
            ? { ...defaultFields, ...JSON.parse(saved) }
            : { ...defaultFields }
    );

    watch(
        fields,
        (value) => {
            localStorage.setItem(
                'productSettings',
                JSON.stringify(value)
            );
        },
        { deep: true }
    );

    function reset() {
        fields.value = { ...defaultFields };
    }

    return {
        fields,
        reset
    };
});