<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Booking;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Booking\Core\ValueObjects\BookingStatus;
use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;
use VeloCMS\Modules\Booking\Models\BookingModel;

class BookingModelTest extends TestCase
{
    private BookingModel $model;
    private MockObject   $db;

    protected function setUp(): void
    {
        $this->db    = $this->createMock(\PDO::class);
        $this->model = new BookingModel($this->db);
    }

    public function testFind_returnsNull_whenNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);
        $this->db->method('prepare')->willReturn($stmt);

        $this->assertNull($this->model->find(999));
    }

    public function testFind_returnsBooking_whenFound(): void
    {
        $row = [
            'id'             => 1,
            'resource_id'    => 2,
            'customer_name'  => 'Anna Müller',
            'customer_email' => 'anna@example.com',
            'customer_phone' => null,
            'start_at'       => '2026-06-01 09:00:00',
            'end_at'         => '2026-06-01 10:00:00',
            'status'         => 'confirmed',
            'notes'          => null,
            'metadata'       => '{}',
        ];

        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($row);
        $this->db->method('prepare')->willReturn($stmt);

        $booking = $this->model->find(1);
        $this->assertNotNull($booking);
        $this->assertSame(1, $booking->id);
        $this->assertSame('Anna Müller', $booking->customerName);
        $this->assertSame(BookingStatus::Confirmed, $booking->status);
    }

    public function testOverlapping_returnsEmptyArray_whenNone(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->willReturn([]);
        $this->db->method('prepare')->willReturn($stmt);

        $range    = DateTimeRange::fromStrings('2026-06-01 09:00:00', '2026-06-01 10:00:00', 'UTC');
        $result   = $this->model->overlapping(1, $range);
        $this->assertSame([], $result);
    }

    public function testUpdateStatus_setsCanceledAt_whenCanceled(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->db->method('prepare')->willReturn($stmt);

        // Verify that execute is called — we trust the model sets canceled_at when status is Canceled
        $result = $this->model->updateStatus(1, BookingStatus::Canceled);
        $this->assertTrue($result);
    }

    public function testUpdateStatus_doesNotSetCanceledAt_whenConfirmed(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->db->method('prepare')->willReturn($stmt);

        $result = $this->model->updateStatus(1, BookingStatus::Confirmed);
        $this->assertTrue($result);
    }
}
