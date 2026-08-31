<template>
    <Select
        v-model="selected"
        :options="options"
        optionLabel="name"
        optionValue="id"
        filter
        filterPlaceholder="Поиск..."
        showClear
        :loading="loading"
        :placeholder="placeholder"
        class="w-full"
        @filter="search"
        @show="onShow"
        fluid
    >
        <template #dropdownicon>
            <i :class="icon"></i>
        </template>

        <template #footer v-if="showAdd">
            <div class="p-2">
                <Button
                    label="Добавить"
                    icon="pi pi-plus"
                    class="w-full"
                    @click="onAdd"
                />
            </div>
        </template>
    </Select>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';

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
    },

    icon: {
        type: String,
        default: 'pi pi-list'
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
const options = ref([]);
const loading = ref(false);

async function load(search = '') {
    loading.value = true;

    try {
        options.value = await props.loader(search);
    } finally {
        loading.value = false;
    }
}

function search(event) {
    load(event.value);
}

function onAdd() {
    emit('add');
}

async function onShow() {
    await nextTick();

    const input = document.querySelector('.p-select-filter');

    if (input) {
        input.focus();
        input.select();
    }
}

watch(
    () => props.modelValue,
    (value) => {
        selected.value = value ?? null;
    },
    { immediate: true }
);

watch(selected, (value) => {
    emit('update:modelValue', value);
});

onMounted(() => {
    load();
});

defineExpose({
    reload: load
});
</script>