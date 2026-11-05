export const initPosForm = () => {
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
