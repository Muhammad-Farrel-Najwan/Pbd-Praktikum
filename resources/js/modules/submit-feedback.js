/**
 * Umpan balik saat formulir dikirim: tombol simpan berubah menjadi keadaan
 * memuat supaya pengguna tahu prosesnya sedang berjalan.
 *
 * Formulir yang dibatalkan (misalnya oleh dialog konfirmasi) tidak ikut
 * berubah, dan keadaan memuat otomatis dikembalikan bila halaman tidak jadi
 * berpindah (mis. validasi HTML bawaan browser).
 */
export const initSubmitFeedback = () => {
    document.addEventListener('submit', (event) => {
        if (event.defaultPrevented) {
            return;
        }

        const form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const submitter = event.submitter instanceof HTMLElement
            ? event.submitter
            : form.querySelector('[type="submit"]');

        if (!submitter || submitter.classList.contains('is-loading')) {
            return;
        }

        submitter.classList.add('is-loading');
        submitter.setAttribute('aria-busy', 'true');

        // Kembalikan bila halaman tidak jadi berpindah (mis. validasi HTML).
        window.setTimeout(() => {
            if (!document.body.contains(submitter)) {
                return;
            }

            submitter.classList.remove('is-loading');
            submitter.removeAttribute('aria-busy');
        }, 6000);
    });
};
