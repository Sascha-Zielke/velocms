<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('contact.admin_view') ?></h1>
    <a href="/admin/contact" class="vcms-btn vcms-btn--ghost">&larr; <?= t('action.back') ?></a>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="vcms-alert vcms-alert--success"><?= e($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']) ?>
<?php endif ?>

<div class="vcms-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:28px;max-width:720px;">

    <table style="border-collapse:collapse;width:100%;margin-bottom:24px;">
        <tr>
            <th style="text-align:left;padding:8px 12px 8px 0;color:#555;font-weight:600;white-space:nowrap;width:140px;">
                <?= t('contact.field_name') ?>
            </th>
            <td style="padding:8px 0;"><?= e($message['name']) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;padding:8px 12px 8px 0;color:#555;font-weight:600;">
                <?= t('contact.field_email') ?>
            </th>
            <td style="padding:8px 0;">
                <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a>
            </td>
        </tr>
        <tr>
            <th style="text-align:left;padding:8px 12px 8px 0;color:#555;font-weight:600;">
                <?= t('contact.field_subject') ?>
            </th>
            <td style="padding:8px 0;"><?= e($message['subject'] ?: '—') ?></td>
        </tr>
        <tr>
            <th style="text-align:left;padding:8px 12px 8px 0;color:#555;font-weight:600;">
                <?= t('field.status') ?>
            </th>
            <td style="padding:8px 0;">
                <span class="vcms-badge vcms-badge--<?= e($message['status']) ?>">
                    <?= t('contact.status_' . $message['status']) ?>
                </span>
            </td>
        </tr>
        <tr>
            <th style="text-align:left;padding:8px 12px 8px 0;color:#555;font-weight:600;">
                <?= t('field.created_at') ?>
            </th>
            <td style="padding:8px 0;"><?= e($message['created_at']) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;padding:8px 12px 8px 0;color:#555;font-weight:600;">IP</th>
            <td style="padding:8px 0;font-family:monospace;font-size:.85rem;"><?= e($message['ip_address']) ?></td>
        </tr>
    </table>

    <div style="background:#f8f9fa;border:1px solid #e2e8f0;border-radius:4px;padding:16px;white-space:pre-wrap;word-break:break-word;font-size:.95rem;line-height:1.6;">
<?= e($message['message']) ?>
    </div>

    <div style="margin-top:24px;display:flex;gap:10px;flex-wrap:wrap;">
        <a href="mailto:<?= e($message['email']) ?>?subject=Re:+<?= rawurlencode($message['subject']) ?>"
           class="vcms-btn vcms-btn--primary">
            ✉ <?= t('contact.admin_reply') ?>
        </a>
        <form method="POST" action="/admin/contact/<?= (int)$message['id'] ?>/spam" style="display:inline">
            <?= csrf_field() ?>
            <button type="submit" class="vcms-btn vcms-btn--ghost">
                🚫 <?= t('contact.admin_mark_spam') ?>
            </button>
        </form>
        <form method="POST" action="/admin/contact/<?= (int)$message['id'] ?>/delete" style="display:inline"
              onsubmit="return confirm('<?= t('confirm.delete') ?>')">
            <?= csrf_field() ?>
            <button type="submit" class="vcms-btn vcms-btn--danger">
                <?= t('action.delete') ?>
            </button>
        </form>
    </div>

</div>

<?php $this->endSection() ?>
