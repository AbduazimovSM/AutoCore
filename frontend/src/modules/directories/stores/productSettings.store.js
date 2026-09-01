import { defineStore } from 'pinia';
import { ref } from 'vue';

const STORAGE_KEY = 'productSettings';

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
    const fields = ref(loadSettings());

    function loadSettings() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);

            if (!saved) {
                return { ...defaultFields };
            }

            return {
                ...defaultFields,
                ...JSON.parse(saved)
            };
        } catch {
            return { ...defaultFields };
        }
    }

    function save(newFields) {
        fields.value = {
            ...defaultFields,
            ...newFields
        };

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(fields.value)
        );
    }

    function reset() {
        fields.value = { ...defaultFields };

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(fields.value)
        );
    }

    function getDefaults() {
        return { ...defaultFields };
    }

    return {
        fields,
        save,
        reset,
        getDefaults
    };
});