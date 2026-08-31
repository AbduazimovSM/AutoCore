import { defineStore } from 'pinia';
import { ref } from 'vue';

const STORAGE_KEY = 'referenceSettings';

const defaultFields = {
    category: {
        name: true,
        parent_category: true,
        description: true,
        status: true
    },

    unit: {
        name: true,
        short_name: true,
        description: true,
        status: true
    },

    brand: {
        name: true,
        description: true,
        status: true
    }
};

export const useReferenceSettingsStore = defineStore('referenceSettings', () => {
    const fields = ref(loadSettings());

    function loadSettings() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);

            if (!saved) {
                return {
                    category: { ...defaultFields.category },
                    unit: { ...defaultFields.unit },
                    brand: { ...defaultFields.brand }
                };
            }

            const parsed = JSON.parse(saved);

            return {
                category: {
                    ...defaultFields.category,
                    ...(parsed.category || {})
                },

                unit: {
                    ...defaultFields.unit,
                    ...(parsed.unit || {})
                },

                brand: {
                    ...defaultFields.brand,
                    ...(parsed.brand || {})
                }
            };
        } catch {
            return {
                category: { ...defaultFields.category },
                unit: { ...defaultFields.unit },
                brand: { ...defaultFields.brand }
            };
        }
    }

    function save(type, newFields) {
        fields.value[type] = {
            ...defaultFields[type],
            ...newFields
        };

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(fields.value)
        );
    }

    function reset(type) {
        fields.value[type] = {
            ...defaultFields[type]
        };

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(fields.value)
        );
    }

    return {
        fields,
        save,
        reset
    };
});