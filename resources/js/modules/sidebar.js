export const initSidebar = () => {
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
