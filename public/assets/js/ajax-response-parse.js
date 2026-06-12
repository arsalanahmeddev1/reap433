/**
 * Parse Laravel JSON from bodies polluted with HTML/PHP warnings (e.g. max_file_uploads exceeded).
 * Uses first "{" through last "}" — sufficient for single JSON objects Laravel returns.
 */
(function (global) {
    'use strict';

    global.parseJsonFromAjaxResponse = function (raw) {
        if (raw == null) {
            return null;
        }
        var s = typeof raw === 'string' ? raw : String(raw);
        s = s.trim();
        var start = s.indexOf('{');
        var end = s.lastIndexOf('}');
        if (start === -1 || end <= start) {
            return null;
        }
        try {
            return JSON.parse(s.slice(start, end + 1));
        } catch (e) {
            return null;
        }
    };
})(typeof window !== 'undefined' ? window : this);
