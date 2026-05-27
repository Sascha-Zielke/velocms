<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('translation.headline') ?> — <?= t('translation.glossary') ?></h1>
    <div style="display:flex;gap:8px">
        <a href="/admin/apps/translation" class="vcms-btn vcms-btn--ghost"><?= t('action.back') ?></a>
    </div>
</div>

<p style="color:var(--vcms-muted);font-size:13px;margin-bottom:24px"><?= t('translation.glossary_hint') ?></p>

<!-- Add entry form -->
<div class="vcms-card" style="padding:20px;margin-bottom:28px">
    <h3 style="margin:0 0 16px"><?= t('translation.glossary_new') ?></h3>
    <form method="POST" action="/admin/apps/translation/glossary/save">
        <?= csrf_field() ?>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:10px;align-items:end">
            <div class="vcms-field" style="margin:0">
                <label><?= t('translation.glossary_source_lang') ?></label>
                <select name="source_lang">
                    <option value="<?= e($defaultLang) ?>" selected><?= strtoupper(e($defaultLang)) ?></option>
                </select>
            </div>
            <div class="vcms-field" style="margin:0">
                <label><?= t('translation.glossary_target_lang') ?></label>
                <select name="target_lang">
                    <?php foreach ($targetLangs as $lang): ?>
                    <option value="<?= e($lang) ?>"><?= strtoupper(e($lang)) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="vcms-field" style="margin:0">
                <label><?= t('translation.glossary_source') ?></label>
                <input type="text" name="source_term" required placeholder="z.B. VeloCMS">
            </div>
            <div class="vcms-field" style="margin:0">
                <label><?= t('translation.glossary_target') ?></label>
                <input type="text" name="target_term" required placeholder="z.B. VeloCMS">
            </div>
            <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
        </div>
    </form>
</div>

<!-- Glossary list -->
<?php if (empty($entries)): ?>
<div class="vcms-card" style="padding:32px;text-align:center;color:var(--vcms-muted)">
    <p style="margin:0"><?= t('translation.glossary_empty') ?></p>
</div>
<?php else: ?>
<div class="vcms-table-wrap">
<table class="vcms-table">
    <thead><tr>
        <th><?= t('translation.glossary_source_lang') ?></th>
        <th><?= t('translation.glossary_target_lang') ?></th>
        <th><?= t('translation.glossary_source') ?></th>
        <th><?= t('translation.glossary_target') ?></th>
        <th><?= t('field.actions') ?></th>
    </tr></thead>
    <tbody>
    <?php foreach ($entries as $entry): ?>
    <tr>
        <td><span class="vcms-badge"><?= strtoupper(e($entry['source_lang'])) ?></span></td>
        <td><span class="vcms-badge"><?= strtoupper(e($entry['target_lang'])) ?></span></td>
        <td><?= e($entry['source_term']) ?></td>
        <td><?= e($entry['target_term']) ?></td>
        <td>
            <form method="POST" action="/admin/apps/translation/glossary/delete/<?= (int)$entry['id'] ?>"
                  style="display:inline"
                  onsubmit="return confirm('<?= t('action.confirm') ?>?')">
                <?= csrf_field() ?>
                <button type="submit" class="vcms-btn vcms-btn--sm vcms-btn--ghost"
                        style="color:var(--vcms-danger,#dc2626)"><?= t('action.delete') ?></button>
            </form>
        </td>
    </tr>
    <?php endforeach ?>
    </tbody>
</table>
</div>
<?php endif ?>

<?php $this->endSection() ?>
