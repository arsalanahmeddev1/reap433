/**
 * Rebuild artifact.html from client CSS + deck data + game logic.
 * Run: node scripts/build-artifact-html.cjs
 */
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const cssPath = path.join(root, 'public/assets/web/css/biblical-trivia.css');
const decksPath = path.join(root, 'public/assets/web/js/biblical-trivia-decks.js');
const logicPath = path.join(root, 'public/assets/web/js/biblical-trivia.js');

const cssRaw = fs.readFileSync(cssPath, 'utf8');
const css = cssRaw
    .replace(/^\/\*[\s\S]*?\*\/\n/, '')
    .replace(/^\.biblical-trivia-page \{ background: #f7f1e3; \}\n/, '')
    .replace(/^\.biblical-trivia-section \{ padding: 0; \}\n/, '')
    .replace(/^\.biblical-trivia-container \{ max-width: 100%; padding: 0; \}\n/, '')
    .trim();

const decksSource = fs.readFileSync(decksPath, 'utf8');
const match = decksSource.match(/global\.BiblicalTriviaDecks = (\{[\s\S]*\});/);
if (!match) {
    console.error('Could not parse biblical-trivia-decks.js');
    process.exit(1);
}

const { DECKS, DECK_ORDER } = new Function('return ' + match[1])();

function toJs(value, indent) {
    if (typeof value === 'string') {
        return JSON.stringify(value);
    }

    if (Array.isArray(value)) {
        if (value.length === 0) {
            return '[]';
        }

        const inner = value.map((item) => '  '.repeat(indent + 1) + toJs(item, indent + 1)).join(',\n');
        return '[\n' + inner + '\n' + '  '.repeat(indent) + ']';
    }

    if (value && typeof value === 'object') {
        const entries = Object.keys(value).map((key) => {
            return '  '.repeat(indent + 1) + key + ': ' + toJs(value[key], indent + 1);
        });

        return '{\n' + entries.join(',\n') + '\n' + '  '.repeat(indent) + '}';
    }

    return String(value);
}

let logic = fs.readFileSync(logicPath, 'utf8');
logic = logic
    .replace(/^\/\*\*[\s\S]*?\*\/\n/, '')
    .replace(/^\(function \(\) \{\n    'use strict';\n\n/, '')
    .replace(/var deckSource = window\.BiblicalTriviaDecks \|\| \{\};\n    var DECKS = deckSource\.DECKS \|\| \{\};\n    var DECK_ORDER = deckSource\.DECK_ORDER \|\| Object\.keys\(DECKS\);\n\n/, '')
    .replace(/\}?\(\)\);?\s*$/, '')
    .trim();

const html = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Reap433 Bible Trivia — Seven decks covering Faith, Baptism, Tithing, Salvation, Holy Spirit, Spiritual Gifts, and Reap What You Sow." />
  <title>Reap433 — Bible Trivia Decks</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { font-size: 16px; }
    body {
      background: #f7f1e3;
      min-height: 100vh;
      -webkit-font-smoothing: antialiased;
    }
  </style>
</head>
<body>
<div id="reap433-decks"></div>

<style>
${css}
</style>

<script>
(function () {
  var DECKS = ${toJs(DECKS, 1)};
  var DECK_ORDER = ${JSON.stringify(DECK_ORDER)};

${logic}
}());
</script>
</body>
</html>
`;

fs.writeFileSync(path.join(root, 'artifact.html'), html);
console.log('Wrote artifact.html');
