<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateSitesTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_sites (
                id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                domain     VARCHAR(255) NOT NULL UNIQUE,
                db_name    VARCHAR(100) NOT NULL,
                name       VARCHAR(255) NOT NULL DEFAULT '',
                active     TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX idx_domain (domain),
                INDEX idx_active (active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_sites");
    }
}
