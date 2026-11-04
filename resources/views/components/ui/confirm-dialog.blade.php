<dialog
    class="rs-modal"
    id="rs-confirm-dialog"
    data-confirm-dialog
    aria-labelledby="rs-confirm-title"
    aria-describedby="rs-confirm-message"
>
    <div class="rs-modal-card">
        <span class="rs-modal-icon" data-confirm-icon>
            <x-ui.icon name="alert" />
        </span>

        <div class="rs-modal-copy">
            <span class="rs-modal-eyebrow">Konfirmasi Tindakan</span>
            <h2 id="rs-confirm-title" data-confirm-title>Konfirmasi</h2>
            <p id="rs-confirm-message" data-confirm-message>Apakah kamu yakin?</p>
        </div>

        <div class="rs-modal-actions">
            <button type="button" class="rs-btn rs-btn-secondary" data-confirm-cancel>
                Batal
            </button>

            <button type="button" class="rs-btn rs-btn-danger" data-confirm-accept>
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</dialog>
