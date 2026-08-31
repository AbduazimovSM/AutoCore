<template>
    <TabMenu
        v-model:activeIndex="activeIndex"
        :model="items"
        @tab-change="changeTab"
    />
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const route = useRoute();
const router = useRouter();

const items = computed(() => [
    {
        label: t('menu.product_tabs.products'),
        icon: 'pi pi-box',
        route: {
            path: '/directories/products'
        }
    },
    {
        label: t('menu.product_tabs.category'),
        icon: 'pi pi-tags',
        route: {
            path: '/references',
            query: { type: 'category' }
        }
    },
    {
        label: t('menu.product_tabs.unit'),
        icon: 'pi pi-list',
        route: {
            path: '/references',
            query: { type: 'unit' }
        }
    },
    {
        label: t('menu.product_tabs.brand'),
        icon: 'pi pi-building',
        route: {
            path: '/references',
            query: { type: 'brand' }
        }
    }
]);

const activeIndex = computed({
    get() {
        if (route.path === '/directories/products') {
            return 0;
        }

        const indexes = {
            category: 1,
            unit: 2,
            brand: 3
        };

        return indexes[route.query.type] ?? 0;
    },

    set() {}
});

function changeTab(event) {
    router.push(items.value[event.index].route);
}
</script>