import { prefersReducedMotion } from '../core/motion.js';

/**
 * Feedback tekan pada tombol: riak cahaya dari titik klik.
 *
 * Hanya dijalankan saat tombol benar-benar diklik dan saat animasi tidak
 * dimatikan pengguna, lalu node riak dibersihkan setelah selesai.
 */
export const initButtonRipple = () => {
    if (prefersReducedMotion) {
        return;
    }

    document.addEventListener('pointerdown', (event) => {
        if (event.button !== 0) {
            return;
        }

        const button = event.target.closest('.rs-btn');

        if (!button || button.disabled || button.classList.contains('is-loading')) {
            return;
        }

        const rect = button.getBoundingClientRect();
        const ripple = document.createElement('span');

        ripple.className = 'rs-ripple';
        ripple.style.left = `${event.clientX - rect.left}px`;
        ripple.style.top = `${event.clientY - rect.top}px`;
        ripple.setAttribute('aria-hidden', 'true');

        button.appendChild(ripple);

        ripple.addEventListener('animationend', () => ripple.remove());
    });
};
