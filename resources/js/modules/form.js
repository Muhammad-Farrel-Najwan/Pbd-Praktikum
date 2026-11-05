export const initPasswordToggles = () => {
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
