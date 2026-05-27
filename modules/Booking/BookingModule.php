<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking;

use VeloCMS\Core\Module;
use VeloCMS\Modules\Booking\Core\Services\TemplateRegistry;
use VeloCMS\Modules\Booking\Extensions\Generic\GenericTemplate;
use VeloCMS\Modules\Booking\Extensions\Handwerker\HandwerkerTemplate;
use VeloCMS\Modules\Booking\Extensions\Restaurant\RestaurantTemplate;
use VeloCMS\Modules\Booking\Extensions\Studio\StudioTemplate;

class BookingModule extends Module
{
    public string $name    = 'Booking';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // Register industry templates
        TemplateRegistry::register(new GenericTemplate());
        TemplateRegistry::register(new RestaurantTemplate());
        TemplateRegistry::register(new HandwerkerTemplate());
        TemplateRegistry::register(new StudioTemplate());

        // Public API
        $this->router->get('/api/booking/resources',          'Booking\Controllers\Api\ApiAvailabilityController@resources');
        $this->router->get('/api/booking/availability',       'Booking\Controllers\Api\ApiAvailabilityController@slots');
        $this->router->post('/api/booking/book',              'Booking\Controllers\Api\ApiBookingController@book');

        // Booking dashboard
        $this->router->get('/admin/apps/booking',                             'Booking\Controllers\Admin\AdminBookingController@index');
        $this->router->get('/admin/apps/booking/detail/[i:id]',              'Booking\Controllers\Admin\AdminBookingController@detail');
        $this->router->post('/admin/apps/booking/confirm/[i:id]',            'Booking\Controllers\Admin\AdminBookingController@confirm');
        $this->router->post('/admin/apps/booking/cancel/[i:id]',             'Booking\Controllers\Admin\AdminBookingController@cancel');

        // Resource management
        $this->router->get('/admin/apps/booking/resources',                   'Booking\Controllers\Admin\AdminResourceController@index');
        $this->router->get('/admin/apps/booking/resources/create',            'Booking\Controllers\Admin\AdminResourceController@create');
        $this->router->post('/admin/apps/booking/resources/store',            'Booking\Controllers\Admin\AdminResourceController@store');
        $this->router->get('/admin/apps/booking/resources/edit/[i:id]',      'Booking\Controllers\Admin\AdminResourceController@edit');
        $this->router->post('/admin/apps/booking/resources/update/[i:id]',   'Booking\Controllers\Admin\AdminResourceController@update');
        $this->router->post('/admin/apps/booking/resources/delete/[i:id]',   'Booking\Controllers\Admin\AdminResourceController@delete');

        // Admin nav
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
