<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('contact.admin_settings') ?></h1>
    <a href="/admin/contact" class="vcms-btn vcms-btn--ghost">&larr; <?= t('action.back') ?></a>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="vcms-alert vcms-alert--success"><?= e($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']) ?>
<?php endif ?>

<form method="POST" action="/admin/contact/settings/save" style="max-width:640px;">
    <?= csrf_field() ?>

    <div class="vcms-settings-section">
        <h2><?= t('contact.settings_section_email') ?></h2>

        <div class="vcms-form-group">
            <label class="vcms-label" for="contact_recipient_email">
                <?= t('contact.settings_recipient_email') ?>
            </label>
            <input class="vcms-input" type="email" id="contact_recipient_email"
                   name="contact_recipient_email"
                   value="<?= e($settings['contact_recipient_email'] ?? '') ?>"
                   placeholder="info@beispiel.de"
                   maxlength="255">
            <p class="vcms-hint"><?= t('contact.settings_recipient_hint') ?></p>
        </div>

        <div class="vcms-form-group">
            <label class="vcms-label" for="contact_from_name">
                <?= t('contact.settings_from_name') ?>
            </label>
            <input class="vcms-input" type="text" id="contact_from_name"
                   name="contact_from_name"
                   value="<?= e($settings['contact_from_name'] ?? 'Kontaktformular') ?>"
                   maxlength="255">
        </div>

        <div class="vcms-form-group">
            <label class="vcms-label" for="contact_subject_prefix">
                <?= t('contact.settings_subject_prefix') ?>
            </label>
            <input class="vcms-input" type="text" id="contact_subject_prefix"
                   name="contact_subject_prefix"
                   value="<?= e($settings['contact_subject_prefix'] ?? '[Kontakt]') ?>"
                   maxlength="100">
        </div>
    </div>

    <div class="vcms-settings-section">
        <h2><?= t('contact.settings_section_spam') ?></h2>

        <div class="vcms-form-group">
            <label class="vcms-label" for="contact_rate_limit">
                <?= t('contact.settings_rate_limit') ?>
            </label>
            <input class="vcms-input" type="number" id="contact_rate_limit"
                   name="contact_rate_limit"
                   value="<?= (int)($settings['contact_rate_limit'] ?? 3) ?>"
                   min="1" max="100" style="width:100px;">
            <p class="vcms-hint"><?= t('contact.settings_rate_limit_hint') ?></p>
        </div>
    </div>

    <div class="vcms-settings-section">
        <h2><?= t('contact.settings_section_dsgvo') ?></h2>

        <div class="vcms-form-group">
            <label class="vcms-checkbox-label">
                <input type="checkbox" name="contact_store_messages" value="1"
                       <?= ($settings['contact_store_messages'] ?? '1') === '1' ? 'checked' : '' ?>>
                <?= t('contact.settings_store_messages') ?>
            </label>
        </div>

        <div class="vcms-form-group">
            <label class="vcms-label" for="contact_retention_days">
                <?= t('contact.settings_retention_days') ?>
            </label>
            <input class="vcms-input" type="number" id="contact_retention_days"
                   name="contact_retention_days"
                   value="<?= (int)($settings['contact_retention_days'] ?? 90) ?>"
                   min="1" max="3650" style="width:120px;">
            <p class="vcms-hint"><?= t('contact.settings_retention_hint') ?></p>
        </div>

        <div class="vcms-form-group">
            <label class="vcms-label" for="contact_privacy_url">
                <?= t('contact.settings_privacy_url') ?>
            </label>
            <input class="vcms-input" type="text" id="contact_privacy_url"
                   name="contact_privacy_url"
                   value="<?= e($settings['contact_privacy_url'] ?? '/datenschutz') ?>"
                   maxlength="500"
                   placeholder="/datenschutz">
        </div>
    </div>

    <div class="vcms-form-group">
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
    </div>

</form>

<style>
.vcms-settings-section { margin-bottom:32px; }
.vcms-settings-section h2 { font-size:1rem;font-weight:700;color:#333;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #e2e8f0; }
.vcms-form-group { margin-bottom:18px; }
.vcms-label { display:block;font-weight:600;margin-bottom:5px;font-size:.875rem; }
.vcms-input { width:100%;max-width:100%;box-sizing:border-box;padding:8px 12px;border:1px solid #ccc;border-radius:4px;font-size:.95rem; }
.vcms-hint { font-size:.8rem;color:#777;margin-top:4px; }
.vcms-checkbox-label { display:flex;gap:8px;align-items:flex-start;font-size:.9rem;cursor:pointer; }
.vcms-checkbox-label input { margin-top:2px; }
</style>

<?php $this->endSection() ?>
