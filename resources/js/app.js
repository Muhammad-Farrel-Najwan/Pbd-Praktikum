const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const initSidebar = () => {
    const sidebar = document.getElementById('rs-sidebar');
    const toggle = document.getElementById('rs-menu-toggle');
    const scrim = document.getElementById('rs-scrim');

    if (!sidebar || !toggle || !scrim) {
        return;
    }

    const setOpen = (open) => {
        sidebar.classList.toggle('is-open', open);
        scrim.classList.toggle('is-visible', open);
        document.body.classList.toggle('rs-nav-open', open);

        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    };

    toggle.addEventListener('click', () => {
        setOpen(!sidebar.classList.contains('is-open'));
    });

    scrim.addEventListener('click', () => {
        setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            setOpen(false);
        }
    });

    sidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                setOpen(false);
            }
        });
    });
};

const initReveal = () => {
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

const initCounters = () => {
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

const initPasswordToggles = () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const targetId = button.getAttribute('data-password-toggle');
        const input = targetId ? document.getElementById(targetId) : null;

        if (!input) {
            return;
        }

        button.addEventListener('click', () => {
            const isVisible = button.getAttribute('data-visible') === 'true';
            const nextVisible = !isVisible;

            input.type = nextVisible ? 'text' : 'password';
            button.setAttribute('data-visible', nextVisible ? 'true' : 'false');
            button.setAttribute('aria-label', nextVisible ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
};

const initMarginRows = () => {
    const rows = document.querySelectorAll('[data-margin-row]');

    if (!rows.length) {
        return;
    }

    const selectRow = (row) => {
        rows.forEach((item) => item.classList.remove('is-selected'));
        row.classList.add('is-selected');
    };

    rows.forEach((row) => {
        const radio = row.querySelector('input[type="radio"]');

        if (!radio) {
            return;
        }

        row.addEventListener('click', (event) => {
            if (event.target === radio || event.target.closest('a, button, label')) {
                return;
            }

            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        });

        radio.addEventListener('change', () => {
            if (radio.checked) {
                selectRow(row);
            }
        });
    });

    const checked = document.querySelector('[data-margin-row] input[type="radio"]:checked');

    if (checked) {
        selectRow(checked.closest('[data-margin-row]'));
    }
};

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initReveal();
    initCounters();
    initPasswordToggles();
    initMarginRows();
});
