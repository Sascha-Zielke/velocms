<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('booking.headline') ?></h1>
    <a href="/admin/apps/booking/resources" class="vcms-btn vcms-btn--ghost"><?= t('booking.field_resource') ?>s</a>
</div>

<div class="vcms-filter-tabs">
    <?php foreach (['', 'pending', 'confirmed', 'canceled'] as $s): ?>
    <a href="/admin/apps/booking<?= $s !== '' ? '?status=' . e($s) : '' ?>"
       class="vcms-filter-tab<?= $filter === $s ? ' vcms-filter-tab--active' : '' ?>">
        <?= $s !== '' ? t('booking.status_' . $s) : t('contact.filter_all') ?>
    </a>
    <?php endforeach ?>
</div>

<div class="vcms-table-wrap">
<table class="vcms-table">
    <thead><tr>
        <th><?= t('booking.field_customer_name') ?></th>
        <th><?= t('booking.field_resource') ?></th>
        <th><?= t('booking.field_start_at') ?></th>
        <th><?= t('booking.field_end_at') ?></th>
        <th><?= t('booking.field_status') ?></th>
        <th><?= t('field.actions') ?></th>
    </tr></thead>
    <tbody>
    <?php if (empty($bookings)): ?>
        <tr><td colspan="6" style="text-align:center;color:#888;padding:32px"><?= t('booking.no_data') ?></td></tr>
    <?php else: ?>
        <?php
        $resourceMap = [];
        foreach ($resources as $r) { $resourceMap[$r->id] = $r; }
        ?>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?= e($b->customerName) ?></td>
            <td><?= isset($resourceMap[$b->resourceId]) ? e($resourceMap[$b->resourceId]->name) : '—' ?></td>
            <td><?= e($b->range->startUtc()) ?></td>
            <td><?= e($b->range->endUtc()) ?></td>
            <td><span class="vcms-badge vcms-badge--<?= e($b->status->value) ?>"><?= e($b->status->label()) ?></span></td>
            <td class="vcms-table-actions">
                <a href="/admin/apps/booking/detail/<?= $b->id ?>" class="vcms-btn vcms-btn--sm vcms-btn--ghost"><?= t('action.edit') ?></a>
            </td>
        </tr>
        <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>
</div>

<?php $this->endSection() ?>
