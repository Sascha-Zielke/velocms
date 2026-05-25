<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<?php $lang = ($lang_raw = ($_COOKIE['vcms_lang'] ?? 'de')) && in_array($lang_raw, ['de', 'en'], true) ? $lang_raw : 'de'; ?>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloCMS &mdash; <?= t('auth.login') ?></title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="vcms-login-page">

<div class="vcms-login-box">
    <h1><?= t('auth.login_headline') ?></h1>

    <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="vcms-alert vcms-alert--error">
        <?= e($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); endif ?>

    <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="vcms-alert vcms-alert--success">
        <?= e($_SESSION['flash_success']) ?>
    </div>
    <?php unset($_SESSION['flash_success']); endif ?>

    <form method="POST" action="/admin/login">
        <?= csrf_field() ?>
        <!-- Honeypot: real users never see or fill this -->
        <div class="vcms-hp-field" aria-hidden="true" tabindex="-1">
            <label for="vcms_name">Name</label>
            <input type="text" id="vcms_name" name="vcms_name" autocomplete="off" tabindex="-1">
        </div>

        <div class="vcms-field">
            <label for="email"><?= t('auth.email') ?></label>
            <input type="email" id="email" name="email" required autofocus
                   value="<?= e($_POST['email'] ?? '') ?>">
        </div>

        <div class="vcms-field">
            <label for="password"><?= t('auth.password') ?></label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn-primary">
            <?= t('action.login') ?>
        </button>
    </form>
</div>

</body>
</html>
