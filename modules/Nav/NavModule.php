<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Nav;

use VeloCMS\Core\Module;

class NavModule extends Module
{
    public string $name    = 'Nav';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // Admin routes
        $this->router->get('/admin/nav',                        'Nav\Controllers\AdminNavController@index');
        $this->router->get('/admin/nav/create',                 'Nav\Controllers\AdminNavController@create');
        $this->router->post('/admin/nav/store',                 'Nav\Controllers\AdminNavController@store');
        $this->router->get('/admin/nav/[i:id]/edit',            'Nav\Controllers\AdminNavController@edit');
        $this->router->post('/admin/nav/[i:id]/update',         'Nav\Controllers\AdminNavController@update');
        $this->router->post('/admin/nav/[i:id]/delete',         'Nav\Controllers\AdminNavController@delete');
        $this->router->post('/admin/nav/[i:id]/move-up',        'Nav\Controllers\AdminNavController@moveUp');
        $this->router->post('/admin/nav/[i:id]/move-down',      'Nav\Controllers\AdminNavController@moveDown');

        $this->admin->addMenuItem([
            'label'    => t('nav.navigation'),
            'url'      => '/admin/nav',
            'icon'     => 'menu',
            'position' => 15,
        ]);
    }

    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/migrations');
    }
}
