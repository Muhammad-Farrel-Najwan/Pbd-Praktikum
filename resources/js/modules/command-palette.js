export const initCommandPalette = () => {
    const root = document.querySelector('[data-cmdk]');

    if (!root) {
        return;
    }

    const input = root.querySelector('[data-cmdk-input]');
    const list = root.querySelector('[data-cmdk-list]');
    const empty = root.querySelector('[data-cmdk-empty]');
    const items = Array.from(root.querySelectorAll('[data-cmdk-item]'));
    const groups = Array.from(root.querySelectorAll('.rs-cmdk-group'));
    const triggers = Array.from(document.querySelectorAll('[data-cmdk-open]'));

    let activeIndex = -1;

    const visibleItems = () => items.filter((item) => !item.hidden);

    const setActive = (index) => {
        const available = visibleItems();

        if (!available.length) {
            activeIndex = -1;

            return;
        }

        activeIndex = (index + available.length) % available.length;

        items.forEach((item) => {
            item.classList.remove('is-active');
            item.setAttribute('aria-selected', 'false');
        });

        const current = available[activeIndex];

        current.classList.add('is-active');
        current.setAttribute('aria-selected', 'true');
        current.scrollIntoView({ block: 'nearest' });
    };

    const filter = () => {
        const term = input.value.trim().toLowerCase();

        items.forEach((item) => {
            const label = item.dataset.cmdkLabel || '';

            item.hidden = term !== '' && !label.includes(term);
        });

        // Hide group headings that no longer have any visible item beneath them.
        groups.forEach((group) => {
            let node = group.nextElementSibling;
            let hasVisible = false;

            while (node && !node.classList.contains('rs-cmdk-group')) {
                if (node.hasAttribute('data-cmdk-item') && !node.hidden) {
                    hasVisible = true;

                    break;
                }

                node = node.nextElementSibling;
            }

            group.hidden = !hasVisible;
        });

        const available = visibleItems();

        if (empty) {
            empty.hidden = available.length > 0;
        }

        setActive(available.length ? 0 : -1);
    };

    const open = () => {
        root.hidden = false;
        root.classList.add('is-open');
        document.body.classList.add('rs-cmdk-open');

        input.value = '';
        filter();

        window.requestAnimationFrame(() => {
            input.focus();
        });
    };

    const close = () => {
        root.classList.remove('is-open');
        document.body.classList.remove('rs-cmdk-open');

        window.setTimeout(() => {
            root.hidden = true;
        }, 180);
    };

    const go = (item) => {
        if (!item) {
            return;
        }

        window.location.href = item.dataset.cmdkHref;
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', open);
    });

    input.addEventListener('input', filter);

    input.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            setActive(activeIndex + 1);

            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            setActive(activeIndex - 1);

            return;
        }

        if (event.key === 'Enter') {
            event.preventDefault();
            go(visibleItems()[activeIndex]);

            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            close();
        }
    });

    list.addEventListener('click', (event) => {
        const item = event.target.closest('[data-cmdk-item]');

        if (item) {
            go(item);
        }
    });

    list.addEventListener('mousemove', (event) => {
        const item = event.target.closest('[data-cmdk-item]');

        if (!item) {
            return;
        }

        const available = visibleItems();
        const index = available.indexOf(item);

        if (index !== -1 && index !== activeIndex) {
            setActive(index);
        }
    });

    root.addEventListener('click', (event) => {
        if (event.target === root) {
            close();
        }
    });

    document.addEventListener('keydown', (event) => {
        const isTyping = ['INPUT', 'TEXTAREA', 'SELECT'].includes(
            document.activeElement?.tagName || '',
        );

        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();

            if (root.hidden) {
                open();
            } else {
                close();
            }

            return;
        }

        if (event.key === 'Escape' && !root.hidden) {
            close();

            return;
        }

        // "/" jumps straight into the palette when not typing in a field.
        if (event.key === '/' && !isTyping && root.hidden) {
            event.preventDefault();
            open();
        }
    });
};
