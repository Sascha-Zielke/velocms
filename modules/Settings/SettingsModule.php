<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Settings;

use VeloCMS\Core\Module;

class SettingsModule extends Module
{
    public string $name    = 'Settings';
    public string $version = '1.0.0';

    public function boot(): void
    {
        $this->router->get('/admin/settings',  'Settings\Controllers\AdminSettingsController@index');
        $this->router->post('/admin/settings', 'Settings\Controllers\AdminSettingsController@save');

        $this->admin->addMenuItem([
            'label'    => t('nav.settings'),
            'url'      => '/admin/settings',
            'icon'     => 'settings',
            'position' => 50,
            'min_role' => 'admin',
        ]);
    }

    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/migrations');
    }
}
