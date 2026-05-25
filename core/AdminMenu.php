<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class AdminMenu
{
    private static array $items = [];

    private const ROLE_WEIGHT = [
        'editor'     => 1,
        'admin'      => 2,
        'superadmin' => 3,
    ];

    public function addMenuItem(array $item): void
    {
        self::$items[] = $item;
    }

    public static function getItems(): array
    {
        $currentWeight = self::ROLE_WEIGHT[Auth::role() ?? 'editor'] ?? 1;

        $items = array_filter(self::$items, function (array $item) use ($currentWeight): bool {
            $minRole    = $item['min_role'] ?? 'editor';
            $minWeight  = self::ROLE_WEIGHT[$minRole] ?? 1;
            return $currentWeight >= $minWeight;
        });

        usort($items, fn($a, $b) => ($a['position'] ?? 0) <=> ($b['position'] ?? 0));
        return array_values($items);
    }

    public static function reset(): void
    {
        self::$items = [];
    }
}
