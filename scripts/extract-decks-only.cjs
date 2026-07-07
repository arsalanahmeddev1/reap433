const fs = require('fs');

const html = fs.readFileSync('artifact.html', 'utf8');
const idx = html.indexOf('var DECKS = {');
const orderIdx = html.indexOf('var DECK_ORDER', idx);

if (idx < 0 || orderIdx < 0) {
    console.error('DECKS block not found in artifact.html');
    process.exit(1);
}

const scriptEnd = html.indexOf('render();', orderIdx);
const chunk = html.slice(idx, scriptEnd);

const decoded = chunk
    .replace(/\\u003e/g, '>')
    .replace(/\\u003c/g, '<')
    .replace(/\\u0026/g, '&')
    .replace(/\\"/g, '"')
    .replace(/\\n/g, '\n');

const decksStart = decoded.indexOf('var DECKS = {');
const decksEnd = decoded.indexOf('var DECK_ORDER');
const orderMatch = decoded.match(/var DECK_ORDER = (\[[^\]]+\]);/);

if (!orderMatch) {
    console.error('DECK_ORDER not found');
    process.exit(1);
}

const deckFn = new Function(
    decoded.slice(decksStart, decksEnd)
        + '\nvar DECK_ORDER = ' + orderMatch[1] + ';\nreturn { DECKS, DECK_ORDER };'
);
const { DECKS, DECK_ORDER } = deckFn();

function normalizeUnicode(value) {
    if (typeof value === 'string') {
        return value.replace(/\\u([0-9a-fA-F]{4})/g, (_, hex) => String.fromCharCode(parseInt(hex, 16)));
    }

    if (Array.isArray(value)) {
        return value.map(normalizeUnicode);
    }

    if (value && typeof value === 'object') {
        const out = {};
        Object.keys(value).forEach((key) => {
            out[key] = normalizeUnicode(value[key]);
        });
        return out;
    }

    return value;
}

const normalizedDecks = normalizeUnicode(DECKS);

fs.writeFileSync(
    'public/assets/web/js/biblical-trivia-decks.js',
    `/**
 * Reap433 Bible Trivia — deck data (from Claude artifact)
 * https://claude.ai/public/artifacts/e35df8f7-cee8-4849-8404-61745b6535d2
 */
(function (global) {
    'use strict';

    global.BiblicalTriviaDecks = {
        DECK_ORDER: ${JSON.stringify(DECK_ORDER, null, 8).replace(/^/gm, '        ')},
        DECKS: ${JSON.stringify(normalizedDecks, null, 4)},
    };
}(typeof window !== 'undefined' ? window : this));
`
);

console.log('Updated biblical-trivia-decks.js only');
console.log('Decks:', DECK_ORDER.join(', '));
DECK_ORDER.forEach((key) => {
    console.log(' ', key, '-', normalizedDecks[key].cards.length, 'cards');
});
