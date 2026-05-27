<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\ValueObjects;

enum ResourceType: string
{
    case Human = 'human';
    case Room  = 'room';
    case Asset = 'asset';

    public function label(): string
    {
        return match($this) {
            self::Human => 'Person',
            self::Room  => 'Raum',
            self::Asset => 'Gegenstand',
        };
    }
}
