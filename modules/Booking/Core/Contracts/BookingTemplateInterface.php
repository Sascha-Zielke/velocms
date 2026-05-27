<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\Contracts;

use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;

interface BookingTemplateInterface
{
    /** Unique identifier used in velocms_booking_templates.template_key */
    public function key(): string;

    /** Human-readable template name for the Admin UI */
    public function label(): string;

    /** Minimum booking duration in minutes */
    public function minDurationMinutes(): int;

    /** Maximum days in advance a booking can be placed */
    public function maxAdvanceDays(): int;

    /** Whether new bookings start as 'pending' and require admin confirmation */
    public function requiresConfirmation(): bool;

    /**
     * Validate template-specific booking fields from request data.
     * Returns array of error messages — empty array means valid.
     *
     * @param array<string,mixed> $data
     * @return string[]
     */
    public function validate(array $data): array;

    /**
     * Extract template-specific metadata to store in velocms_bookings.metadata.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function extractMetadata(array $data): array;

    /**
     * Return additional fields to render in the booking form widget.
     * Each entry: ['name' => string, 'label' => string, 'type' => string, 'required' => bool]
     *
     * @return array<int, array<string,mixed>>
     */
    public function formFields(): array;
}
