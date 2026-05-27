<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Controllers\Api;

use VeloCMS\Core\Controller;
use VeloCMS\Modules\Booking\Core\Services\AvailabilityEngine;
use VeloCMS\Modules\Booking\Core\Services\TemplateRegistry;
use VeloCMS\Modules\Booking\Models\BookingModel;
use VeloCMS\Modules\Booking\Models\ResourceModel;
use VeloCMS\Modules\Booking\Models\SlotModel;

class ApiAvailabilityController extends Controller
{
    private AvailabilityEngine $engine;
    private ResourceModel      $resourceModel;

    public function __construct()
    {
        parent::__construct();
        $bookingModel        = new BookingModel();
        $this->resourceModel = new ResourceModel();
        $this->engine        = new AvailabilityEngine(new SlotModel(), $bookingModel);
    }

    /**
     * GET /api/booking/availability?resource_id=1&date=2026-06-01&timezone=Europe/Berlin
     */
    public function slots(): void
    {
        $resourceId = (int) $this->input('resource_id', 0);
        $date       = trim((string) $this->input('date', ''));
        $timezone   = trim((string) $this->input('timezone', 'UTC'));

        if ($resourceId <= 0 || $date === '') {
            $this->json(['error' => 'resource_id and date are required'], 400);
        }

        // Validate date format
        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
            $this->json(['error' => 'Invalid date format. Use Y-m-d.'], 400);
        }

        // Validate timezone
        try {
            new \DateTimeZone($timezone);
        } catch (\Throwable) {
            $timezone = 'UTC';
        }

        $resource = $this->resourceModel->find($resourceId);
        if ($resource === null || !$resource->isActive) {
            $this->json(['error' => 'Resource not found'], 404);
        }

        $template = TemplateRegistry::get($resource->templateKey);
        $duration = $template !== null ? $template->minDurationMinutes() : 60;

        $available = $this->engine->availableSlots($resourceId, $date, $duration, $timezone);

        $this->json([
            'resource_id'       => $resourceId,
            'date'              => $date,
            'duration_minutes'  => $duration,
            'slots'             => array_map(fn($r) => [
                'start' => $r->startUtc(),
                'end'   => $r->endUtc(),
            ], $available),
        ]);
    }

    /**
     * GET /api/booking/resources — list all active resources with their template info
     */
    public function resources(): void
    {
        $resources = $this->resourceModel->all(activeOnly: true);

        $this->json([
            'resources' => array_map(fn($r) => [
                'id'           => $r->id,
                'name'         => $r->name,
                'type'         => $r->type->value,
                'template_key' => $r->templateKey,
                'form_fields'  => TemplateRegistry::get($r->templateKey)?->formFields() ?? [],
            ], $resources),
        ]);
    }
}
