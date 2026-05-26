<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Auth;

use VeloCMS\Core\Module;

class AuthModule extends Module
{
    public string $name    = 'Auth';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // ── Auth ─────────────────────────────────────────────────────────
        $this->router->get('/admin/login',  'Auth\Controllers\AuthController@showLogin');
        $this->router->post('/admin/login',  'Auth\Controllers\AuthController@login');
        $this->router->post('/admin/logout', 'Auth\Controllers\AuthController@logout');
        $this->router->get('/admin',         'Auth\Controllers\DashboardController@index');

        // ── Password reset (self-service, no auth required) ───────────────
        $this->router->get('/admin/password/reset',          'Auth\Controllers\PasswordResetController@showRequest');
        $this->router->post('/admin/password/reset',         'Auth\Controllers\PasswordResetController@sendReset');
        $this->router->get('/admin/password/reset/[a:token]', 'Auth\Controllers\PasswordResetController@showForm');
        $this->router->post('/admin/password/reset/[a:token]', 'Auth\Controllers\PasswordResetController@reset');

        // ── User management (admin + superadmin) ─────────────────────────
        $this->router->get('/admin/users',                  'Auth\Controllers\UserManagementController@index');
        $this->router->get('/admin/users/create',           'Auth\Controllers\UserManagementController@create');
        $this->router->post('/admin/users/create',          'Auth\Controllers\UserManagementController@store');
        $this->router->get('/admin/users/[i:id]/edit',      'Auth\Controllers\UserManagementController@edit');
        $this->router->post('/admin/users/[i:id]/edit',     'Auth\Controllers\UserManagementController@update');
        $this->router->post('/admin/users/[i:id]/password', 'Auth\Controllers\UserManagementController@resetPassword');
        $this->router->post('/admin/users/[i:id]/delete',   'Auth\Controllers\UserManagementController@delete');

        // ── Nav entry (only visible to admin+) ───────────────────────────
        $this->admin->addMenuItem([
            'label'    => t('nav.users'),
            'url'      => '/admin/users',
            'icon'     => 'users',
            'position' => 40,
            'min_role' => 'admin',
        ]);
    }
}
