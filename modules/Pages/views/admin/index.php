<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('pages.headline') ?></h1>
    <a href="/admin/pages/new" class="vcms-btn vcms-btn--primary"><?= t('action.new') ?></a>
</div>

<?php if (empty($pages)): ?>
    <p class="vcms-empty"><?= t('pages.empty') ?></p>
<?php else: ?>
<table class="vcms-table">
    <thead>
        <tr>
            <th><?= t('field.title') ?></th>
            <th><?= t('field.slug') ?></th>
            <th><?= t('field.status') ?></th>
            <th><?= t('field.actions') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pages as $page): ?>
        <tr>
            <td><?= e($page['title']) ?></td>
            <td><code>/<?= e($page['slug']) ?></code></td>
            <td>
                <span class="vcms-badge vcms-badge--<?= e($page['status']) ?>">
                    <?= t('status.' . $page['status']) ?>
                </span>
            </td>
            <td class="vcms-actions">
                <a href="/admin/pages/edit/<?= (int)$page['id'] ?>" class="vcms-btn vcms-btn--sm">
                    <?= t('action.edit') ?>
                </a>
                <a href="/<?= e($page['slug']) ?>" target="_blank" class="vcms-btn vcms-btn--sm vcms-btn--ghost">
                    <?= t('action.preview') ?>
                </a>
                <form method="POST" action="/admin/pages/delete/<?= (int)$page['id'] ?>"
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
    </tbody>
</table>
<?php endif ?>

<?php $this->endSection() ?>
