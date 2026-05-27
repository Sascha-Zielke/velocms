<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateBookings extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_bookings (
                id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
                resource_id      INT UNSIGNED  NOT NULL,
                customer_name    VARCHAR(255)  NOT NULL,
                customer_email   VARCHAR(255)  NOT NULL,
                customer_phone   VARCHAR(50)   NULL,
                start_at         DATETIME      NOT NULL COMMENT 'UTC',
                end_at           DATETIME      NOT NULL COMMENT 'UTC',
                status           ENUM('pending','confirmed','canceled') NOT NULL DEFAULT 'pending',
                notes            TEXT          NULL,
                metadata         JSON          NULL     COMMENT 'Template-specific extra fields',
                created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at       DATETIME      NULL     ON UPDATE CURRENT_TIMESTAMP,
                canceled_at      DATETIME      NULL,

                PRIMARY KEY (id),
                KEY idx_resource_time  (resource_id, start_at, end_at),
                KEY idx_status         (status),
                KEY idx_customer_email (customer_email),
                CONSTRAINT fk_bookings_resource
                    FOREIGN KEY (resource_id)
                    REFERENCES velocms_booking_resources (id)
                    ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_bookings");
    }
}
