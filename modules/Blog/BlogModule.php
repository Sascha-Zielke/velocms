<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Blog;

use VeloCMS\Core\Module;

class BlogModule extends Module
{
    public string $name    = 'Blog';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // Admin routes
        $this->router->get('/admin/blog',                  'Blog\Controllers\AdminBlogController@index');
        $this->router->get('/admin/blog/new',              'Blog\Controllers\AdminBlogController@new');
        $this->router->post('/admin/blog/save',            'Blog\Controllers\AdminBlogController@save');
        $this->router->get('/admin/blog/edit/[i:id]',      'Blog\Controllers\AdminBlogController@edit');
        $this->router->post('/admin/blog/update/[i:id]',   'Blog\Controllers\AdminBlogController@update');
        $this->router->post('/admin/blog/delete/[i:id]',   'Blog\Controllers\AdminBlogController@delete');

        // Frontend routes — register BEFORE Pages catch-all
        $this->router->get('/blog',             'Blog\Controllers\BlogController@index');
        $this->router->get('/blog/[a:slug]',    'Blog\Controllers\BlogController@show');

        // Admin menu
        $this->admin->addMenuItem([
            'label'    => t('nav.blog'),
            'url'      => '/admin/blog',
            'icon'     => 'edit',
            'position' => 30,
        ]);
    }

    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/migrations');
    }
}
