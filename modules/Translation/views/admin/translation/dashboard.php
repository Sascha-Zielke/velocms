<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<h1><?= t('translation.headline') ?></h1>

<div class="vcms-card" style="padding: 32px; text-align: center; color: var(--vcms-muted); margin-top: 24px;">
    <p style="font-size: 2rem; margin: 0 0 12px;">🌍</p>
    <p style="font-size: 1.1rem; font-weight: 600; margin: 0 0 8px;"><?= t('translation.headline') ?></p>
    <p style="margin: 0 0 4px;">Phase 1 aktiv — Datenbank-Fundament bereit.</p>
    <p style="margin: 0; font-size: 13px;">Dashboard wird in Phase 4 vollständig implementiert.</p>
</div>

<?php $this->endSection() ?>
