<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= function_exists('setting') ? htmlspecialchars(setting('site_name', 'VeloCMS'), ENT_QUOTES, 'UTF-8') : 'VeloCMS' ?> &mdash; <?= function_exists('t') ? t('maintenance.title') : 'Wartungsarbeiten' ?></title>
    <link rel="stylesheet" href="/assets/css/frontend.css">
</head>
<body class="vcms-frontend">

<main class="vcms-page">
    <div class="vcms-error-page">
        <div class="vcms-error-code">503</div>
        <h1><?= function_exists('t') ? t('maintenance.headline') : 'Wartungsarbeiten' ?></h1>
        <p><?= function_exists('t') ? t('maintenance.text') : 'Die Website wird gerade gewartet. Bitte versuche es später erneut.' ?></p>
    </div>
</main>

</body>
</html>
