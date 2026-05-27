<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Maxiworx;

use VeloCMS\Core\Module;

class MaxiworxModule extends Module
{
    public string $name    = 'Maxiworx';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // Only register routes for the Maxiworx tenant
        $domain = \VeloCMS\Core\Tenant::domain();
        if (!str_contains($domain, 'maxiworx')) {
            return;
        }

        $this->router->get('/',                'Maxiworx\Controllers\MaxiworxController@home');
        $this->router->get('/equipment',       'Maxiworx\Controllers\MaxiworxController@equipment');
        $this->router->get('/service-preise',  'Maxiworx\Controllers\MaxiworxController@servicePreise');
        $this->router->get('/specials',        'Maxiworx\Controllers\MaxiworxController@specials');
        $this->router->get('/referenzen',      'Maxiworx\Controllers\MaxiworxController@referenzen');
        $this->router->get('/kontakt',         'Maxiworx\Controllers\MaxiworxController@kontakt');
        $this->router->post('/kontakt',        'Maxiworx\Controllers\MaxiworxController@kontaktSend');
        $this->router->get('/impressum',       'Maxiworx\Controllers\MaxiworxController@impressum');
        $this->router->get('/datenschutz',     'Maxiworx\Controllers\MaxiworxController@datenschutz');
        $this->router->get('/agb',             'Maxiworx\Controllers\MaxiworxController@agb');
        $this->router->post('/book-session',   'Maxiworx\Controllers\MaxiworxController@bookSession');
    }

    public function install(): void {}
}
