<?php declare(strict_types=1); ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= $item ? t('nav.edit_item') : t('nav.new_item') ?></h1>
    <a href="/admin/nav" class="vcms-btn"><?= t('action.back') ?></a>
</div>

<form method="POST" action="<?= e($action) ?>" class="vcms-form">
    <?= csrf_field() ?>

    <div class="vcms-form-group">
        <label for="label"><?= t('field.label') ?> (DE) <span class="vcms-required">*</span></label>
        <input type="text" id="label" name="label" required
               value="<?= e($item['label'] ?? '') ?>" class="vcms-input">
    </div>

    <div class="vcms-form-group">
        <label for="label_en"><?= t('field.label') ?> (EN)</label>
        <input type="text" id="label_en" name="label_en"
               value="<?= e($item['label_en'] ?? '') ?>" class="vcms-input">
    </div>

    <div class="vcms-form-group">
        <label for="url"><?= t('field.url') ?> <span class="vcms-required">*</span></label>
        <input type="text" id="url" name="url" required
               value="<?= e($item['url'] ?? '') ?>" class="vcms-input" placeholder="/seite oder https://...">
    </div>

    <div class="vcms-form-group">
        <label for="target">Link-Ziel</label>
        <select id="target" name="target" class="vcms-input">
            <option value="_self"  <?= ($item['target'] ?? '_self') === '_self'  ? 'selected' : '' ?>>_self (gleiche Seite)</option>
            <option value="_blank" <?= ($item['target'] ?? '_self') === '_blank' ? 'selected' : '' ?>>_blank (neues Tab)</option>
        </select>
    </div>

    <div class="vcms-form-group">
        <label for="active"><?= t('field.status') ?></label>
        <select id="active" name="active" class="vcms-input">
            <option value="1" <?= ($item['active'] ?? 1) == 1 ? 'selected' : '' ?>><?= t('users.active') ?></option>
            <option value="0" <?= ($item['active'] ?? 1) == 0 ? 'selected' : '' ?>><?= t('users.inactive') ?></option>
        </select>
    </div>

    <div class="vcms-form-actions">
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
        <a href="/admin/nav" class="vcms-btn"><?= t('action.cancel') ?></a>
    </div>
</form>

<?php $this->endSection() ?>
