export const initNotificationCenter = () => {
    const center = document.querySelector('[data-notification-center]');

    if (!center) {
        return;
    }

    const toggle = center.querySelector('[data-notification-toggle]');
    const panel = center.querySelector('[data-notification-panel]');

    if (!toggle || !panel) {
        return;
    }

    const setOpen = (open) => {
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        center.classList.toggle('is-open', open);

        if (open) {
            panel.querySelector('a')?.focus();
        }
    };

    toggle.addEventListener('click', () => {
        setOpen(panel.hidden);
    });

    document.addEventListener('click', (event) => {
        if (!center.contains(event.target)) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.hidden) {
            setOpen(false);
            toggle.focus();
        }
    });

    panel.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setOpen(false));
    });
};
