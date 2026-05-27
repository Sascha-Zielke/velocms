<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('booking.field_resource') ?>s</h1>
    <a href="/admin/apps/booking/resources/create" class="vcms-btn vcms-btn--primary"><?= t('action.new') ?></a>
</div>

<div class="vcms-table-wrap">
<table class="vcms-table">
    <thead><tr>
        <th><?= t('field.name') ?></th>
        <th><?= t('booking.field_template') ?></th>
        <th><?= t('field.status') ?></th>
        <th><?= t('field.actions') ?></th>
    </tr></thead>
    <tbody>
    <?php if (empty($resources)): ?>
        <tr><td colspan="4" style="text-align:center;color:#888;padding:32px"><?= t('booking.no_data') ?></td></tr>
    <?php else: ?>
        <?php foreach ($resources as $r): ?>
        <tr>
            <td><?= e($r->name) ?> <small style="color:#888">(<?= e($r->type->label()) ?>)</small></td>
            <td><?= e($r->templateKey) ?></td>
            <td>
                <span class="vcms-badge vcms-badge--<?= $r->isActive ? 'published' : 'draft' ?>">
                    <?= $r->isActive ? t('sites.status_active') : t('status.draft') ?>
                </span>
            </td>
            <td class="vcms-table-actions">
                <a href="/admin/apps/booking/resources/edit/<?= $r->id ?>" class="vcms-btn vcms-btn--sm vcms-btn--ghost"><?= t('action.edit') ?></a>
                <form method="POST" action="/admin/apps/booking/resources/delete/<?= $r->id ?>" style="display:inline"
                      onsubmit="return confirm('<?= t('action.confirm') ?>?')">
                    <?= csrf_field() ?>
                    <button class="vcms-btn vcms-btn--sm vcms-btn--danger"><?= t('action.delete') ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>
</div>

<?php $this->endSection() ?>
