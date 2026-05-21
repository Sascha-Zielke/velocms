<?php

declare(strict_types=1);

namespace VeloCMS\Core;

abstract class Model
{
    protected \PDO $db;
    protected string $table = '';

    public function __construct(?\PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getPdo();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function softDelete(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }
}
