<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<?php
$activeLangs = json_decode(setting('active_languages', '["de","en"]'), true) ?: ['de', 'en'];
$activeLangs = array_values(array_filter($activeLangs, fn($l) => preg_match('/^[a-z]{2}$/', (string)$l)));
$currentLang = ($l = $_COOKIE['vcms_lang'] ?? '') && in_array($l, $activeLangs, true) ? $l : ($activeLangs[0] ?? 'de');
$lang        = $currentLang; // keep $lang for backward compat in nav rendering
?>
<?php
$siteName    = setting('site_name', 'VeloCMS');
$logoPath    = setting('logo_path');
$titleSuffix = setting('meta_title_suffix');
$navItems    = nav();
$appUrl      = rtrim(setting('app_url', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), '/');
$currentUri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$canonicalUrl = $appUrl . $currentUri;

// Resolve title + description for OG tags
$pageTitle   = $this->yield('title');
$fullTitle   = $pageTitle !== '' ? $pageTitle . $titleSuffix : $siteName;
$metaDesc    = $this->yield('meta_description') ?: setting('meta_description_default');
$ogImage     = $this->yield('og_image') ?: setting('logo_path');
?>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e($metaDesc) ?>">
    <?php if (setting('meta_keywords_default')): ?>
    <meta name="keywords" content="<?= e(setting('meta_keywords_default')) ?>">
    <?php endif ?>

    <!-- Canonical + hreflang -->
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <?php if (count($activeLangs) > 1):
        $defaultLang = setting('default_language', $activeLangs[0]);
    ?>
    <?php foreach ($activeLangs as $hLang): ?>
    <link rel="alternate" hreflang="<?= e($hLang) ?>" href="<?= e($appUrl . $currentUri) ?>">
    <?php endforeach ?>
    <link rel="alternate" hreflang="x-default" href="<?= e($appUrl . $currentUri) ?>">
    <?php endif ?>

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?= e($canonicalUrl) ?>">
    <meta property="og:title"       content="<?= e($fullTitle) ?>">
    <meta property="og:description" content="<?= e($metaDesc) ?>">
    <meta property="og:site_name"   content="<?= e($siteName) ?>">
    <?php if ($ogImage): ?>
    <meta property="og:image"       content="<?= e(str_starts_with($ogImage, 'http') ? $ogImage : $appUrl . $ogImage) ?>">
    <?php endif ?>

    <?php if ($favicon = setting('favicon_path')): ?>
    <link rel="icon" href="<?= e($favicon) ?>">
    <?php endif ?>
    <link rel="stylesheet" href="/assets/css/frontend.css">
    <?= ve_head((int)($pageId ?? ($page['id'] ?? 0))) ?>
    <?= $this->yield('head') ?>
</head>
<body class="vcms-frontend">

<header class="vcms-header">
    <div class="vcms-header-inner vcms-container">
        <a href="/" class="vcms-logo">
            <?php if ($logoPath): ?>
                <img src="<?= e($logoPath) ?>" alt="<?= e($siteName) ?>" class="vcms-logo-img">
            <?php else: ?>
                <?= e($siteName) ?>
            <?php endif ?>
        </a>

        <?php if (!empty($navItems)): ?>
        <nav class="vcms-nav-bar" aria-label="Hauptnavigation">
            <ul class="vcms-nav-list">
                <?php foreach ($navItems as $item): ?>
                <?php
                $itemUrl  = $item['url'] ?? '/';
                $isActive = rtrim($currentUri, '/') === rtrim($itemUrl, '/');
                $target   = ($item['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
                $rel      = $target === '_blank' ? ' rel="noopener noreferrer"' : '';
                ?>
                <li>
                    <a href="<?= e($itemUrl) ?>"
                       target="<?= e($target) ?>"<?= $rel ?>
                       class="vcms-nav-link<?= $isActive ? ' vcms-nav-link--active' : '' ?>"
                       <?= $isActive ? 'aria-current="page"' : '' ?>>
                        <?= e(localized($item, 'label', 'velocms_nav_items')) ?>
                    </a>
                </li>
                <?php endforeach ?>
            </ul>
        </nav>

        <!-- Hamburger (visible on mobile only via CSS) -->
        <button class="vcms-hamburger" aria-label="Menü öffnen" aria-expanded="false" aria-controls="vcms-mobile-nav">
            <span class="vcms-hamburger-bar"></span>
            <span class="vcms-hamburger-bar"></span>
            <span class="vcms-hamburger-bar"></span>
        </button>
        <?php endif ?>

        <?php if (count($activeLangs) > 1): ?>
        <div class="vcms-lang-switcher" role="group" aria-label="Language">
            <?php if (count($activeLangs) > 2): ?>
            <select class="vcms-lang-select" aria-label="Language">
                <?php foreach ($activeLangs as $lng): ?>
                <option value="<?= e($lng) ?>"<?= $lng === $currentLang ? ' selected' : '' ?>>
                    <?= strtoupper(e($lng)) ?>
                </option>
                <?php endforeach ?>
            </select>
            <?php else: ?>
            <?php foreach ($activeLangs as $lng): ?>
            <button class="vcms-lang-btn<?= $lng === $currentLang ? ' is-active' : '' ?>"
                    data-lang="<?= e($lng) ?>"
                    type="button"
                    aria-pressed="<?= $lng === $currentLang ? 'true' : 'false' ?>">
                <?= strtoupper(e($lng)) ?>
            </button>
            <?php endforeach ?>
            <?php endif ?>
        </div>
        <?php endif ?>
    </div>
</header>

<?php if (!empty($navItems)): ?>
<div class="vcms-mobile-nav" id="vcms-mobile-nav" role="dialog" aria-label="Navigation">
    <ul class="vcms-mobile-nav__list">
        <?php foreach ($navItems as $item): ?>
        <?php
        $itemUrl  = $item['url'] ?? '/';
        $isActive = rtrim($currentUri, '/') === rtrim($itemUrl, '/');
        $target   = ($item['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
        $rel      = $target === '_blank' ? ' rel="noopener noreferrer"' : '';
        ?>
        <li>
            <a href="<?= e($itemUrl) ?>"
               target="<?= e($target) ?>"<?= $rel ?>
               class="vcms-mobile-nav__link<?= $isActive ? ' vcms-mobile-nav__link--active' : '' ?>">
                <?= e(localized($item, 'label', 'velocms_nav_items')) ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
</div>
<?php endif ?>

<main class="vcms-page" id="vcms-content">
    <?= $this->yield('content') ?>
</main>

<footer class="vcms-footer">
    <div class="vcms-footer-inner vcms-container">
        <p class="vcms-footer-copy"><?= setting('footer_text', '&copy; ' . date('Y')) ?></p>
        <?php
        $impressumUrl   = setting('footer_impressum_url');
        $datenschutzUrl = setting('footer_datenschutz_url');
        ?>
        <?php if ($impressumUrl || $datenschutzUrl): ?>
        <nav class="vcms-footer-nav" aria-label="Rechtliches">
            <?php if ($impressumUrl): ?>
                <a href="<?= e($impressumUrl) ?>">Impressum</a>
            <?php endif ?>
            <?php if ($datenschutzUrl): ?>
                <a href="<?= e($datenschutzUrl) ?>">Datenschutz</a>
            <?php endif ?>
        </nav>
        <?php endif ?>
    </div>
</footer>

<script src="/assets/js/frontend.js"></script>
<script src="/assets/js/lang-switcher.js"></script>
<?= ve_scripts() ?>
<?= $this->yield('scripts') ?>
</body>
</html>
