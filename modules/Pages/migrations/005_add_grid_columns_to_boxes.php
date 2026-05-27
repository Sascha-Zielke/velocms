<?php
declare(strict_types=1);
use VeloCMS\Core\Migration;

class AddGridColumnsToBoxes extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            ALTER TABLE velocms_boxes
                ADD COLUMN grid_x SMALLINT NULL DEFAULT NULL AFTER data,
                ADD COLUMN grid_y SMALLINT NULL DEFAULT NULL AFTER grid_x,
                ADD COLUMN grid_w SMALLINT NULL DEFAULT NULL AFTER grid_y,
                ADD COLUMN grid_h SMALLINT NULL DEFAULT NULL AFTER grid_w
        ");
    }

    public function down(): void
    {
        $this->db->exec("
            ALTER TABLE velocms_boxes
                DROP COLUMN grid_x,
                DROP COLUMN grid_y,
                DROP COLUMN grid_w,
                DROP COLUMN grid_h
        ");
    }
}
