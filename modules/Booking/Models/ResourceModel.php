<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Booking\Models;

use VeloCMS\Core\Database;
use VeloCMS\Modules\Booking\Core\Entities\Resource;

class ResourceModel
{
    private \PDO $db;

    public function __construct(?\PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getPdo();
    }

    /** @return Resource[] */
    public function all(bool $activeOnly = false): array
    {
        $sql = 'SELECT * FROM velocms_booking_resources';
        if ($activeOnly) {
            $sql .= ' WHERE is_active = 1';
        }
        $sql .= ' ORDER BY name ASC';

        $stmt = $this->db->query($sql);
        return array_map(fn(array $row) => Resource::fromRow($row), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function find(int $id): ?Resource
    {
        $stmt = $this->db->prepare('SELECT * FROM velocms_booking_resources WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? Resource::fromRow($row) : null;
    }

    public function create(string $name, string $type, string $templateKey, array $metadata = []): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO velocms_booking_resources (name, type, template_key, metadata)
            VALUES (:name, :type, :template_key, :metadata)
        ');
        $stmt->execute([
            ':name'         => $name,
            ':type'         => $type,
            ':template_key' => $templateKey,
            ':metadata'     => json_encode($metadata),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $type, string $templateKey, array $metadata, bool $isActive): bool
    {
        $stmt = $this->db->prepare('
            UPDATE velocms_booking_resources
            SET name = :name, type = :type, template_key = :template_key,
                metadata = :metadata, is_active = :is_active
            WHERE id = :id
        ');
        return $stmt->execute([
            ':name'         => $name,
            ':type'         => $type,
            ':template_key' => $templateKey,
            ':metadata'     => json_encode($metadata),
            ':is_active'    => (int) $isActive,
            ':id'           => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM velocms_booking_resources WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
