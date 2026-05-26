<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('translation.headline') ?> — <?= t('translation.dashboard') ?></h1>
    <div style="display:flex;gap:8px">
        <a href="/admin/apps/translation/editor" class="vcms-btn vcms-btn--ghost"><?= t('translation.editor') ?></a>
        <a href="/admin/apps/translation/settings" class="vcms-btn vcms-btn--ghost"><?= t('translation.settings') ?></a>
    </div>
</div>

<?php if (empty($targetLangs)): ?>
<div class="vcms-card" style="padding:32px;text-align:center;color:var(--vcms-muted);margin-top:24px">
    <p style="margin:0"><?= t('translation.no_data') ?></p>
</div>
<?php else: ?>

<div class="vcms-table-wrap" style="margin-top:24px">
<table class="vcms-table">
    <thead><tr>
        <th><?= t('field.language') ?></th>
        <th><?= t('translation.progress_fields') ?></th>
        <th><?= t('translation.progress_auto') ?></th>
        <th><?= t('translation.progress_manual') ?></th>
        <th><?= t('translation.progress_stale') ?></th>
        <th></th>
    </tr></thead>
    <tbody>
    <?php foreach ($targetLangs as $lang):
        $s = $stats[$lang] ?? ['total' => 0, 'auto_ok' => 0, 'manual_ok' => 0, 'stale' => 0];
        $total = (int) $s['total'];
        $pct   = $total > 0 ? round(($s['auto_ok'] + $s['manual_ok']) / $total * 100) : 0;
    ?>
    <tr>
        <td><strong><?= strtoupper(e($lang)) ?></strong>
            <?php if (isset($s['total']) && $total > 0): ?>
            <span style="font-size:12px;color:var(--vcms-muted);margin-left:6px"><?= $pct ?>%</span>
            <?php endif ?>
        </td>
        <td><?= (int) $s['total'] ?></td>
        <td><?= (int) $s['auto_ok'] ?></td>
        <td><?= (int) $s['manual_ok'] ?></td>
        <td>
            <?php if ((int) $s['stale'] > 0): ?>
            <span class="vcms-badge vcms-badge--draft"><?= (int) $s['stale'] ?></span>
            <?php else: ?>
            <span style="color:var(--vcms-muted)">—</span>
            <?php endif ?>
        </td>
        <td>
            <a href="/admin/apps/translation/editor?lang=<?= e($lang) ?>"
               class="vcms-btn vcms-btn--sm vcms-btn--ghost"><?= t('translation.editor') ?></a>
        </td>
    </tr>
    <?php endforeach ?>
    </tbody>
</table>
</div>

<?php if ($stats && array_sum(array_column($stats, 'total')) === 0): ?>
<div class="vcms-card" style="padding:24px;margin-top:24px;color:var(--vcms-muted)">
    <strong>Hinweis:</strong> Noch keine Übersetzungen in der Datenbank.
    Beim nächsten Speichern eines Blog-Beitrags oder Nav-Eintrags werden automatisch Übersetzungen erzeugt
    (sofern DEEPL_API_KEY oder ANTHROPIC_API_KEY in .env konfiguriert ist).
</div>
<?php endif ?>

<?php endif ?>

<?php $this->endSection() ?>
