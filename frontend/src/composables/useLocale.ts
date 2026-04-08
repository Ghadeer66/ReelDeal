import { ref } from 'vue';

const isBrowser = typeof window !== 'undefined';
const currentLocale = ref(isBrowser ? (window.localStorage.getItem('locale') || 'en') : 'en');

export function useLocale() {
    const setLocale = (locale: 'en' | 'ar') => {
        currentLocale.value = locale;

        if (isBrowser) {
            window.localStorage.setItem('locale', locale);

            // Setup RTL / LTR
            if (locale === 'ar') {
                document.documentElement.dir = 'rtl';
                document.documentElement.lang = 'ar';
            } else {
                document.documentElement.dir = 'ltr';
                document.documentElement.lang = 'en';
            }
        }
    };

    // Initialize on load
    const initLocale = () => {
        setLocale(currentLocale.value as 'en' | 'ar');
    };

    return {
        currentLocale,
        setLocale,
        initLocale
    };
}
