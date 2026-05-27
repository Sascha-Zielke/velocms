<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Controllers\Api;

use VeloCMS\Core\Controller;
use VeloCMS\Modules\Booking\Core\Services\AvailabilityEngine;
use VeloCMS\Modules\Booking\Core\Services\BookingConflictException;
use VeloCMS\Modules\Booking\Core\Services\BookingMailer;
use VeloCMS\Modules\Booking\Core\Services\BookingOutsideSlotsException;
use VeloCMS\Modules\Booking\Core\Services\BookingService;
use VeloCMS\Modules\Booking\Core\Services\TemplateRegistry;
use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;
use VeloCMS\Modules\Booking\Models\BookingModel;
use VeloCMS\Modules\Booking\Models\ResourceModel;
use VeloCMS\Modules\Booking\Models\SlotModel;

class ApiBookingController extends Controller
{
    private BookingService $service;
    private ResourceModel  $resourceModel;

    public function __construct()
    {
        parent::__construct();
        $bookingModel        = new BookingModel();
        $this->resourceModel = new ResourceModel();
        $this->service       = new BookingService(
            $bookingModel,
            $this->resourceModel,
            new AvailabilityEngine(new SlotModel(), $bookingModel),
        );
    }

    /**
     * POST /api/booking/book
     *
     * Body (JSON or form-encoded):
     *   resource_id, customer_name, customer_email, customer_phone?,
     *   start_at (Y-m-d H:i:s UTC), end_at (Y-m-d H:i:s UTC),
     *   notes?, timezone?, + template-specific fields
     */
    public function book(): void
    {
        $resourceId    = (int) $this->input('resource_id', 0);
        $customerName  = trim((string) $this->input('customer_name', ''));
        $customerEmail = trim((string) $this->input('customer_email', ''));
        $customerPhone = $this->input('customer_phone') ?: null;
        $startAt       = trim((string) $this->input('start_at', ''));
        $endAt         = trim((string) $this->input('end_at', ''));
        $notes         = $this->input('notes') ?: null;
        $timezone      = trim((string) $this->input('timezone', 'UTC'));

        // Basic validation
        $errors = [];
        if ($resourceId <= 0) {
            $errors[] = 'resource_id is required';
        }
        if ($customerName === '') {
            $errors[] = t('error.required') . ': customer_name';
        }
        if ($customerEmail === '' || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = t('error.invalid_email');
        }
        if ($startAt === '' || $endAt === '') {
            $errors[] = 'start_at and end_at are required (Y-m-d H:i:s UTC)';
        }

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
        }

        // Validate timezone
        try {
            new \DateTimeZone($timezone);
        } catch (\Throwable) {
            $timezone = 'UTC';
        }

        // Parse range
        try {
            $range = DateTimeRange::fromStrings($startAt, $endAt, 'UTC');
        } catch (\InvalidArgumentException $e) {
            $this->json(['errors' => [$e->getMessage()]], 422);
        }

        // Template validation
        $resource = $this->resourceModel->find($resourceId);
        if ($resource === null || !$resource->isActive) {
            $this->json(['errors' => [t('error.not_found')]], 404);
        }

        $template = TemplateRegistry::get($resource->templateKey);
        $metadata = [];
        if ($template !== null) {
            $templateErrors = $template->validate($_POST + $_GET);
            if (!empty($templateErrors)) {
                $this->json(['errors' => $templateErrors], 422);
            }
            $metadata = $template->extractMetadata($_POST + $_GET);
        }

        // Book
        try {
            $booking = $this->service->book(
                resourceId:    $resourceId,
                customerName:  $customerName,
                customerEmail: $customerEmail,
                customerPhone: is_string($customerPhone) ? $customerPhone : null,
                range:         $range,
                notes:         is_string($notes) ? $notes : null,
                metadata:      $metadata,
                timezone:      $timezone,
            );
        } catch (BookingConflictException) {
            $this->json(['errors' => [t('booking.error_conflict')]], 409);
        } catch (BookingOutsideSlotsException) {
            $this->json(['errors' => [t('booking.error_outside_slots')]], 422);
        }

        // Send confirmation email in background (fire-and-forget)
        $mailer = new BookingMailer();
        $mailer->sendCustomerConfirmation($booking, $resource);
        $mailer->sendAdminNotification($booking, $resource);

        $this->json([
            'booking_id' => $booking->id,
            'status'     => $booking->status->value,
            'start_at'   => $booking->range->startUtc(),
            'end_at'     => $booking->range->endUtc(),
        ], 201);
    }
}
