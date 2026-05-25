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
    <div class="vcms-form-row">
        <div class="vcms-field">
            <label><?= t('field.title') ?> *</label>
            <input type="text" name="title" value="<?= e($post['title'] ?? '') ?>" required
                   oninput="autoSlug(this.value)">
        </div>
        <div class="vcms-field">
            <label><?= t('field.title_en') ?></label>
            <input type="text" name="title_en" value="<?= e($post['title_en'] ?? '') ?>">
        </div>
    </div>
    <div class="vcms-form-row">
        <div class="vcms-field">
            <label><?= t('field.slug') ?></label>
            <input type="text" name="slug" id="post-slug" value="<?= e($post['slug'] ?? '') ?>" required>
        </div>
        <div class="vcms-field">
            <label><?= t('field.status') ?></label>
            <select name="status">
                <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                <option value="<?= $s ?>" <?= ($post['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= t('status.' . $s) ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>
    <div class="vcms-field">
        <label><?= t('blog.cover_image') ?></label>
        <input type="text" name="cover_image" value="<?= e($post['cover_image'] ?? '') ?>" placeholder="/uploads/2026/05/...">
    </div>
    <div class="vcms-form-row">
        <div class="vcms-field">
            <label><?= t('blog.excerpt') ?></label>
            <textarea name="excerpt" rows="3"><?= e($post['excerpt'] ?? '') ?></textarea>
        </div>
        <div class="vcms-field">
            <label><?= t('blog.excerpt_en') ?></label>
            <textarea name="excerpt_en" rows="3"><?= e($post['excerpt_en'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="vcms-field">
        <label><?= t('blog.content') ?></label>
        <textarea name="content" rows="12"><?= e($post['content'] ?? '') ?></textarea>
    </div>
    <div class="vcms-field">
        <label><?= t('blog.content_en') ?></label>
        <textarea name="content_en" rows="12"><?= e($post['content_en'] ?? '') ?></textarea>
    </div>
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
