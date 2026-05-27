<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Extensions\Studio;

use VeloCMS\Modules\Booking\Core\Contracts\BookingTemplateInterface;

class StudioTemplate implements BookingTemplateInterface
{
    public function key(): string
    {
        return 'studio';
    }

    public function label(): string
    {
        return t('booking.template_studio');
    }

    public function minDurationMinutes(): int
    {
        return 60;
    }

    public function maxAdvanceDays(): int
    {
        return 90;
    }

    public function requiresConfirmation(): bool
    {
        return false;
    }

    public function validate(array $data): array
    {
        $errors = [];
        if (empty(trim((string) ($data['project_name'] ?? '')))) {
            $errors[] = 'Projektname ist erforderlich.';
        }
        return $errors;
    }

    public function extractMetadata(array $data): array
    {
        return [
            'project_name' => trim((string) ($data['project_name'] ?? '')),
            'equipment'    => trim((string) ($data['equipment'] ?? '')),
        ];
    }

    public function formFields(): array
    {
        return [
            [
                'name'     => 'project_name',
                'label'    => 'Projektname',
                'type'     => 'text',
                'required' => true,
            ],
            [
                'name'     => 'equipment',
                'label'    => 'Benötigtes Equipment',
                'type'     => 'text',
                'required' => false,
            ],
        ];
    }
}
