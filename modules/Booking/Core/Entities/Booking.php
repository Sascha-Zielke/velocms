<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\Entities;

use VeloCMS\Modules\Booking\Core\ValueObjects\BookingStatus;
use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;

final class Booking
{
    public function __construct(
        public readonly int           $id,
        public readonly int           $resourceId,
        public readonly string        $customerName,
        public readonly string        $customerEmail,
        public readonly ?string       $customerPhone,
        public readonly DateTimeRange $range,
        public readonly BookingStatus $status,
        public readonly ?string       $notes,
        public readonly array         $metadata,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id:            (int) $row['id'],
            resourceId:    (int) $row['resource_id'],
            customerName:  (string) $row['customer_name'],
            customerEmail: (string) $row['customer_email'],
            customerPhone: $row['customer_phone'] !== null ? (string) $row['customer_phone'] : null,
            range:         DateTimeRange::fromStrings(
                               (string) $row['start_at'],
                               (string) $row['end_at'],
                               'UTC'
                           ),
            status:        BookingStatus::from($row['status']),
            notes:         $row['notes'] !== null ? (string) $row['notes'] : null,
            metadata:      json_decode((string) ($row['metadata'] ?? '{}'), true) ?: [],
        );
    }

    public function isPending(): bool
    {
        return $this->status === BookingStatus::Pending;
    }

    public function isCanceled(): bool
    {
        return $this->status === BookingStatus::Canceled;
    }
}
