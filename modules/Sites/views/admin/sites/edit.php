<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('sites.edit_headline') ?>: <?= e($site['name']) ?></h1>
    <a href="/admin/sites" class="vcms-btn vcms-btn--secondary">&larr; <?= t('action.back') ?></a>
</div>

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

<?php /* ── Provisioning banner ── */ ?>
<?php if ($site['status'] === 'provisioning'): ?>
<div class="vcms-alert vcms-alert--warning">
    <?= t('sites.provision_hint') ?>
    <form method="POST" action="/admin/sites/<?= (int)$site['id'] ?>/provision" style="display:inline;margin-left:16px">
        <?= csrf_field() ?>
        <button type="submit" class="vcms-btn vcms-btn--sm vcms-btn--primary">
            <?= t('sites.provision_btn') ?>
        </button>
    </form>
</div>
<?php endif ?>

<div class="vcms-card">
    <form method="POST" action="/admin/sites/<?= (int)$site['id'] ?>/edit">
        <?= csrf_field() ?>

        <div class="vcms-form-row">
            <label class="vcms-label" for="name"><?= t('sites.field_name') ?> *</label>
            <input class="vcms-input" type="text" id="name" name="name"
                   value="<?= e($site['name']) ?>"
                   maxlength="255" required>
        </div>

        <div class="vcms-form-row">
            <label class="vcms-label" for="domain"><?= t('sites.field_domain') ?> *</label>
            <input class="vcms-input" type="text" id="domain" name="domain"
                   value="<?= e($site['domain']) ?>"
                   maxlength="255" required>
        </div>

        <div class="vcms-form-row">
            <label class="vcms-label" for="www_alias"><?= t('sites.field_www_alias') ?></label>
            <input class="vcms-input" type="text" id="www_alias" name="www_alias"
                   value="<?= e($site['www_alias'] ?? '') ?>"
                   maxlength="255">
        </div>

        <div class="vcms-form-row">
            <label class="vcms-label" for="db_name"><?= t('sites.field_db_name') ?> *</label>
            <input class="vcms-input" type="text" id="db_name" name="db_name"
                   value="<?= e($site['db_name']) ?>"
                   maxlength="64" pattern="[a-zA-Z0-9_]{1,64}" required>
            <small class="vcms-hint"><?= t('sites.hint_db_name') ?></small>
        </div>

        <div class="vcms-form-row">
            <label class="vcms-label" for="status"><?= t('sites.field_status') ?></label>
            <select class="vcms-input" id="status" name="status">
                <?php foreach (['active', 'suspended', 'provisioning'] as $s): ?>
                <option value="<?= e($s) ?>" <?= $site['status'] === $s ? 'selected' : '' ?>>
                    <?= t('sites.status_' . $s) ?>
                </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="vcms-form-actions">
            <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
            <a href="/admin/sites" class="vcms-btn vcms-btn--secondary"><?= t('action.cancel') ?></a>
        </div>
    </form>
</div>

<?php /* ── Danger zone ── */ ?>
<div class="vcms-card vcms-card--danger" style="margin-top:32px">
    <h3><?= t('sites.danger_zone') ?></h3>
    <p><?= t('sites.danger_hint') ?></p>
    <form method="POST" action="/admin/sites/<?= (int)$site['id'] ?>/delete"
          onsubmit="return confirm('<?= t('confirm.delete') ?>')">
        <?= csrf_field() ?>
        <button type="submit" class="vcms-btn vcms-btn--danger">
            <?= t('sites.delete_btn') ?>
        </button>
    </form>
</div>

<?php $this->endSection() ?>
