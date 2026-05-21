<!DOCTYPE html>
<html lang="<?= e($_COOKIE['vcms_lang'] ?? 'de') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->yield('title', 'VeloCMS') ?></title>
    <meta name="description" content="<?= $this->yield('meta_description') ?>">
    <link rel="stylesheet" href="/assets/css/frontend.css">
    <?= $this->yield('head') ?>
</head>
<body class="vcms-frontend">

    <header class="vcms-header">
        <?= $this->yield('header') ?>
    </header>

    <main class="vcms-page">
        <?= $this->yield('content') ?>
    </main>

    <footer class="vcms-footer">
        <?= $this->yield('footer') ?>
    </footer>

    <script src="/assets/js/frontend.js"></script>
    <?= $this->yield('scripts') ?>
</body>
</html>
