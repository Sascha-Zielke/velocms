<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('blog.headline') ?></h1>
    <a href="/admin/blog/new" class="vcms-btn vcms-btn--primary"><?= t('blog.new') ?></a>
</div>

<div class="vcms-table-wrap">
<table class="vcms-table">
    <thead><tr>
        <th><?= t('field.title') ?></th>
        <th><?= t('field.status') ?></th>
        <th><?= t('field.created_at') ?></th>
        <th><?= t('field.actions') ?></th>
    </tr></thead>
    <tbody>
    <?php if (empty($posts)): ?>
        <tr><td colspan="4" style="text-align:center;color:#888;padding:32px"><?= t('blog.empty') ?></td></tr>
    <?php else: ?>
        <?php foreach ($posts as $p): ?>
        <tr>
            <td><?= e($p['title']) ?></td>
            <td><span class="vcms-badge vcms-badge--<?= e($p['status']) ?>"><?= t('status.' . $p['status']) ?></span></td>
            <td><?= e(substr($p['created_at'], 0, 10)) ?></td>
            <td class="vcms-table-actions">
                <a href="/admin/blog/edit/<?= (int)$p['id'] ?>" class="vcms-btn vcms-btn--sm vcms-btn--ghost"><?= t('action.edit') ?></a>
                <?php if ($p['status'] === 'published'): ?>
                <a href="/blog/<?= e($p['slug']) ?>" target="_blank" class="vcms-btn vcms-btn--sm vcms-btn--ghost"><?= t('action.preview') ?></a>
                <?php endif ?>
                <form method="POST" action="/admin/blog/delete/<?= (int)$p['id'] ?>" style="display:inline"
                      onsubmit="return confirm('<?= t('confirm.delete') ?>')">
                    <?= csrf_field() ?>
                    <button type="submit" class="vcms-btn vcms-btn--sm vcms-btn--danger"><?= t('action.delete') ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>
</div>
<?php $this->endSection() ?>
