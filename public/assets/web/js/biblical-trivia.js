/**
 * Biblical Trivia Card Game — client logic
 * Requires: biblical-trivia-decks.js, #biblical-trivia-root
 */
(function () {
    'use strict';

    var root = document.getElementById('biblical-trivia-root');
    if (!root) {
        return;
    }

    var deckSource = window.BiblicalTriviaDecks || {};
    var DECKS = deckSource.DECKS || {};
    var DECK_ORDER = deckSource.DECK_ORDER || Object.keys(DECKS);

    var state = {
        screen: 'menu',
        deckKey: null,
        order: [],
        pos: 0,
        score: 0,
        answered: 0,
        selected: null,
        revealed: false,
    };

    function escapeHtml(str) {
        if (str == null) {
            return '';
        }

        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function shuffle(arr) {
        var copy = arr.slice();

        for (var i = copy.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var temp = copy[i];
            copy[i] = copy[j];
            copy[j] = temp;
        }

        return copy;
    }

    function resetPlayState() {
        state.pos = 0;
        state.score = 0;
        state.answered = 0;
        state.selected = null;
        state.revealed = false;
        state.order = [];
    }

    function getDeck(key) {
        if (!key || !DECKS[key]) {
            return null;
        }

        return DECKS[key];
    }

    function openDeck(key) {
        var deck = getDeck(key);

        state.screen = 'play';
        state.deckKey = key;
        resetPlayState();

        if (!deck || !Array.isArray(deck.cards) || deck.cards.length === 0) {
            render();
            return;
        }

        state.order = shuffle(deck.cards.map(function (_, index) {
            return index;
        }));

        render();
    }

    function goMenu() {
        state.screen = 'menu';
        state.deckKey = null;
        resetPlayState();
        render();
    }

    function render() {
        if (state.screen === 'menu') {
            renderMenu();
            return;
        }

        renderPlay();
    }

    function renderMenu() {
        var html = '';

        html += '<div class="bt-header">'
            + '<div class="bt-eyebrow">Reap433 &middot; Foundations</div>'
            + '<h1 class="bt-title" id="biblical-trivia-heading">Biblical Trivia Card Game</h1>'
            + '<p class="bt-sub">Six trivia decks covering the core building blocks of the faith &mdash; '
            + 'each pulling questions and verses straight from Scripture.</p>'
            + '</div>';

        html += '<div class="bt-deck-grid" role="list">';

        DECK_ORDER.forEach(function (key) {
            var deck = getDeck(key);
            if (!deck) {
                return;
            }

            var cardCount = Array.isArray(deck.cards) ? deck.cards.length : 0;

            html += '<button type="button" class="bt-deck-tile" data-deck="' + escapeHtml(key) + '" '
                + 'role="listitem" aria-label="' + escapeHtml(deck.title + ', ' + cardCount + ' cards') + '">'
                + '<div class="bt-deck-bar" style="background:' + escapeHtml(deck.color) + ';"></div>'
                + '<div class="bt-deck-body">'
                + '<p class="bt-deck-name">' + escapeHtml(deck.title) + '</p>'
                + '<p class="bt-deck-verse">' + escapeHtml(deck.verse) + ' &mdash; ' + escapeHtml(deck.ref) + '</p>'
                + '<div class="bt-deck-meta">' + cardCount + ' cards &middot; ' + escapeHtml(deck.desc) + '</div>'
                + '</div></button>';
        });

        html += '</div>';

        root.innerHTML = html;
        attachMenuHandlers();
    }

    function renderEmptyDeck(deck) {
        return '<div class="bt-empty-deck" role="alert">'
            + '<p class="bt-empty-title">This deck has no cards yet.</p>'
            + '<p class="bt-empty-text">Please choose another deck to continue playing.</p>'
            + '<div class="bt-final-actions">'
            + '<button type="button" class="bt-btn-solid" data-action="menu">Choose another deck</button>'
            + '</div></div>';
    }

    function renderPlay() {
        var deck = getDeck(state.deckKey);
        var html = '';

        html += '<button type="button" class="bt-back-link" aria-label="Return to all decks">&larr; All decks</button>';

        if (!deck) {
            html += '<div class="bt-empty-deck" role="alert">'
                + '<p class="bt-empty-title">Deck not found.</p>'
                + '<button type="button" class="bt-btn-solid" data-action="menu">Back to all decks</button>'
                + '</div>';
            root.innerHTML = html;
            attachPlayHandlers();
            return;
        }

        html += '<div class="bt-header">'
            + '<div class="bt-eyebrow">Reap433 &middot; ' + escapeHtml(deck.title) + '</div>'
            + '<h2 class="bt-title">' + escapeHtml(deck.title) + '</h2>'
            + '<p class="bt-sub">' + escapeHtml(deck.verse) + ' &mdash; ' + escapeHtml(deck.ref) + '</p>'
            + '</div>';

        if (!Array.isArray(deck.cards) || deck.cards.length === 0) {
            html += renderEmptyDeck(deck);
            root.innerHTML = html;
            attachPlayHandlers();
            return;
        }

        var finished = state.pos >= state.order.length;

        html += '<div class="bt-stats" aria-live="polite">'
            + '<span>Card <strong>' + Math.min(state.pos + 1, state.order.length) + '</strong> / ' + state.order.length + '</span>'
            + '<span class="bt-stats-sep" aria-hidden="true">&middot;</span>'
            + '<span>Score <strong>' + state.score + '</strong> / ' + state.answered + '</span>'
            + '</div>';

        var progressPct = ((state.pos + (finished ? 1 : 0)) / state.order.length) * 100;
        html += '<div class="bt-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + Math.round(progressPct) + '" aria-label="Deck progress">'
            + '<div class="bt-progress-fill" style="width:' + progressPct + '%; background:' + escapeHtml(deck.color) + ';"></div>'
            + '</div>';

        if (!finished) {
            var cardIndex = state.order[state.pos];
            var card = deck.cards[cardIndex];

            if (!card) {
                html += '<div class="bt-empty-deck" role="alert">'
                    + '<p class="bt-empty-title">Unable to load this card.</p>'
                    + '<button type="button" class="bt-btn-solid" data-action="menu">Back to all decks</button>'
                    + '</div>';
                root.innerHTML = html;
                attachPlayHandlers();
                return;
            }

            html += '<div class="bt-card">';

            html += '<div class="bt-band" style="background:' + escapeHtml(deck.color) + ';">'
                + '<span class="bt-band-label">' + escapeHtml(deck.title) + '</span>'
                + '<span class="bt-band-desc">Card ' + (state.pos + 1) + ' of ' + state.order.length + '</span>'
                + '</div>';

            html += '<div class="bt-body">';
            html += '<p class="bt-question" id="bt-current-question">' + escapeHtml(card.q) + '</p>';
            html += '<div class="bt-options" role="group" aria-labelledby="bt-current-question">';

            card.options.forEach(function (opt, i) {
                var cls = 'bt-opt';
                var mark = '';
                var letter = String.fromCharCode(65 + i);
                var ariaLabel = 'Option ' + letter + ': ' + opt;

                if (state.revealed) {
                    cls += ' bt-locked';
                    if (i === card.answer) {
                        cls += ' bt-correct';
                        mark = '<span class="bt-mark" aria-hidden="true">&#10003;</span>';
                        ariaLabel += ', correct answer';
                    } else if (i === state.selected) {
                        cls += ' bt-incorrect';
                        mark = '<span class="bt-mark" aria-hidden="true">&#10007;</span>';
                        ariaLabel += ', your answer, incorrect';
                    } else {
                        cls += ' bt-dim';
                    }
                }

                html += '<button type="button" class="' + cls + '" data-idx="' + i + '" '
                    + (state.revealed ? 'disabled aria-disabled="true"' : '')
                    + ' aria-label="' + escapeHtml(ariaLabel) + '">'
                    + '<span class="bt-opt-letter" aria-hidden="true">' + letter + '</span>'
                    + escapeHtml(opt) + mark
                    + '</button>';
            });

            html += '</div>';

            html += '<div class="bt-note' + (state.revealed ? ' bt-show' : '') + '" '
                + (state.revealed ? '' : 'hidden')
                + ' role="note">'
                + '<div class="bt-note-ref" style="color:' + escapeHtml(deck.color) + ';">' + escapeHtml(card.ref) + '</div>'
                + escapeHtml(card.note)
                + '</div>';

            var isLast = state.pos + 1 >= state.order.length;
            html += '<button type="button" class="bt-next-btn' + (state.revealed ? ' bt-active' : '') + '" '
                + (state.revealed ? '' : 'disabled aria-disabled="true"')
                + ' aria-label="' + (isLast ? 'Finish deck' : 'Go to next card') + '">'
                + (isLast ? 'Finish' : 'Next card &rarr;')
                + '</button>';

            html += '</div></div>';
        } else {
            var pct = state.order.length > 0 ? state.score / state.order.length : 0;
            var msg = pct === 1
                ? 'A full harvest &mdash; every seed accounted for.'
                : pct >= 0.7
                    ? 'Good soil. Most of it took root.'
                    : 'Some seed fell on rocky ground &mdash; worth another pass.';

            html += '<div class="bt-final">'
                + '<div class="bt-final-eyebrow">Deck Complete</div>'
                + '<p class="bt-final-score" aria-live="polite">' + state.score + ' / ' + state.order.length + '</p>'
                + '<p class="bt-final-msg">' + msg + '</p>'
                + '<div class="bt-final-actions">'
                + '<button type="button" class="bt-btn-solid" data-action="replay">Play again</button>'
                + '<button type="button" class="bt-btn-outline" data-action="menu">Choose another deck</button>'
                + '</div></div>';
        }

        root.innerHTML = html;
        attachPlayHandlers();
    }

    function attachMenuHandlers() {
        root.querySelectorAll('.bt-deck-tile').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openDeck(this.getAttribute('data-deck'));
            });
        });
    }

    function attachPlayHandlers() {
        var back = root.querySelector('.bt-back-link');
        if (back) {
            back.addEventListener('click', goMenu);
        }

        var deck = getDeck(state.deckKey);

        root.querySelectorAll('.bt-opt').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (state.revealed || !deck) {
                    return;
                }

                var cardIndex = state.order[state.pos];
                var card = deck.cards[cardIndex];

                if (!card) {
                    return;
                }

                state.selected = parseInt(this.getAttribute('data-idx'), 10);
                state.revealed = true;
                state.answered += 1;

                if (state.selected === card.answer) {
                    state.score += 1;
                }

                render();
            });

            btn.addEventListener('keydown', function (event) {
                if (state.revealed) {
                    return;
                }

                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    this.click();
                }
            });
        });

        var nextBtn = root.querySelector('.bt-next-btn');
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (!state.revealed) {
                    return;
                }

                state.pos += 1;
                state.selected = null;
                state.revealed = false;
                render();
            });
        }

        var replayBtn = root.querySelector('[data-action="replay"]');
        if (replayBtn) {
            replayBtn.addEventListener('click', function () {
                openDeck(state.deckKey);
            });
        }

        var menuBtn = root.querySelector('[data-action="menu"]');
        if (menuBtn) {
            menuBtn.addEventListener('click', goMenu);
        }
    }

    render();
}());
