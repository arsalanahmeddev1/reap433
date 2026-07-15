THE CROSS — Reap Threads Trivia PWA
===================================

WHAT'S IN HERE
  index.html                  The whole app (self-contained: HTML + CSS + JS)
  manifest.json               Install identity (name, icons, navy theme, standalone)
  sw.js                       Service worker — caches the app for offline play
  icons/                      App icons (192, 512, maskable, Apple touch, favicon)

HOW TO PUBLISH (one requirement: it must be served over HTTPS)
  A PWA can't install from a local file — it needs a real HTTPS URL. Any of these
  free static hosts work; upload this whole folder as-is:
    - Netlify Drop  (netlify.com/drop — literally drag the folder onto the page)
    - Cloudflare Pages, Vercel, or GitHub Pages
  Once live, open the URL on your phone:
    - iPhone (Safari): Share → Add to Home Screen
    - Android (Chrome): you'll get an "Install app" prompt, or menu → Install

A NOTE ON WORDPRESS.COM
  Dropping a service worker at the site root on WordPress.com needs a plan with
  file/plugin access (Business or higher). The simplest path is to host this
  folder on one of the free static hosts above and link or embed it from
  reapthreads.com — the app still lives "under" your brand, and installs cleanly.

UPDATING THE DECK
  All 12 cards live in the DECK array near the top of the <script> in index.html.
  Each card is: category, question (q), choices[], answer (0-based index),
  ref (scripture, or "—" for none), and note (the teaching line shown after answering).
  After any change, bump the CACHE name in sw.js (e.g. "reap-cross-v1" -> "v2")
  so installed devices pull the new version.

TURNING THIS INTO STORE APPS LATER
  This same folder is what you'd feed to PWABuilder (free) to package an Android
  (.aab for Google Play) and iOS build — no rebuild required.
