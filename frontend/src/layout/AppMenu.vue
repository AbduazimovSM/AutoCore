<script setup>
import { ref, computed } from 'vue';
import AppMenuItem from './AppMenuItem.vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const model = computed(() =>[
    {
        items: [{ label: t('menu.home'), icon: 'pi pi-fw pi-home', to: '/' }]
    },
    {
        items: [
            {
                label: t('menu.accounting.title'),
                icon: 'pi pi-fw pi-wallet',
                items: [
                    {
                        label: t('menu.accounting.kassa'),
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: t('menu.accounting.cash'),
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label:  t('menu.sales.title'),
                icon: 'pi pi-fw pi-shopping-cart',
                items: [
                    {
                        label: t('menu.sales.add'),
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: t('menu.sales.list'),
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: t('menu.purchase.title'),
                icon: 'pi pi-fw pi-truck',
                items: [
                    {
                        label: t('menu.purchase.add'),
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: t('menu.purchase.list'),
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label:  t('menu.warehouse.title'),
                icon: 'pi pi-fw pi-warehouse',
                items: [
                    {
                        label: t('menu.warehouse.stock_balance'),
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: t('menu.warehouse.inventory'),
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: t('menu.report.title'),
                icon: 'pi pi-fw pi-chart-bar',
                items: [
                    {
                        label:  t('menu.report.sales'),
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label:  t('menu.report.purchase'),
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: t('menu.catalog.title'),
                icon: 'pi pi-fw pi-book',
                items: [
                    {
                        label: t('menu.catalog.products'),
                        icon: 'pi pi-fw pi-box',
                        to: '/directories/products'
                    },

                    {
                        label: t('menu.catalog.counterparties'),
                        icon: 'pi pi-fw pi-users',
                        to: '/directories/counterparties'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label:  t('menu.settings.title'),
                icon: 'pi pi-fw pi-cog',
                items: [
                    {
                        label: t('menu.settings.account'),
                        icon: 'pi pi-fw pi-user',
                        to: '/settings/profile'
                    },
                    {
                        label: t('menu.settings.general'),
                        icon: 'pi pi-fw pi-sliders-h',
                        to: '/settings/general'
                    },
                    {
                        label:  t('menu.settings.filials'),
                        icon: 'pi pi-fw pi-building',
                        to: '/settings/branches'
                    },
                    {
                        label: t('menu.settings.users'),
                        icon: 'pi pi-fw pi-users',
                        to: '/settings/users'
                    },
                    {
                        label: t('menu.settings.currency'),
                        icon: 'pi pi-fw pi-dollar',
                        to: '/settings/currencies'
                    },
                    {
                        label: t('menu.settings.accounts'),
                        icon: 'pi pi-fw pi-wallet',
                        to: '/settings/accounts'
                    },
                    {
                        label: t('menu.settings.warehouses'),
                        icon: 'pi pi-fw pi-warehouse',
                        to: '/settings/warehouses'
                    },
                    {
                        label: t('menu.settings.begin_balance'),
                        icon: 'pi pi-fw pi-database',
                        to: '/settings/begin_balances'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: t('menu.support.title'),
                icon: 'pi pi-fw pi-question-circle',
                items: [
                    {
                        label: t('menu.support.guide'),
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: t('menu.support.faq'),
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
