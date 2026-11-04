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

const initPosForm = () => {
    const form = document.querySelector('[data-pos-form]');

    if (!form) {
        return;
    }

    const itemsContainer = form.querySelector('[data-pos-items]');
    const emptyState = form.querySelector('[data-pos-empty]');
    const hiddenInputs = form.querySelector('[data-pos-hidden-inputs]');
    const subtotalElement = form.querySelector('[data-pos-subtotal]');
    const ppnElement = form.querySelector('[data-pos-ppn]');
    const totalElement = form.querySelector('[data-pos-total]');
    const submitButton = form.querySelector('[data-pos-submit]');
    const marginActive = form.dataset.marginActive === '1';
    const cart = new Map();
    const ppnRate = 0.11;

    if (!itemsContainer || !emptyState || !hiddenInputs || !subtotalElement || !ppnElement || !totalElement) {
        return;
    }

    const formatRupiah = (value) => `Rp ${new Intl.NumberFormat('id-ID').format(value)}`;

    const renderCart = () => {
        itemsContainer.innerHTML = '';
        hiddenInputs.innerHTML = '';

        emptyState.hidden = cart.size > 0;

        let subtotal = 0;
        let index = 0;

        cart.forEach((item) => {
            const lineTotal = item.harga * item.qty;
            subtotal += lineTotal;

            const row = document.createElement('div');
            row.className = 'rs-pos-item';
            row.dataset.posItem = item.id;

            const main = document.createElement('div');
            main.className = 'rs-pos-item-main';

            const name = document.createElement('span');
            name.className = 'rs-pos-item-name';
            name.textContent = item.nama;

            const price = document.createElement('span');
            price.className = 'rs-pos-item-price';
            price.textContent = `${formatRupiah(item.harga)} / unit`;

            main.append(name, price);

            const qty = document.createElement('div');
            qty.className = 'rs-pos-qty';

            const decrease = document.createElement('button');
            decrease.type = 'button';
            decrease.className = 'rs-pos-qty-btn';
            decrease.dataset.posDecrease = '';
            decrease.setAttribute('aria-label', `Kurangi jumlah ${item.nama}`);
            decrease.textContent = '\u2212';

            const qtyInput = document.createElement('input');
            qtyInput.type = 'number';
            qtyInput.className = 'rs-pos-qty-input';
            qtyInput.min = '1';
            qtyInput.max = String(item.stok);
            qtyInput.value = String(item.qty);
            qtyInput.dataset.posQty = '';
            qtyInput.setAttribute('aria-label', `Jumlah ${item.nama}`);

            const increase = document.createElement('button');
            increase.type = 'button';
            increase.className = 'rs-pos-qty-btn';
            increase.dataset.posIncrease = '';
            increase.setAttribute('aria-label', `Tambah jumlah ${item.nama}`);
            increase.textContent = '+';

            qty.append(decrease, qtyInput, increase);

            const lineTotalElement = document.createElement('strong');
            lineTotalElement.className = 'rs-pos-item-total';
            lineTotalElement.textContent = formatRupiah(lineTotal);

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'rs-pos-remove';
            remove.dataset.posRemove = '';
            remove.setAttribute('aria-label', `Hapus ${item.nama}`);
            remove.textContent = '\u00d7';

            row.append(main, qty, lineTotalElement, remove);
            itemsContainer.append(row);

            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = `items[${index}][idbarang]`;
            idInput.value = item.id;

            const qtyHidden = document.createElement('input');
            qtyHidden.type = 'hidden';
            qtyHidden.name = `items[${index}][jumlah]`;
            qtyHidden.value = String(item.qty);

            hiddenInputs.append(idInput, qtyHidden);

            index += 1;
        });

        const ppn = Math.round(subtotal * ppnRate);
        const total = subtotal + ppn;

        subtotalElement.textContent = formatRupiah(subtotal);
        ppnElement.textContent = formatRupiah(ppn);
        totalElement.textContent = formatRupiah(total);

        if (submitButton) {
            submitButton.disabled = cart.size === 0 || !marginActive;
        }
    };

    form.querySelectorAll('[data-pos-add]').forEach((button) => {
        button.addEventListener('click', () => {
            const stock = Number(button.dataset.stok ?? 0);

            if (stock <= 0) {
                return;
            }

            const id = button.dataset.id;
            const existing = cart.get(id);

            if (existing) {
                existing.qty = Math.min(existing.qty + 1, existing.stok);
            } else {
                cart.set(id, {
                    id,
                    nama: button.dataset.nama ?? '',
                    harga: Number(button.dataset.harga ?? 0),
                    stok: stock,
                    qty: 1,
                });
            }

            renderCart();
        });
    });

    itemsContainer.addEventListener('click', (event) => {
        const row = event.target.closest('[data-pos-item]');

        if (!row) {
            return;
        }

        const item = cart.get(row.dataset.posItem);

        if (!item) {
            return;
        }

        if (event.target.closest('[data-pos-increase]')) {
            item.qty = Math.min(item.qty + 1, item.stok);
            renderCart();

            return;
        }

        if (event.target.closest('[data-pos-decrease]')) {
            item.qty -= 1;

            if (item.qty <= 0) {
                cart.delete(item.id);
            }

            renderCart();

            return;
        }

        if (event.target.closest('[data-pos-remove]')) {
            cart.delete(item.id);
            renderCart();
        }
    });

    itemsContainer.addEventListener('change', (event) => {
        const input = event.target.closest('[data-pos-qty]');

        if (!input) {
            return;
        }

        const row = input.closest('[data-pos-item]');
        const item = row ? cart.get(row.dataset.posItem) : null;

        if (!item) {
            return;
        }

        let qty = Number(input.value);

        if (! Number.isFinite(qty) || qty < 1) {
            qty = 1;
        }

        item.qty = Math.min(Math.floor(qty), item.stok);
        renderCart();
    });

    renderCart();
};

