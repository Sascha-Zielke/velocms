<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Controllers\Admin;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Booking\Core\Services\BookingMailer;
use VeloCMS\Modules\Booking\Core\Services\BookingService;
use VeloCMS\Modules\Booking\Core\ValueObjects\BookingStatus;
use VeloCMS\Modules\Booking\Models\BookingModel;
use VeloCMS\Modules\Booking\Models\ResourceModel;
use VeloCMS\Modules\Booking\Core\Services\AvailabilityEngine;
use VeloCMS\Modules\Booking\Models\SlotModel;

class AdminBookingController extends Controller
{
    private BookingModel  $bookingModel;
    private ResourceModel $resourceModel;
    private BookingService $service;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->bookingModel  = new BookingModel();
        $this->resourceModel = new ResourceModel();
        $this->service       = new BookingService(
            $this->bookingModel,
            $this->resourceModel,
            new AvailabilityEngine(new SlotModel(), $this->bookingModel),
        );
    }

    public function index(): void
    {
        $status   = $this->input('status');
        $bookings = $this->bookingModel->recent(100, $status !== '' ? $status : null);

        $this->render('admin/booking/index', [
            'bookings'  => $bookings,
            'resources' => $this->resourceModel->all(),
            'filter'    => $status ?? '',
        ]);
    }

    public function detail(int $id): void
    {
        $booking = $this->bookingModel->find($id);
        if ($booking === null) {
            $this->redirectWithError('/admin/apps/booking', t('error.not_found'));
        }

        $resource = $this->resourceModel->find($booking->resourceId);

        $this->render('admin/booking/detail', [
            'booking'  => $booking,
            'resource' => $resource,
        ]);
    }

    public function confirm(int $id): void
    {
        Auth::verifyCsrf();
        $booking = $this->bookingModel->find($id);
        if ($booking !== null && $this->service->confirm($id)) {
            $confirmed = $this->bookingModel->find($id);
            $resource  = $confirmed !== null ? $this->resourceModel->find($confirmed->resourceId) : null;
            if ($confirmed !== null && $resource !== null) {
                (new BookingMailer())->sendCustomerConfirmation($confirmed, $resource);
            }
        }
        $this->redirectWithSuccess('/admin/apps/booking/detail/' . $id, t('success.saved'));
    }

    public function cancel(int $id): void
    {
        Auth::verifyCsrf();
        $booking = $this->bookingModel->find($id);
        if ($booking !== null && $this->service->cancel($id)) {
            $canceled = $this->bookingModel->find($id);
            if ($canceled !== null) {
                (new BookingMailer())->sendCancellationNotice($canceled);
            }
        }
        $this->redirectWithSuccess('/admin/apps/booking/detail/' . $id, t('success.saved'));
    }
}
