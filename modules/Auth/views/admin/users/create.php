<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('users.new') ?></h1>
    <a href="/admin/users" class="vcms-btn"><?= t('action.back') ?></a>
</div>

<form method="POST" action="/admin/users/create" class="vcms-form" autocomplete="off">
    <?= csrf_field() ?>

    <div class="vcms-form__row">
        <label for="name"><?= t('field.name') ?> *</label>
        <input type="text" id="name" name="name" required
               value="<?= e($_SESSION['_old']['name'] ?? '') ?>">
    </div>

    <div class="vcms-form__row">
        <label for="email"><?= t('field.email') ?> *</label>
        <input type="email" id="email" name="email" required
               value="<?= e($_SESSION['_old']['email'] ?? '') ?>">
    </div>

    <div class="vcms-form__row">
        <label for="role"><?= t('field.role') ?></label>
        <select id="role" name="role">
            <?php foreach ($assignableRoles as $r): ?>
                <option value="<?= e($r) ?>"><?= t('role.' . $r) ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="vcms-form__row">
        <label for="password"><?= t('field.password') ?> * <small><?= t('users.password_hint') ?></small></label>
        <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
    </div>

    <?php unset($_SESSION['_old']); ?>

    <div class="vcms-form__actions">
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
        <a href="/admin/users" class="vcms-btn"><?= t('action.cancel') ?></a>
    </div>
</form>

<?php $this->endSection() ?>
