<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Models;

use VeloCMS\Core\Database;
use VeloCMS\Modules\Booking\Core\Entities\Booking;
use VeloCMS\Modules\Booking\Core\ValueObjects\BookingStatus;
use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;

class BookingModel
{
    private \PDO $db;

    public function __construct(?\PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getPdo();
    }

    public function find(int $id): ?Booking
    {
        $stmt = $this->db->prepare('SELECT * FROM velocms_bookings WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? Booking::fromRow($row) : null;
    }

    /**
     * Returns all bookings for a resource that overlap with the given range.
     * Called inside a FOR UPDATE transaction to prevent double-bookings.
     *
     * @return Booking[]
     */
    public function overlapping(int $resourceId, DateTimeRange $range): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM velocms_bookings
            WHERE resource_id = :rid
              AND status != :canceled
              AND start_at < :end_at
              AND end_at   > :start_at
        ');
        $stmt->execute([
            ':rid'      => $resourceId,
            ':canceled' => BookingStatus::Canceled->value,
            ':end_at'   => $range->endUtc(),
            ':start_at' => $range->startUtc(),
        ]);
        return array_map(fn(array $row) => Booking::fromRow($row), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /** @return Booking[] */
    public function forResource(int $resourceId, ?string $status = null, int $limit = 50): array
    {
        $sql    = 'SELECT * FROM velocms_bookings WHERE resource_id = :rid';
        $params = [':rid' => $resourceId];
        if ($status !== null) {
            $sql           .= ' AND status = :status';
            $params[':status'] = $status;
        }
        $sql .= ' ORDER BY start_at DESC LIMIT ' . $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return array_map(fn(array $row) => Booking::fromRow($row), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /** @return Booking[] */
    public function recent(int $limit = 50, ?string $status = null): array
    {
        $sql    = 'SELECT * FROM velocms_bookings';
        $params = [];
        if ($status !== null) {
            $sql            .= ' WHERE status = :status';
            $params[':status'] = $status;
        }
        $sql .= ' ORDER BY created_at DESC LIMIT ' . $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return array_map(fn(array $row) => Booking::fromRow($row), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function insert(
        int    $resourceId,
        string $customerName,
        string $customerEmail,
        ?string $customerPhone,
        DateTimeRange $range,
        BookingStatus $status,
        ?string $notes,
        array  $metadata
    ): int {
        $stmt = $this->db->prepare('
            INSERT INTO velocms_bookings
                (resource_id, customer_name, customer_email, customer_phone,
                 start_at, end_at, status, notes, metadata)
            VALUES
                (:resource_id, :customer_name, :customer_email, :customer_phone,
                 :start_at, :end_at, :status, :notes, :metadata)
        ');
        $stmt->execute([
            ':resource_id'    => $resourceId,
            ':customer_name'  => $customerName,
            ':customer_email' => $customerEmail,
            ':customer_phone' => $customerPhone,
            ':start_at'       => $range->startUtc(),
            ':end_at'         => $range->endUtc(),
            ':status'         => $status->value,
            ':notes'          => $notes,
            ':metadata'       => json_encode($metadata),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, BookingStatus $status): bool
    {
        $canceledAt = $status === BookingStatus::Canceled ? date('Y-m-d H:i:s') : null;
        $stmt       = $this->db->prepare('
            UPDATE velocms_bookings
            SET status = :status, canceled_at = :canceled_at
            WHERE id = :id
        ');
        return $stmt->execute([
            ':status'      => $status->value,
            ':canceled_at' => $canceledAt,
            ':id'          => $id,
        ]);
    }
}
