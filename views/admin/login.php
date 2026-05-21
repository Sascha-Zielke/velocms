<!DOCTYPE html>
<html lang="<?= e($_COOKIE['vcms_lang'] ?? 'de') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloCMS Login</title>
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

    <form method="POST" action="/admin/login">
        <?= csrf_field() ?>

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
