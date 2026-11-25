import { prefersReducedMotion } from '../core/motion.js';

/**
 * Sorot cahaya yang mengikuti kursor di dalam kartu.
 *
 * Posisi pointer ditulis sebagai CSS variable --rs-card-x/--rs-card-y pada
 * kartu yang sedang disorot saja, supaya tidak ada perhitungan saat idle.
 * Hanya aktif di perangkat berpointer presisi (desktop).
 */
export const initCardSpotlight = () => {
    const cards = Array.from(document.querySelectorAll('.rs-stat-card, .rs-module-card, .rs-panel'));

    if (!cards.length) {
        return;
    }

    const finePointer = window.matchMedia('(pointer: fine)').matches;

    if (prefersReducedMotion || !finePointer) {
        return;
    }

    cards.forEach((card) => {
        let frame = null;
        const isTiltable = card.matches('.rs-stat-card, .rs-module-card.is-available');

        // Layer sorotan ditempatkan sebagai child supaya tidak bentrok dengan
        // pseudo-element dekoratif yang sudah dipakai kartu.
        if (!card.querySelector(':scope > .rs-card-glow')) {
            const glow = document.createElement('span');
            glow.className = 'rs-card-glow';
            glow.setAttribute('aria-hidden', 'true');
            card.prepend(glow);
        }

        const move = (event) => {
            if (frame !== null) {
                return;
            }

            frame = window.requestAnimationFrame(() => {
                const rect = card.getBoundingClientRect();

                card.style.setProperty('--rs-card-x', `${event.clientX - rect.left}px`);
                card.style.setProperty('--rs-card-y', `${event.clientY - rect.top}px`);

                if (isTiltable) {
                    const relativeX = (event.clientX - rect.left) / rect.width - 0.5;
                    const relativeY = (event.clientY - rect.top) / rect.height - 0.5;

                    card.style.setProperty('--rs-tilt-y', `${(relativeX * 5).toFixed(2)}deg`);
                    card.style.setProperty('--rs-tilt-x', `${(-relativeY * 5).toFixed(2)}deg`);
                }

                frame = null;
            });
        };

        card.addEventListener('pointerenter', () => {
            card.classList.add('is-spotlight');

            if (isTiltable) {
                card.classList.add('is-tilting');
            }

            card.addEventListener('pointermove', move, { passive: true });
        });

        card.addEventListener('pointerleave', () => {
            card.classList.remove('is-spotlight', 'is-tilting');
            card.removeEventListener('pointermove', move);
            card.style.removeProperty('--rs-tilt-x');
            card.style.removeProperty('--rs-tilt-y');
        });
    });
};
