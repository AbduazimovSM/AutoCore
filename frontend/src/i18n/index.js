// src/i18n/index.js
import { createI18n } from 'vue-i18n';

const i18n = createI18n({
    legacy: false,
    locale: localStorage.getItem('lang') || 'ru',
    fallbackLocale: 'ru',
    messages: {}
});

export default i18n;
