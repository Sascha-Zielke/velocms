<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1>
        <?= t('contact.admin_menu') ?>
        <?php if ($unread > 0): ?>
            <span class="vcms-badge vcms-badge--new" style="font-size:.75rem;vertical-align:middle;margin-left:8px;">
                <?= (int)$unread ?> neu
            </span>
        <?php endif ?>
    </h1>
    <div style="display:flex;gap:8px;">
        <a href="/admin/contact/settings" class="vcms-btn vcms-btn--ghost vcms-btn--sm">⚙ <?= t('contact.admin_settings') ?></a>
        <form method="POST" action="/admin/contact/purge" style="display:inline"
              onsubmit="return confirm('<?= t('contact.admin_purge_confirm') ?>')">
            <?= csrf_field() ?>
            <button type="submit" class="vcms-btn vcms-btn--sm vcms-btn--ghost">
                🗑 <?= t('contact.admin_purge') ?>
            </button>
        </form>
    </div>
</div>

<?php /* ── Status filter tabs ── */ ?>
<div style="margin-bottom:20px;display:flex;gap:4px;flex-wrap:wrap;">
    <?php
    $statuses = ['' => t('contact.filter_all'), 'new' => t('contact.filter_new'),
                 'read' => t('contact.filter_read'), 'replied' => t('contact.filter_replied'),
                 'spam' => t('contact.filter_spam')];
    foreach ($statuses as $val => $label):
        $active = $filter === $val;
    ?>
        <a href="/admin/contact<?= $val !== '' ? '?status=' . e($val) : '' ?>"
           class="vcms-btn vcms-btn--sm <?= $active ? 'vcms-btn--primary' : 'vcms-btn--ghost' ?>">
            <?= e($label) ?>
        </a>
    <?php endforeach ?>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="vcms-alert vcms-alert--success"><?= e($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']) ?>
<?php endif ?>

<div class="vcms-table-wrap">
    <table class="vcms-table">
        <thead><tr>
            <th><?= t('contact.field_name') ?></th>
            <th><?= t('contact.field_email') ?></th>
            <th><?= t('contact.field_subject') ?></th>
            <th><?= t('field.status') ?></th>
            <th><?= t('field.created_at') ?></th>
            <th><?= t('field.actions') ?></th>
        </tr></thead>
        <tbody>
        <?php if (empty($messages)): ?>
            <tr><td colspan="6" style="text-align:center;color:#888;padding:32px;">
                <?= t('contact.admin_empty') ?>
            </td></tr>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
            <tr<?= $msg['status'] === 'new' ? ' style="font-weight:600;"' : '' ?>>
                <td><?= e($msg['name']) ?></td>
                <td><?= e($msg['email']) ?></td>
                <td><?= e($msg['subject'] ?: '—') ?></td>
                <td>
                    <span class="vcms-badge vcms-badge--<?= e($msg['status']) ?>">
                        <?= t('contact.status_' . $msg['status']) ?>
                    </span>
                </td>
                <td><?= e(substr($msg['created_at'], 0, 16)) ?></td>
                <td class="vcms-table-actions">
                    <a href="/admin/contact/<?= (int)$msg['id'] ?>"
                       class="vcms-btn vcms-btn--sm vcms-btn--ghost"><?= t('action.edit') ?></a>
                    <form method="POST" action="/admin/contact/<?= (int)$msg['id'] ?>/delete"
                          style="display:inline"
                          onsubmit="return confirm('<?= t('confirm.delete') ?>')">
                        <?= csrf_field() ?>
                        <button type="submit" class="vcms-btn vcms-btn--sm vcms-btn--danger">
                            <?= t('action.delete') ?>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach ?>
        <?php endif ?>
        </tbody>
    </table>
</div>

<?php /* ── Pagination ── */ ?>
<?php if ($pages > 1): ?>
<div style="margin-top:16px;display:flex;gap:8px;align-items:center;">
    <?php if ($currentPage > 1): ?>
        <a href="?page=<?= $currentPage - 1 ?><?= $filter ? '&status=' . e($filter) : '' ?>"
           class="vcms-btn vcms-btn--sm vcms-btn--ghost">&larr; <?= t('pagination.prev') ?></a>
    <?php endif ?>
    <span style="font-size:.9rem;color:#555;">
        <?= $currentPage ?> / <?= $pages ?>
    </span>
    <?php if ($currentPage < $pages): ?>
        <a href="?page=<?= $currentPage + 1 ?><?= $filter ? '&status=' . e($filter) : '' ?>"
           class="vcms-btn vcms-btn--sm vcms-btn--ghost"><?= t('pagination.next') ?> &rarr;</a>
    <?php endif ?>
</div>
<?php endif ?>

<?php $this->endSection() ?>
