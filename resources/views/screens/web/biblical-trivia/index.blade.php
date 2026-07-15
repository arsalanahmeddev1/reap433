@extends('layouts.web.master')

@section('title', 'Cards Game')

@push('styles')
<style>
    .cards-game-page {
        background: #0B1120;
        min-height: calc(100vh - 80px);
    }
    .cards-game-tabs {
        display: flex;
        gap: 0;
        justify-content: center;
        background: #11192E;
        border-bottom: 1px solid rgba(201, 169, 97, 0.28);
    }
    .cards-game-tab {
        appearance: none;
        border: 0;
        background: transparent;
        color: #EDE7D8;
        font-family: 'Source Sans 3', system-ui, -apple-system, sans-serif;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.04em;
        padding: 14px 28px;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        opacity: 0.7;
    }
    .cards-game-tab:hover { opacity: 1; }
    .cards-game-tab.is-active {
        color: #C9A961;
        border-bottom-color: #C9A961;
        opacity: 1;
    }
    .cards-game-panel { display: none; }
    .cards-game-panel.is-active { display: block; }
    .cards-game-frame {
        display: block;
        width: 100%;
        height: calc(100vh - 140px);
        min-height: 640px;
        border: 0;
        background: #11192E;
    }
</style>
@endpush

@section('content')
<main id="main" class="cards-game-page">
    <div class="cards-game-tabs" role="tablist">
        <button type="button" class="cards-game-tab is-active" role="tab" aria-selected="true" data-panel="pwa-01">Option 1</button>
        <button type="button" class="cards-game-tab" role="tab" aria-selected="false" data-panel="pwa-02">Option 2</button>
    </div>

    <div id="pwa-01" class="cards-game-panel is-active" role="tabpanel">
        <iframe class="cards-game-frame" src="{{ asset('reap-cross-pwa-01/index.html') }}" title="Option 1"></iframe>
    </div>

    <div id="pwa-02" class="cards-game-panel" role="tabpanel" hidden>
        <iframe class="cards-game-frame" src="{{ asset('reap-cross-pwa-02/index.html') }}" title="Option 2" loading="lazy"></iframe>
    </div>
</main>
@endsection

@push('scripts')
<script>
(function () {
    var tabs = document.querySelectorAll('.cards-game-tab');
    var panels = document.querySelectorAll('.cards-game-panel');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var id = tab.getAttribute('data-panel');

            tabs.forEach(function (t) {
                var active = t === tab;
                t.classList.toggle('is-active', active);
                t.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            panels.forEach(function (panel) {
                var active = panel.id === id;
                panel.classList.toggle('is-active', active);
                if (active) {
                    panel.removeAttribute('hidden');
                } else {
                    panel.setAttribute('hidden', '');
                }
            });
        });
    });
}());
</script>
@endpush
