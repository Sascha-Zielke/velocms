<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateBookingTemplates extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_booking_templates (
                id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
                template_key VARCHAR(50)  NOT NULL COMMENT 'e.g. restaurant, handwerker, studio',
                config       JSON         NOT NULL COMMENT 'Template-specific configuration',
                created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at   DATETIME     NULL     ON UPDATE CURRENT_TIMESTAMP,

                PRIMARY KEY (id),
                UNIQUE KEY uq_template_key (template_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Seed default generic template
        $this->db->exec("
            INSERT IGNORE INTO velocms_booking_templates (template_key, config)
            VALUES ('generic', '{\"slot_duration_minutes\": 60, \"max_advance_days\": 30, \"requires_confirmation\": false}')
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_booking_templates");
    }
}
