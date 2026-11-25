/**
 * Pencarian tabel di sisi klien (tanpa reload).
 *
 * Dipakai oleh tabel yang seluruh barisnya sudah ada di halaman,
 * misalnya daftar master data pada panel admin.
 */
export const initTableSearch = () => {
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

/**
 * Pencarian tabel di sisi server (form GET).
 *
 * Dipakai oleh tabel berpaginasi supaya kata kunci berlaku untuk seluruh
 * data, bukan hanya baris pada halaman yang sedang tampil. Filter lain
 * dipertahankan oleh hidden input di komponen table-search.
 */
export const initTableSearchForm = () => {
    document.querySelectorAll('[data-table-search-form]').forEach((form) => {
        const input = form.querySelector('input[name="q"]');
        const clear = form.querySelector('[data-table-search-clear]');

        if (!input) {
            return;
        }

        // Tekan Escape untuk mengosongkan kata kunci lalu cari ulang.
        input.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            event.preventDefault();
            input.value = '';
            form.submit();
        });

        clear?.addEventListener('click', () => {
            input.value = '';
            form.submit();
        });
    });
};
