<?php
declare(strict_types=1);

class CreateMediaTable
{
    public function __construct(private \PDO $db) {}

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_media (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                filename    VARCHAR(255) NOT NULL,
                original    VARCHAR(255) NOT NULL,
                mime        VARCHAR(100) NOT NULL,
                size        INT UNSIGNED NOT NULL DEFAULT 0,
                width       SMALLINT UNSIGNED NULL,
                height      SMALLINT UNSIGNED NULL,
                path        VARCHAR(500) NOT NULL,
                alt_de      VARCHAR(255) NOT NULL DEFAULT '',
                alt_en      VARCHAR(255) NOT NULL DEFAULT '',
                uploaded_by INT UNSIGNED NOT NULL DEFAULT 1,
                created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_mime (mime),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
