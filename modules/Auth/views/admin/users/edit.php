<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('users.edit') ?>: <?= e($user['name']) ?></h1>
    <a href="/admin/users" class="vcms-btn"><?= t('action.back') ?></a>
</div>

<?php $isSelf = (int)$user['id'] === $currentUserId; ?>

<!-- ── Profile form ─────────────────────────────────────────────────── -->
<section class="vcms-card">
    <h2 class="vcms-card__title"><?= t('users.section_profile') ?></h2>

    <form method="POST" action="/admin/users/<?= (int)$user['id'] ?>/edit" class="vcms-form" autocomplete="off">
        <?= csrf_field() ?>

        <div class="vcms-form__row">
            <label for="name"><?= t('field.name') ?> *</label>
            <input type="text" id="name" name="name" required value="<?= e($user['name']) ?>">
        </div>

        <div class="vcms-form__row">
            <label for="email"><?= t('field.email') ?> *</label>
            <input type="email" id="email" name="email" required value="<?= e($user['email']) ?>">
        </div>

        <div class="vcms-form__row">
            <label for="role"><?= t('field.role') ?></label>
            <?php if ($currentRole === 'superadmin' && !$isSelf): ?>
                <select id="role" name="role">
                    <?php foreach ($assignableRoles as $r): ?>
                        <option value="<?= e($r) ?>" <?= $user['role'] === $r ? 'selected' : '' ?>>
                            <?= t('role.' . $r) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            <?php elseif ($currentRole === 'admin' && $user['role'] === 'editor'): ?>
                <select id="role" name="role">
                    <?php foreach ($assignableRoles as $r): ?>
                        <option value="<?= e($r) ?>" <?= $user['role'] === $r ? 'selected' : '' ?>>
                            <?= t('role.' . $r) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="role" value="<?= e($user['role']) ?>">
                <span class="vcms-badge vcms-badge--<?= e($user['role']) ?>"><?= t('role.' . $user['role']) ?></span>
            <?php endif ?>
        </div>

        <?php if (!$isSelf): ?>
        <div class="vcms-form__row">
            <label><?= t('users.status') ?></label>
            <label class="vcms-toggle">
                <input type="checkbox" name="active" value="1" <?= $user['active'] ? 'checked' : '' ?>>
                <span class="vcms-toggle__label"><?= t('users.active') ?></span>
            </label>
        </div>
        <?php endif ?>

        <div class="vcms-form__actions">
            <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
        </div>
    </form>
</section>

<!-- ── Password reset ───────────────────────────────────────────────── -->
<section class="vcms-card" style="margin-top:2rem">
    <h2 class="vcms-card__title"><?= t('users.section_password') ?></h2>

    <form method="POST" action="/admin/users/<?= (int)$user['id'] ?>/password" class="vcms-form" autocomplete="off">
        <?= csrf_field() ?>

        <div class="vcms-form__row">
            <label for="password"><?= t('field.password') ?> * <small><?= t('users.password_hint') ?></small></label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
        </div>

        <div class="vcms-form__row">
            <label for="password_confirm"><?= t('users.password_confirm') ?> *</label>
            <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password">
        </div>

        <div class="vcms-form__actions">
            <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('users.set_password') ?></button>
        </div>
    </form>
</section>

<?php $this->endSection() ?>
