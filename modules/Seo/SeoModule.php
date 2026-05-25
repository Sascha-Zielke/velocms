<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Seo;

use VeloCMS\Core\Module;

class SeoModule extends Module
{
    public string $name    = 'Seo';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // Special SEO routes — before Pages catch-all
        $this->router->get('/sitemap.xml', 'Seo\Controllers\SeoController@sitemap');
        $this->router->get('/robots.txt',  'Seo\Controllers\SeoController@robots');
    }

    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/migrations');
    }
}
