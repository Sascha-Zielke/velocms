<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_users (
                id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name          VARCHAR(255) NOT NULL,
                email         VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role          ENUM('superadmin', 'admin', 'editor') NOT NULL DEFAULT 'editor',
                active        TINYINT(1) NOT NULL DEFAULT 1,
                last_login_at DATETIME NULL,
                created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at    DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at    DATETIME NULL,
                PRIMARY KEY (id),
                INDEX idx_email (email),
                INDEX idx_role (role),
                INDEX idx_deleted (deleted_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_users");
    }
}
