<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Auth\Models;

use VeloCMS\Core\Model;

class UserModel extends Model
{
    protected string $table = 'velocms_users';

    private const ALLOWED_ROLES  = ['editor', 'admin', 'superadmin'];
    private const DEFAULT_ROLE   = 'editor';

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
            throw new \RuntimeException('Failed to insert user — lastInsertId() returned 0');
        }

        return $id;
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET last_login_at = NOW() WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }
}
