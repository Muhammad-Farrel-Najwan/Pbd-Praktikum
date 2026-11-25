import { prefersReducedMotion } from '../core/motion.js';

/**
 * Sorot cahaya yang mengikuti kursor pada latar, plus dua lapis ekor cahaya.
 *
 * Posisi kepala ditulis langsung sebagai CSS variable, sedangkan ekor
 * dihaluskan lewat requestAnimationFrame. Loop berhenti sendiri saat ekor
 * sudah mengejar kepala, jadi tidak ada perhitungan terus-menerus saat idle.
 * Hanya aktif di perangkat dengan pointer presisi (desktop).
 */
export const initSpotlight = () => {
    const layer = document.querySelector('[data-spotlight]');
    const scene = layer?.closest('.rs-bg') ?? null;
    const trailElements = Array.from(document.querySelectorAll('[data-trail]'));

    // Pause seluruh animasi ambient saat tab tidak aktif.
    document.addEventListener(
        'visibilitychange',
        () => {
            document.documentElement.classList.toggle('is-ambient-paused', document.hidden);
        },
        { passive: true },
    );

    if (!layer) {
        return;
    }

    const finePointer = window.matchMedia('(pointer: fine)').matches;

    if (prefersReducedMotion || !finePointer) {
        return;
    }

    const trails = trailElements.map((element, index) => ({
        element,
        ease: index === 0 ? 0.22 : 0.12,
        x: window.innerWidth * 0.5,
        y: window.innerHeight * 0.3,
    }));

    let headX = window.innerWidth * 0.5;
    let headY = window.innerHeight * 0.3;
    let frame = null;
    let hasMoved = false;

    const setHead = () => {
        layer.style.setProperty('--rs-pointer-x', `${headX}px`);
        layer.style.setProperty('--rs-pointer-y', `${headY}px`);
    };

    const setTrail = (trail) => {
        trail.element.style.setProperty('--rs-trail-x', `${trail.x}px`);
        trail.element.style.setProperty('--rs-trail-y', `${trail.y}px`);
    };

    setHead();

    trails.forEach((trail) => {
        trail.x = headX;
        trail.y = headY;
        setTrail(trail);
    });

    const step = () => {
        let moving = false;

        trails.forEach((trail) => {
            trail.x += (headX - trail.x) * trail.ease;
            trail.y += (headY - trail.y) * trail.ease;

            setTrail(trail);

            if (Math.abs(headX - trail.x) > 0.4 || Math.abs(headY - trail.y) > 0.4) {
                moving = true;
            }
        });

        frame = moving ? window.requestAnimationFrame(step) : null;
    };

    const start = () => {
        if (frame === null) {
            frame = window.requestAnimationFrame(step);
        }
    };

    const update = (event) => {
        headX = event.clientX;
        headY = event.clientY;

        if (!hasMoved) {
            hasMoved = true;
            scene?.classList.add('is-trailing');
        }

        setHead();
        start();
    };

    layer.classList.add('is-active');

    window.addEventListener('pointermove', update, { passive: true });

    document.addEventListener('pointerleave', () => {
        layer.classList.remove('is-active');
        scene?.classList.remove('is-trailing');
    });

    document.addEventListener('pointerenter', () => {
        layer.classList.add('is-active');

        if (hasMoved && !document.hidden) {
            scene?.classList.add('is-trailing');
        }
    });
};
