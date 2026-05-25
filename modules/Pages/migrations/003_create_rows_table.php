<?php
declare(strict_types=1);
use VeloCMS\Core\Migration;

class CreateRowsTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_rows (
                id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
                section_id  INT UNSIGNED NOT NULL,
                sort_order  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                cols_config JSON NULL,
                created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at  DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX idx_section(section_id),
                INDEX idx_sort(section_id, sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
    public function down(): void { $this->db->exec('DROP TABLE IF EXISTS velocms_rows'); }
}
