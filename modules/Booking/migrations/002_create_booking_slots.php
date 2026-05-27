<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateBookingSlots extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_booking_slots (
                id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
                resource_id INT UNSIGNED NOT NULL,
                weekday     TINYINT      NOT NULL COMMENT '0=Sunday … 6=Saturday',
                start_time  TIME         NOT NULL,
                end_time    TIME         NOT NULL,
                is_active   TINYINT(1)   NOT NULL DEFAULT 1,

                PRIMARY KEY (id),
                KEY idx_resource_day (resource_id, weekday),
                CONSTRAINT fk_slots_resource
                    FOREIGN KEY (resource_id)
                    REFERENCES velocms_booking_resources (id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_booking_slots");
    }
}
