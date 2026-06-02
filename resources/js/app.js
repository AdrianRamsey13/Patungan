import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global confirmation dialog store
Alpine.store('dialog', {
    open: false,
    title: '',
    message: '',
    confirmText: 'Ya, lanjutkan',
    cancelText: 'Batal',
    isDanger: false,
    _resolve: null,

    show({ title = '', message = '', confirmText = 'Ya, lanjutkan', cancelText = 'Batal', isDanger = false } = {}) {
        this.title       = title;
        this.message     = message;
        this.confirmText = confirmText;
        this.cancelText  = cancelText;
        this.isDanger    = isDanger;
        this.open        = true;

        // Returns a promise so callers can await the result
        return new Promise(resolve => { this._resolve = resolve; });
    },

    confirm() {
        this.open = false;
        if (this._resolve) { this._resolve(true); this._resolve = null; }
    },

    cancel() {
        this.open = false;
        if (this._resolve) { this._resolve(false); this._resolve = null; }
    },
});

// Helper: drop-in replacement for window.confirm — async-friendly
window.ptConfirm = async function (options) {
    return await Alpine.store('dialog').show(options);
};

Alpine.start();
