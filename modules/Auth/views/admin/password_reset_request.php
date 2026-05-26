<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<?php $lang = ($lang_raw = ($_COOKIE['vcms_lang'] ?? 'de')) && in_array($lang_raw, ['de', 'en'], true) ? $lang_raw : 'de'; ?>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloCMS &mdash; <?= t('password_reset.page_title_request') ?></title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="vcms-login-page">

<div class="vcms-login-box">
    <h1><?= t('password_reset.headline_request') ?></h1>
    <p class="vcms-login-hint"><?= t('password_reset.intro_request') ?></p>

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

    <form method="POST" action="/admin/password/reset">
        <?= csrf_field() ?>

        <div class="vcms-field">
            <label for="email"><?= t('auth.email') ?></label>
            <input type="email" id="email" name="email" required autofocus
                   value="<?= e($_POST['email'] ?? '') ?>">
        </div>

        <button type="submit" class="btn-primary">
            <?= t('password_reset.submit_request') ?>
        </button>
    </form>

    <p class="vcms-login-back">
        <a href="/admin/login">&larr; <?= t('password_reset.back_to_login') ?></a>
    </p>
</div>

</body>
</html>
