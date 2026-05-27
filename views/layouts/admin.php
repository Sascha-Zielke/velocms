<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<?php
$activeLangs = json_decode(setting('active_languages', '["de","en"]'), true) ?: ['de', 'en'];
$activeLangs = array_values(array_filter($activeLangs, fn($l) => preg_match('/^[a-z]{2}$/', (string)$l)));
$currentLang = ($l = $_COOKIE['vcms_admin_lang'] ?? '') && in_array($l, $activeLangs, true) ? $l : ($activeLangs[0] ?? 'de');
?>
<html lang="<?= e($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e($_SESSION['csrf_token'] ?? '') ?>">
    <title>VeloCMS Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
    <?php
    $_veDomain    = \VeloCMS\Core\Tenant::domain();
    $_veThemePath = $_SERVER['DOCUMENT_ROOT'] . '/assets/css/sites/' . $_veDomain . '/theme.css';
    if (file_exists($_veThemePath)):
    ?>
    <link rel="stylesheet" href="/assets/css/sites/<?= e($_veDomain) ?>/theme.css">
    <?php endif ?>
</head>
<body class="vcms-admin">

<?php $adminLogoName = setting('site_name', 'VeloCMS'); ?>
<div class="vcms-sidebar">
    <div class="vcms-logo">
        <a href="/admin"><?= e($adminLogoName) ?></a>
    </div>

    <nav class="vcms-nav">
        <a href="/admin" class="vcms-nav__item">
            <?= t('nav.dashboard') ?>
        </a>

        <?php foreach (\VeloCMS\Core\AdminMenu::getItems() as $item): ?>
        <?php if (($item['type'] ?? '') === 'section'): ?>
        <div class="vcms-nav__section"><?= e($item['label']) ?></div>
        <?php else: ?>
        <a href="<?= e($item['url']) ?>" class="vcms-nav__item">
            <?= e($item['label']) ?>
        </a>
        <?php endif ?>
        <?php endforeach ?>
    </nav>

    <div class="vcms-nav-footer">
        <span class="vcms-nav__user"><?= e(\VeloCMS\Core\Auth::name() ?? '') ?></span>

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

        <form method="POST" action="/admin/logout" style="display:inline">
            <?= csrf_field() ?>
            <button type="submit" class="vcms-nav__item vcms-btn-link">
                <?= t('nav.logout') ?>
            </button>
        </form>
    </div>
</div>

<div class="vcms-main">
    <div class="vcms-content" id="vcms-content">

        <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="vcms-alert vcms-alert--success">
            <?= e($_SESSION['flash_success']) ?>
        </div>
        <?php unset($_SESSION['flash_success']); endif ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="vcms-alert vcms-alert--error">
            <?= e($_SESSION['flash_error']) ?>
        </div>
        <?php unset($_SESSION['flash_error']); endif ?>

        <?= $this->yield('content') ?>

    </div>
</div>

<script src="/assets/js/admin.js"></script>
<script src="/assets/js/lang-switcher.js"></script>
</body>
</html>
