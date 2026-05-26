<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('sites.create_headline') ?></h1>
    <a href="/admin/sites" class="vcms-btn vcms-btn--secondary">&larr; <?= t('action.back') ?></a>
</div>

<?php if (!empty($_SESSION['flash_error'])): ?>
<div class="vcms-alert vcms-alert--error">
    <?= e($_SESSION['flash_error']) ?>
</div>
<?php unset($_SESSION['flash_error']); endif ?>

<div class="vcms-card">
    <form method="POST" action="/admin/sites/create">
        <?= csrf_field() ?>

        <div class="vcms-form-row">
            <label class="vcms-label" for="name"><?= t('sites.field_name') ?> *</label>
            <input class="vcms-input" type="text" id="name" name="name"
                   value="<?= e($_POST['name'] ?? '') ?>"
                   maxlength="255" required>
        </div>

        <div class="vcms-form-row">
            <label class="vcms-label" for="domain"><?= t('sites.field_domain') ?> *</label>
            <input class="vcms-input" type="text" id="domain" name="domain"
                   value="<?= e($_POST['domain'] ?? '') ?>"
                   placeholder="example.com" maxlength="255" required>
            <small class="vcms-hint"><?= t('sites.hint_domain') ?></small>
        </div>

        <div class="vcms-form-row">
            <label class="vcms-label" for="www_alias"><?= t('sites.field_www_alias') ?></label>
            <input class="vcms-input" type="text" id="www_alias" name="www_alias"
                   value="<?= e($_POST['www_alias'] ?? '') ?>"
                   placeholder="www.example.com" maxlength="255">
            <small class="vcms-hint"><?= t('sites.hint_www_alias') ?></small>
        </div>

        <div class="vcms-form-row">
            <label class="vcms-label" for="db_name"><?= t('sites.field_db_name') ?> *</label>
            <input class="vcms-input" type="text" id="db_name" name="db_name"
                   value="<?= e($_POST['db_name'] ?? '') ?>"
                   placeholder="velocms_example" maxlength="64"
                   pattern="[a-zA-Z0-9_]{1,64}" required>
            <small class="vcms-hint"><?= t('sites.hint_db_name') ?></small>
        </div>

        <div class="vcms-form-actions">
            <button type="submit" class="vcms-btn vcms-btn--primary">
                <?= t('sites.create_submit') ?>
            </button>
            <a href="/admin/sites" class="vcms-btn vcms-btn--secondary"><?= t('action.cancel') ?></a>
        </div>
    </form>
</div>

<?php $this->endSection() ?>
