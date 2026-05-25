<?php declare(strict_types=1); ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('nav.navigation') ?></h1>
    <a href="/admin/nav/create" class="vcms-btn vcms-btn--primary">
        + <?= t('nav.new_item') ?>
    </a>
</div>

<?php if (empty($items)): ?>
    <p class="vcms-empty"><?= t('nav.empty') ?></p>
<?php else: ?>
<div class="vcms-table-wrap">
    <table class="vcms-table">
        <thead>
            <tr>
                <th style="width:60px"><?= t('nav.col_order') ?></th>
                <th><?= t('field.label') ?></th>
                <th><?= t('field.url') ?></th>
                <th><?= t('field.status') ?></th>
                <th><?= t('field.actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr class="<?= $item['active'] ? '' : 'vcms-row--inactive' ?>">
                <td>
                    <form method="POST" action="/admin/nav/<?= (int)$item['id'] ?>/move-up" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="vcms-btn vcms-btn--sm" title="<?= t('action.move_up') ?>">↑</button>
                    </form>
                    <form method="POST" action="/admin/nav/<?= (int)$item['id'] ?>/move-down" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="vcms-btn vcms-btn--sm" title="<?= t('action.move_down') ?>">↓</button>
                    </form>
                </td>
                <td><?= e($item['label']) ?><?php if ($item['label_en']): ?> <span class="vcms-muted">(EN: <?= e($item['label_en']) ?>)</span><?php endif ?></td>
                <td><code><?= e($item['url']) ?></code><?php if ($item['target'] === '_blank'): ?> <span class="vcms-badge">_blank</span><?php endif ?></td>
                <td>
                    <?php if ($item['active']): ?>
                        <span class="vcms-badge vcms-badge--active"><?= t('users.active') ?></span>
                    <?php else: ?>
                        <span class="vcms-badge vcms-badge--inactive"><?= t('users.inactive') ?></span>
                    <?php endif ?>
                </td>
                <td>
                    <a href="/admin/nav/<?= (int)$item['id'] ?>/edit" class="vcms-btn vcms-btn--sm">
                        <?= t('action.edit') ?>
                    </a>
                    <form method="POST" action="/admin/nav/<?= (int)$item['id'] ?>/delete"
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
</div>
<?php endif ?>

<?php $this->endSection() ?>
