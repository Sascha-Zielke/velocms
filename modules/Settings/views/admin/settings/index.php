<?php declare(strict_types=1); ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('nav.settings') ?></h1>
</div>

<form method="POST" action="/admin/settings" class="vcms-form">
    <?= csrf_field() ?>

    <?php $s = fn(string $k): string => e($settings[$k] ?? '') ?>

    <!-- ── Site ──────────────────────────────────────────── -->
    <fieldset class="vcms-fieldset">
        <legend><?= t('settings.section_site') ?></legend>

        <div class="vcms-form-group">
            <label for="site_name"><?= t('settings.site_name') ?></label>
            <input type="text" id="site_name" name="site_name" value="<?= $s('site_name') ?>" class="vcms-input">
        </div>

        <div class="vcms-form-group">
            <label for="site_tagline"><?= t('settings.site_tagline') ?></label>
            <input type="text" id="site_tagline" name="site_tagline" value="<?= $s('site_tagline') ?>" class="vcms-input">
        </div>

        <div class="vcms-form-group">
            <label for="site_email"><?= t('settings.site_email') ?></label>
            <input type="email" id="site_email" name="site_email" value="<?= $s('site_email') ?>" class="vcms-input">
        </div>

        <div class="vcms-form-group">
            <label for="homepage_slug"><?= t('settings.homepage_slug') ?></label>
            <input type="text" id="homepage_slug" name="homepage_slug" value="<?= $s('homepage_slug') ?>"
                   class="vcms-input" placeholder="startseite">
            <small class="vcms-hint"><?= t('settings.homepage_slug_hint') ?></small>
        </div>

        <div class="vcms-form-group">
            <label for="maintenance_mode"><?= t('settings.maintenance_mode') ?></label>
            <select id="maintenance_mode" name="maintenance_mode" class="vcms-input">
                <option value="0" <?= ($settings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' ?>>Aus</option>
                <option value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' ?>>An</option>
            </select>
        </div>
    </fieldset>

    <!-- ── Branding ──────────────────────────────────────── -->
    <fieldset class="vcms-fieldset">
        <legend><?= t('settings.section_branding') ?></legend>

        <div class="vcms-form-group">
            <label for="logo_path"><?= t('settings.logo_path') ?></label>
            <input type="text" id="logo_path" name="logo_path" value="<?= $s('logo_path') ?>"
                   class="vcms-input" placeholder="/uploads/logo.png">
        </div>

        <div class="vcms-form-group">
            <label for="favicon_path"><?= t('settings.favicon_path') ?></label>
            <input type="text" id="favicon_path" name="favicon_path" value="<?= $s('favicon_path') ?>"
                   class="vcms-input" placeholder="/uploads/favicon.ico">
        </div>
    </fieldset>

    <!-- ── SEO ───────────────────────────────────────────── -->
    <fieldset class="vcms-fieldset">
        <legend><?= t('settings.section_seo') ?></legend>

        <div class="vcms-form-group">
            <label for="meta_title_suffix"><?= t('settings.meta_title_suffix') ?></label>
            <input type="text" id="meta_title_suffix" name="meta_title_suffix"
                   value="<?= $s('meta_title_suffix') ?>" class="vcms-input" placeholder=" | Meine Website">
        </div>

        <div class="vcms-form-group">
            <label for="meta_description_default"><?= t('settings.meta_description_default') ?></label>
            <textarea id="meta_description_default" name="meta_description_default"
                      rows="2" class="vcms-input"><?= $s('meta_description_default') ?></textarea>
        </div>

        <div class="vcms-form-group">
            <label for="meta_keywords_default"><?= t('settings.meta_keywords_default') ?></label>
            <input type="text" id="meta_keywords_default" name="meta_keywords_default"
                   value="<?= $s('meta_keywords_default') ?>" class="vcms-input">
        </div>
    </fieldset>

    <!-- ── Social ────────────────────────────────────────── -->
    <fieldset class="vcms-fieldset">
        <legend><?= t('settings.section_social') ?></legend>

        <?php foreach (['facebook', 'instagram', 'linkedin', 'twitter'] as $network): ?>
        <div class="vcms-form-group">
            <label for="social_<?= $network ?>"><?= ucfirst($network) ?></label>
            <input type="url" id="social_<?= $network ?>" name="social_<?= $network ?>"
                   value="<?= $s('social_' . $network) ?>" class="vcms-input" placeholder="https://...">
        </div>
        <?php endforeach ?>
    </fieldset>

    <!-- ── Footer ────────────────────────────────────────── -->
    <fieldset class="vcms-fieldset">
        <legend><?= t('settings.section_footer') ?></legend>

        <div class="vcms-form-group">
            <label for="footer_text"><?= t('settings.footer_text') ?></label>
            <input type="text" id="footer_text" name="footer_text"
                   value="<?= $s('footer_text') ?>" class="vcms-input">
        </div>

        <div class="vcms-form-group">
            <label for="footer_impressum_url"><?= t('settings.footer_impressum_url') ?></label>
            <input type="text" id="footer_impressum_url" name="footer_impressum_url"
                   value="<?= $s('footer_impressum_url') ?>" class="vcms-input" placeholder="/impressum">
        </div>

        <div class="vcms-form-group">
            <label for="footer_datenschutz_url"><?= t('settings.footer_datenschutz_url') ?></label>
            <input type="text" id="footer_datenschutz_url" name="footer_datenschutz_url"
                   value="<?= $s('footer_datenschutz_url') ?>" class="vcms-input" placeholder="/datenschutz">
        </div>
    </fieldset>

    <div class="vcms-form-actions">
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
    </div>
</form>

<?php $this->endSection() ?>
