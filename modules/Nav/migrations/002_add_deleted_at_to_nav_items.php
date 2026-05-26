<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

/**
 * Adds the soft-delete column to velocms_nav_items.
 * The table was created before the column existed in the migration,
 * so CREATE TABLE IF NOT EXISTS silently skipped the full schema.
 */
class AddDeletedAtToNavItems extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            ALTER TABLE velocms_nav_items
                ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL
        ");
    }

    public function down(): void
    {
        $this->db->exec("
            ALTER TABLE velocms_nav_items
                DROP COLUMN deleted_at
        ");
    }
}
