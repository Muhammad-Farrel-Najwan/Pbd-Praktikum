const ICONS = {
    success: '<path d="m5 12 5 5L20 7"></path>',
    error: '<circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>',
    info: '<circle cx="12" cy="12" r="9"></circle><line x1="12" y1="11" x2="12" y2="16"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>',
};

const buildIcon = (variant) => {
    const wrapper = document.createElementNS('http://www.w3.org/2000/svg', 'svg');

    wrapper.setAttribute('viewBox', '0 0 24 24');
    wrapper.setAttribute('fill', 'none');
    wrapper.setAttribute('stroke', 'currentColor');
    wrapper.setAttribute('stroke-width', '1.8');
    wrapper.setAttribute('stroke-linecap', 'round');
    wrapper.setAttribute('stroke-linejoin', 'round');
    wrapper.setAttribute('aria-hidden', 'true');
    wrapper.innerHTML = ICONS[variant] || ICONS.info;

    return wrapper;
};

const dismiss = (toast) => {
    if (toast.classList.contains('is-leaving')) {
        return;
    }

    toast.classList.add('is-leaving');

    window.setTimeout(() => {
        toast.remove();
    }, 220);
};

export const showToast = (message, options = {}) => {
    const stack = document.querySelector('[data-toast-stack]');

    if (!stack) {
        return;
    }

    const variant = options.variant || 'info';
    const title = options.title || null;

    const toast = document.createElement('div');

    toast.className = `rs-toast rs-toast-${variant}`;
    toast.setAttribute('role', variant === 'error' ? 'alert' : 'status');
    toast.appendChild(buildIcon(variant));

    const body = document.createElement('div');

    if (title) {
        const heading = document.createElement('strong');

        heading.textContent = title;
        body.appendChild(heading);
    }

    const text = document.createElement('span');

    text.textContent = message;
    body.appendChild(text);

    toast.appendChild(body);
    stack.appendChild(toast);

    const timeout = options.duration ?? 4600;

    if (timeout > 0) {
        window.setTimeout(() => dismiss(toast), timeout);
    }

    toast.addEventListener('click', () => dismiss(toast));

    return toast;
};

export const initToasts = () => {
    const stack = document.querySelector('[data-toast-stack]');

    if (!stack) {
        return;
    }

    stack.querySelectorAll('[data-toast-auto]').forEach((toast) => {
        window.setTimeout(() => dismiss(toast), 4600);
        toast.addEventListener('click', () => dismiss(toast));
    });
};

export const initFlashToasts = () => {
    const source = document.querySelector('[data-flash-toasts]');

    if (!source) {
        return;
    }

    let payload = [];

    try {
        payload = JSON.parse(source.dataset.flashToasts || '[]');
    } catch {
        payload = [];
    }

    if (!Array.isArray(payload)) {
        return;
    }

    payload.forEach((entry) => {
        showToast(entry.message, {
            variant: entry.variant || 'info',
            title: entry.title || null,
        });
    });
};
