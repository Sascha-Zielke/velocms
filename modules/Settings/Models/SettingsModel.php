<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Settings\Models;

use VeloCMS\Core\Model;

class SettingsModel extends Model
{
    protected string $table = 'velocms_settings';

    /**
     * Returns all settings as key → value array.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT `key`, value FROM velocms_settings');
        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
    }

    /**
     * Insert or update a single setting.
     */
    public function set(string $key, string $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO velocms_settings (`key`, value) VALUES (:key, :value)
             ON DUPLICATE KEY UPDATE value = :value2'
        );
        $stmt->execute([':key' => $key, ':value' => $value, ':value2' => $value]);
    }

    /**
     * Save multiple settings at once.
     *
     * @param array<string, string> $data
     */
    public function bulkSave(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}
