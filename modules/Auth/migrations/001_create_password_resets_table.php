<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreatePasswordResetsTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_password_resets (
                id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id     INT UNSIGNED NOT NULL,
                token_hash  VARCHAR(128) NOT NULL COMMENT 'SHA-256 hash of the raw token',
                expires_at  DATETIME NOT NULL,
                used_at     DATETIME NULL,
                created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_token_hash (token_hash),
                INDEX idx_user_id (user_id),
                INDEX idx_expires_at (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_password_resets");
    }
}
