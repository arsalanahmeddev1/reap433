/**
 * Reusable Yes/No confirmation (SweetAlert2 when loaded; falls back to window.confirm).
 *
 * @param {{ title?: string, text?: string, html?: string, icon?: string, confirmText?: string, cancelText?: string }} opts
 * @returns {Promise<boolean>} true if user confirms
 */
window.reusableConfirm = function (opts) {
    opts = opts || {};
    var title = opts.title || 'Attention';
    var confirmText = opts.confirmText || 'Yes';
    var cancelText = opts.cancelText || 'No';

    if (typeof Swal === 'undefined') {
        var msg = opts.text || (opts.html ? opts.html.replace(/<[^>]+>/g, ' ') : '') || 'Continue?';
        return Promise.resolve(window.confirm(msg));
    }

    var cfg = {
        icon: opts.icon || 'warning',
        title: title,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true,
        focusCancel: true,
    };

    if (opts.html) {
        cfg.html = opts.html;
    } else {
        cfg.text = opts.text || '';
    }

    return Swal.fire(cfg).then(function (r) {
        return r.isConfirmed === true;
    });
};
