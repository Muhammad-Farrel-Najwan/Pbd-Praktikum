export const initProgressBar = () => {
    const bar = document.querySelector('[data-progress]');

    if (!bar) {
        return;
    }

    const fill = bar.querySelector('[data-progress-fill]');

    if (!fill) {
        return;
    }

    let timer = null;

    const start = () => {
        window.clearTimeout(timer);

        bar.classList.add('is-active');
        fill.style.width = '12%';

        window.requestAnimationFrame(() => {
            fill.style.width = '68%';
        });
    };

    const finish = () => {
        fill.style.width = '100%';

        timer = window.setTimeout(() => {
            bar.classList.remove('is-active');

            window.setTimeout(() => {
                fill.style.width = '0';
            }, 240);
        }, 260);
    };

    // Show progress for internal navigations and form submissions.
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');

        if (!link) {
            return;
        }

        const href = link.getAttribute('href');

        if (
            !href ||
            href.startsWith('#') ||
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:')
        ) {
            return;
        }

        const url = new URL(link.href, window.location.href);

        if (url.origin !== window.location.origin || url.pathname === window.location.pathname) {
            return;
        }

        start();
    });

    document.addEventListener('submit', (event) => {
        if (event.defaultPrevented) {
            return;
        }

        start();
    });

    window.addEventListener('pageshow', () => {
        window.clearTimeout(timer);
        bar.classList.remove('is-active');
        fill.style.width = '0';
    });
};
