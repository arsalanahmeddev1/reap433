@extends('layouts.web.master')
@section('title', 'Home')
@section('content')
<main id="main">

    <!-- ═══ HERO SECTION -->
    <section class="hero" id="hero" aria-labelledby="hero-headline">
      <div class="hero-bg">
        <div class="hero-gradient-mesh" aria-hidden="true"></div>
        <div class="hero-noise" aria-hidden="true"></div>
        <img
          src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1600&q=85&auto=format&fit=crop"
          alt="Confident man wearing REAP433 premium hoodie, Grand Prairie Texas"
          class="hero-image"
          loading="eager"
          fetchpriority="high"
        />
        <div class="hero-overlay" aria-hidden="true"></div>
      </div>

      <div class="hero-content container">
        <div class="hero-text">
          <span class="hero-eyebrow" data-animate="eyebrow">
            <span class="eyebrow-line"></span>
            Grand Prairie, Texas · Est. 2024
          </span>
          <h1 class="hero-headline" id="hero-headline" data-animate="headline">
            <span class="headline-line">Wear the</span>
            <span class="headline-line accent">Legacy.</span>
            <span class="headline-line">Lead the</span>
            <span class="headline-line accent">Change.</span>
          </h1>
          <p class="hero-sub" data-animate="sub">
            REAP433 is where commerce meets purpose. Premium trademarked streetwear 
            built for those who carry the weight of a movement — and dress like it.
          </p>
          <div class="hero-ctas" data-animate="ctas">
            <a href="#shop" class="btn btn-gold">
              Shop the Collection
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
            </a>
            <a href="#hub" class="btn btn-outline">
              Explore Civic Hub
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
            </a>
          </div>
        </div>

        <div class="hero-badge-group" data-animate="badge" aria-hidden="true">
          <div class="hero-infinity-badge">
            <svg class="infinity-hero" viewBox="0 0 120 60" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M30 30C30 16.745 21.255 8 8 8S0 16.745 0 30s8.745 22 22 22c8.8 0 16.4-5.2 20-13C45.6 46.8 53.2 52 62 52c13.255 0 22-8.745 22-22S75.255 8 62 8c-8.8 0-16.4 5.2-20 13C38.4 13.2 30.8 8 22 8" stroke="#C9A227" stroke-width="4" stroke-linecap="round" fill="none"/>
              <path d="M58 30C54 21 47 16 40 16s-16 7-16 14 7 14 16 14 16-7 20-15c4 8 11 15 20 15s16-7 16-14-7-14-16-14-16 7-20 14" stroke="#C9A227" stroke-width="2.5" stroke-linecap="round" fill="none" opacity="0.45"/>
            </svg>
            <span class="badge-label">Infinite Impact™</span>
          </div>
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-number">2,400+</span>
              <span class="stat-label">Community Members</span>
            </div>
            <div class="stat-divider" aria-hidden="true"></div>
            <div class="stat-item">
              <span class="stat-number">TX #1</span>
              <span class="stat-label">Civic Brand</span>
            </div>
            <div class="stat-divider" aria-hidden="true"></div>
            <div class="stat-item">
              <span class="stat-number">100%</span>
              <span class="stat-label">Trademarked</span>
            </div>
          </div>
        </div>
      </div>

      <div class="hero-scroll-cue" aria-hidden="true">
        <span>Scroll</span>
        <div class="scroll-line"></div>
      </div>
    </section>

    <!-- ═══ MARQUEE STRIP -->
    <section class="marquee-strip" aria-label="Brand values" aria-hidden="true">
      <div class="marquee-track">
        <div class="marquee-content">
          <span>Infinite Impact™</span>
          <span class="marquee-dot">✦</span>
          <span>Grand Prairie, TX</span>
          <span class="marquee-dot">✦</span>
          <span>Wear the Legacy</span>
          <span class="marquee-dot">✦</span>
          <span>Lead the Change</span>
          <span class="marquee-dot">✦</span>
          <span>Voting Rights</span>
          <span class="marquee-dot">✦</span>
          <span>Education Reform</span>
          <span class="marquee-dot">✦</span>
          <span>Premium Streetwear</span>
          <span class="marquee-dot">✦</span>
          <span>REAP433™</span>
          <span class="marquee-dot">✦</span>
          <span>Infinite Impact™</span>
          <span class="marquee-dot">✦</span>
          <span>Grand Prairie, TX</span>
          <span class="marquee-dot">✦</span>
          <span>Wear the Legacy</span>
          <span class="marquee-dot">✦</span>
          <span>Lead the Change</span>
          <span class="marquee-dot">✦</span>
          <span>Voting Rights</span>
          <span class="marquee-dot">✦</span>
          <span>Education Reform</span>
          <span class="marquee-dot">✦</span>
          <span>Premium Streetwear</span>
          <span class="marquee-dot">✦</span>
          <span>REAP433™</span>
          <span class="marquee-dot">✦</span>
        </div>
      </div>
    </section>

    <!-- ═══ BRAND STATEMENT -->
    <section class="brand-statement section-pad" aria-labelledby="brand-stmt-title">
      <div class="container">
        <div class="brand-stmt-grid">
          <div class="brand-stmt-left" data-scroll-reveal>
            <span class="section-eyebrow">The Philosophy</span>
            <h2 class="brand-stmt-title" id="brand-stmt-title">
              More Than a Brand.<br />
              <em>A Movement.</em>
            </h2>
          </div>
          <div class="brand-stmt-right" data-scroll-reveal data-delay="100">
            <p class="brand-stmt-body">
              REAP433 was born at the intersection of culture, commerce, and civic duty. 
              Our infinity mark isn't just a logo — it's a covenant. Every piece you wear 
              carries the weight of a community demanding to be seen, heard, and represented.
            </p>
            <p class="brand-stmt-body">
              From the streets of Grand Prairie to the halls of civic power, REAP433 
              dresses leaders. Bold enough to start conversations. Strong enough to end injustice.
            </p>
            <a href="#about" class="btn btn-text">
              Our Full Story
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
            </a>
          </div>
        </div>

        <div class="brand-pillars" role="list" aria-label="Brand pillars">
          <div class="pillar-card" role="listitem" data-scroll-reveal data-delay="0">
            <div class="pillar-icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#C9A227" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <h3 class="pillar-title">Premium Quality</h3>
            <p class="pillar-desc">Every garment is a statement — premium materials, trademarked design, built to last a legacy.</p>
          </div>
          <div class="pillar-card" role="listitem" data-scroll-reveal data-delay="120">
            <div class="pillar-icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#C9A227" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
            </div>
            <h3 class="pillar-title">Civic Power</h3>
            <p class="pillar-desc">We fund voting rights campaigns, education reform initiatives, and community leadership programs.</p>
          </div>
          <div class="pillar-card" role="listitem" data-scroll-reveal data-delay="240">
            <div class="pillar-icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#C9A227" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <h3 class="pillar-title">Community First</h3>
            <p class="pillar-desc">Grand Prairie built us. Our community is our board, our compass, and our north star.</p>
          </div>
          <div class="pillar-card" role="listitem" data-scroll-reveal data-delay="360">
            <div class="pillar-icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#C9A227" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3 class="pillar-title">Trademarked Legacy</h3>
            <p class="pillar-desc">The REAP433 mark is federally protected. When you wear it, you carry authentic, protected culture.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ SHOP SECTION -->
    <section class="shop-section section-pad" id="shop" aria-labelledby="shop-title">
      <div class="container">
        <div class="section-header" data-scroll-reveal>
          <span class="section-eyebrow">The Collection</span>
          <h2 class="section-title" id="shop-title">Premium Trademarked<br />Merchandise</h2>
          <p class="section-sub">Each piece is a wearable declaration of identity, purpose, and pride.</p>
        </div>

        <!-- Product Filters -->
        <div class="product-filters" role="group" aria-label="Filter products">
          <button class="filter-btn active" data-filter="all" aria-pressed="true">All Pieces</button>
          <button class="filter-btn" data-filter="tops" aria-pressed="false">Tops</button>
          <button class="filter-btn" data-filter="headwear" aria-pressed="false">Headwear</button>
          <button class="filter-btn" data-filter="footwear" aria-pressed="false">Footwear</button>
          <button class="filter-btn" data-filter="limited" aria-pressed="false">Limited Edition</button>
        </div>

        <!-- Product Grid -->
        <div class="product-grid" role="list" aria-label="Products">

          <!-- Product 1 -->
          <article class="product-card" role="listitem" data-category="tops limited" data-scroll-reveal data-delay="0">
            <div class="product-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?w=600&q=80&auto=format&fit=crop"
                alt="REAP433 Infinity Hoodie in Charcoal Black"
                class="product-image"
                loading="lazy"
              />
              <div class="product-overlay" aria-hidden="true">
                <button class="product-quick-view" aria-label="Quick view Infinity Hoodie">Quick View</button>
              </div>
              <span class="product-badge badge-limited">Limited</span>
            </div>
            <div class="product-info">
              <div class="product-meta">
                <span class="product-category">Hoodies</span>
                <div class="product-rating" aria-label="4.9 out of 5 stars">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="#C9A227" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                  <span>4.9</span>
                </div>
              </div>
              <h3 class="product-name">Infinity Signature Hoodie</h3>
              <p class="product-desc">Premium heavyweight fleece, embroidered infinity mark, oversized silhouette.</p>
              <div class="product-footer">
                <span class="product-price">$89.00</span>
                <button class="btn btn-gold-sm" aria-label="Add Infinity Signature Hoodie to cart">
                  Add to Cart
                </button>
              </div>
            </div>
          </article>

          <!-- Product 2 -->
          <article class="product-card" role="listitem" data-category="headwear" data-scroll-reveal data-delay="80">
            <div class="product-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=600&q=80&auto=format&fit=crop"
                alt="REAP433 Legacy Cap in Black and Gold"
                class="product-image"
                loading="lazy"
              />
              <div class="product-overlay" aria-hidden="true">
                <button class="product-quick-view" aria-label="Quick view Legacy Cap">Quick View</button>
              </div>
              <span class="product-badge badge-new">New</span>
            </div>
            <div class="product-info">
              <div class="product-meta">
                <span class="product-category">Headwear</span>
                <div class="product-rating" aria-label="4.8 out of 5 stars">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="#C9A227" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                  <span>4.8</span>
                </div>
              </div>
              <h3 class="product-name">Legacy Structured Cap</h3>
              <p class="product-desc">6-panel structured fit, gold embroidered R433 mark, premium wool blend.</p>
              <div class="product-footer">
                <span class="product-price">$48.00</span>
                <button class="btn btn-gold-sm" aria-label="Add Legacy Structured Cap to cart">
                  Add to Cart
                </button>
              </div>
            </div>
          </article>

          <!-- Product 3 -->
          <article class="product-card" role="listitem" data-category="tops" data-scroll-reveal data-delay="160">
            <div class="product-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=600&q=80&auto=format&fit=crop"
                alt="REAP433 Civic Statement Tee in Off-White"
                class="product-image"
                loading="lazy"
              />
              <div class="product-overlay" aria-hidden="true">
                <button class="product-quick-view" aria-label="Quick view Civic Statement Tee">Quick View</button>
              </div>
            </div>
            <div class="product-info">
              <div class="product-meta">
                <span class="product-category">T-Shirts</span>
                <div class="product-rating" aria-label="5.0 out of 5 stars">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="#C9A227" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                  <span>5.0</span>
                </div>
              </div>
              <h3 class="product-name">Civic Statement Tee</h3>
              <p class="product-desc">400gsm pima cotton, screen-printed infinity mark, relaxed premium fit.</p>
              <div class="product-footer">
                <span class="product-price">$52.00</span>
                <button class="btn btn-gold-sm" aria-label="Add Civic Statement Tee to cart">
                  Add to Cart
                </button>
              </div>
            </div>
          </article>

          <!-- Product 4 -->
          <article class="product-card" role="listitem" data-category="footwear limited" data-scroll-reveal data-delay="240">
            <div class="product-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80&auto=format&fit=crop"
                alt="REAP433 Infinity Runner Shoes"
                class="product-image"
                loading="lazy"
              />
              <div class="product-overlay" aria-hidden="true">
                <button class="product-quick-view" aria-label="Quick view Infinity Runner">Quick View</button>
              </div>
              <span class="product-badge badge-limited">Drops Soon</span>
            </div>
            <div class="product-info">
              <div class="product-meta">
                <span class="product-category">Footwear</span>
                <div class="product-rating" aria-label="4.9 out of 5 stars">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="#C9A227" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                  <span>4.9</span>
                </div>
              </div>
              <h3 class="product-name">Infinity Runner</h3>
              <p class="product-desc">Premium collab sneaker, hand-finished gold R433 accent, limited 433 pairs.</p>
              <div class="product-footer">
                <span class="product-price">$165.00</span>
                <button class="btn btn-outline-sm" aria-label="Notify me when Infinity Runner drops">
                  Notify Me
                </button>
              </div>
            </div>
          </article>

          <!-- Product 5 -->
          <article class="product-card" role="listitem" data-category="tops" data-scroll-reveal data-delay="0">
            <div class="product-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600&q=80&auto=format&fit=crop"
                alt="REAP433 Movement Crew Sweater"
                class="product-image"
                loading="lazy"
              />
              <div class="product-overlay" aria-hidden="true">
                <button class="product-quick-view" aria-label="Quick view Movement Crew Sweater">Quick View</button>
              </div>
              <span class="product-badge badge-best">Bestseller</span>
            </div>
            <div class="product-info">
              <div class="product-meta">
                <span class="product-category">Sweaters</span>
                <div class="product-rating" aria-label="4.9 out of 5 stars">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="#C9A227" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                  <span>4.9</span>
                </div>
              </div>
              <h3 class="product-name">Movement Crew Sweater</h3>
              <p class="product-desc">French terry crew neck, tone-on-tone jacquard infinity weave, relaxed premium cut.</p>
              <div class="product-footer">
                <span class="product-price">$74.00</span>
                <button class="btn btn-gold-sm" aria-label="Add Movement Crew Sweater to cart">
                  Add to Cart
                </button>
              </div>
            </div>
          </article>

          <!-- Product 6 -->
          <article class="product-card" role="listitem" data-category="headwear limited" data-scroll-reveal data-delay="80">
            <div class="product-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?w=600&q=80&auto=format&fit=crop"
                alt="REAP433 Gold Label Snapback"
                class="product-image"
                loading="lazy"
              />
              <div class="product-overlay" aria-hidden="true">
                <button class="product-quick-view" aria-label="Quick view Gold Label Snapback">Quick View</button>
              </div>
            </div>
            <div class="product-info">
              <div class="product-meta">
                <span class="product-category">Headwear</span>
                <div class="product-rating" aria-label="4.7 out of 5 stars">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="#C9A227" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                  <span>4.7</span>
                </div>
              </div>
              <h3 class="product-name">Gold Label Snapback</h3>
              <p class="product-desc">Flat-brim snapback with metallic gold embroidered logo, premium woven tag.</p>
              <div class="product-footer">
                <span class="product-price">$54.00</span>
                <button class="btn btn-gold-sm" aria-label="Add Gold Label Snapback to cart">
                  Add to Cart
                </button>
              </div>
            </div>
          </article>

        </div>

        <div class="shop-cta-row" data-scroll-reveal>
          <a href="#shop" class="btn btn-gold">
            View Full Collection
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
          </a>
        </div>
      </div>
    </section>

    <!-- ═══ IMPACT DIVIDER -->
    <section class="impact-divider" aria-hidden="true">
      <div class="divider-content">
        <div class="divider-infinity">
          <svg viewBox="0 0 200 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="divider-svg">
            <path d="M50 40C50 22.327 35.673 8 18 8S0 22.327 0 40s14.327 32 36 32c14.4 0 26.8-8 32-20C73.2 64 85.6 72 100 72c21.673 0 36-14.327 36-32S121.673 8 100 8c-14.4 0-26.8 8-32 20C62.8 16 50.4 8 36 8" stroke="#C9A227" stroke-width="3" stroke-linecap="round"/>
            <path d="M96 40C90 28 80 22 70 22s-24 10-24 18 10 18 24 18 24-10 30-20c6 10 16 20 30 20s24-10 24-18-10-18-24-18-24 10-30 18" stroke="#C9A227" stroke-width="2" fill="none" opacity="0.4"/>
          </svg>
        </div>
        <p class="divider-text">"Every dollar you spend with REAP433 funds a community that refuses to be ignored."</p>
      </div>
    </section>

    <!-- ═══ CIVIC HUB SECTION -->
    <section class="civic-hub section-pad" id="hub" aria-labelledby="hub-title">
      <div class="container">
        <div class="section-header" data-scroll-reveal>
          <span class="section-eyebrow">The REAP433 Civic Hub</span>
          <h2 class="section-title" id="hub-title">Informed Citizens.<br />Empowered Communities.</h2>
          <p class="section-sub">Stay current on voting rights, education reform, and civic leadership across Texas and beyond.</p>
        </div>

        <!-- Hub Categories -->
        <div class="hub-categories" role="tablist" aria-label="Blog categories">
          <button class="hub-cat active" role="tab" aria-selected="true" data-cat="all">All Articles</button>
          <button class="hub-cat" role="tab" aria-selected="false" data-cat="voting">Voting Rights</button>
          <button class="hub-cat" role="tab" aria-selected="false" data-cat="education">Education Reform</button>
          <button class="hub-cat" role="tab" aria-selected="false" data-cat="community">Community Leadership</button>
          <button class="hub-cat" role="tab" aria-selected="false" data-cat="policy">Policy Watch</button>
        </div>

        <!-- Blog Grid -->
        <div class="blog-grid" id="blog" role="list" aria-label="Blog articles">

          <!-- Featured Article -->
          <article class="blog-card blog-featured" role="listitem" data-cat="voting" data-scroll-reveal data-delay="0">
            <div class="blog-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?w=900&q=80&auto=format&fit=crop"
                alt="Voting booth with American flag, representing voting rights advocacy"
                class="blog-image"
                loading="lazy"
              />
              <span class="blog-category-badge">Voting Rights</span>
            </div>
            <div class="blog-content">
              <div class="blog-meta">
                <span class="blog-author">
                  <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&q=80&auto=format&fit=crop&crop=face" alt="Author Marcus J. Williams" class="author-avatar" />
                  Marcus J. Williams
                </span>
                <time class="blog-date" datetime="2024-11-15">Nov 15, 2024</time>
                <span class="blog-read-time">8 min read</span>
              </div>
              <h3 class="blog-title">Texas Voter Suppression Laws: What Every Grand Prairie Resident Needs to Know Before 2025</h3>
              <p class="blog-excerpt">A deep-dive analysis of SB1 and its compounding effects on Black and Brown communities across Dallas County — and the grassroots organizations fighting back on every front.</p>
              <a href="#" class="blog-read-more">
                Read Full Article
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
              </a>
            </div>
          </article>

          <!-- Standard Articles -->
          <article class="blog-card blog-standard" role="listitem" data-cat="education" data-scroll-reveal data-delay="80">
            <div class="blog-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=600&q=80&auto=format&fit=crop"
                alt="Students in classroom representing education reform"
                class="blog-image"
                loading="lazy"
              />
              <span class="blog-category-badge cat-education">Education</span>
            </div>
            <div class="blog-content">
              <div class="blog-meta">
                <time class="blog-date" datetime="2024-11-10">Nov 10, 2024</time>
                <span class="blog-read-time">5 min read</span>
              </div>
              <h3 class="blog-title">Defunding Public Schools: How GISD and Grand Prairie ISD Are Responding</h3>
              <p class="blog-excerpt">An honest look at budget cuts hitting the Grand Prairie Independent School District and the parent coalitions mobilizing to push back.</p>
              <a href="#" class="blog-read-more">
                Read More
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
              </a>
            </div>
          </article>

          <article class="blog-card blog-standard" role="listitem" data-cat="community" data-scroll-reveal data-delay="160">
            <div class="blog-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=600&q=80&auto=format&fit=crop"
                alt="Community members gathered for a civic meeting"
                class="blog-image"
                loading="lazy"
              />
              <span class="blog-category-badge cat-community">Community</span>
            </div>
            <div class="blog-content">
              <div class="blog-meta">
                <time class="blog-date" datetime="2024-11-05">Nov 5, 2024</time>
                <span class="blog-read-time">6 min read</span>
              </div>
              <h3 class="blog-title">Rising Leaders: 10 Grand Prairie Changemakers Under 35 You Need to Know</h3>
              <p class="blog-excerpt">From school board members to nonprofit founders — meet the next generation of civic leaders reshaping the DFW landscape from the ground up.</p>
              <a href="#" class="blog-read-more">
                Read More
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
              </a>
            </div>
          </article>

          <article class="blog-card blog-standard" role="listitem" data-cat="policy" data-scroll-reveal data-delay="240">
            <div class="blog-image-wrap">
              <img
                src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&q=80&auto=format&fit=crop"
                alt="Policy documents and gavel representing policy watch"
                class="blog-image"
                loading="lazy"
              />
              <span class="blog-category-badge cat-policy">Policy Watch</span>
            </div>
            <div class="blog-content">
              <div class="blog-meta">
                <time class="blog-date" datetime="2024-10-28">Oct 28, 2024</time>
                <span class="blog-read-time">4 min read</span>
              </div>
              <h3 class="blog-title">Breaking: Texas Legislature's New Education Voucher Bill — Full Analysis</h3>
              <p class="blog-excerpt">What HB174 means for public education funding in Texas, which communities it targets, and how to make your voice heard before it passes.</p>
              <a href="#" class="blog-read-more">
                Read More
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
              </a>
            </div>
          </article>

        </div>

        <div class="hub-cta-row" data-scroll-reveal>
          <a href="#blog" class="btn btn-outline">
            Read All Articles
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
          </a>
        </div>
      </div>
    </section>

    <!-- ═══ COMMENTS & FEEDBACK SECTION -->
    <section class="feedback-section section-pad" id="contact" aria-labelledby="feedback-title">
      <div class="container">
        <div class="feedback-grid">

          <!-- Comment Feed -->
          <div class="comments-panel" data-scroll-reveal>
            <div class="panel-header">
              <h2 class="panel-title" id="feedback-title">Community Voices</h2>
              <span class="comment-count" aria-live="polite">47 Comments</span>
            </div>

            <div class="comments-list" role="list" aria-label="Community comments">

              <div class="comment-item" role="listitem">
                <img src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=48&h=48&q=80&auto=format&fit=crop&crop=face" alt="Profile photo of Darius T." class="comment-avatar" />
                <div class="comment-body">
                  <div class="comment-meta">
                    <strong class="comment-author">Darius T.</strong>
                    <span class="comment-tag tag-community">Grand Prairie</span>
                    <time class="comment-time" datetime="2024-11-14">2 days ago</time>
                  </div>
                  <p class="comment-text">The Infinity Hoodie is next level. I wore it to the city council meeting and people kept asking where I got it. Proud to rep REAP433 while fighting for our community.</p>
                  <div class="comment-actions">
                    <button class="comment-like" aria-label="Like this comment, currently 24 likes">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14z"/><path d="M7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>
                      <span>24</span>
                    </button>
                    <button class="comment-reply-btn" aria-label="Reply to Darius T.">Reply</button>
                  </div>
                </div>
              </div>

              <div class="comment-item" role="listitem">
                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=48&h=48&q=80&auto=format&fit=crop&crop=face" alt="Profile photo of Kevin A." class="comment-avatar" />
                <div class="comment-body">
                  <div class="comment-meta">
                    <strong class="comment-author">Kevin A.</strong>
                    <span class="comment-tag tag-civic">Civic Leader</span>
                    <time class="comment-time" datetime="2024-11-13">3 days ago</time>
                  </div>
                  <p class="comment-text">The article on SB1 is the most thorough breakdown I've seen from a local source. Share this everywhere. Our people need to understand what's happening before 2025.</p>
                  <div class="comment-actions">
                    <button class="comment-like" aria-label="Like this comment, currently 41 likes">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14z"/><path d="M7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>
                      <span>41</span>
                    </button>
                    <button class="comment-reply-btn" aria-label="Reply to Kevin A.">Reply</button>
                  </div>
                </div>
              </div>

              <div class="comment-item" role="listitem">
                <img src="https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?w=48&h=48&q=80&auto=format&fit=crop&crop=face" alt="Profile photo of Simone R." class="comment-avatar" />
                <div class="comment-body">
                  <div class="comment-meta">
                    <strong class="comment-author">Simone R.</strong>
                    <span class="comment-tag tag-educator">Educator</span>
                    <time class="comment-time" datetime="2024-11-12">4 days ago</time>
                  </div>
                  <p class="comment-text">As a GPISD teacher, the education voucher analysis hit home. We're losing 3 veteran teachers at our school alone next semester. REAP433 is one of the few brands that actually shows up for us.</p>
                  <div class="comment-actions">
                    <button class="comment-like" aria-label="Like this comment, currently 38 likes">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14z"/><path d="M7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>
                      <span>38</span>
                    </button>
                    <button class="comment-reply-btn" aria-label="Reply to Simone R.">Reply</button>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Feedback Form -->
          <div class="feedback-form-panel" data-scroll-reveal data-delay="150">
            <div class="panel-header">
              <h2 class="panel-title">Leave Your Voice</h2>
              <p class="panel-sub">Join the conversation. Share your story, feedback, or civic question.</p>
            </div>

            <form class="feedback-form" id="feedback-form" novalidate aria-label="Feedback and comment form">
              <div class="form-row">
                <div class="form-group">
                  <label for="f-name" class="form-label">Full Name <span aria-hidden="true" class="required-mark">*</span></label>
                  <input
                    type="text"
                    id="f-name"
                    name="name"
                    class="form-input"
                    placeholder="Your full name"
                    autocomplete="name"
                    required
                    aria-required="true"
                    aria-describedby="f-name-error"
                  />
                  <span class="form-error" id="f-name-error" role="alert" aria-live="polite"></span>
                </div>
                <div class="form-group">
                  <label for="f-email" class="form-label">Email Address <span aria-hidden="true" class="required-mark">*</span></label>
                  <input
                    type="email"
                    id="f-email"
                    name="email"
                    class="form-input"
                    placeholder="your@email.com"
                    autocomplete="email"
                    required
                    aria-required="true"
                    aria-describedby="f-email-error"
                  />
                  <span class="form-error" id="f-email-error" role="alert" aria-live="polite"></span>
                </div>
              </div>

              <div class="form-group">
                <label for="f-city" class="form-label">City / Region</label>
                <input
                  type="text"
                  id="f-city"
                  name="city"
                  class="form-input"
                  placeholder="Grand Prairie, TX"
                  autocomplete="address-level2"
                />
              </div>

              <div class="form-group">
                <label for="f-topic" class="form-label">Topic</label>
                <select id="f-topic" name="topic" class="form-select" aria-label="Select topic">
                  <option value="">Choose a topic…</option>
                  <option value="voting">Voting Rights</option>
                  <option value="education">Education Reform</option>
                  <option value="community">Community Leadership</option>
                  <option value="merchandise">Merchandise Feedback</option>
                  <option value="partnership">Partnership Inquiry</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div class="form-group">
                <label for="f-message" class="form-label">Your Message <span aria-hidden="true" class="required-mark">*</span></label>
                <textarea
                  id="f-message"
                  name="message"
                  class="form-textarea"
                  placeholder="Share your voice, story, or question…"
                  rows="5"
                  required
                  aria-required="true"
                  aria-describedby="f-message-error f-char-count"
                ></textarea>
                <div class="textarea-footer">
                  <span class="form-error" id="f-message-error" role="alert" aria-live="polite"></span>
                  <span class="char-count" id="f-char-count" aria-live="polite">0 / 500</span>
                </div>
              </div>

              <div class="form-checkbox-group">
                <label class="checkbox-label" for="f-newsletter">
                  <input type="checkbox" id="f-newsletter" name="newsletter" class="checkbox-input" />
                  <span class="checkbox-custom" aria-hidden="true"></span>
                  Subscribe to the REAP433 Civic Newsletter
                </label>
              </div>

              <button type="submit" class="btn btn-gold btn-full " id="submit-btn">
                <span class="btn-text text-white">Submit Your Voice</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22,2 15,22 11,13 2,9"/></svg>
              </button>

              <div class="form-success" id="form-success" role="alert" aria-live="polite" hidden>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.5" aria-hidden="true"><polyline points="20,6 9,17 4,12"/></svg>
                Your voice has been received. Thank you for being part of the movement.
              </div>
            </form>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══ STATS SECTION -->
    <section class="stats-section section-pad" aria-labelledby="stats-title">
      <div class="container">
        <h2 class="visually-hidden" id="stats-title">REAP433 Impact Numbers</h2>
        <div class="stats-grid" role="list" aria-label="Impact statistics">
          <div class="stat-card" role="listitem" data-scroll-reveal data-delay="0">
            <div class="stat-card-icon" aria-hidden="true">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#C9A227" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <span class="stat-card-number" data-count="2400">0</span><span class="stat-card-suffix">+</span>
            <span class="stat-card-label">Community Members</span>
          </div>
          <div class="stat-card" role="listitem" data-scroll-reveal data-delay="100">
            <div class="stat-card-icon" aria-hidden="true">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#C9A227" stroke-width="1.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="stat-card-number" data-count="1250">0</span><span class="stat-card-suffix">+</span>
            <span class="stat-card-label">Voters Registered</span>
          </div>
          <div class="stat-card" role="listitem" data-scroll-reveal data-delay="200">
            <div class="stat-card-icon" aria-hidden="true">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#C9A227" stroke-width="1.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            </div>
            <span class="stat-card-number" data-count="84">0</span><span class="stat-card-suffix"></span>
            <span class="stat-card-label">Articles Published</span>
          </div>
          <div class="stat-card" role="listitem" data-scroll-reveal data-delay="300">
            <div class="stat-card-icon" aria-hidden="true">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#C9A227" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <span class="stat-card-number" data-count="100">0</span><span class="stat-card-suffix">%</span>
            <span class="stat-card-label">Trademarked & Protected</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ CTA SECTION -->
    <section class="cta-section" aria-labelledby="cta-title">
      <div class="cta-bg" aria-hidden="true">
        <div class="cta-gradient" aria-hidden="true"></div>
        <div class="cta-noise" aria-hidden="true"></div>
      </div>
      <div class="container cta-content">
        <div class="cta-infinity" aria-hidden="true">
          <svg viewBox="0 0 160 70" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M40 35C40 19.536 28.464 8 13 8S0 19.536 0 35s11.536 27 28 27c11.2 0 20.8-6.4 25.6-16 4.8 9.6 14.4 16 25.6 16 16.464 0 28-11.536 28-27S95.464 8 79 8c-11.2 0-20.8 6.4-25.6 16C48.6 14.4 39 8 28 8" stroke="#C9A227" stroke-width="3" stroke-linecap="round"/>
          </svg>
        </div>
        <h2 class="cta-title" id="cta-title" data-scroll-reveal>
          Join the Movement.<br />
          <span class="cta-accent">Your Legacy Starts Now.</span>
        </h2>
        <p class="cta-sub" data-scroll-reveal data-delay="100">
          Whether you're here for the collection, the cause, or both — 
          REAP433 is built for those who refuse to be ordinary.
        </p>
        <div class="cta-actions" data-scroll-reveal data-delay="200">
          <a href="#shop" class="btn btn-gold btn-lg">
            Shop Premium Collection
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
          </a>
          <a href="#hub" class="btn btn-cream btn-lg">
            Enter the Civic Hub
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
          </a>
        </div>
        <div class="cta-newsletter" data-scroll-reveal data-delay="300">
          <form class="newsletter-form" aria-label="Newsletter signup">
            <label for="nl-email" class="visually-hidden">Email address for newsletter</label>
            <input
              type="email"
              id="nl-email"
              name="email"
              class="newsletter-input"
              placeholder="Enter your email for the movement…"
              autocomplete="email"
              aria-label="Email for newsletter"
            />
            <button type="submit" class="btn btn-gold newsletter-submit">
              Subscribe
            </button>
          </form>
          <p class="newsletter-note">No spam. Weekly civic updates + exclusive product drops.</p>
        </div>
      </div>
    </section>
  </main>
@endsection