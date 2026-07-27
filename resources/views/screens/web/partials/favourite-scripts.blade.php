<script>
(function () {
    if (window.__reapFavouriteToggleBound) {
        return;
    }
    window.__reapFavouriteToggleBound = true;

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function showFavouriteMessage(message, isError) {
        var existing = document.querySelector('[data-favourite-toast]');
        if (existing) {
            existing.remove();
        }

        var toast = document.createElement('div');
        toast.setAttribute('data-favourite-toast', '1');
        toast.setAttribute('role', isError ? 'alert' : 'status');
        toast.className = 'favourite-toast' + (isError ? ' favourite-toast--error' : '');
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(function () {
            toast.classList.add('is-visible');
        }, 10);

        setTimeout(function () {
            toast.classList.remove('is-visible');
            setTimeout(function () {
                toast.remove();
            }, 250);
        }, 2800);
    }

    function setFavouriteState(btn, isFavourite) {
        btn.classList.toggle('is-favourite', !!isFavourite);
        btn.setAttribute('aria-pressed', isFavourite ? 'true' : 'false');
        var label = isFavourite ? @json(__('Remove from favourites')) : @json(__('Add to favourites'));
        btn.setAttribute('aria-label', label);
        btn.setAttribute('title', label);
    }

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('[data-favourite-toggle]');
        if (!btn || btn.disabled || btn.getAttribute('aria-busy') === 'true') {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        var url = btn.getAttribute('data-toggle-url');
        if (!url) {
            return;
        }

        btn.setAttribute('aria-busy', 'true');
        btn.disabled = true;
        btn.classList.add('is-loading');

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({})
        })
            .then(function (response) {
                if (response.status === 401 || response.status === 419) {
                    window.location.href = @json(route('login'));
                    return null;
                }
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (!result) {
                    return;
                }
                if (!result.ok || !result.data || !result.data.success) {
                    showFavouriteMessage(
                        (result.data && result.data.message) || @json(__('Unable to update favourites. Please try again.')),
                        true
                    );
                    return;
                }
                setFavouriteState(btn, !!result.data.is_favourite);
                showFavouriteMessage(result.data.message || @json(__('Product added to favourites.')), false);
            })
            .catch(function () {
                showFavouriteMessage(@json(__('Unable to update favourites. Please try again.')), true);
            })
            .finally(function () {
                btn.removeAttribute('aria-busy');
                btn.disabled = false;
                btn.classList.remove('is-loading');
            });
    });
})();
</script>
