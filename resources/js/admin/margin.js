export const initMarginRows = () => {
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
