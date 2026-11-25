import { readPreference, writePreference } from '../core/storage.js';

const THEMES = ['light', 'dark'];
const DEFAULT_THEME = 'dark';

// Tema awal: pilihan pengguna. Default gelap karena tema utama aplikasi gelap.
export const resolveTheme = () => {
    const stored = readPreference('theme');

    if (THEMES.includes(stored)) {
        return stored;
    }

    return DEFAULT_THEME;
};

const applyTheme = (theme) => {
    const value = THEMES.includes(theme) ? theme : DEFAULT_THEME;

    document.documentElement.dataset.theme = value;

    return value;
};

export const initThemeToggle = () => {
    let current = applyTheme(resolveTheme());

    const buttons = Array.from(document.querySelectorAll('[data-theme-toggle]'));

    const sync = () => {
        buttons.forEach((button) => {
            const isDark = current === 'dark';

            button.setAttribute('aria-label', isDark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap');
            button.setAttribute('title', isDark ? 'Tema terang' : 'Tema gelap');
        });
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            current = applyTheme(current === 'dark' ? 'light' : 'dark');
            writePreference('theme', current);
            sync();

            document.dispatchEvent(
                new CustomEvent('rs:theme-changed', { detail: { theme: current } }),
            );
        });
    });

    // Ikuti perubahan tema sistem selama pengguna belum memilih sendiri
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (THEMES.includes(readPreference('theme'))) {
            return;
        }

        current = applyTheme(DEFAULT_THEME);
        sync();
    });

    sync();
};
