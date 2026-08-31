export default [
    {
        path: '/references',
        name: 'references',
        component: () => import('./pages/Reference.vue'),
        meta: { module: 'references'}
    }
];