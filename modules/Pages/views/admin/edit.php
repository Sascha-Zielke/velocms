<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= $page ? t('pages.edit') . ': ' . e($page['title']) : t('pages.new') ?></h1>
    <a href="/admin/pages" class="vcms-btn vcms-btn--ghost"><?= t('action.back') ?></a>
</div>

<!-- Page Meta Form -->
<form method="POST" action="/admin/pages/save" class="vcms-form vcms-card">
    <?= csrf_field() ?>
    <?php if ($page): ?>
    <input type="hidden" name="id" value="<?= (int)$page['id'] ?>">
    <?php endif ?>

    <div class="vcms-form-row vcms-form-row--2col">
        <div class="vcms-form-group">
            <label class="vcms-label"><?= t('field.title') ?> *</label>
            <input type="text" name="title" class="vcms-input" required
                   value="<?= e($page['title'] ?? '') ?>"
                   oninput="autoSlug(this.value)">
        </div>
        <div class="vcms-form-group">
            <label class="vcms-label"><?= t('field.title_en') ?></label>
            <input type="text" name="title_en" class="vcms-input"
                   value="<?= e($page['title_en'] ?? '') ?>">
        </div>
    </div>

    <div class="vcms-form-row vcms-form-row--2col">
        <div class="vcms-form-group">
            <label class="vcms-label"><?= t('field.slug') ?> *</label>
            <div class="vcms-input-prefix">
                <span>/</span>
                <input type="text" name="slug" id="slug" class="vcms-input" required
                       value="<?= e($page['slug'] ?? '') ?>"
                       pattern="[a-z0-9\-]+"
                       title="Nur Kleinbuchstaben, Zahlen und Bindestriche">
            </div>
        </div>
        <div class="vcms-form-group">
            <label class="vcms-label"><?= t('field.status') ?></label>
            <select name="status" class="vcms-select">
                <option value="draft" <?= ($page['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>
                    <?= t('status.draft') ?>
                </option>
                <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>
                    <?= t('status.published') ?>
                </option>
            </select>
        </div>
    </div>

    <div class="vcms-form-group">
        <label class="vcms-label"><?= t('field.meta_title') ?></label>
        <input type="text" name="meta_title" class="vcms-input"
               value="<?= e($page['meta_title'] ?? '') ?>" maxlength="255">
    </div>
    <div class="vcms-form-group">
        <label class="vcms-label"><?= t('field.meta_description') ?></label>
        <textarea name="meta_description" class="vcms-textarea" rows="2" maxlength="500"><?= e($page['meta_description'] ?? '') ?></textarea>
    </div>

    <div class="vcms-form-actions">
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
    </div>
</form>

<?php if ($page): ?>
<!-- Visual Editor -->
<div class="vcms-editor" id="vcms-editor" data-page-id="<?= (int)$page['id'] ?>">
    <div class="vcms-editor-header">
        <h2><?= t('editor.headline') ?></h2>
        <button class="vcms-btn vcms-btn--primary" id="btn-add-section"><?= t('editor.add_section') ?></button>
    </div>

    <div id="sections-container">
    <?php foreach ($sections as $section): ?>
        <div class="vcms-section-block" data-section-id="<?= (int)$section['id'] ?>">
            <div class="vcms-section-toolbar">
                <span class="vcms-section-label"><?= t('editor.section') ?> #<?= (int)$section['id'] ?></span>
                <div class="vcms-section-settings">
                    <label><?= t('editor.overlay') ?>:
                        <input type="range" min="0" max="100" value="<?= (int)($section['settings']['overlay'] ?? 0) ?>"
                               class="vcms-overlay-range" data-section-id="<?= (int)$section['id'] ?>">
                        <span class="vcms-overlay-val"><?= (int)($section['settings']['overlay'] ?? 0) ?></span>%
                    </label>
                    <label><?= t('editor.bg_color') ?>:
                        <input type="color" value="#<?= e($section['settings']['bg_color'] ?? 'ffffff') ?>"
                               class="vcms-bg-color" data-section-id="<?= (int)$section['id'] ?>">
                    </label>
                    <select class="vcms-padding-select" data-section-id="<?= (int)$section['id'] ?>">
                        <?php foreach (['none','sm','md','lg'] as $p): ?>
                        <option value="<?= $p ?>" <?= ($section['settings']['padding'] ?? 'md') === $p ? 'selected' : '' ?>>
                            Padding: <?= $p ?>
                        </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <button class="vcms-btn vcms-btn--sm vcms-btn--ghost btn-add-row"
                        data-section-id="<?= (int)$section['id'] ?>"><?= t('editor.add_row') ?></button>
                <button class="vcms-btn vcms-btn--sm vcms-btn--danger btn-delete-section"
                        data-section-id="<?= (int)$section['id'] ?>"><?= t('action.delete') ?></button>
            </div>

            <div class="vcms-rows-container">
            <?php foreach ($section['rows'] as $row): ?>
                <div class="vcms-row-block" data-row-id="<?= (int)$row['id'] ?>">
                    <div class="vcms-row-toolbar">
                        <span class="vcms-row-label"><?= t('editor.row') ?></span>
                        <div class="vcms-box-add-btns">
                            <?php foreach (['text','image','video','button','spacer'] as $boxType): ?>
                            <button class="vcms-btn vcms-btn--xs btn-add-box"
                                    data-row-id="<?= (int)$row['id'] ?>"
                                    data-type="<?= $boxType ?>">+ <?= $boxType ?></button>
                            <?php endforeach ?>
                        </div>
                        <button class="vcms-btn vcms-btn--xs vcms-btn--danger btn-delete-row"
                                data-row-id="<?= (int)$row['id'] ?>"><?= t('action.delete') ?></button>
                    </div>

                    <div class="vcms-boxes-container">
                    <?php foreach ($row['boxes'] as $box): ?>
                        <?php include __DIR__ . '/_box_editor.php'; ?>
                    <?php endforeach ?>
                    </div>
                </div>
            <?php endforeach ?>
            </div>
        </div>
    <?php endforeach ?>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const PAGE_ID = <?= (int)$page['id'] ?>;

function api(url, data = {}) {
    return fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': CSRF},
        body: JSON.stringify({_csrf: CSRF, ...data})
    }).then(r => r.json());
}

