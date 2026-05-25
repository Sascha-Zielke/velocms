<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Auth\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Auth\Models\UserModel;

class UserManagementController extends Controller
{
    private UserModel $model;

    private const ASSIGNABLE_ROLES = [
        'superadmin' => ['editor', 'admin', 'superadmin'],
        'admin'      => ['editor'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
        $this->model = new UserModel();
    }

    public function index(): void
    {
        $this->view->extend('admin');
        $this->render('admin/users/index', [
            'users'         => $this->model->getAll(),
            'currentUserId' => Auth::id(),
            'currentRole'   => Auth::role(),
        ]);
    }

    public function create(): void
    {
        $this->view->extend('admin');
        $this->render('admin/users/create', [
            'assignableRoles' => self::ASSIGNABLE_ROLES[Auth::role()] ?? [],
        ]);
    }

    public function store(): void
    {
        Auth::verifyCsrf();

        $name     = trim($this->input('name', ''));
        $email    = trim($this->input('email', ''));
        $password = $this->input('password', '');
        $role     = $this->input('role', 'editor');

        if ($name === '' || $email === '' || $password === '') {
            $this->redirectWithError('/admin/users/create', t('error.required'));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithError('/admin/users/create', t('error.invalid_email'));
        }
        if ($this->model->emailExists($email)) {
            $this->redirectWithError('/admin/users/create', t('error.email_taken'));
        }
        if (!in_array($role, self::ASSIGNABLE_ROLES[Auth::role()] ?? [], true)) {
            $this->redirectWithError('/admin/users/create', t('error.forbidden'));
        }
        if (strlen($password) < 8) {
            $this->redirectWithError('/admin/users/create', t('error.password_min'));
        }

        $this->model->create(['name' => $name, 'email' => $email, 'password' => $password, 'role' => $role]);
        $this->redirectWithSuccess('/admin/users', t('success.user_created'));
    }

    public function edit(string $id): void
    {
        $user = $this->loadUser((int) $id);
        $this->guardRoleEdit($user);

        $this->view->extend('admin');
        $this->render('admin/users/edit', [
            'user'            => $user,
            'currentUserId'   => Auth::id(),
            'currentRole'     => Auth::role(),
            'assignableRoles' => self::ASSIGNABLE_ROLES[Auth::role()] ?? [],
        ]);
    }

    public function update(string $id): void
    {
        Auth::verifyCsrf();
        $user = $this->loadUser((int) $id);
        $this->guardRoleEdit($user);

        $name   = trim($this->input('name', ''));
        $email  = trim($this->input('email', ''));
        $role   = $this->input('role', $user['role']);
        $active = $this->input('active', '1');

        if ($name === '' || $email === '') {
            $this->redirectWithError('/admin/users/' . $id . '/edit', t('error.required'));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithError('/admin/users/' . $id . '/edit', t('error.invalid_email'));
        }
        if ($this->model->emailExists($email, (int) $id)) {
            $this->redirectWithError('/admin/users/' . $id . '/edit', t('error.email_taken'));
        }
        if (!in_array($role, self::ASSIGNABLE_ROLES[Auth::role()] ?? [], true)) {
            $role = $user['role'];
        }
        if ((int) $id === Auth::id()) {
            $active = '1';
        }

        $this->model->update((int) $id, [
            'name'   => $name,
            'email'  => $email,
            'role'   => $role,
            'active' => $active,
        ]);
        $this->redirectWithSuccess('/admin/users/' . $id . '/edit', t('success.saved'));
    }

    public function resetPassword(string $id): void
    {
        Auth::verifyCsrf();
        $user = $this->loadUser((int) $id);
        $this->guardRoleEdit($user);

        $password = $this->input('password', '');
        $confirm  = $this->input('password_confirm', '');

        if (strlen($password) < 8) {
            $this->redirectWithError('/admin/users/' . $id . '/edit', t('error.password_min'));
        }
        if ($password !== $confirm) {
            $this->redirectWithError('/admin/users/' . $id . '/edit', t('error.password_mismatch'));
        }

        $this->model->setPassword((int) $id, $password);
        $this->redirectWithSuccess('/admin/users/' . $id . '/edit', t('success.password_changed'));
    }

    public function delete(string $id): void
    {
        Auth::verifyCsrf();
        $user = $this->loadUser((int) $id);

        if ((int) $id === Auth::id()) {
            $this->redirectWithError('/admin/users', t('error.cannot_delete_self'));
        }
        if ($user['role'] === 'superadmin' && Auth::role() !== 'superadmin') {
            $this->redirectWithError('/admin/users', t('error.forbidden'));
        }

        $this->model->softDelete((int) $id);
        $this->redirectWithSuccess('/admin/users', t('success.deleted'));
    }

    private function loadUser(int $id): array
    {
        $user = $this->model->getById($id);
        if (!$user) {
            $this->redirectWithError('/admin/users', t('error.not_found'));
        }
        return $user;
    }

    private function guardRoleEdit(array $user): void
    {
        if (
            Auth::role() === 'admin'
            && $user['role'] !== 'editor'
            && (int) $user['id'] !== Auth::id()
        ) {
            $_SESSION['flash_error'] = t('error.forbidden');
            $this->redirect('/admin/users');
        }
    }
}
