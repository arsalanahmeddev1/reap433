/**
 * Reap433 Bible Trivia — client logic (from Claude artifact)
 * Requires: biblical-trivia-decks.js, #reap433-decks
 */
(function () {
    'use strict';

    var root = document.getElementById('reap433-decks');
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
        revealed: false,
        selected: null,
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
        var a = arr.slice();

        for (var i = a.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = a[i];
            a[i] = a[j];
            a[j] = tmp;
        }

        return a;
    }

    function getDeck(key) {
        if (!key || !DECKS[key]) {
            return null;
        }

        return DECKS[key];
    }

    function openDeck(key) {
        var deck = getDeck(key);

        if (!deck || !Array.isArray(deck.cards) || deck.cards.length === 0) {
            state.screen = 'play';
            state.deckKey = key;
            state.order = [];
            state.pos = 0;
            state.score = 0;
            state.answered = 0;
            state.revealed = false;
            state.selected = null;
            render();
            return;
        }

        state.screen = 'play';
        state.deckKey = key;
        state.order = shuffle(deck.cards.map(function (_, i) {
            return i;
        }));
        state.pos = 0;
        state.score = 0;
        state.answered = 0;
        state.revealed = false;
        state.selected = null;
        render();
    }

    function goMenu() {
        state.screen = 'menu';
        state.deckKey = null;
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

        html += '<div class="header">'
            + '<div class="eyebrow">Reap433 &middot; Foundations</div>'
            + '<h1 class="title" id="biblical-trivia-heading">Choose a Deck</h1>'
            + '<p class="sub">Seven trivia decks covering Faith, Baptism, Tithing, Salvation, Holy Spirit, Spiritual Gifts, and Reap What You Sow &mdash; '
            + 'each pulling questions and verses straight from Scripture.</p>'
            + '</div>';

        html += '<div class="deck-grid" role="list">';

        DECK_ORDER.forEach(function (key) {
            var d = getDeck(key);
            if (!d) {
                return;
            }

            var cardCount = Array.isArray(d.cards) ? d.cards.length : 0;

            html += '<button type="button" class="deck-tile" data-deck="' + escapeHtml(key) + '" '
                + 'role="listitem" aria-label="' + escapeHtml(d.title + ', ' + cardCount + ' cards') + '">'
                + '<div class="deck-bar" style="background:' + escapeHtml(d.color) + ';"></div>'
                + '<div class="deck-body">'
                + '<p class="deck-name">' + escapeHtml(d.title) + '</p>'
                + '<p class="deck-verse">' + escapeHtml(d.verse) + ' &mdash; ' + escapeHtml(d.ref) + '</p>'
                + '<div class="deck-meta">' + cardCount + ' cards &middot; ' + escapeHtml(d.desc) + '</div>'
                + '</div></button>';
        });

        html += '</div>';

        root.innerHTML = html;

        root.querySelectorAll('.deck-tile').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openDeck(this.getAttribute('data-deck'));
            });
        });
    }

    function renderPlay() {
        var deck = getDeck(state.deckKey);
        var html = '';

        html += '<button type="button" class="back-link" aria-label="Return to all decks">&larr; All decks</button>';

        if (!deck || !Array.isArray(deck.cards) || deck.cards.length === 0) {
            html += '<div class="final" role="alert">'
                + '<p class="final-msg">This deck has no cards yet. Please choose another deck.</p>'
                + '<div class="final-actions">'
                + '<button type="button" class="btn-solid" data-action="menu">Choose another deck</button>'
                + '</div></div>';
            root.innerHTML = html;
            attachPlayHandlers();
            return;
        }

        var finished = state.pos >= state.order.length;

        html += '<div class="header">'
            + '<div class="eyebrow">Reap433 &middot; ' + escapeHtml(deck.title) + '</div>'
            + '<h2 class="title">' + escapeHtml(deck.title) + '</h2>'
            + '<p class="sub">' + escapeHtml(deck.verse) + ' &mdash; ' + escapeHtml(deck.ref) + '</p>'
            + '</div>';

        html += '<div class="stats" aria-live="polite">'
            + '<span>Card <strong>' + Math.min(state.pos + 1, state.order.length) + '</strong> / ' + state.order.length + '</span>'
            + '<span>&middot;</span>'
            + '<span>Score <strong>' + state.score + '</strong> / ' + state.answered + '</span>'
            + '</div>';

        var progressPct = ((state.pos + (finished ? 1 : 0)) / state.order.length) * 100;
        html += '<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + Math.round(progressPct) + '">'
            + '<div class="progress-fill" style="width:' + progressPct + '%; background:' + escapeHtml(deck.color) + ';"></div>'
            + '</div>';

        if (!finished) {
            var card = deck.cards[state.order[state.pos]];

            if (!card) {
                html += '<div class="final" role="alert">'
                    + '<p class="final-msg">Unable to load this card.</p>'
                    + '<button type="button" class="btn-solid" data-action="menu">Back to all decks</button>'
                    + '</div>';
                root.innerHTML = html;
                attachPlayHandlers();
                return;
            }

            html += '<div class="card">';
            html += '<div class="band" style="background:' + escapeHtml(deck.color) + ';">'
                + '<span class="band-label">' + escapeHtml(deck.title) + '</span>'
                + '<span class="band-desc">Card ' + (state.pos + 1) + ' of ' + state.order.length + '</span>'
                + '</div>';
            html += '<div class="body">';
            html += '<p class="question" id="bt-current-question">' + escapeHtml(card.q) + '</p>';
            html += '<div class="options" role="group" aria-labelledby="bt-current-question">';

            card.options.forEach(function (opt, i) {
                var cls = 'opt';
                var mark = '';

                if (state.revealed) {
                    cls += ' locked';
                    if (i === card.answer) {
                        cls += ' correct';
                        mark = '<span class="mark" aria-hidden="true">&#10003;</span>';
                    } else if (i === state.selected) {
                        cls += ' incorrect';
                        mark = '<span class="mark" aria-hidden="true">&#10007;</span>';
                    } else {
                        cls += ' dim';
                    }
                }

                html += '<button type="button" class="' + cls + '" data-idx="' + i + '" '
                    + (state.revealed ? 'disabled aria-disabled="true"' : '')
                    + ' aria-label="Option ' + String.fromCharCode(65 + i) + ': ' + escapeHtml(opt) + '">'
                    + '<span class="opt-letter" aria-hidden="true">' + String.fromCharCode(65 + i) + '</span>'
                    + escapeHtml(opt) + mark
                    + '</button>';
            });

            html += '</div>';

            html += '<div class="note' + (state.revealed ? ' show' : '') + '" '
                + (state.revealed ? '' : 'hidden')
                + ' role="note">'
                + '<div class="note-ref" style="color:' + escapeHtml(deck.color) + ';">' + escapeHtml(card.ref) + '</div>'
                + escapeHtml(card.note)
                + '</div>';

            html += '<button type="button" class="next-btn' + (state.revealed ? ' active' : '') + '" '
                + (state.revealed ? '' : 'disabled aria-disabled="true"')
                + ' aria-label="' + (state.pos + 1 >= state.order.length ? 'Finish deck' : 'Go to next card') + '">'
                + (state.pos + 1 >= state.order.length ? 'Finish' : 'Next card &rarr;')
                + '</button>';

            html += '</div></div>';
        } else {
            var pct = state.order.length > 0 ? state.score / state.order.length : 0;
            var msg = pct === 1
                ? 'A full harvest &mdash; every seed accounted for.'
                : pct >= 0.7
                    ? 'Good soil. Most of it took root.'
                    : 'Some seed fell on rocky ground &mdash; worth another pass.';

            html += '<div class="final">'
                + '<div class="final-eyebrow">Deck Complete</div>'
                + '<p class="final-score" aria-live="polite">' + state.score + ' / ' + state.order.length + '</p>'
                + '<p class="final-msg">' + msg + '</p>'
                + '<div class="final-actions">'
                + '<button type="button" class="btn-solid" data-action="replay">Play again</button>'
                + '<button type="button" class="btn-outline" data-action="menu">Choose another deck</button>'
                + '</div></div>';
        }

        root.innerHTML = html;
        attachPlayHandlers();
    }

    function attachPlayHandlers() {
        var back = root.querySelector('.back-link');
        if (back) {
            back.addEventListener('click', goMenu);
        }

        var deck = getDeck(state.deckKey);

        root.querySelectorAll('.opt').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (state.revealed || !deck) {
                    return;
                }

                var card = deck.cards[state.order[state.pos]];
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

        var nextBtn = root.querySelector('.next-btn');
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
