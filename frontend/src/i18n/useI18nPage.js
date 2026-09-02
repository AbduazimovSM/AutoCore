
import i18n from '@/i18n';
import { loadLocale } from '@/i18n/load';

export async function useI18nPage(namespace) {
    const lang =
        localStorage.getItem('lang') ||
        i18n.global.locale.value ||
        'ru';

    if (namespace) {
        await loadLocale(i18n, lang, namespace);
    }

    i18n.global.locale.value = lang;
}

export async function changeLanguage(lang, namespaces = []) {
    const list = Array.isArray(namespaces)
        ? namespaces
        : [namespaces];

    const loaders = [
        loadLocale(i18n, lang, 'global'),
        loadLocale(i18n, lang, 'menu')
    ];

    for (const ns of list) {
        if (ns) {
            loaders.push(
                loadLocale(i18n, lang, ns)
            );
        }
    }

    await Promise.all(loaders);

    i18n.global.locale.value = lang;
    localStorage.setItem('lang', lang);
}
