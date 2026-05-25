<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 – Seite nicht gefunden</title>
    <link rel="stylesheet" href="/assets/css/frontend.css">
    <style>
        .vcms-error-page { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:60vh; text-align:center; padding:2rem; }
        .vcms-error-page h1 { font-size:4rem; margin-bottom:0.5rem; color:#1a1a1a; }
        .vcms-error-page p  { font-size:1.125rem; color:#555; margin-bottom:1.5rem; }
    </style>
</head>
<body class="vcms-frontend">

<?php
// Try to render within the full frontend layout (with nav + footer)
if (function_exists('nav') && function_exists('setting')) {
    $siteName = setting('site_name', 'VeloCMS');
    $navItems = nav();
?>
<header class="vcms-header">
    <nav class="vcms-nav-bar">
        <a href="/" class="vcms-logo"><?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?></a>
        <ul class="vcms-nav-list">
            <?php foreach ($navItems as $item): ?>
            <li><a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>"
                   target="<?= htmlspecialchars($item['target'] ?? '_self', ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
            </a></li>
            <?php endforeach ?>
        </ul>
    </nav>
</header>
<?php } ?>

<main class="vcms-page">
    <div class="vcms-error-page">
        <h1>404</h1>
        <p>Die gesuchte Seite wurde nicht gefunden.</p>
        <a href="/" class="vcms-btn vcms-btn--primary">Zur Startseite</a>
    </div>
</main>

<?php if (function_exists('setting')): ?>
<footer class="vcms-footer">
    <p><?= setting('footer_text', '&copy; ' . date('Y')) ?></p>
    <?php
    $impressumUrl    = setting('footer_impressum_url');
    $datenschutzUrl  = setting('footer_datenschutz_url');
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
</footer>
<?php endif ?>

</body>
</html>
