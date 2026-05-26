<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('sites.headline') ?></h1>
    <a href="/admin/sites/create" class="vcms-btn vcms-btn--primary">
        + <?= t('sites.new') ?>
    </a>
</div>

<?php if (empty($sites)): ?>
    <p class="vcms-empty"><?= t('sites.empty') ?></p>
<?php else: ?>
<div class="vcms-table-wrap">
    <table class="vcms-table">
        <thead>
            <tr>
                <th><?= t('sites.field_name') ?></th>
                <th><?= t('sites.field_domain') ?></th>
                <th><?= t('sites.field_db_name') ?></th>
                <th><?= t('sites.field_status') ?></th>
                <th><?= t('field.created_at') ?></th>
                <th><?= t('field.actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($sites as $site): ?>
            <tr class="<?= $site['status'] !== 'active' ? 'vcms-row--inactive' : '' ?>">
                <td><?= e($site['name']) ?></td>
                <td>
                    <?= e($site['domain']) ?>
                    <?php if (!empty($site['www_alias'])): ?>
                        <br><small class="vcms-muted"><?= e($site['www_alias']) ?></small>
                    <?php endif ?>
                </td>
                <td><code><?= e($site['db_name']) ?></code></td>
                <td>
                    <span class="vcms-badge vcms-badge--<?= e($site['status']) ?>">
                        <?= t('sites.status_' . $site['status']) ?>
                    </span>
                </td>
                <td class="vcms-muted">
                    <?= date('d.m.Y', strtotime($site['created_at'])) ?>
                </td>
                <td>
                    <a href="/admin/sites/<?= (int)$site['id'] ?>/edit"
                       class="vcms-btn vcms-btn--sm">
                        <?= t('action.edit') ?>
                    </a>
                    <form method="POST" action="/admin/sites/<?= (int)$site['id'] ?>/delete"
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
