<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Models;

use VeloCMS\Core\Database;
use VeloCMS\Modules\Booking\Core\Entities\TimeSlot;

class SlotModel
{
    private \PDO $db;

    public function __construct(?\PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getPdo();
    }

    /** @return TimeSlot[] */
    public function forResource(int $resourceId, bool $activeOnly = true): array
    {
        $sql = 'SELECT * FROM velocms_booking_slots WHERE resource_id = :rid';
        if ($activeOnly) {
            $sql .= ' AND is_active = 1';
        }
        $sql .= ' ORDER BY weekday ASC, start_time ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':rid' => $resourceId]);
        return array_map(fn(array $row) => TimeSlot::fromRow($row), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function create(int $resourceId, int $weekday, string $startTime, string $endTime): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO velocms_booking_slots (resource_id, weekday, start_time, end_time, is_active)
            VALUES (:resource_id, :weekday, :start_time, :end_time, 1)
        ');
        $stmt->execute([
            ':resource_id' => $resourceId,
            ':weekday'     => $weekday,
            ':start_time'  => $startTime,
            ':end_time'    => $endTime,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM velocms_booking_slots WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function deleteForResource(int $resourceId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM velocms_booking_slots WHERE resource_id = :rid');
        return $stmt->execute([':rid' => $resourceId]);
    }
}
