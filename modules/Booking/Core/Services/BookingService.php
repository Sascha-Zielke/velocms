<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\Services;

use VeloCMS\Core\Database;
use VeloCMS\Modules\Booking\Core\Entities\Booking;
use VeloCMS\Modules\Booking\Core\ValueObjects\BookingStatus;
use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;
use VeloCMS\Modules\Booking\Models\BookingModel;
use VeloCMS\Modules\Booking\Models\ResourceModel;

class BookingService
{
    private \PDO $db;

    public function __construct(
        private readonly BookingModel      $bookingModel,
        private readonly ResourceModel     $resourceModel,
        private readonly AvailabilityEngine $availability,
        ?\PDO $db = null,
    ) {
        $this->db = $db ?? Database::getInstance()->getPdo();
    }

    /**
     * Creates a booking with double-booking protection.
     *
     * Locks the resource row for the duration of the overlap check so
     * concurrent requests cannot race into the same slot.
     *
     * @param array<string,mixed> $metadata Template-specific extra fields
     * @throws BookingConflictException   When the slot is already taken
     * @throws BookingOutsideSlotsException When the time falls outside configured availability
     */
    public function book(
        int    $resourceId,
        string $customerName,
        string $customerEmail,
        ?string $customerPhone,
        DateTimeRange $range,
        ?string $notes,
        array  $metadata,
        string $timezone = 'UTC',
    ): Booking {
        $this->db->beginTransaction();

        try {
            // Lock the resource row to serialise concurrent booking attempts
            $lock = $this->db->prepare(
                'SELECT id FROM velocms_booking_resources WHERE id = :id FOR UPDATE'
            );
            $lock->execute([':id' => $resourceId]);

            if (!$this->availability->isWithinSlot($resourceId, $range, $timezone)) {
                $this->db->rollBack();
                throw new BookingOutsideSlotsException('The selected time is outside available hours.');
            }

            $conflicts = $this->bookingModel->overlapping($resourceId, $range);
            if (!empty($conflicts)) {
                $this->db->rollBack();
                throw new BookingConflictException('The selected time is already booked.');
            }

            $resource = $this->resourceModel->find($resourceId);
            $status   = $resource !== null && $this->requiresConfirmation($resourceId)
                ? BookingStatus::Pending
                : BookingStatus::Confirmed;

            $id = $this->bookingModel->insert(
                resourceId:    $resourceId,
                customerName:  $customerName,
                customerEmail: $customerEmail,
                customerPhone: $customerPhone,
                range:         $range,
                status:        $status,
                notes:         $notes,
                metadata:      $metadata,
            );

            $this->db->commit();

            return $this->bookingModel->find($id)
                ?? throw new \RuntimeException('Booking inserted but not found.');

        } catch (BookingConflictException|BookingOutsideSlotsException $e) {
            // Already rolled back above
            throw $e;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function confirm(int $bookingId): bool
    {
        $booking = $this->bookingModel->find($bookingId);
        if ($booking === null || !$booking->isPending()) {
            return false;
        }
        return $this->bookingModel->updateStatus($bookingId, BookingStatus::Confirmed);
    }

    public function cancel(int $bookingId): bool
    {
        $booking = $this->bookingModel->find($bookingId);
        if ($booking === null || $booking->isCanceled()) {
            return false;
        }
        return $this->bookingModel->updateStatus($bookingId, BookingStatus::Canceled);
    }

    private function requiresConfirmation(int $resourceId): bool
    {
        $stmt = $this->db->prepare('
            SELECT t.config
            FROM velocms_booking_templates t
            JOIN velocms_booking_resources r ON r.template_key = t.template_key
            WHERE r.id = :rid
        ');
        $stmt->execute([':rid' => $resourceId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row === false) {
            return false;
        }
        $config = json_decode((string) $row['config'], true);
        return !empty($config['requires_confirmation']);
    }
}
