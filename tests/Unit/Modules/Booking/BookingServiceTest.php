<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Booking;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Booking\Core\Entities\Booking;
use VeloCMS\Modules\Booking\Core\Services\AvailabilityEngine;
use VeloCMS\Modules\Booking\Core\Services\BookingConflictException;
use VeloCMS\Modules\Booking\Core\Services\BookingOutsideSlotsException;
use VeloCMS\Modules\Booking\Core\Services\BookingService;
use VeloCMS\Modules\Booking\Core\ValueObjects\BookingStatus;
use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;
use VeloCMS\Modules\Booking\Models\BookingModel;
use VeloCMS\Modules\Booking\Models\ResourceModel;

class BookingServiceTest extends TestCase
{
    private MockObject $bookingModel;
    private MockObject $resourceModel;
    private MockObject $availability;
    private MockObject $db;
    private BookingService $service;

    protected function setUp(): void
    {
        $this->bookingModel  = $this->createMock(BookingModel::class);
        $this->resourceModel = $this->createMock(ResourceModel::class);
        $this->availability  = $this->createMock(AvailabilityEngine::class);

        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);

        $this->db = $this->createMock(\PDO::class);
        $this->db->method('prepare')->willReturn($stmt);
        $this->db->method('beginTransaction')->willReturn(true);
        $this->db->method('commit')->willReturn(true);
        $this->db->method('rollBack')->willReturn(true);

        $this->service = new BookingService(
            $this->bookingModel,
            $this->resourceModel,
            $this->availability,
            $this->db,
        );
    }

    private function makeRange(): DateTimeRange
    {
        return DateTimeRange::fromStrings('2026-06-01 09:00:00', '2026-06-01 10:00:00', 'UTC');
    }

    public function testBook_throwsConflictException_whenSlotTaken(): void
    {
        $this->expectException(BookingConflictException::class);

        $range   = $this->makeRange();
        $booking = $this->buildBooking(BookingStatus::Confirmed);

        $this->availability->method('isWithinSlot')->willReturn(true);
        $this->bookingModel->method('overlapping')->willReturn([$booking]);

        $this->service->book(1, 'Test', 'test@example.com', null, $range, null, []);
    }

    public function testBook_throwsOutsideSlotsException_whenOutsideSlot(): void
    {
        $this->expectException(BookingOutsideSlotsException::class);

        $range = $this->makeRange();
        $this->availability->method('isWithinSlot')->willReturn(false);

        $this->service->book(1, 'Test', 'test@example.com', null, $range, null, []);
    }

    public function testConfirm_returnsFalse_whenBookingNotFound(): void
    {
        $this->bookingModel->method('find')->willReturn(null);
        $this->assertFalse($this->service->confirm(999));
    }

    public function testConfirm_returnsFalse_whenBookingNotPending(): void
    {
        $booking = $this->buildBooking(BookingStatus::Confirmed);
        $this->bookingModel->method('find')->willReturn($booking);
        $this->assertFalse($this->service->confirm(1));
    }

    public function testCancel_returnsFalse_whenAlreadyCanceled(): void
    {
        $booking = $this->buildBooking(BookingStatus::Canceled);
        $this->bookingModel->method('find')->willReturn($booking);
        $this->assertFalse($this->service->cancel(1));
    }

    private function buildBooking(BookingStatus $status): Booking
    {
        $range = $this->makeRange();
        return new Booking(
            id:            1,
            resourceId:    1,
            customerName:  'Test',
            customerEmail: 'test@example.com',
            customerPhone: null,
            range:         $range,
            status:        $status,
            notes:         null,
            metadata:      [],
        );
    }
}
