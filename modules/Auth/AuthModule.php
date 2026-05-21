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
        $this->router->get('/admin/login',  'Auth\Controllers\AuthController@showLogin');
        $this->router->post('/admin/login',  'Auth\Controllers\AuthController@login');
        $this->router->post('/admin/logout', 'Auth\Controllers\AuthController@logout');
        $this->router->get('/admin',         'Auth\Controllers\DashboardController@index');
    }
}
