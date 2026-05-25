<?php declare(strict_types=1); ?>
<?php $this->extend('frontend') ?>
<?php $this->section('content') ?>
<main class="vcms-container" style="padding:4rem 1rem; text-align:center;">
    <h1>404</h1>
    <p><?= t('error.not_found') ?></p>
    <a href="/" class="vcms-btn-frontend"><?= t('action.home') ?></a>
</main>
<?php $this->endSection() ?>
