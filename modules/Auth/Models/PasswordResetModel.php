<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Auth\Models;

use VeloCMS\Core\Model;

/**
 * Manages password-reset tokens.
 *
 * Security notes:
 * - Raw token is never stored; only its SHA-256 hash is persisted.
 * - Tokens expire after 1 hour and can only be used once.
 * - `findValidToken()` returns null for expired or already-used tokens,
 *   preventing any timing hints about token existence.
 */
class PasswordResetModel extends Model
{
    protected string $table = 'velocms_password_resets';

    private const TOKEN_TTL_MINUTES = 60;

    // ------------------------------------------------------------------ writes

    /**
     * Invalidate all existing tokens for this user, then create a new one.
     *
     * Returns the raw (unhashed) token that must be sent via e-mail.
     * The caller MUST NOT store the raw token anywhere beyond the e-mail.
     */
    public function createToken(int $userId): string
    {
        // Purge old tokens for this user (clean state before issuing new one)
        $del = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE user_id = :uid"
        );
        $del->execute([':uid' => $userId]);

        $rawToken  = bin2hex(random_bytes(64));          // 128 hex chars
        $tokenHash = hash('sha256', $rawToken);
        $expiresAt = date('Y-m-d H:i:s', time() + self::TOKEN_TTL_MINUTES * 60);

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (user_id, token_hash, expires_at)
             VALUES (:user_id, :token_hash, :expires_at)"
        );
        $stmt->execute([
            ':user_id'    => $userId,
            ':token_hash' => $tokenHash,
            ':expires_at' => $expiresAt,
        ]);

        return $rawToken;
    }

    /**
     * Look up a valid (non-expired, not-yet-used) token row.
     *
     * Returns the full row array, or null if the token is invalid/expired/used.
     */
    public function findValidToken(string $rawToken): ?array
    {
        $tokenHash = hash('sha256', $rawToken);

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE token_hash = :hash
               AND used_at   IS NULL
               AND expires_at > NOW()
             LIMIT 1"
        );
        $stmt->execute([':hash' => $tokenHash]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Mark a token as used (one-time-use enforcement).
     */
    public function markUsed(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET used_at = NOW() WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }

    /**
     * Remove all expired tokens (housekeeping — called after reset or on cron).
     */
    public function purgeExpired(): int
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE expires_at < NOW()"
        );
        $stmt->execute();
        return (int) $stmt->rowCount();
    }
}
