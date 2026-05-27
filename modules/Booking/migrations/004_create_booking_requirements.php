<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateBookingRequirements extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_booking_requirements (
                id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
                booking_id  INT UNSIGNED NOT NULL,
                resource_id INT UNSIGNED NOT NULL,
                quantity    TINYINT      NOT NULL DEFAULT 1 COMMENT 'Number of resource units needed',

                PRIMARY KEY (id),
                UNIQUE KEY uq_booking_resource (booking_id, resource_id),
                KEY idx_resource (resource_id),
                CONSTRAINT fk_req_booking
                    FOREIGN KEY (booking_id)
                    REFERENCES velocms_bookings (id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_req_resource
                    FOREIGN KEY (resource_id)
                    REFERENCES velocms_booking_resources (id)
                    ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_booking_requirements");
    }
}
