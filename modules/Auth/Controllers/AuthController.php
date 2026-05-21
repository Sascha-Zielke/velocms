<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Auth\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Auth\Models\UserModel;

class AuthController extends Controller
{
    private UserModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new UserModel();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin');
        }

        $this->render('admin/login', []);
    }

    public function login(): void
    {
        Auth::verifyCsrf();

        $email    = filter_var($this->input('email', ''), FILTER_VALIDATE_EMAIL);
        $password = (string) $this->input('password', '');

        if ($email === false || $password === '') {
            $this->redirectWithError('/admin/login', t('error.invalid_login'));
        }

        $user = $this->model->getByEmail($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            $this->redirectWithError('/admin/login', t('error.invalid_login'));
        }

        Auth::login($user);
        $this->model->updateLastLogin((int) $user['id']);

        $this->redirect('/admin');
    }

    // logout is POST-only to prevent CSRF-driven forced logouts
    public function logout(): void
    {
        Auth::verifyCsrf();
        Auth::logout();
        $this->redirectWithSuccess('/admin/login', t('auth.logout_success'));
    }
}
