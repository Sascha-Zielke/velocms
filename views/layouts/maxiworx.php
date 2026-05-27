<?php declare(strict_types=1); ?>
<?php
$siteName   = 'Maxiworx';
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$pageTitle  = $this->yield('title');
$fullTitle  = $pageTitle !== '' ? $pageTitle . ' — Maxiworx' : 'Maxiworx — Superior Music Production';
$metaDesc   = $this->yield('meta_description') ?: 'Maxiworx — Superior Music Production. Professional recording, mixing & mastering in Munich.';

$nav = [
    ['label' => 'Equipment',       'url' => '/equipment'],
    ['label' => 'Service & Preise','url' => '/service-preise'],
    ['label' => 'Specials',        'url' => '/specials'],
    ['label' => 'Referenzen',      'url' => '/referenzen'],
    ['label' => 'Kontakt',         'url' => '/kontakt'],
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e($metaDesc) ?>">
    <link rel="canonical" href="https://maxiworx.de<?= e($currentUri) ?>">
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="<?= e($fullTitle) ?>">
    <meta property="og:description" content="<?= e($metaDesc) ?>">
    <meta property="og:site_name"   content="Maxiworx">
    <link rel="stylesheet" href="/assets/css/maxiworx.css">
    <?= $this->yield('head') ?>
</head>
<body class="mw-body">

<!-- ─── Header ─────────────────────────────────────────────────────────────── -->
<header class="mw-header" id="mw-header">
    <div class="mw-header__inner mw-container">

        <a href="/" class="mw-logo" aria-label="Maxiworx Startseite">
            <img src="/assets/images/maxiworx-logo.png"
                 alt="Maxiworx"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            <span class="mw-logo__text" style="display:none">MAXIWORX</span>
        </a>

        <nav class="mw-nav" aria-label="Hauptnavigation">
            <?php foreach ($nav as $item):
                $active = rtrim($currentUri, '/') === rtrim($item['url'], '/');
            ?>
            <a href="<?= e($item['url']) ?>"
               class="mw-nav__link<?= $active ? ' mw-nav__link--active' : '' ?>"
               <?= $active ? 'aria-current="page"' : '' ?>>
                <?= e($item['label']) ?>
            </a>
            <?php endforeach ?>
        </nav>

        <button class="mw-btn-book" id="mw-open-overlay" type="button" aria-haspopup="dialog">
            Book Session
        </button>

        <button class="mw-hamburger" id="mw-hamburger" aria-label="Menü öffnen" aria-expanded="false" aria-controls="mw-mobile-nav">
            <span></span><span></span><span></span>
        </button>

    </div>
</header>

<!-- ─── Mobile Nav ──────────────────────────────────────────────────────────── -->
<nav class="mw-mobile-nav" id="mw-mobile-nav" aria-label="Mobile Navigation">
    <?php foreach ($nav as $item):
        $active = rtrim($currentUri, '/') === rtrim($item['url'], '/');
    ?>
    <a href="<?= e($item['url']) ?>"<?= $active ? ' aria-current="page"' : '' ?>>
        <?= e($item['label']) ?>
    </a>
    <?php endforeach ?>
    <button class="mw-btn-book" id="mw-open-overlay-mobile" type="button">Book Session</button>
</nav>

<!-- ─── Booking Overlay ─────────────────────────────────────────────────────── -->
<div class="mw-overlay" id="mw-overlay" hidden role="dialog" aria-modal="true" aria-labelledby="mw-overlay-title">
    <div class="mw-overlay__backdrop" id="mw-overlay-backdrop"></div>
    <div class="mw-overlay__panel">
        <button class="mw-overlay__close" id="mw-close-overlay" aria-label="Schließen">✕</button>
        <p class="mw-overlay__title" id="mw-overlay-title">Book a Session</p>
        <p class="mw-overlay__sub">Fill in your details — we'll confirm your slot within 24 hours.</p>

        <?php if (!empty($_SESSION['mw_booking_success'])): ?>
        <div class="mw-form__success">
            <?= e($_SESSION['mw_booking_success']) ?>
            <?php unset($_SESSION['mw_booking_success']); ?>
        </div>
        <?php elseif (!empty($_SESSION['mw_booking_error'])): ?>
        <div class="mw-form__error">
            <?= e($_SESSION['mw_booking_error']) ?>
            <?php unset($_SESSION['mw_booking_error']); ?>
        </div>
        <?php endif ?>

        <form class="mw-form" method="POST" action="/book-session">
            <?= csrf_field() ?>
            <div class="mw-form__row">
                <div class="mw-form__group">
                    <label class="mw-form__label" for="bs-name">Name *</label>
                    <input class="mw-form__input" id="bs-name" name="name" type="text"
                           placeholder="Max Mustermann" required autocomplete="name">
                </div>
                <div class="mw-form__group">
                    <label class="mw-form__label" for="bs-email">E-Mail *</label>
                    <input class="mw-form__input" id="bs-email" name="email" type="email"
                           placeholder="max@example.com" required autocomplete="email">
                </div>
            </div>
            <div class="mw-form__group">
                <label class="mw-form__label" for="bs-type">Session-Typ *</label>
                <select class="mw-form__select" id="bs-type" name="session_type" required>
                    <option value="" disabled selected>Bitte wählen …</option>
                    <option value="recording">Recording</option>
                    <option value="mixing">Mixing</option>
                    <option value="mastering">Mastering</option>
                    <option value="podcast">Podcast / Voiceover</option>
                    <option value="other">Sonstiges</option>
                </select>
            </div>
            <div class="mw-form__row">
                <div class="mw-form__group">
                    <label class="mw-form__label" for="bs-date">Wunschdatum</label>
                    <input class="mw-form__input" id="bs-date" name="preferred_date" type="date">
                </div>
                <div class="mw-form__group">
                    <label class="mw-form__label" for="bs-phone">Telefon</label>
                    <input class="mw-form__input" id="bs-phone" name="phone" type="tel"
                           placeholder="+49 …" autocomplete="tel">
                </div>
            </div>
            <div class="mw-form__group">
                <label class="mw-form__label" for="bs-message">Projektbeschreibung</label>
                <textarea class="mw-form__textarea" id="bs-message" name="message"
                          placeholder="Beschreib kurz dein Projekt, Genre, Anzahl der Tracks …"></textarea>
            </div>
            <button class="mw-btn-primary" type="submit" style="align-self:flex-start">
                Anfrage senden →
            </button>
        </form>
    </div>
</div>

<!-- ─── Main Content ─────────────────────────────────────────────────────────── -->
<main id="mw-content">
    <?= $this->yield('content') ?>
</main>

<!-- ─── Footer ──────────────────────────────────────────────────────────────── -->
<footer class="mw-footer">
    <div class="mw-container">
        <div class="mw-footer__top">

            <!-- Brand -->
            <div class="mw-footer__brand">
                <img src="/assets/images/maxiworx-logo.png" alt="Maxiworx"
                     onerror="this.style.display='none'">
                <p class="mw-footer__brand-text">Maxiworx</p>
                <p class="mw-footer__tagline">Superior Music Production — Recording, Mixing &amp; Mastering in Munich.</p>
                <div class="mw-footer__social">
                    <a href="#" aria-label="Instagram" rel="noopener noreferrer" target="_blank">IG</a>
                    <a href="#" aria-label="YouTube"   rel="noopener noreferrer" target="_blank">YT</a>
                    <a href="#" aria-label="Spotify"   rel="noopener noreferrer" target="_blank">SP</a>
                    <a href="#" aria-label="SoundCloud" rel="noopener noreferrer" target="_blank">SC</a>
                </div>
            </div>

            <!-- Explore -->
            <div>
                <p class="mw-footer__col-title">Explore</p>
                <div class="mw-footer__links">
                    <a href="/equipment">Equipment</a>
                    <a href="/service-preise">Service &amp; Preise</a>
                    <a href="/specials">Specials</a>
                    <a href="/referenzen">Referenzen</a>
                    <a href="/kontakt">Kontakt</a>
                </div>
            </div>

            <!-- Legal -->
            <div>
                <p class="mw-footer__col-title">Legal</p>
                <div class="mw-footer__links">
                    <a href="/impressum">Impressum</a>
                    <a href="/datenschutz">Datenschutzerklärung</a>
                    <a href="/agb">AGB</a>
                    <a href="/admin" rel="nofollow">Admin Login</a>
                </div>
            </div>

        </div>
        <div class="mw-footer__bottom">
            <span>&copy; <?= date('Y') ?> Maxiworx. All rights reserved.</span>
            <span>Made with VeloCMS</span>
        </div>
    </div>
</footer>

<!-- ─── Scripts ──────────────────────────────────────────────────────────────── -->
<script>
(function () {
    'use strict';

    /* Hamburger + mobile nav */
    const hamburger  = document.getElementById('mw-hamburger');
    const mobileNav  = document.getElementById('mw-mobile-nav');
    const openMobile = document.getElementById('mw-open-overlay-mobile');

    hamburger?.addEventListener('click', function () {
        const open = mobileNav.classList.toggle('is-open');
        this.setAttribute('aria-expanded', String(open));
        this.setAttribute('aria-label', open ? 'Menü schließen' : 'Menü öffnen');
    });

    /* Booking overlay */
    const overlay     = document.getElementById('mw-overlay');
    const openBtn     = document.getElementById('mw-open-overlay');
    const closeBtn    = document.getElementById('mw-close-overlay');
    const backdrop    = document.getElementById('mw-overlay-backdrop');

    function openOverlay() {
        overlay.hidden = false;
        document.body.style.overflow = 'hidden';
        closeBtn.focus();
    }

    function closeOverlay() {
        overlay.hidden = true;
        document.body.style.overflow = '';
        openBtn?.focus();
    }

    openBtn?.addEventListener('click', openOverlay);
    openMobile?.addEventListener('click', function () {
        mobileNav.classList.remove('is-open');
        hamburger?.setAttribute('aria-expanded', 'false');
        openOverlay();
    });
    closeBtn?.addEventListener('click', closeOverlay);
    backdrop?.addEventListener('click', closeOverlay);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !overlay.hidden) closeOverlay();
    });

    /* Open overlay if there's a flash message (booking result) */
    const hasFlash = overlay.querySelector('.mw-form__success, .mw-form__error');
    if (hasFlash) openOverlay();

    /* Header shadow on scroll */
    const header = document.getElementById('mw-header');
    window.addEventListener('scroll', function () {
        header.style.boxShadow = window.scrollY > 20
            ? '0 4px 24px rgba(0,0,0,0.5)'
            : '';
    }, { passive: true });

    /* Scroll-reveal */
    if ('IntersectionObserver' in window) {
        const style = document.createElement('style');
        style.textContent = '.mw-reveal{opacity:0;transform:translateY(24px);transition:opacity .6s ease,transform .6s ease}.mw-reveal.mw-visible{opacity:1;transform:none}';
        document.head.appendChild(style);

        document.querySelectorAll('.mw-section > .mw-container > *, .mw-service-card, .mw-portfolio__item, .mw-hardware__item').forEach(function (el) {
            el.classList.add('mw-reveal');
        });

        const io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('mw-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('.mw-reveal').forEach(function (el) { io.observe(el); });
    }
})();
</script>
<?= $this->yield('scripts') ?>
</body>
</html>
