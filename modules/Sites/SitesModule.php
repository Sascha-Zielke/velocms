<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Sites;

use VeloCMS\Core\Module;

class SitesModule extends Module
{
    public string $name    = 'Sites';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // ── Site management (superadmin only) ────────────────────────────────
        $this->router->get('/admin/sites',                    'Sites\Controllers\AdminSitesController@index');
        $this->router->get('/admin/sites/create',             'Sites\Controllers\AdminSitesController@create');
        $this->router->post('/admin/sites/create',            'Sites\Controllers\AdminSitesController@store');
        $this->router->get('/admin/sites/[i:id]/edit',        'Sites\Controllers\AdminSitesController@edit');
        $this->router->post('/admin/sites/[i:id]/edit',       'Sites\Controllers\AdminSitesController@update');
        $this->router->post('/admin/sites/[i:id]/provision',  'Sites\Controllers\AdminSitesController@provision');
        $this->router->post('/admin/sites/[i:id]/delete',     'Sites\Controllers\AdminSitesController@delete');

        // ── Nav entry (superadmin only) ───────────────────────────────────────
        $this->admin->addMenuItem([
            'label'    => t('nav.sites'),
            'url'      => '/admin/sites',
            'icon'     => 'globe',
            'position' => 90,
            'min_role' => 'superadmin',
        ]);
    }
}
