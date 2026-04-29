import ru from './ru';
import en from './en';

const dictionaries = {
    ru,
    en,
};

function getLocale() {
    const locale = window.CMS_CONFIG?.locale;

    if (locale && dictionaries[locale]) {
        return locale;
    }

    return 'en';
}

export function t(key, params = {}) {
    const locale = getLocale();
    let value = dictionaries[locale]?.[key] ?? dictionaries.en[key] ?? key;

    if (typeof value !== 'string') {
        return value;
    }

    Object.entries(params).forEach(([paramKey, paramValue]) => {
        value = value.replace(new RegExp(`:${paramKey}`, 'g'), String(paramValue));
    });

    return value;
}
