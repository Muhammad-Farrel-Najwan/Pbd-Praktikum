const PREFIX = 'redstock:';

export const readPreference = (key, fallback = null) => {
    try {
        const value = window.localStorage.getItem(PREFIX + key);

        return value === null ? fallback : value;
    } catch {
        return fallback;
    }
};

export const writePreference = (key, value) => {
    try {
        window.localStorage.setItem(PREFIX + key, value);
    } catch {
        // Storage can be unavailable in private mode — fail silently.
    }
};
