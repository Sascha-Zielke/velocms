<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= $post ? t('blog.edit') : t('blog.new') ?></h1>
    <a href="/admin/blog" class="vcms-btn vcms-btn--ghost"><?= t('action.back') ?></a>
</div>

<form method="POST" action="<?= $post ? '/admin/blog/update/' . (int)$post['id'] : '/admin/blog/save' ?>">
<?= csrf_field() ?>
<div class="vcms-page-meta">

    <!-- Title + Slug -->
    <div class="vcms-form-row">
        <div class="vcms-field">
            <label><?= t('field.title') ?> (<?= strtoupper(e($defaultLang ?? 'DE')) ?>) *</label>
            <input type="text" name="title" value="<?= e($post['title'] ?? '') ?>" required
                   oninput="autoSlug(this.value)">
        </div>
        <div class="vcms-field">
            <label><?= t('field.slug') ?></label>
            <input type="text" name="slug" id="post-slug" value="<?= e($post['slug'] ?? '') ?>" required>
        </div>
    </div>

    <!-- Status + Cover -->
    <div class="vcms-form-row">
        <div class="vcms-field">
            <label><?= t('field.status') ?></label>
            <select name="status">
                <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                <option value="<?= $s ?>" <?= ($post['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= t('status.' . $s) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="vcms-field">
            <label><?= t('blog.cover_image') ?></label>
            <input type="text" name="cover_image" value="<?= e($post['cover_image'] ?? '') ?>" placeholder="/uploads/2026/05/...">
        </div>
    </div>

    <!-- Excerpt (default lang) -->
    <div class="vcms-field">
        <label><?= t('blog.excerpt') ?> (<?= strtoupper(e($defaultLang ?? 'DE')) ?>)</label>
        <textarea name="excerpt" rows="3"><?= e($post['excerpt'] ?? '') ?></textarea>
    </div>

    <!-- Content (default lang) -->
    <div class="vcms-field">
        <label><?= t('blog.content') ?> (<?= strtoupper(e($defaultLang ?? 'DE')) ?> — HTML)</label>
        <textarea name="content" rows="12"><?= e($post['content'] ?? '') ?></textarea>
    </div>

    <!-- SEO -->
    <div class="vcms-form-row">
        <div class="vcms-field">
            <label><?= t('field.meta_title') ?></label>
            <input type="text" name="meta_title" value="<?= e($post['meta_title'] ?? '') ?>" maxlength="255">
        </div>
        <div class="vcms-field">
            <label><?= t('field.meta_description') ?></label>
            <input type="text" name="meta_description" value="<?= e($post['meta_description'] ?? '') ?>" maxlength="320">
        </div>
    </div>

    <?php if (!empty($targetLangs) && $post): ?>
    <!-- ── Translation sections per target language ──────────────────── -->
    <hr style="margin:32px 0;border-color:var(--vcms-border,#dde3ee)">
    <h2 style="font-size:16px;font-weight:600;margin-bottom:16px"><?= t('blog.translations') ?></h2>

    <?php foreach ($targetLangs as $lang): ?>
    <?php $tr = $translations[$lang] ?? []; ?>
    <details style="margin-bottom:20px;border:1px solid var(--vcms-border,#dde3ee);border-radius:8px;padding:0">
        <summary style="padding:12px 16px;cursor:pointer;font-weight:600;user-select:none">
            <?= strtoupper(e($lang)) ?>
            <?php if (!empty($tr['title'])): ?>
            <span style="font-weight:400;color:var(--vcms-muted,#6b7280);font-size:13px;margin-left:8px">
                — <?= t('blog.trans_notice') ?>
            </span>
            <?php else: ?>
            <span style="font-weight:400;color:var(--vcms-warning,#d97706);font-size:13px;margin-left:8px">
                — <?= t('translation.status_missing') ?>
            </span>
            <?php endif ?>
        </summary>
        <div style="padding:16px;border-top:1px solid var(--vcms-border,#dde3ee)">
            <div class="vcms-field" style="margin-bottom:12px">
                <label><?= t('field.title') ?> (<?= strtoupper(e($lang)) ?>)</label>
                <input type="text" name="trans[<?= e($lang) ?>][title]"
                       value="<?= e($tr['title'] ?? '') ?>">
            </div>
            <div class="vcms-field" style="margin-bottom:12px">
                <label><?= t('blog.excerpt') ?> (<?= strtoupper(e($lang)) ?>)</label>
                <textarea name="trans[<?= e($lang) ?>][excerpt]" rows="3"><?= e($tr['excerpt'] ?? '') ?></textarea>
            </div>
            <div class="vcms-field">
                <label><?= t('blog.content') ?> (<?= strtoupper(e($lang)) ?> — HTML)</label>
                <textarea name="trans[<?= e($lang) ?>][content]" rows="10"><?= e($tr['content'] ?? '') ?></textarea>
            </div>
        </div>
    </details>
    <?php endforeach ?>
    <?php endif ?>

    <div class="vcms-form-actions">
        <a href="/admin/blog" class="vcms-btn vcms-btn--ghost"><?= t('action.cancel') ?></a>
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
    </div>
</div>
</form>

<script>
function autoSlug(title) {
    const slugField = document.getElementById('post-slug');
    if (slugField.dataset.locked) return;
    slugField.value = title.toLowerCase()
        .replace(/[äöüß]/g, c => ({ä:'ae',ö:'oe',ü:'ue',ß:'ss'}[c]))
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');
}
document.getElementById('post-slug').addEventListener('focus', function() {
    if (this.value) this.dataset.locked = '1';
});
</script>
<?php $this->endSection() ?>
