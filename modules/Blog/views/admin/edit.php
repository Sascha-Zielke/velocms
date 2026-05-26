<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= $post ? t('blog.edit') : t('blog.new') ?></h1>
    <a href="/admin/blog" class="vcms-btn vcms-btn--ghost"><?= t('action.back') ?></a>
</div>

<?php $defLang = strtoupper(e($defaultLang ?? 'DE')); ?>

<!-- Language tabs -->
<?php if ($post && !empty($targetLangs)): ?>
<div class="vcms-edit-tabs" style="display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--vcms-border,#dde3ee);padding-bottom:0">
    <button type="button" class="vcms-edit-tab is-active" data-tab="<?= e($defaultLang ?? 'de') ?>"
            style="padding:8px 16px;border:none;background:none;cursor:pointer;font-weight:600;color:var(--vcms-accent,#3b6bdb);border-bottom:2px solid var(--vcms-accent,#3b6bdb);margin-bottom:-2px">
        <?= $defLang ?>
    </button>
    <?php foreach ($targetLangs as $lang): ?>
    <?php $hasTrans = !empty($translations[$lang]['title']); ?>
    <button type="button" class="vcms-edit-tab" data-tab="<?= e($lang) ?>"
            style="padding:8px 16px;border:none;background:none;cursor:pointer;font-weight:600;color:var(--vcms-muted,#6b7280);margin-bottom:-2px">
        <?= strtoupper(e($lang)) ?>
        <?php if (!$hasTrans): ?>
        <span style="font-size:10px;vertical-align:middle;color:var(--vcms-warning,#d97706)">●</span>
        <?php endif ?>
    </button>
    <?php endforeach ?>
</div>
<?php endif ?>

<form method="POST" action="<?= $post ? '/admin/blog/update/' . (int)$post['id'] : '/admin/blog/save' ?>">
<?= csrf_field() ?>
<div class="vcms-page-meta">

    <!-- ── Default language panel ──────────────────────────────────── -->
    <div class="vcms-tab-panel" id="tab-<?= e($defaultLang ?? 'de') ?>">
        <div class="vcms-form-row">
            <div class="vcms-field">
                <label><?= t('field.title') ?> (<?= $defLang ?>) *</label>
                <input type="text" name="title" value="<?= e($post['title'] ?? '') ?>" required
                       oninput="autoSlug(this.value)">
            </div>
            <div class="vcms-field">
                <label><?= t('field.slug') ?></label>
                <input type="text" name="slug" id="post-slug" value="<?= e($post['slug'] ?? '') ?>" required>
            </div>
        </div>
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
        <div class="vcms-field">
            <label><?= t('blog.excerpt') ?> (<?= $defLang ?>)</label>
            <textarea name="excerpt" rows="3"><?= e($post['excerpt'] ?? '') ?></textarea>
        </div>
        <div class="vcms-field">
            <label><?= t('blog.content') ?> (<?= $defLang ?> — HTML)</label>
            <textarea name="content" rows="12"><?= e($post['content'] ?? '') ?></textarea>
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
    </div>

    <!-- ── Translation panels (one per target language) ────────────── -->
    <?php foreach ($targetLangs as $lang): ?>
    <?php $tr = $translations[$lang] ?? []; ?>
    <div class="vcms-tab-panel" id="tab-<?= e($lang) ?>" hidden>
        <?php if (empty($tr['title'])): ?>
        <p style="color:var(--vcms-muted,#6b7280);font-size:13px;margin-bottom:16px">
            <?= t('translation.status_missing') ?> — <?= t('blog.trans_notice') ?>
        </p>
        <?php else: ?>
        <p style="color:var(--vcms-muted,#6b7280);font-size:13px;margin-bottom:16px">
            <?= t('blog.trans_notice') ?>
        </p>
        <?php endif ?>
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
            <textarea name="trans[<?= e($lang) ?>][content]" rows="12"><?= e($tr['content'] ?? '') ?></textarea>
        </div>
    </div>
    <?php endforeach ?>

    <div class="vcms-form-actions">
        <a href="/admin/blog" class="vcms-btn vcms-btn--ghost"><?= t('action.cancel') ?></a>
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
    </div>
</div>
</form>

<script>
(function () {
    var tabs   = document.querySelectorAll('.vcms-edit-tab');
    var panels = document.querySelectorAll('.vcms-tab-panel');

    function activate(lang) {
        tabs.forEach(function (btn) {
            var active = btn.dataset.tab === lang;
            btn.classList.toggle('is-active', active);
            btn.style.color       = active ? 'var(--vcms-accent,#3b6bdb)' : 'var(--vcms-muted,#6b7280)';
            btn.style.borderBottom = active ? '2px solid var(--vcms-accent,#3b6bdb)' : '2px solid transparent';
        });
        panels.forEach(function (panel) {
            panel.hidden = panel.id !== 'tab-' + lang;
        });
    }

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () { activate(btn.dataset.tab); });
    });

    function autoSlug(title) {
        var slugField = document.getElementById('post-slug');
        if (slugField && !slugField.dataset.locked) {
            slugField.value = title.toLowerCase()
                .replace(/[äöüß]/g, function(c) { return {ä:'ae',ö:'oe',ü:'ue',ß:'ss'}[c]; })
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '');
        }
    }
    window.autoSlug = autoSlug;

    var slugField = document.getElementById('post-slug');
    if (slugField) {
        slugField.addEventListener('focus', function () {
            if (this.value) this.dataset.locked = '1';
        });
    }
})();
</script>
<?php $this->endSection() ?>
