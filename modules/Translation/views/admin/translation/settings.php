<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('translation.headline') ?> — <?= t('translation.settings') ?></h1>
    <a href="/admin/apps/translation" class="vcms-btn vcms-btn--ghost"><?= t('translation.dashboard') ?></a>
</div>

<form method="POST" action="/admin/apps/translation/settings" style="max-width:560px;margin-top:24px">
    <?= csrf_field() ?>

    <!-- Active Languages -->
    <div class="vcms-form-group">
        <label class="vcms-label"><?= t('translation.settings_languages') ?></label>
        <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:8px">
            <?php foreach ($knownLangs as $lng): ?>
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                <input type="checkbox" name="active_languages[]" value="<?= e($lng) ?>"
                    <?= in_array($lng, $activeLangs, true) ? 'checked' : '' ?>>
                <span><?= t('lang.' . $lng) ?> (<?= strtoupper(e($lng)) ?>)</span>
            </label>
            <?php endforeach ?>
        </div>
    </div>

    <!-- Default Language -->
    <div class="vcms-form-group" style="margin-top:20px">
        <label class="vcms-label" for="default_language"><?= t('translation.settings_default') ?></label>
        <select name="default_language" id="default_language" class="vcms-input">
            <?php foreach ($knownLangs as $lng): ?>
            <option value="<?= e($lng) ?>"<?= $lng === $defaultLang ? ' selected' : '' ?>>
                <?= t('lang.' . $lng) ?> (<?= strtoupper(e($lng)) ?>)
            </option>
            <?php endforeach ?>
        </select>
    </div>

    <!-- Provider -->
    <div class="vcms-form-group" style="margin-top:20px">
        <label class="vcms-label" for="translation_provider"><?= t('translation.settings_provider') ?></label>
        <select name="translation_provider" id="translation_provider" class="vcms-input">
            <option value="deepl"     <?= $provider === 'deepl'     ? 'selected' : '' ?>>DeepL (primär)</option>
            <option value="anthropic" <?= $provider === 'anthropic' ? 'selected' : '' ?>>Anthropic Claude (Fallback)</option>
        </select>
        <p class="vcms-hint" style="margin-top:6px"><?= t('translation.settings_deepl_hint') ?></p>
        <p class="vcms-hint"><?= t('translation.settings_anth_hint') ?></p>
    </div>

    <div style="margin-top:28px">
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
    </div>
</form>

<?php $this->endSection() ?>