// Add section
document.getElementById('btn-add-section').addEventListener('click', () => {
    api(`/admin/pages/${PAGE_ID}/section/add`).then(r => {
        if (r.ok) location.reload();
    });
});

// Add row
document.querySelectorAll('.btn-add-row').forEach(btn => {
    btn.addEventListener('click', () => {
        const sid = btn.dataset.sectionId;
        api(`/admin/pages/section/${sid}/row/add`).then(r => {
            if (r.ok) location.reload();
        });
    });
});

// Add box
document.querySelectorAll('.btn-add-box').forEach(btn => {
    btn.addEventListener('click', () => {
        const rid = btn.dataset.rowId;
        const type = btn.dataset.type;
        api(`/admin/pages/row/${rid}/box/add`, {type}).then(r => {
            if (r.ok) location.reload();
        });
    });
});

// Delete section
document.querySelectorAll('.btn-delete-section').forEach(btn => {
    btn.addEventListener('click', () => {
        if (!confirm('<?= t('confirm.delete') ?>')) return;
        api(`/admin/pages/section/${btn.dataset.sectionId}/delete`).then(r => {
            if (r.ok) location.reload();
        });
    });
});

// Delete row
document.querySelectorAll('.btn-delete-row').forEach(btn => {
    btn.addEventListener('click', () => {
        if (!confirm('<?= t('confirm.delete') ?>')) return;
        api(`/admin/pages/row/${btn.dataset.rowId}/delete`).then(r => {
            if (r.ok) location.reload();
        });
    });
});

// Delete box
document.querySelectorAll('.btn-delete-box').forEach(btn => {
    btn.addEventListener('click', () => {
        if (!confirm('<?= t('confirm.delete') ?>')) return;
        api(`/admin/pages/box/${btn.dataset.boxId}/delete`).then(r => {
            if (r.ok) location.reload();
        });
    });
});

// Save box
document.querySelectorAll('.btn-save-box').forEach(btn => {
    btn.addEventListener('click', () => {
        const boxId = btn.dataset.boxId;
        const type  = btn.dataset.type;
        const form  = btn.closest('.vcms-box-form');
        const data  = {};
        form.querySelectorAll('[data-field]').forEach(el => {
            data[el.dataset.field] = el.value;
        });
        api(`/admin/pages/box/${boxId}/save`, {type, data}).then(r => {
            if (r.ok) btn.textContent = '✓ Gespeichert';
        });
    });
});

// Section settings (overlay, bg_color, padding)
function saveSectionSettings(sid) {
    const block = document.querySelector(`.vcms-section-block[data-section-id="${sid}"]`);
    const overlay  = block.querySelector('.vcms-overlay-range')?.value ?? 0;
    const bg_color = block.querySelector('.vcms-bg-color')?.value?.replace('#','') ?? 'ffffff';
    const padding  = block.querySelector('.vcms-padding-select')?.value ?? 'md';
    api(`/admin/pages/section/${sid}/settings`, {settings: {overlay, bg_color, padding}});
}

document.querySelectorAll('.vcms-overlay-range').forEach(el => {
    el.addEventListener('input', () => {
        el.nextElementSibling.textContent = el.value;
        saveSectionSettings(el.dataset.sectionId);
    });
});
document.querySelectorAll('.vcms-bg-color').forEach(el => {
    el.addEventListener('change', () => saveSectionSettings(el.dataset.sectionId));
});
document.querySelectorAll('.vcms-padding-select').forEach(el => {
    el.addEventListener('change', () => saveSectionSettings(el.dataset.sectionId));
});

// Auto-slug from title
function autoSlug(title) {
    const slugField = document.getElementById('slug');
    if (slugField && !slugField.dataset.manual) {
        slugField.value = title.toLowerCase()
            .replace(/[äöü]/g, c => ({a:'ae',o:'oe',u:'ue'})[c] || c)
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
}
document.getElementById('slug')?.addEventListener('input', function() {
    this.dataset.manual = '1';
});
</script>
<?php endif ?>

<?php $this->endSection() ?>