const initTableSearch = () => {
    document.querySelectorAll('[data-table-search]').forEach((input) => {
        const panel = input.closest('.rs-panel');

        if (!panel) {
            return;
        }

        const rows = Array.from(panel.querySelectorAll('[data-table-row]'));

        if (!rows.length) {
            return;
        }

        const emptyRow = panel.querySelector('[data-table-search-empty]');
        const count = panel.querySelector('[data-table-search-count]');
        const clear = panel.querySelector('[data-table-search-clear]');
        const total = rows.length;

        const applyFilter = () => {
            const terms = input.value
                .trim()
                .toLowerCase()
                .split(/\s+/)
                .filter(Boolean);

            let visible = 0;

            rows.forEach((row) => {
                const text = Array.from(row.cells)
                    .slice(0, -1)
                    .map((cell) => cell.textContent)
                    .join(' ')
                    .toLowerCase();

                const matches = terms.every((term) => text.includes(term));

                row.hidden = !matches;

                if (matches) {
                    visible += 1;
                }
            });

            if (emptyRow) {
                emptyRow.hidden = visible !== 0;
            }

            if (count) {
                count.textContent = terms.length
                    ? `${visible} dari ${total} data`
                    : `${total} data`;
            }

            if (clear) {
                clear.hidden = terms.length === 0;
            }
        };

        input.addEventListener('input', applyFilter);

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && input.value !== '') {
                input.value = '';
                applyFilter();
            }
        });

        clear?.addEventListener('click', () => {
            input.value = '';
            input.focus();
            applyFilter();
        });

        applyFilter();
    });
};

const initNotificationCenter = () => {
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

const initConfirmDialog = () => {
    const dialog = document.querySelector('[data-confirm-dialog]');

    if (!dialog) {
        return;
    }

    const title = dialog.querySelector('[data-confirm-title]');
    const message = dialog.querySelector('[data-confirm-message]');
    const accept = dialog.querySelector('[data-confirm-accept]');
    const cancel = dialog.querySelector('[data-confirm-cancel]');
    const icon = dialog.querySelector('[data-confirm-icon]');
    const confirmedForms = new WeakSet();

    let pendingForm = null;

    const close = () => {
        if (dialog.open) {
            dialog.close();
        }

        pendingForm = null;
    };

    const open = (form) => {
        pendingForm = form;

        title.textContent = form.dataset.confirmTitle || 'Konfirmasi Tindakan';
        message.textContent = form.dataset.confirm || 'Apakah kamu yakin?';
        accept.textContent = form.dataset.confirmLabel || 'Ya, Lanjutkan';

        const variant = form.dataset.confirmVariant || 'danger';

        accept.classList.remove('rs-btn-danger', 'rs-btn-primary', 'rs-btn-warning');
        accept.classList.add(`rs-btn-${variant}`);

        icon?.classList.toggle('is-warning', variant === 'warning');

        dialog.showModal();
        cancel.focus();
    };

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (confirmedForms.has(form)) {
                return;
            }

            event.preventDefault();
            open(form);
        });
    });

    accept.addEventListener('click', () => {
        if (!pendingForm) {
            close();

            return;
        }

        const form = pendingForm;
        confirmedForms.add(form);
        close();
        form.requestSubmit();
    });

    cancel.addEventListener('click', close);

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            close();
        }
    });

    dialog.addEventListener('close', () => {
        pendingForm = null;
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initReveal();
    initCounters();
    initPasswordToggles();
    initMarginRows();
    initPosForm();
    initTableSearch();
    initNotificationCenter();
    initConfirmDialog();
});
