import { initSidebar } from './modules/sidebar.js';
import { initReveal } from './modules/reveal.js';
import { initCounters } from './modules/counters.js';
import { initPasswordToggles } from './modules/form.js';
import { initMarginRows } from './admin/margin.js';
import { initPosForm } from './modules/pos.js';
import { initTableSearch } from './modules/table.js';
import { initNotificationCenter } from './modules/notification-center.js';
import { initConfirmDialog } from './modules/confirm-dialog.js';
import { initCommandPalette } from './modules/command-palette.js';
import { initDensityToggle } from './modules/density.js';
import { initProgressBar } from './modules/progress.js';
import { initToasts, initFlashToasts, showToast } from './modules/toasts.js';

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initReveal();
    initCounters();
    initPasswordToggles();
    initMarginRows();
    initPosForm();
    initTableSearch();
    initNotificationCenter();
    initConfirmDialog();
    initCommandPalette();
    initDensityToggle();
    initProgressBar();
    initToasts();
    initFlashToasts();

    document.addEventListener('rs:density-changed', (event) => {
        const compact = event.detail?.density === 'compact';

        showToast(compact ? 'Tampilan padat diaktifkan.' : 'Tampilan nyaman diaktifkan.', {
            variant: 'info',
            title: 'Kepadatan tampilan',
            duration: 2400,
        });
    });

    window.RedStock = { toast: showToast };
});
