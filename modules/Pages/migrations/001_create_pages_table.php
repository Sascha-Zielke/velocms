<?php
declare(strict_types=1);
use VeloCMS\Core\Migration;

class CreatePagesTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_pages (
                id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
                slug               VARCHAR(255) NOT NULL UNIQUE,
                title              VARCHAR(255) NOT NULL,
                title_en           VARCHAR(255) NULL,
                status             ENUM('draft','published') NOT NULL DEFAULT 'draft',
                meta_title         VARCHAR(255) NULL,
                meta_description   VARCHAR(500) NULL,
                manual_override_en TINYINT(1) NOT NULL DEFAULT 0,
                created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at         DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at         DATETIME NULL,
                PRIMARY KEY (id),
                INDEX idx_slug   (slug),
                INDEX idx_status (status),
                INDEX idx_deleted(deleted_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
    public function down(): void { $this->db->exec('DROP TABLE IF EXISTS velocms_pages'); }
}
