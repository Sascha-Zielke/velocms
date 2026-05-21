<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class AdminMenu
{
    private static array $items = [];

    public function addMenuItem(array $item): void
    {
        self::$items[] = $item;
    }

    public static function getItems(): array
    {
        $items = self::$items;
        usort($items, fn($a, $b) => ($a['position'] ?? 0) <=> ($b['position'] ?? 0));
        return $items;
    }

    public static function reset(): void
    {
        self::$items = [];
    }
}
