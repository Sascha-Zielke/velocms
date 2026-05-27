<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\Entities;

use VeloCMS\Modules\Booking\Core\ValueObjects\ResourceType;

final class Resource
{
    public function __construct(
        public readonly int          $id,
        public readonly string       $name,
        public readonly ResourceType $type,
        public readonly string       $templateKey,
        public readonly array        $metadata,
        public readonly bool         $isActive,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id:          (int) $row['id'],
            name:        (string) $row['name'],
            type:        ResourceType::from($row['type']),
            templateKey: (string) $row['template_key'],
            metadata:    json_decode((string) ($row['metadata'] ?? '{}'), true) ?: [],
            isActive:    (bool) $row['is_active'],
        );
    }
}
