const loaded = new Set();

export async function loadLocale(i18n, locale, name) {
    const key = `${locale}:${name}`;
    if (loaded.has(key)) return;

    const messages = await import(`./locales/${locale}/${name}.json`);

    i18n.global.mergeLocaleMessage(locale, {
        [name]: messages.default
    });

    loaded.add(key);
}
