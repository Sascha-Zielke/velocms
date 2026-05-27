<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\ValueObjects;

use InvalidArgumentException;

final class DateTimeRange
{
    public function __construct(
        public readonly \DateTimeImmutable $start,
        public readonly \DateTimeImmutable $end,
    ) {
        if ($end <= $start) {
            throw new InvalidArgumentException('end must be after start');
        }
    }

    public static function fromStrings(string $start, string $end, string $timezone = 'UTC'): self
    {
        $tz = new \DateTimeZone($timezone);
        return new self(
            new \DateTimeImmutable($start, $tz),
            new \DateTimeImmutable($end, $tz),
        );
    }

    public function overlaps(self $other): bool
    {
        return $this->start < $other->end && $this->end > $other->start;
    }

    public function durationMinutes(): int
    {
        return (int) round(($this->end->getTimestamp() - $this->start->getTimestamp()) / 60);
    }

    /** Returns start in UTC for DB storage. */
    public function startUtc(): string
    {
        return $this->start->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }

    /** Returns end in UTC for DB storage. */
    public function endUtc(): string
    {
        return $this->end->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }
}
