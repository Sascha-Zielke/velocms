<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<?php $lang = ($lang_raw = ($_COOKIE['vcms_lang'] ?? 'de')) && in_array($lang_raw, ['de', 'en'], true) ? $lang_raw : 'de'; ?>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e($_SESSION['csrf_token'] ?? '') ?>">
    <title>VeloCMS Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="vcms-admin">

<div class="vcms-sidebar">
    <div class="vcms-logo">
        <a href="/admin">VeloCMS</a>
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
        <form method="POST" action="/admin/logout" style="display:inline">
            <?= csrf_field() ?>
            <button type="submit" class="vcms-nav__item vcms-btn-link">
                <?= t('nav.logout') ?>
            </button>
        </form>
    </div>
</div>

<div class="vcms-main">
    <div class="vcms-content">

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
</body>
</html>
