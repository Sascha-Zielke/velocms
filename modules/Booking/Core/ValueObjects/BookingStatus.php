<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\ValueObjects;

enum BookingStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Canceled  = 'canceled';

    public function label(): string
    {
        return match($this) {
            self::Pending   => t('booking.status_pending'),
            self::Confirmed => t('booking.status_confirmed'),
            self::Canceled  => t('booking.status_canceled'),
        };
    }

    public function isTerminal(): bool
    {
        return $this === self::Canceled;
    }
}
