import { readPreference, writePreference } from '../core/storage.js';

const DENSITIES = ['comfortable', 'compact'];
const DEFAULT_DENSITY = 'comfortable';

const applyDensity = (density) => {
    const value = DENSITIES.includes(density) ? density : DEFAULT_DENSITY;

    document.documentElement.dataset.density = value;

    return value;
};

export const initDensityToggle = () => {
    const buttons = Array.from(document.querySelectorAll('[data-density-toggle]'));

    if (!buttons.length) {
        return;
    }

    let current = applyDensity(readPreference('density', DEFAULT_DENSITY));

    const sync = () => {
        buttons.forEach((button) => {
            const target = button.dataset.densityToggle;

            button.setAttribute('aria-pressed', target === current ? 'true' : 'false');
        });
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.dataset.densityToggle;

            current = applyDensity(target === current ? DEFAULT_DENSITY : target);
            writePreference('density', current);
            sync();

            document.dispatchEvent(
                new CustomEvent('rs:density-changed', { detail: { density: current } }),
            );
        });
    });

    sync();
};
