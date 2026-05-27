<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking;

use VeloCMS\Core\Module;

class BookingModule extends Module
{
    public string $name    = 'Booking';
    public string $version = '1.0.0';

    public function boot(): void
    {
        $this->admin->addMenuItem([
            'type'     => 'section',
            'label'    => t('nav.apps'),
            'position' => 95,
            'min_role' => 'admin',
        ]);

        $this->admin->addMenuItem([
            'label'    => t('nav.booking'),
            'url'      => '/admin/apps/booking',
            'position' => 97,
            'min_role' => 'admin',
        ]);
    }

    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/migrations');
    }
}
