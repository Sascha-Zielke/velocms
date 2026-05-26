<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 – Seite nicht gefunden</title>
    <link rel="stylesheet" href="/assets/css/frontend.css">
</head>
<body class="vcms-frontend">

<?php
if (function_exists('nav') && function_exists('setting')):
    $siteName   = setting('site_name', 'VeloCMS');
    $logoPath   = setting('logo_path');
    $navItems   = nav();
    $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $lang       = in_array($_COOKIE['vcms_lang'] ?? 'de', ['de','en']) ? ($_COOKIE['vcms_lang'] ?? 'de') : 'de';
?>
<header class="vcms-header">
    <div class="vcms-header-inner vcms-container">
        <a href="/" class="vcms-logo">
            <?php if ($logoPath): ?>
                <img src="<?= htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>"
                     class="vcms-logo-img">
            <?php else: ?>
                <?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>
            <?php endif ?>
        </a>
        <?php if (!empty($navItems)): ?>
        <nav class="vcms-nav-bar" aria-label="Hauptnavigation">
            <ul class="vcms-nav-list">
                <?php foreach ($navItems as $item):
                    $itemUrl  = $item['url'] ?? '/';
                    $isActive = rtrim($currentUri, '/') === rtrim($itemUrl, '/');
                    $target   = ($item['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
                    $rel      = $target === '_blank' ? ' rel="noopener noreferrer"' : '';
                ?>
                <li>
                    <a href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"
                       target="<?= htmlspecialchars($target, ENT_QUOTES, 'UTF-8') ?>"<?= $rel ?>
                       class="vcms-nav-link<?= $isActive ? ' vcms-nav-link--active' : '' ?>">
                        <?= htmlspecialchars(
                            $lang === 'en' && !empty($item['label_en']) ? $item['label_en'] : $item['label'],
                            ENT_QUOTES, 'UTF-8'
                        ) ?>
                    </a>
                </li>
                <?php endforeach ?>
            </ul>
        </nav>
        <button class="vcms-hamburger" aria-label="Menü öffnen" aria-expanded="false">
            <span class="vcms-hamburger-bar"></span>
            <span class="vcms-hamburger-bar"></span>
            <span class="vcms-hamburger-bar"></span>
        </button>
        <?php endif ?>
    </div>
</header>

<?php if (!empty($navItems)): ?>
<div class="vcms-mobile-nav" id="vcms-mobile-nav" role="dialog" aria-label="Navigation">
    <ul class="vcms-mobile-nav__list">
        <?php foreach ($navItems as $item):
            $itemUrl  = $item['url'] ?? '/';
            $isActive = rtrim($currentUri, '/') === rtrim($itemUrl, '/');
        ?>
        <li>
            <a href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"
               class="vcms-mobile-nav__link<?= $isActive ? ' vcms-mobile-nav__link--active' : '' ?>">
                <?= htmlspecialchars(
                    $lang === 'en' && !empty($item['label_en']) ? $item['label_en'] : $item['label'],
                    ENT_QUOTES, 'UTF-8'
                ) ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
</div>
<?php endif ?>

<?php endif ?>

<main class="vcms-page">
    <div class="vcms-error-page">
        <div class="vcms-error-code">404</div>
        <h1>Seite nicht gefunden</h1>
        <p>Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
        <a href="/" class="vcms-btn-frontend">Zur Startseite</a>
    </div>
</main>

<?php if (function_exists('setting')): ?>
<footer class="vcms-footer">
    <div class="vcms-footer-inner vcms-container">
        <p class="vcms-footer-copy"><?= setting('footer_text', '&copy; ' . date('Y')) ?></p>
        <?php
        $impressumUrl   = setting('footer_impressum_url');
        $datenschutzUrl = setting('footer_datenschutz_url');
        if ($impressumUrl || $datenschutzUrl): ?>
        <nav class="vcms-footer-nav">
            <?php if ($impressumUrl): ?>
                <a href="<?= htmlspecialchars($impressumUrl, ENT_QUOTES, 'UTF-8') ?>">Impressum</a>
            <?php endif ?>
            <?php if ($datenschutzUrl): ?>
                <a href="<?= htmlspecialchars($datenschutzUrl, ENT_QUOTES, 'UTF-8') ?>">Datenschutz</a>
            <?php endif ?>
        </nav>
        <?php endif ?>
    </div>
</footer>
<?php endif ?>

<script src="/assets/js/frontend.js"></script>
</body>
</html>
