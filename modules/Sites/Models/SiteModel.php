<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Sites\Models;

use VeloCMS\Core\Model;

class SiteModel extends Model
{
    protected string $table = 'velocms_sites';

    private const VALID_STATUSES = ['active', 'suspended', 'provisioning'];
    private const DB_NAME_PATTERN = '/^[a-zA-Z0-9_]{1,64}$/';

    // ------------------------------------------------------------------ reads

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table}
             WHERE deleted_at IS NULL
             ORDER BY created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE id = :id AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function domainExists(string $domain, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM {$this->table}
             WHERE domain = :domain AND id != :exclude AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute([':domain' => $domain, ':exclude' => $excludeId]);
        return (bool) $stmt->fetch();
    }

    public function dbNameExists(string $dbName, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM {$this->table}
             WHERE db_name = :db_name AND id != :exclude AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute([':db_name' => $dbName, ':exclude' => $excludeId]);
        return (bool) $stmt->fetch();
    }

    // ----------------------------------------------------------------- writes

    public function create(array $data): int
    {
        $status = $data['status'] ?? 'provisioning';
        if (!in_array($status, self::VALID_STATUSES, true)) {
            $status = 'provisioning';
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (domain, www_alias, name, db_name, status)
             VALUES (:domain, :www_alias, :name, :db_name, :status)"
        );
        $stmt->execute([
            ':domain'    => $data['domain'],
            ':www_alias' => $data['www_alias'] ?: null,
            ':name'      => $data['name'],
            ':db_name'   => $data['db_name'],
            ':status'    => $status,
        ]);

        $id = (int) $this->db->lastInsertId();
        if ($id === 0) {
            throw new \RuntimeException('Failed to insert site record');
        }
        return $id;
    }

    public function update(int $id, array $data): void
    {
        $status = $data['status'] ?? 'active';
        if (!in_array($status, self::VALID_STATUSES, true)) {
            $status = 'active';
        }

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET domain    = :domain,
                 www_alias = :www_alias,
                 name      = :name,
                 db_name   = :db_name,
                 status    = :status
             WHERE id = :id AND deleted_at IS NULL"
        );
        $stmt->execute([
            ':domain'    => $data['domain'],
            ':www_alias' => $data['www_alias'] ?: null,
            ':name'      => $data['name'],
            ':db_name'   => $data['db_name'],
            ':status'    => $status,
            ':id'        => $id,
        ]);
    }

    public function softDelete(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET deleted_at = NOW(), status = 'suspended'
             WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }

    // ---------------------------------------------------------------- helpers

    public static function isValidDbName(string $name): bool
    {
        return (bool) preg_match(self::DB_NAME_PATTERN, $name);
    }

    /**
     * Attempt to create the tenant's MySQL database.
     *
     * Requires the DB user to have CREATE privilege.
     * Returns true on success, false if the DB user lacks permission.
     *
     * @throws \PDOException on unexpected DB errors
     */
    public function provisionDb(string $dbName): bool
    {
        if (!self::isValidDbName($dbName)) {
            return false;
        }

        try {
            // Backtick-quote after whitelist validation above
            $this->db->exec(
                "CREATE DATABASE IF NOT EXISTS `{$dbName}`
                 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
            return true;
        } catch (\PDOException $e) {
            // Most likely: DB user lacks CREATE DATABASE privilege
            error_log('[VeloCMS] provisionDb failed for ' . $dbName . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set site status to 'active' after successful provisioning.
     */
    public function markActive(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET status = 'active' WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }
}
