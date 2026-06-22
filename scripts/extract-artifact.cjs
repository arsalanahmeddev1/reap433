const fs = require('fs');

const raw = fs.readFileSync('artifact-extracted.js', 'utf8');
const decoded = raw
    .replace(/\\u003e/g, '>')
    .replace(/\\u003c/g, '<')
    .replace(/\\u0026/g, '&')
    .replace(/\\"/g, '"')
    .replace(/\\n/g, '\n');

const decksStart = decoded.indexOf('var DECKS = {');
const decksEnd = decoded.indexOf('var DECK_ORDER');
const orderMatch = decoded.match(/var DECK_ORDER = (\[[^\]]+\]);/);

if (!orderMatch) {
    console.error('Could not find DECK_ORDER');
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

const html = fs.readFileSync('artifact.html', 'utf8');
const cssStart = html.indexOf('#reap433-decks * { box-sizing: border-box; }');
const cssEnd = html.indexOf('@media (prefers-reduced-motion: reduce)', cssStart);
const cssTail = html.indexOf('}', html.indexOf('transition: none', cssEnd) + 20) + 1;
const css = html.slice(cssStart, cssTail).replace(/\\n/g, '\n').replace(/\\"/g, '"');

fs.writeFileSync(
    'public/assets/web/css/biblical-trivia.css',
    `/* Reap433 Bible Trivia — matches Claude artifact */\n.biblical-trivia-page { background: #f7f1e3; }\n.biblical-trivia-section { padding: 0; }\n.biblical-trivia-container { max-width: 100%; padding: 0; }\n${css}\n`
);

console.log('Extracted', DECK_ORDER.length, 'decks');
DECK_ORDER.forEach((key) => {
    console.log(' ', key, '-', normalizedDecks[key].cards.length, 'cards');
});
console.log('Faith verse char code:', normalizedDecks.faith.verse.charCodeAt(0));
