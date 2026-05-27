<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\Services;

use VeloCMS\Modules\Booking\Core\Entities\TimeSlot;
use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;
use VeloCMS\Modules\Booking\Models\BookingModel;
use VeloCMS\Modules\Booking\Models\SlotModel;

class AvailabilityEngine
{
    public function __construct(
        private readonly SlotModel   $slotModel,
        private readonly BookingModel $bookingModel,
    ) {}

    /**
     * Returns available DateTimeRange slots for a resource on a given UTC date.
     * Each slot has exactly $durationMinutes length and fits within a TimeSlot window
     * without overlapping existing confirmed/pending bookings.
     *
     * @return DateTimeRange[]
     */
    public function availableSlots(
        int    $resourceId,
        string $dateUtc,
        int    $durationMinutes,
        string $timezone = 'UTC',
    ): array {
        $slots    = $this->slotModel->forResource($resourceId);
        $tz       = new \DateTimeZone($timezone);
        $date     = new \DateTimeImmutable($dateUtc, $tz);
        $weekday  = (int) $date->format('w');

        $daySlots = array_filter($slots, fn(TimeSlot $s) => $s->weekday === $weekday && $s->isActive);

        if (empty($daySlots)) {
            return [];
        }

        $available = [];

        foreach ($daySlots as $slot) {
            $windowStart = \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $date->format('Y-m-d') . ' ' . $slot->startTime,
                $tz
            );
            $windowEnd = \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $date->format('Y-m-d') . ' ' . $slot->endTime,
                $tz
            );

            if ($windowStart === false || $windowEnd === false) {
                continue;
            }

            $step = new \DateInterval('PT' . $durationMinutes . 'M');
            $cursor = $windowStart;

            while (true) {
                $slotEnd = $cursor->add($step);
                if ($slotEnd > $windowEnd) {
                    break;
                }

                $range = new DateTimeRange($cursor, $slotEnd);

                if (empty($this->bookingModel->overlapping($resourceId, $range))) {
                    $available[] = $range;
                }

                $cursor = $slotEnd;
            }
        }

        return $available;
    }

    /**
     * Returns true if the given range is within any active slot for the resource.
     */
    public function isWithinSlot(int $resourceId, DateTimeRange $range, string $timezone = 'UTC'): bool
    {
        $slots = $this->slotModel->forResource($resourceId);

        foreach ($slots as $slot) {
            $endMinus1 = $range->end->modify('-1 second');
            if ($endMinus1 === false) {
                continue;
            }
            if ($slot->covers($range->start, $timezone) && $slot->covers($endMinus1, $timezone)) {
                return true;
            }
        }

        return false;
    }
}
