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
