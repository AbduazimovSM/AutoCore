<template>
    <TreeSelect
        v-model="selected"
        :options="nodes"
        filter
        filterPlaceholder="Поиск..."
        showClear
        :loading="loading"
        :placeholder="placeholder"
        class="w-full"
        fluid
    >
        <template #footer v-if="showAdd">
            <div class="tree-select-footer">
                <Button
                    label="Добавить"
                    icon="pi pi-plus"
                    class="w-full"
                    @click="onAdd"
                />
            </div>
        </template>
    </TreeSelect>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: [Number, String, null],
        default: null
    },

    loader: {
        type: Function,
        required: true
    },

    placeholder: {
        type: String,
        default: ''
    },

    showAdd: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits([
    'update:modelValue',
    'add'
]);

const selected = ref(null);
const nodes = ref([]);
const loading = ref(false);

function buildTree(items) {
    const map = {};
    const roots = [];

    items.forEach(item => {
        map[item.id] = {
            key: item.id,
            label: item.name,
            data: item,
            children: []
        };
    });

    items.forEach(item => {
        if (item.parent_id && map[item.parent_id]) {
            map[item.parent_id].children.push(
                map[item.id]
            );
        } else {
            roots.push(map[item.id]);
        }
    });

    return roots;
}

async function load() {
    loading.value = true;

    try {
        const items = await props.loader();
        nodes.value = buildTree(items);
    } finally {
        loading.value = false;
    }
}

function onAdd() {
    emit('add');
}

watch(
    () => props.modelValue,
    (value) => {
        selected.value = value != null
            ? { [String(value)]: true }
            : null;
    },
    { immediate: true }
);

watch(selected, (value) => {
    if (!value) {
        emit('update:modelValue', null);
        return;
    }

    if (typeof value === 'object') {
        const key = Object.keys(value)[0];

        emit(
            'update:modelValue',
            key ? Number(key) : null
        );

        return;
    }

    emit(
        'update:modelValue',
        Number(value)
    );
});

onMounted(() => {
    load();
});

defineExpose({
    reload: load
});
</script>

<style scoped>
.tree-select-footer {
    border-top: 1px solid var(--surface-border);
    padding: 0.5rem;
}
</style>