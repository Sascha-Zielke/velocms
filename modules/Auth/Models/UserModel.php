<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Auth\Models;

use VeloCMS\Core\Model;

class UserModel extends Model
{
    protected string $table = 'velocms_users';

    private const ALLOWED_ROLES = ['editor', 'admin', 'superadmin'];
    private const DEFAULT_ROLE  = 'editor';

    // ------------------------------------------------------------------ reads

    public function getByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE email = :email AND active = 1 AND deleted_at IS NULL
             LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
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

    /** Returns all non-deleted users ordered by role weight desc, then name. */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT id, name, email, role, active, last_login_at, created_at
             FROM {$this->table}
             WHERE deleted_at IS NULL
             ORDER BY FIELD(role,'superadmin','admin','editor'), name ASC"
        );
        return $stmt->fetchAll();
    }

    // ----------------------------------------------------------------- writes

    public function create(array $data): int
    {
        if (empty($data['password'])) {
            throw new \InvalidArgumentException('Password is required');
        }

        $role = $data['role'] ?? self::DEFAULT_ROLE;
        if (!in_array($role, self::ALLOWED_ROLES, true)) {
            throw new \InvalidArgumentException("Invalid role: {$role}");
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (name, email, password_hash, role)
             VALUES (:name, :email, :password_hash, :role)"
        );
        $stmt->execute([
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role'          => $role,
        ]);

        $id = (int) $this->db->lastInsertId();
        if ($id === 0) {
            throw new \RuntimeException('Failed to insert user');
        }
        return $id;
    }

    public function update(int $id, array $data): void
    {
        $role = $data['role'] ?? self::DEFAULT_ROLE;
        if (!in_array($role, self::ALLOWED_ROLES, true)) {
            throw new \InvalidArgumentException("Invalid role: {$role}");
        }

        $active = isset($data['active']) ? (int)(bool)$data['active'] : 1;

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET name = :name, email = :email, role = :role, active = :active
             WHERE id = :id AND deleted_at IS NULL"
        );
        $stmt->execute([
            ':name'   => $data['name'],
            ':email'  => $data['email'],
            ':role'   => $role,
            ':active' => $active,
            ':id'     => $id,
        ]);
    }

    public function setPassword(int $id, string $password): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET password_hash = :hash
             WHERE id = :id AND deleted_at IS NULL"
        );
        $stmt->execute([
            ':hash' => password_hash($password, PASSWORD_DEFAULT),
            ':id'   => $id,
        ]);
    }

    public function softDelete(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET deleted_at = NOW(), active = 0
             WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }

    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM {$this->table}
             WHERE email = :email AND id != :exclude AND deleted_at IS NULL
             LIMIT 1"
        );
        $stmt->execute([':email' => $email, ':exclude' => $excludeId]);
        return (bool) $stmt->fetch();
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET last_login_at = NOW() WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }
}
