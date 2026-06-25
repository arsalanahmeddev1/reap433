<header class="nav-header" id="nav-header" role="banner">
    <nav class="nav-container" aria-label="Primary navigation">
      <a href="{{ route('home') }}" class="nav-logo" aria-label="REAP433 Home">
        <img src="{{ asset('assets/web/images/logo.png') }}" alt="REAP433 Logo">
      </a>

      <!-- Mode Toggle -->
      <div class="nav-mode-toggle" role="group" aria-label="Site mode">
        <button class="mode-btn active" id="shop-mode-btn" data-mode="shop" aria-pressed="true">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
          Shop
        </button>
        <button class="mode-btn" id="impact-mode-btn" data-mode="impact" aria-pressed="false">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
          Impact
        </button>
      </div>

      <!-- Desktop Nav -->
      <ul class="nav-links" role="list">
        <li><a href="#shop" class="nav-link shop-nav">Collection</a></li>
        <li><a href="{{ route('biblical-trivia.index') }}" class="nav-link shop-nav">Cards Game</a></li>
        <li><a href="#hub" class="nav-link impact-nav" style="display:none">Civic Hub</a></li>
        <li><a href="#blog" class="nav-link impact-nav" style="display:none">Blog</a></li>
        <li><a href="#about" class="nav-link">Our Story</a></li>
        <li><a href="#contact" class="nav-link">Contact</a></li>
      </ul>

      <div class="nav-actions">
        @auth
          <a href="{{ route('profile.edit') }}" class="nav-auth-link nav-auth-link--user" title="{{ auth()->user()->name }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span class="nav-auth-name">{{ auth()->user()->name }}</span>
          </a>
          <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
            @csrf
            <button type="submit" class="nav-auth-link nav-auth-link--logout">Log out</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="nav-auth-link">Log in</a>
          <a href="{{ route('register') }}" class="btn btn-gold-sm nav-signup-btn">Sign up</a>
        @endauth
        <button
          type="button"
          class="nav-icon-btn nav-search-toggle"
          id="nav-search-toggle"
          aria-label="Open search"
          aria-expanded="false"
          aria-controls="nav-search-panel"
        >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <a href="{{ route('cart.index') }}" class="nav-icon-btn cart-btn" aria-label="Shopping cart, {{ $cartCount }} items">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
          <span class="cart-badge" data-cart-count @if ($cartCount < 1) hidden @endif>{{ $cartCount }}</span>
        </a>
        <button class="nav-hamburger" id="nav-hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </nav>
  </header>

  {{-- Storefront search panel — GET /search queries database via SearchController --}}
  <div class="nav-search-panel" id="nav-search-panel" hidden>
    <div class="container nav-search-inner">
      <form class="nav-search-field" action="{{ route('search.index') }}" method="GET" role="search">
        <span class="nav-search-field-icon" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </span>
        <label for="nav-search-input" class="visually-hidden">Search</label>
        <input
          type="search"
          id="nav-search-input"
          name="q"
          class="nav-search-input"
          placeholder="Search collection…"
          value="{{ request()->routeIs('search.index') ? request('q') : '' }}"
          autocomplete="off"
        />
        <button type="submit" class="btn btn-gold-sm nav-search-submit" aria-label="Search">
          <span class="nav-search-submit-label">Search</span>
          <svg class="nav-search-submit-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
        </button>
      </form>
    </div>
  </div>

  <!-- Mobile Menu Overlay -->
  <div class="mobile-menu" id="mobile-menu" role="dialog" aria-label="Mobile navigation" aria-modal="true" hidden>
    <nav aria-label="Mobile navigation">
      <ul class="mobile-nav-links" role="list">
        <li><a href="#shop" class="mobile-nav-link">Collection</a></li>
        <li><a href="{{ route('biblical-trivia.index') }}" class="mobile-nav-link">Cards Game</a></li>
        <li><a href="#shop" class="mobile-nav-link">New Arrivals</a></li>
        <li><a href="#hub" class="mobile-nav-link">Civic Hub</a></li>
        <li><a href="#blog" class="mobile-nav-link">Blog</a></li>
        <li><a href="{{ route('cart.index') }}" class="mobile-nav-link">Cart ({{ $cartCount }})</a></li>
        <li><a href="#about" class="mobile-nav-link">Our Story</a></li>
        <li><a href="#contact" class="mobile-nav-link">Contact</a></li>
      </ul>
    </nav>
    <div class="mobile-menu-footer">
      <div class="mobile-auth-links">
        @auth
          <a href="{{ route('profile.edit') }}" class="mobile-nav-link">My account</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="mobile-nav-link mobile-logout-btn">Log out</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="mobile-nav-link">Log in</a>
          <a href="{{ route('register') }}" class="btn btn-gold mobile-signup-btn">Sign up</a>
        @endauth
      </div>
      <div class="mobile-mode-toggle">
        <button class="mode-btn active" data-mode="shop">Shop</button>
        <button class="mode-btn" data-mode="impact">Impact</button>
      </div>
    </div>
  </div>