<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateBookingResources extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_booking_resources (
                id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
                name         VARCHAR(255)  NOT NULL,
                type         ENUM('human','room','asset') NOT NULL DEFAULT 'human',
                template_key VARCHAR(50)   NOT NULL DEFAULT 'generic' COMMENT 'Links to velocms_booking_templates',
                metadata     JSON          NULL                        COMMENT 'Industry-specific parameters',
                is_active    TINYINT(1)    NOT NULL DEFAULT 1,
                created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at   DATETIME      NULL     ON UPDATE CURRENT_TIMESTAMP,

                PRIMARY KEY (id),
                KEY idx_type_active (type, is_active),
                KEY idx_template    (template_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_booking_resources");
    }
}
