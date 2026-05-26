<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateContactMessagesTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_contact_messages (
                id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name        VARCHAR(255) NOT NULL,
                email       VARCHAR(255) NOT NULL,
                subject     VARCHAR(255) NOT NULL DEFAULT '',
                message     TEXT NOT NULL,
                ip_address  VARCHAR(45) NOT NULL,
                user_agent  VARCHAR(500) NULL,
                status      ENUM('new','read','replied','spam') NOT NULL DEFAULT 'new',
                created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at  DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                deleted_at  DATETIME NULL,
                PRIMARY KEY (id),
                INDEX idx_ip_created (ip_address, created_at),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec('DROP TABLE IF EXISTS velocms_contact_messages');
    }
}
