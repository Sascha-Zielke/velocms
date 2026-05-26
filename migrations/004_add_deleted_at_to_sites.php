<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

/**
 * Adds the soft-delete column to velocms_sites.
 * SiteModel::getAll/getById/softDelete all rely on deleted_at,
 * which was missing from the original schema.
 */
class AddDeletedAtToSites extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            ALTER TABLE velocms_sites
                ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL
        ");
    }

    public function down(): void
    {
        $this->db->exec("
            ALTER TABLE velocms_sites
                DROP COLUMN deleted_at
        ");
    }
}
