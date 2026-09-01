import { createApp, watch } from 'vue';
import App from './App.vue';
import router from './core/router/index.js';

import Aura from '@primeuix/themes/aura';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';

import { createPinia } from 'pinia';

import i18n from './i18n';
import { loadLocale } from './i18n/load';

import '@/assets/tailwind.css';
import '@/assets/styles.scss';

async function bootstrap() {
    const app = createApp(App);

    const pinia = createPinia();

    app.use(pinia);

    const locale = localStorage.getItem('lang') || i18n.global.locale.value || 'ru';

    i18n.global.locale.value = locale;

    await Promise.all([
        loadLocale(i18n, locale, 'global'),
        loadLocale(i18n, locale, 'references'),
        loadLocale(i18n, locale, 'menu'),
    ]);

    watch(
    () => i18n.global.locale.value,
    async (newLocale) => {
        localStorage.setItem('lang', newLocale);

        const currentModule =
            router.currentRoute.value.meta.module;

        const loaders = [
            loadLocale(i18n, newLocale, 'global'),
            loadLocale(i18n, newLocale, 'references'),
            loadLocale(i18n, newLocale, 'menu')
        ];

        if (currentModule) {
            loaders.push(
                loadLocale(
                    i18n,
                    newLocale,
                    currentModule
                )
            );
        }

        await Promise.all(loaders);
    }
);

    app.use(i18n);
    app.use(router);

    app.use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: '.app-dark'
            }
        }
    });

    app.use(ToastService);
    app.use(ConfirmationService);

    app.mount('#app');
}

bootstrap();