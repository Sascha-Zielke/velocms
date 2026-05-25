<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('users.headline') ?></h1>
    <a href="/admin/users/create" class="vcms-btn vcms-btn--primary">
        + <?= t('users.new') ?>
    </a>
</div>

<?php if (empty($users)): ?>
    <p class="vcms-empty"><?= t('users.empty') ?></p>
<?php else: ?>
<div class="vcms-table-wrap">
    <table class="vcms-table">
        <thead>
            <tr>
                <th><?= t('field.name') ?></th>
                <th><?= t('field.email') ?></th>
                <th><?= t('field.role') ?></th>
                <th><?= t('users.status') ?></th>
                <th><?= t('users.last_login') ?></th>
                <th><?= t('field.actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr class="<?= $user['active'] ? '' : 'vcms-row--inactive' ?>">
                <td>
                    <?= e($user['name']) ?>
                    <?php if ((int)$user['id'] === $currentUserId): ?>
                        <span class="vcms-badge vcms-badge--me"><?= t('users.you') ?></span>
                    <?php endif ?>
                </td>
                <td><?= e($user['email']) ?></td>
                <td>
                    <span class="vcms-badge vcms-badge--<?= e($user['role']) ?>">
                        <?= t('role.' . $user['role']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($user['active']): ?>
                        <span class="vcms-badge vcms-badge--active"><?= t('users.active') ?></span>
                    <?php else: ?>
                        <span class="vcms-badge vcms-badge--inactive"><?= t('users.inactive') ?></span>
                    <?php endif ?>
                </td>
                <td class="vcms-muted">
                    <?= $user['last_login_at'] ? date('d.m.Y H:i', strtotime($user['last_login_at'])) : '—' ?>
                </td>
                <td>
                    <?php
                    $canEdit = $currentRole === 'superadmin'
                        || $user['role'] === 'editor'
                        || (int)$user['id'] === $currentUserId;
                    ?>
                    <?php if ($canEdit): ?>
                        <a href="/admin/users/<?= (int)$user['id'] ?>/edit" class="vcms-btn vcms-btn--sm">
                            <?= t('action.edit') ?>
                        </a>
                    <?php endif ?>
                    <?php if ((int)$user['id'] !== $currentUserId && ($currentRole === 'superadmin' || $user['role'] === 'editor')): ?>
                        <form method="POST" action="/admin/users/<?= (int)$user['id'] ?>/delete"
                              style="display:inline"
                              onsubmit="return confirm('<?= t('confirm.delete') ?>')">
                            <?= csrf_field() ?>
                            <button type="submit" class="vcms-btn vcms-btn--sm vcms-btn--danger">
                                <?= t('action.delete') ?>
                            </button>
                        </form>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
<?php endif ?>

<?php $this->endSection() ?>
