<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('booking.headline') ?> #<?= $booking->id ?></h1>
    <a href="/admin/apps/booking" class="vcms-btn vcms-btn--ghost"><?= t('action.back') ?></a>
</div>

<div class="vcms-card" style="max-width:640px">
    <dl class="vcms-definition-list">
        <dt><?= t('booking.field_customer_name') ?></dt>
        <dd><?= e($booking->customerName) ?></dd>

        <dt><?= t('booking.field_customer_email') ?></dt>
        <dd><?= e($booking->customerEmail) ?></dd>

        <?php if ($booking->customerPhone !== null): ?>
        <dt><?= t('booking.field_customer_phone') ?></dt>
        <dd><?= e($booking->customerPhone) ?></dd>
        <?php endif ?>

        <dt><?= t('booking.field_resource') ?></dt>
        <dd><?= $resource !== null ? e($resource->name) : '—' ?></dd>

        <dt><?= t('booking.field_start_at') ?></dt>
        <dd><?= e($booking->range->startUtc()) ?> UTC</dd>

        <dt><?= t('booking.field_end_at') ?></dt>
        <dd><?= e($booking->range->endUtc()) ?> UTC</dd>

        <dt><?= t('booking.field_status') ?></dt>
        <dd><span class="vcms-badge vcms-badge--<?= e($booking->status->value) ?>"><?= e($booking->status->label()) ?></span></dd>

        <?php if ($booking->notes !== null): ?>
        <dt><?= t('booking.field_notes') ?></dt>
        <dd><?= e($booking->notes) ?></dd>
        <?php endif ?>
    </dl>

    <div class="vcms-form-actions" style="margin-top:24px">
        <?php if ($booking->isPending()): ?>
        <form method="POST" action="/admin/apps/booking/confirm/<?= $booking->id ?>" style="display:inline">
            <?= csrf_field() ?>
            <button class="vcms-btn vcms-btn--primary"><?= t('booking.action_confirm') ?></button>
        </form>
        <?php endif ?>

        <?php if (!$booking->isCanceled()): ?>
        <form method="POST" action="/admin/apps/booking/cancel/<?= $booking->id ?>" style="display:inline"
              onsubmit="return confirm('<?= t('action.confirm') ?>?')">
            <?= csrf_field() ?>
            <button class="vcms-btn vcms-btn--danger"><?= t('booking.action_cancel') ?></button>
        </form>
        <?php endif ?>
    </div>
</div>

<?php $this->endSection() ?>
