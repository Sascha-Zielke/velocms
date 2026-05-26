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
        // Deduplicate section headers by label — only one "Apps" header even if
        // multiple app-modules try to register it.
        if (($item['type'] ?? '') === 'section') {
            foreach (self::$items as $existing) {
                if (($existing['type'] ?? '') === 'section' && $existing['label'] === $item['label']) {
                    return;
                }
            }
        }

        self::$items[] = $item;
    }

    /**
     * Returns menu items visible to the current user, sorted by position.
     * Items with type='section' act as non-clickable group headers.
     */
    public static function getItems(): array
    {
        $currentWeight = self::ROLE_WEIGHT[Auth::role() ?? 'editor'] ?? 1;

        $items = array_filter(self::$items, function (array $item) use ($currentWeight): bool {
            $minRole   = $item['min_role'] ?? 'editor';
            $minWeight = self::ROLE_WEIGHT[$minRole] ?? 1;
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
