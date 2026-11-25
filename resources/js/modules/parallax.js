import { prefersReducedMotion } from '../core/motion.js';

/**
 * Parallax tipis untuk elemen [data-parallax].
 *
 * Nilai data-parallax adalah kekuatan geser (piksel) yang ditulis sebagai
 * CSS variable --rs-parallax. Lapisan dekoratif yang memakainya, bukan
 * elemen utamanya, supaya tata letak tidak ikut bergeser.
 */
export const initParallax = () => {
    const elements = Array.from(document.querySelectorAll('[data-parallax]'));

    if (!elements.length) {
        return;
    }

    const finePointer = window.matchMedia('(pointer: fine)').matches;

    if (prefersReducedMotion || !finePointer) {
        return;
    }

    let frame = null;

    const apply = () => {
        const offset = window.scrollY;

        elements.forEach((element) => {
            const strength = Number(element.dataset.parallax) || 12;
            const shift = Math.min(offset * (strength / 100), strength * 2);

            element.style.setProperty('--rs-parallax', `${shift}px`);
        });

        frame = null;
    };

    window.addEventListener(
        'scroll',
        () => {
            if (frame !== null) {
                return;
            }

            frame = window.requestAnimationFrame(apply);
        },
        { passive: true },
    );

    apply();
};
