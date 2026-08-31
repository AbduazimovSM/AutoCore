export default [{
        path: 'directories/counterparties',
        name: 'directories.counterparties',
        component: () => import ('./pages/Counterparty.vue'),
        meta: { module: 'directories' }
    },
    {
        path: 'directories/products',
        name: 'directories.products',
        component: () => import ('./pages/Product.vue'),
        meta: { module: 'directories' }
    }
];