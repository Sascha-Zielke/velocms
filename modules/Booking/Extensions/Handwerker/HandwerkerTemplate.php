<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Extensions\Handwerker;

use VeloCMS\Modules\Booking\Core\Contracts\BookingTemplateInterface;

class HandwerkerTemplate implements BookingTemplateInterface
{
    public function key(): string
    {
        return 'handwerker';
    }

    public function label(): string
    {
        return t('booking.template_handwerker');
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
        return true;
    }

    public function validate(array $data): array
    {
        $errors = [];
        if (empty(trim((string) ($data['address'] ?? '')))) {
            $errors[] = 'Einsatzadresse ist erforderlich.';
        }
        if (empty(trim((string) ($data['job_description'] ?? '')))) {
            $errors[] = 'Auftragsbeschreibung ist erforderlich.';
        }
        return $errors;
    }

    public function extractMetadata(array $data): array
    {
        return [
            'address'         => trim((string) ($data['address'] ?? '')),
            'job_description' => trim((string) ($data['job_description'] ?? '')),
        ];
    }

    public function formFields(): array
    {
        return [
            [
                'name'     => 'address',
                'label'    => 'Einsatzadresse',
                'type'     => 'text',
                'required' => true,
            ],
            [
                'name'     => 'job_description',
                'label'    => 'Auftragsbeschreibung',
                'type'     => 'textarea',
                'required' => true,
            ],
        ];
    }
}
