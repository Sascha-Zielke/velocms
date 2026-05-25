<?php
declare(strict_types=1);
use VeloCMS\Core\Migration;

class CreateBoxesTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_boxes (
                id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                row_id     INT UNSIGNED NOT NULL,
                sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                type       ENUM('text','image','video','button','spacer') NOT NULL DEFAULT 'text',
                data       JSON NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX idx_row (row_id),
                INDEX idx_sort(row_id, sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
    public function down(): void { $this->db->exec('DROP TABLE IF EXISTS velocms_boxes'); }
}
