<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Core\Services;

use VeloCMS\Modules\Booking\Core\Contracts\BookingTemplateInterface;

class TemplateRegistry
{
    /** @var array<string, BookingTemplateInterface> */
    private static array $templates = [];

    public static function register(BookingTemplateInterface $template): void
    {
        self::$templates[$template->key()] = $template;
    }

    public static function get(string $key): ?BookingTemplateInterface
    {
        return self::$templates[$key] ?? null;
    }

    /** @return BookingTemplateInterface[] */
    public static function all(): array
    {
        return array_values(self::$templates);
    }

    public static function keys(): array
    {
        return array_keys(self::$templates);
    }
}
