<?php
declare(strict_types=1);
use VeloCMS\Core\Migration;

class CreateSectionsTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_sections (
                id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                page_id    INT UNSIGNED NOT NULL,
                sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                settings   JSON NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX idx_page(page_id),
                INDEX idx_sort(page_id, sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
    public function down(): void { $this->db->exec('DROP TABLE IF EXISTS velocms_sections'); }
}
