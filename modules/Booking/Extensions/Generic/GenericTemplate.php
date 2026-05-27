<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Extensions\Generic;

use VeloCMS\Modules\Booking\Core\Contracts\BookingTemplateInterface;
use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;

class GenericTemplate implements BookingTemplateInterface
{
    public function key(): string
    {
        return 'generic';
    }

    public function label(): string
    {
        return t('booking.template_generic');
    }

    public function minDurationMinutes(): int
    {
        return 30;
    }

    public function maxAdvanceDays(): int
    {
        return 30;
    }

    public function requiresConfirmation(): bool
    {
        return false;
    }

    public function validate(array $data): array
    {
        return [];
    }

    public function extractMetadata(array $data): array
    {
        return [];
    }

    public function formFields(): array
    {
        return [];
    }
}
