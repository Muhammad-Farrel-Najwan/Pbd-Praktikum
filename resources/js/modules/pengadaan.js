const MAX_QTY = 100000;

/**
 * Form pengadaan: katalog barang + keranjang.
 *
 * Pola ini sama dengan form penjualan supaya seluruh transaksi memakai
 * bahasa UX yang konsisten: tambah barang dari katalog, atur jumlah di
 * keranjang, lalu simpan.
 */
export const initPengadaanForm = () => {
    const form = document.querySelector('[data-pengadaan-form]');

    if (!form) {
        return;
    }

    const itemsContainer = form.querySelector('[data-pengadaan-items]');
    const emptyState = form.querySelector('[data-pengadaan-empty]');
    const hiddenInputs = form.querySelector('[data-pengadaan-hidden]');
    const subtotalElement = form.querySelector('[data-pengadaan-subtotal]');
    const totalElement = form.querySelector('[data-pengadaan-total]');
    const countElement = form.querySelector('[data-pengadaan-count]');
    const submitButton = form.querySelector('[data-pengadaan-submit]');
    const searchInput = form.querySelector('[data-table-search]');
    const oldItemsData = document.getElementById('pengadaan-old-items');
    const rows = Array.from(form.querySelectorAll('[data-pengadaan-row]'));

    if (!itemsContainer || !emptyState || !hiddenInputs || !subtotalElement || !totalElement) {
        return;
    }

    const cart = new Map();
    const formatRupiah = (value) => `Rp ${new Intl.NumberFormat('id-ID').format(value)}`;

    const findRow = (id) => rows.find((row) => row.dataset.id === String(id));

    const syncRow = (id) => {
        const row = findRow(id);

        if (!row) {
            return;
        }

        const item = cart.get(String(id));
        const badge = row.querySelector('[data-pengadaan-badge]');

        row.classList.toggle('is-in-cart', Boolean(item));

        if (badge) {
            badge.hidden = !item;
            badge.textContent = item ? `\u00d7${item.qty}` : '';
        }
    };

    const render = () => {
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
            row.dataset.pengadaanItem = item.id;

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
            decrease.dataset.pengadaanDecrease = '';
            decrease.setAttribute('aria-label', `Kurangi jumlah ${item.nama}`);
            decrease.textContent = '\u2212';

            const qtyInput = document.createElement('input');
            qtyInput.type = 'number';
            qtyInput.className = 'rs-pos-qty-input';
            qtyInput.min = '1';
            qtyInput.max = String(MAX_QTY);
            qtyInput.value = String(item.qty);
            qtyInput.dataset.pengadaanQty = '';
            qtyInput.setAttribute('aria-label', `Jumlah ${item.nama}`);

            const increase = document.createElement('button');
            increase.type = 'button';
            increase.className = 'rs-pos-qty-btn';
            increase.dataset.pengadaanIncrease = '';
            increase.setAttribute('aria-label', `Tambah jumlah ${item.nama}`);
            increase.textContent = '+';

            qty.append(decrease, qtyInput, increase);

            const lineTotalElement = document.createElement('strong');
            lineTotalElement.className = 'rs-pos-item-total';
            lineTotalElement.textContent = formatRupiah(lineTotal);

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'rs-pos-remove';
            remove.dataset.pengadaanRemove = '';
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

        subtotalElement.textContent = formatRupiah(subtotal);
        totalElement.textContent = formatRupiah(subtotal);

        if (countElement) {
            const unit = Array.from(cart.values()).reduce((sum, item) => sum + item.qty, 0);
            countElement.textContent = cart.size
                ? `${cart.size} barang dipilih \u00b7 ${unit} unit`
                : 'Belum ada barang dipilih';
        }

        if (submitButton) {
            submitButton.disabled = cart.size === 0;
        }

        rows.forEach((row) => syncRow(row.dataset.id));
    };

    const addItem = (row) => {
        const id = String(row.dataset.id);
        const existing = cart.get(id);

        if (existing) {
            existing.qty = Math.min(existing.qty + 1, MAX_QTY);
        } else {
            cart.set(id, {
                id,
                nama: row.dataset.nama ?? '',
                harga: Number(row.dataset.harga ?? 0),
                qty: 1,
            });
        }

        render();
    };

    // Tombol Tambah di setiap baris katalog
    rows.forEach((row) => {
        row.querySelector('[data-pengadaan-add]')?.addEventListener('click', () => addItem(row));
    });

    // Enter di kolom pencarian menambahkan hasil teratas yang terlihat
    searchInput?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') {
            return;
        }

        const firstVisible = rows.find((row) => !row.hidden);

        if (!firstVisible) {
            return;
        }

        event.preventDefault();
        addItem(firstVisible);
        searchInput.select();
    });

    // Interaksi di keranjang
    itemsContainer.addEventListener('click', (event) => {
        const row = event.target.closest('[data-pengadaan-item]');

        if (!row) {
            return;
        }

        const item = cart.get(row.dataset.pengadaanItem);

        if (!item) {
            return;
        }

        if (event.target.closest('[data-pengadaan-increase]')) {
            item.qty = Math.min(item.qty + 1, MAX_QTY);
            render();

            return;
        }

        if (event.target.closest('[data-pengadaan-decrease]')) {
            item.qty -= 1;

            if (item.qty <= 0) {
                cart.delete(item.id);
            }

            render();

            return;
        }

        if (event.target.closest('[data-pengadaan-remove]')) {
            cart.delete(item.id);
            render();
        }
    });

    const commitQty = (input) => {
        const row = input.closest('[data-pengadaan-item]');
        const item = row ? cart.get(row.dataset.pengadaanItem) : null;

        if (!item) {
            return;
        }

        let qty = Number(input.value);

        if (! Number.isFinite(qty) || qty < 1) {
            qty = 1;
        }

        item.qty = Math.min(Math.floor(qty), MAX_QTY);
        render();
    };

    itemsContainer.addEventListener('change', (event) => {
        const input = event.target.closest('[data-pengadaan-qty]');

        if (input) {
            commitQty(input);
        }
    });

    itemsContainer.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && event.target.closest('[data-pengadaan-qty]')) {
            event.preventDefault();
            commitQty(event.target);
        }
    });

    // Pulihkan keranjang setelah validasi gagal
    if (oldItemsData) {
        try {
            const oldItems = JSON.parse(oldItemsData.textContent ?? '{}');

            Object.entries(oldItems).forEach(([id, qty]) => {
                const row = findRow(id);

                if (!row) {
                    return;
                }

                cart.set(String(id), {
                    id: String(id),
                    nama: row.dataset.nama ?? '',
                    harga: Number(row.dataset.harga ?? 0),
                    qty: Math.min(Math.max(1, Number(qty) || 1), MAX_QTY),
                });
            });
        } catch (error) {
            // Data lama tidak valid: biarkan keranjang kosong
        }
    }

    render();
};
