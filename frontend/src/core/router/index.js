import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '@/layout/AppLayout.vue';

import i18n from '@/i18n';
import { loadLocale } from '@/i18n/load';
// modules
import authRoutes from '@/modules/auth/router';
import dashboardRoutes from '@/modules/dashboard/router';
import directoryRoutes from '@/modules/directories/router';
import settingRoutes from '@/modules/settings/router';
import referenceRoutes from '@/modules/references/router';

const routes = [
    {
        path: '/',
        component: AppLayout,
        children: [
            ...dashboardRoutes,
            ...directoryRoutes,
            ...settingRoutes,
            ...referenceRoutes,
        ]
    },
    ...authRoutes,

];


const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to) => {
    const locale =
        localStorage.getItem('lang') ||
        i18n.global.locale.value ||
        'ru';

    i18n.global.locale.value = locale;

    const module = to.meta.module;

    if (module) {
        await loadLocale(
            i18n,
            locale,
            module
        );
    }
});

export default router;
