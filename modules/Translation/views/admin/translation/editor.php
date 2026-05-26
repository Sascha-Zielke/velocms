<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('translation.headline') ?> — <?= t('translation.editor') ?></h1>
    <a href="/admin/apps/translation" class="vcms-btn vcms-btn--ghost"><?= t('translation.dashboard') ?></a>
</div>

<?php
$qs = fn(array $extra = []) => http_build_query(array_merge(
    ['lang' => $lang, 'table' => $table, 'source' => $source, 'page' => $page],
    $extra
));
?>

<!-- Filters -->
<form method="GET" action="/admin/apps/translation/editor" style="display:flex;gap:10px;flex-wrap:wrap;margin:20px 0">
    <select name="lang" class="vcms-input" style="width:auto">
        <?php foreach ($targetLangs as $l): ?>
        <option value="<?= e($l) ?>"<?= $l === $lang ? ' selected' : '' ?>><?= strtoupper(e($l)) ?></option>
        <?php endforeach ?>
    </select>

    <select name="table" class="vcms-input" style="width:auto">
        <option value=""><?= t('translation.col_table') ?>: <?= t('translation.filter_all') ?></option>
        <?php foreach ($tables as $tbl): ?>
        <option value="<?= e($tbl) ?>"<?= $tbl === $table ? ' selected' : '' ?>><?= e($tbl) ?></option>
        <?php endforeach ?>
    </select>

    <select name="source" class="vcms-input" style="width:auto">
        <option value=""><?= t('translation.filter_all') ?></option>
        <option value="auto"  <?= $source === 'auto'   ? 'selected' : '' ?>><?= t('translation.source_auto') ?></option>
        <option value="manual"<?= $source === 'manual' ? 'selected' : '' ?>><?= t('translation.source_manual') ?></option>
        <option value="stale" <?= $source === 'stale'  ? 'selected' : '' ?>><?= t('translation.filter_stale') ?></option>
    </select>

    <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.search') ?></button>
</form>

<!-- Table -->
<?php if (empty($rows)): ?>
<div class="vcms-card" style="padding:32px;text-align:center;color:var(--vcms-muted)">
    <?= t('translation.no_data') ?>
</div>
<?php else: ?>

<div class="vcms-table-wrap">
<table class="vcms-table">
    <thead><tr>
        <th><?= t('translation.col_table') ?></th>
        <th><?= t('translation.col_field') ?></th>
        <th><?= t('field.status') ?></th>
        <th><?= t('translation.col_value') ?></th>
        <th><?= t('translation.col_updated') ?></th>
        <th><?= t('field.actions') ?></th>
    </tr></thead>
    <tbody>
    <?php foreach ($rows as $row): ?>

    <tr<?= $row['id'] == $editId ? ' style="background:var(--vcms-sidebar-hover,#f5f8ff)"' : '' ?>>
        <td style="font-size:12px;color:var(--vcms-muted)"><?= e($row['table_name']) ?></td>
        <td style="font-size:12px"><?= e($row['field']) ?></td>
        <td>
            <?php if ($row['stale']): ?>
            <span class="vcms-badge vcms-badge--draft"><?= t('translation.status_stale') ?></span>
            <?php elseif ($row['source'] === 'manual'): ?>
            <span class="vcms-badge vcms-badge--published"><?= t('translation.source_manual') ?></span>
            <?php else: ?>
            <span class="vcms-badge vcms-badge--archived"><?= t('translation.source_auto') ?></span>
            <?php endif ?>
        </td>
        <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            <?= e(mb_strimwidth((string) $row['value'], 0, 100, '…')) ?>
        </td>
        <td style="font-size:12px;color:var(--vcms-muted)">
            <?= e(substr((string) $row['translated_at'], 0, 10)) ?>
        </td>
        <td class="vcms-table-actions">
            <a href="/admin/apps/translation/editor?<?= $qs(['edit' => $row['id']]) ?>"
               class="vcms-btn vcms-btn--sm vcms-btn--ghost"><?= t('action.edit') ?></a>
            <?php if ($row['source'] === 'manual'): ?>
            <form method="POST" action="/admin/apps/translation/editor/<?= (int)$row['id'] ?>/unlock"
                  style="display:inline"
                  onsubmit="return confirm('<?= t('translation.confirm_unlock') ?>')">
                <?= csrf_field() ?>
                <input type="hidden" name="_redirect_qs" value="<?= e($qs()) ?>">
                <button type="submit" class="vcms-btn vcms-btn--sm vcms-btn--ghost">
                    <?= t('translation.action_unlock') ?>
                </button>
            </form>
            <?php endif ?>
        </td>
    </tr>

    <?php if ($row['id'] == $editId): ?>
    <tr>
        <td colspan="6" style="padding:16px;background:var(--vcms-sidebar-hover,#f5f8ff)">
            <form method="POST" action="/admin/apps/translation/editor/<?= (int)$row['id'] ?>/save">
                <?= csrf_field() ?>
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px">
                    <?= t('translation.save_manual') ?> — <?= e($row['table_name']) ?>.<?= e($row['field']) ?>
                    [<?= strtoupper(e($lang)) ?>]
                </label>
                <textarea name="value" rows="4" class="vcms-input" style="width:100%;font-family:monospace"><?= e((string) $row['value']) ?></textarea>
                <div style="margin-top:8px;display:flex;gap:8px">
                    <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('translation.save_manual') ?></button>
                    <a href="/admin/apps/translation/editor?<?= e($qs()) ?>"
                       class="vcms-btn vcms-btn--ghost"><?= t('action.cancel') ?></a>
                </div>
            </form>
        </td>
    </tr>
    <?php endif ?>

    <?php endforeach ?>
    </tbody>
</table>
</div>

<!-- Pagination -->
<?php if ($pages > 1): ?>
<div style="display:flex;gap:6px;margin-top:16px;align-items:center">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
    <a href="/admin/apps/translation/editor?<?= $qs(['page' => $p]) ?>"
       class="vcms-btn vcms-btn--sm <?= $p === $page ? 'vcms-btn--primary' : 'vcms-btn--ghost' ?>">
        <?= $p ?>
    </a>
    <?php endfor ?>
    <span style="font-size:12px;color:var(--vcms-muted);margin-left:8px"><?= $total ?> <?= t('translation.entries') ?></span>
</div>
<?php endif ?>

<?php endif ?>

<?php $this->endSection() ?>
