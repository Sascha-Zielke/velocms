<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Pages;

use VeloCMS\Core\Module;

class PagesModule extends Module
{
    public string $name    = 'Pages';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // Admin routes
        $this->router->get('/admin/pages',                      'Pages\Controllers\AdminPagesController@index');
        $this->router->get('/admin/pages/new',                  'Pages\Controllers\AdminPagesController@new');
        $this->router->post('/admin/pages/save',                'Pages\Controllers\AdminPagesController@save');
        $this->router->get('/admin/pages/edit/[i:id]',          'Pages\Controllers\AdminPagesController@edit');
        $this->router->post('/admin/pages/delete/[i:id]',       'Pages\Controllers\AdminPagesController@delete');

        // Visual Editor API (JSON)
        $this->router->post('/admin/pages/[i:id]/section/add',  'Pages\Controllers\AdminPagesController@addSection');
        $this->router->post('/admin/pages/section/[i:id]/row/add', 'Pages\Controllers\AdminPagesController@addRow');
        $this->router->post('/admin/pages/row/[i:id]/box/add',  'Pages\Controllers\AdminPagesController@addBox');
        $this->router->post('/admin/pages/box/[i:id]/save',     'Pages\Controllers\AdminPagesController@saveBox');
        $this->router->post('/admin/pages/box/[i:id]/delete',   'Pages\Controllers\AdminPagesController@deleteBox');
        $this->router->post('/admin/pages/section/[i:id]/delete', 'Pages\Controllers\AdminPagesController@deleteSection');
        $this->router->post('/admin/pages/row/[i:id]/delete',   'Pages\Controllers\AdminPagesController@deleteRow');
        $this->router->post('/admin/pages/section/[i:id]/settings', 'Pages\Controllers\AdminPagesController@saveSectionSettings');

        // Frontend: catch-all slug routing (must be last)
        $this->router->get('/[*:slug]', 'Pages\Controllers\PagesController@show');

        // Admin menu
        $this->admin->addMenuItem([
            'label'    => t('nav.pages'),
            'url'      => '/admin/pages',
            'icon'     => 'file',
            'position' => 10,
        ]);
    }

    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/migrations');
    }
}
