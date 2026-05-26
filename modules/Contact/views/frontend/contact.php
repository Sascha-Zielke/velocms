<?php declare(strict_types=1); ?>
<?php $this->extend('frontend') ?>
<?php $this->section('title') ?>Kontakt<?php $this->endSection() ?>
<?php $this->section('meta_description') ?>Nehmen Sie Kontakt mit uns auf.<?php $this->endSection() ?>
<?php $this->section('content') ?>

<main class="vcms-page">
    <section class="vcms-section vcms-section--pad-md" style="background-color:#ffffff;">
        <div class="vcms-container vcms-contact-wrap">

            <h1><?= t('contact.headline') ?></h1>
            <p class="contact-intro"><?= t('contact.intro') ?></p>

            <?php if ($success): ?>
                <div class="vcms-alert vcms-alert--success" role="alert">
                    <?= t('contact.success') ?>
                </div>
            <?php else: ?>

                <?php if (!empty($errors['form'])): ?>
                    <div class="vcms-alert vcms-alert--error" role="alert">
                        <?= e($errors['form']) ?>
                    </div>
                <?php endif ?>

                <form method="POST" action="/kontakt" novalidate>
                    <?= csrf_field() ?>

                    <?php /* ── Honeypot — hidden from real users, bots fill it ── */ ?>
                    <div style="display:none;visibility:hidden;position:absolute;left:-9999px;" aria-hidden="true">
                        <label for="hp_url">Website (please leave empty)</label>
                        <input type="text" id="hp_url" name="hp_url" value="" tabindex="-1" autocomplete="off">
                    </div>

                    <?php /* ── Name ── */ ?>
                    <div class="vcms-form-group<?= isset($errors['name']) ? ' vcms-form-group--error' : '' ?>">
                        <label class="vcms-label" for="contact_name">
                            <?= t('contact.field_name') ?> <span aria-hidden="true">*</span>
                        </label>
                        <input class="vcms-input"
                               type="text"
                               id="contact_name"
                               name="name"
                               value="<?= e($old['name'] ?? '') ?>"
                               maxlength="255"
                               required
                               autocomplete="name">
                        <?php if (isset($errors['name'])): ?>
                            <p class="vcms-field-error"><?= e($errors['name']) ?></p>
                        <?php endif ?>
                    </div>

                    <?php /* ── E-Mail ── */ ?>
                    <div class="vcms-form-group<?= isset($errors['email']) ? ' vcms-form-group--error' : '' ?>">
                        <label class="vcms-label" for="contact_email">
                            <?= t('contact.field_email') ?> <span aria-hidden="true">*</span>
                        </label>
                        <input class="vcms-input"
                               type="email"
                               id="contact_email"
                               name="email"
                               value="<?= e($old['email'] ?? '') ?>"
                               maxlength="255"
                               required
                               autocomplete="email">
                        <?php if (isset($errors['email'])): ?>
                            <p class="vcms-field-error"><?= e($errors['email']) ?></p>
                        <?php endif ?>
                    </div>

                    <?php /* ── Subject ── */ ?>
                    <div class="vcms-form-group<?= isset($errors['subject']) ? ' vcms-form-group--error' : '' ?>">
                        <label class="vcms-label" for="contact_subject">
                            <?= t('contact.field_subject') ?>
                        </label>
                        <input class="vcms-input"
                               type="text"
                               id="contact_subject"
                               name="subject"
                               value="<?= e($old['subject'] ?? '') ?>"
                               maxlength="255"
                               autocomplete="off">
                        <?php if (isset($errors['subject'])): ?>
                            <p class="vcms-field-error"><?= e($errors['subject']) ?></p>
                        <?php endif ?>
                    </div>

                    <?php /* ── Message ── */ ?>
                    <div class="vcms-form-group<?= isset($errors['message']) ? ' vcms-form-group--error' : '' ?>">
                        <label class="vcms-label" for="contact_message">
                            <?= t('contact.field_message') ?> <span aria-hidden="true">*</span>
                        </label>
                        <textarea class="vcms-input"
                                  id="contact_message"
                                  name="message"
                                  rows="7"
                                  maxlength="10000"
                                  required><?= e($old['message'] ?? '') ?></textarea>
                        <?php if (isset($errors['message'])): ?>
                            <p class="vcms-field-error"><?= e($errors['message']) ?></p>
                        <?php endif ?>
                    </div>

                    <?php /* ── DSGVO consent ── */ ?>
                    <?php $privacyUrl = setting('contact_privacy_url', '/datenschutz'); ?>
                    <div class="vcms-form-group vcms-form-group--checkbox<?= isset($errors['consent']) ? ' vcms-form-group--error' : '' ?>">
                        <label class="vcms-checkbox-label">
                            <input type="checkbox" name="consent" value="1"
                                   <?= !empty($old) && !empty($_POST['consent']) ? 'checked' : '' ?>>
                            <?= sprintf(
                                t('contact.consent_text'),
                                e($privacyUrl)
                            ) ?>
                        </label>
                        <?php if (isset($errors['consent'])): ?>
                            <p class="vcms-field-error"><?= e($errors['consent']) ?></p>
                        <?php endif ?>
                    </div>

                    <div class="vcms-form-group">
                        <button type="submit" class="vcms-btn vcms-btn--primary">
                            <?= t('contact.submit') ?>
                        </button>
                    </div>

                </form>

            <?php endif ?>

        </div>
    </section>
</main>

<style>
/* Contact page — supplement to frontend.css theme */
.contact-intro { color: var(--c-text-muted); font-size: var(--font-size-lg); margin: 8px 0 32px; }
.vcms-contact-wrap h1 { margin-bottom: 0; }
</style>

<?php $this->endSection() ?>
