<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\Entities;

final class TimeSlot
{
    public function __construct(
        public readonly int    $id,
        public readonly int    $resourceId,
        public readonly int    $weekday,   // 0 = Sunday … 6 = Saturday
        public readonly string $startTime, // 'HH:MM:SS'
        public readonly string $endTime,   // 'HH:MM:SS'
        public readonly bool   $isActive,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id:         (int) $row['id'],
            resourceId: (int) $row['resource_id'],
            weekday:    (int) $row['weekday'],
            startTime:  (string) $row['start_time'],
            endTime:    (string) $row['end_time'],
            isActive:   (bool) $row['is_active'],
        );
    }

    /** Returns true if a given UTC datetime falls within this slot's weekday + time window. */
    public function covers(\DateTimeImmutable $dt, string $timezone = 'UTC'): bool
    {
        $local   = $dt->setTimezone(new \DateTimeZone($timezone));
        $weekday = (int) $local->format('w');
        $time    = $local->format('H:i:s');

        return $weekday === $this->weekday
            && $time >= $this->startTime
            && $time < $this->endTime;
    }
}
