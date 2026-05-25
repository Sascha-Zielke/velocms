<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateNavItemsTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_nav_items (
                id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
                label       VARCHAR(255) NOT NULL,
                label_en    VARCHAR(255) NULL,
                url         VARCHAR(500) NOT NULL,
                target      VARCHAR(20)  NOT NULL DEFAULT '_self',
                active      TINYINT(1)   NOT NULL DEFAULT 1,
                position    INT          NOT NULL DEFAULT 0,
                created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at  DATETIME     NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at  DATETIME     NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Seed with homepage entry if table is freshly created
        $count = (int) $this->db->query('SELECT COUNT(*) FROM velocms_nav_items')->fetchColumn();
        if ($count === 0) {
            $this->db->exec("
                INSERT INTO velocms_nav_items (label, url, active, position)
                VALUES ('Startseite', '/startseite', 1, 10)
            ");
        }
    }

    public function down(): void
    {
        $this->db->exec('DROP TABLE IF EXISTS velocms_nav_items');
    }
}
