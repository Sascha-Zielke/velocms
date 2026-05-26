<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<?php $lang = ($lang_raw = ($_COOKIE['vcms_lang'] ?? 'de')) && in_array($lang_raw, ['de', 'en'], true) ? $lang_raw : 'de'; ?>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloCMS &mdash; <?= t('password_reset.page_title_form') ?></title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="vcms-login-page">

<div class="vcms-login-box">
    <h1><?= t('password_reset.headline_form') ?></h1>

    <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="vcms-alert vcms-alert--error">
        <?= e($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); endif ?>

    <form method="POST" action="/admin/password/reset/<?= urlencode($token) ?>">
        <?= csrf_field() ?>

        <div class="vcms-field">
            <label for="password"><?= t('auth.password') ?></label>
            <input type="password" id="password" name="password"
                   required autofocus minlength="8"
                   autocomplete="new-password">
            <small><?= t('users.password_hint') ?></small>
        </div>

        <div class="vcms-field">
            <label for="password_confirm"><?= t('users.password_confirm') ?></label>
            <input type="password" id="password_confirm" name="password_confirm"
                   required minlength="8"
                   autocomplete="new-password">
        </div>

        <button type="submit" class="btn-primary">
            <?= t('password_reset.submit_form') ?>
        </button>
    </form>

    <p class="vcms-login-back">
        <a href="/admin/login">&larr; <?= t('password_reset.back_to_login') ?></a>
    </p>
</div>

</body>
</html>
