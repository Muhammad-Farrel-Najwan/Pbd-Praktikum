import { prefersReducedMotion } from '../core/motion.js';

export const initCounters = () => {
    const counters = document.querySelectorAll('[data-count]');

    if (!counters.length || prefersReducedMotion || !('IntersectionObserver' in window)) {
        return;
    }

    const formatter = new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: 2,
    });

    const animate = (element) => {
        const target = Number(element.dataset.count);

        if (!Number.isFinite(target)) {
            return;
        }

        const suffix = element.dataset.suffix ?? '';
        const duration = 850;
        const startedAt = performance.now();

        const step = (now) => {
            const progress = Math.min((now - startedAt) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = Number((target * eased).toFixed(2));

            element.textContent = `${formatter.format(value)}${suffix}`;

            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };

        window.requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                animate(entry.target);
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.65 },
    );

    counters.forEach((counter) => observer.observe(counter));
};
