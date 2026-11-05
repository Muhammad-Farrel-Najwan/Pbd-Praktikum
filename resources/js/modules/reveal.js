import { prefersReducedMotion } from '../core/motion.js';

export const initReveal = () => {
    const elements = document.querySelectorAll('[data-reveal]');

    if (!elements.length) {
        return;
    }

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        elements.forEach((element) => element.classList.add('is-visible'));

        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px',
        },
    );

    elements.forEach((element) => observer.observe(element));
};
