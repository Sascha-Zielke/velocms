<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Media;

use VeloCMS\Core\Module;

class MediaModule extends Module
{
    public string $name    = 'Media';
    public string $version = '1.0.0';

    public function boot(): void
    {
        $this->router->get('/admin/media',               'Media\Controllers\MediaController@index');
        $this->router->post('/admin/media/upload',        'Media\Controllers\MediaController@upload');
        $this->router->post('/admin/media/[i:id]/alt',    'Media\Controllers\MediaController@updateAlt');
        $this->router->post('/admin/media/[i:id]/delete', 'Media\Controllers\MediaController@delete');
        $this->router->get('/admin/media/list.json',      'Media\Controllers\MediaController@listJson');

        $this->admin->addMenuItem([
            'label'    => t('nav.media'),
            'url'      => '/admin/media',
            'icon'     => 'image',
            'position' => 20,
        ]);
    }

    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/migrations');
    }
}
