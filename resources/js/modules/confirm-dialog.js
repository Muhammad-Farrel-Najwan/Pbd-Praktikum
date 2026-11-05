export const initConfirmDialog = () => {
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
