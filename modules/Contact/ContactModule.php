<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Contact;

use VeloCMS\Core\Module;

class ContactModule extends Module
{
    public string $name    = 'Contact';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // ── Frontend routes ──────────────────────────────────────────────────
        // Must be registered BEFORE the Pages catch-all ([*:slug])
        $this->router->get('/kontakt',  'Contact\Controllers\ContactController@show');
        $this->router->post('/kontakt', 'Contact\Controllers\ContactController@submit');

        // ── Admin routes ─────────────────────────────────────────────────────
        $this->router->get('/admin/contact',                         'Contact\Controllers\AdminContactController@index');
        $this->router->get('/admin/contact/settings',                'Contact\Controllers\AdminContactController@settings');
        $this->router->post('/admin/contact/settings/save',          'Contact\Controllers\AdminContactController@saveSettings');
        $this->router->post('/admin/contact/purge',                  'Contact\Controllers\AdminContactController@purge');
        $this->router->get('/admin/contact/[i:id]',                  'Contact\Controllers\AdminContactController@show');
        $this->router->post('/admin/contact/[i:id]/spam',            'Contact\Controllers\AdminContactController@spam');
        $this->router->post('/admin/contact/[i:id]/delete',          'Contact\Controllers\AdminContactController@delete');

        // ── Admin menu ───────────────────────────────────────────────────────
        $this->admin->addMenuItem([
            'label'    => t('contact.admin_menu'),
            'url'      => '/admin/contact',
            'icon'     => 'mail',
            'position' => 45,
        ]);
    }

    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/migrations');
    }
}
