<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Extensions\Restaurant;

use VeloCMS\Modules\Booking\Core\Contracts\BookingTemplateInterface;

class RestaurantTemplate implements BookingTemplateInterface
{
    public function key(): string
    {
        return 'restaurant';
    }

    public function label(): string
    {
        return t('booking.template_restaurant');
    }

    public function minDurationMinutes(): int
    {
        return 60;
    }

    public function maxAdvanceDays(): int
    {
        return 60;
    }

    public function requiresConfirmation(): bool
    {
        return false;
    }

    public function validate(array $data): array
    {
        $errors = [];
        $guests = (int) ($data['guests'] ?? 0);
        if ($guests < 1 || $guests > 50) {
            $errors[] = 'Anzahl der Gäste muss zwischen 1 und 50 liegen.';
        }
        return $errors;
    }

    public function extractMetadata(array $data): array
    {
        return [
            'guests'      => (int) ($data['guests'] ?? 1),
            'preferences' => trim((string) ($data['preferences'] ?? '')),
        ];
    }

    public function formFields(): array
    {
        return [
            [
                'name'     => 'guests',
                'label'    => 'Anzahl Gäste',
                'type'     => 'number',
                'required' => true,
                'attrs'    => ['min' => 1, 'max' => 50],
            ],
            [
                'name'     => 'preferences',
                'label'    => 'Besondere Wünsche',
                'type'     => 'textarea',
                'required' => false,
            ],
        ];
    }
}
