<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

/**
 * Aligns velocms_sites with what Tenant::resolve() expects:
 *  - www_alias  for automatic www-redirect resolution
 *  - status     ENUM replaces the old `active` TINYINT
 */
class UpdateSitesTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            ALTER TABLE velocms_sites
                ADD COLUMN www_alias VARCHAR(255) NULL   AFTER domain,
                ADD COLUMN status    ENUM('active','suspended','provisioning')
                                     NOT NULL DEFAULT 'active' AFTER db_name
        ");

        // Carry forward existing active flag into status
        $this->db->exec("
            UPDATE velocms_sites
               SET status = CASE WHEN active = 1 THEN 'active' ELSE 'suspended' END
        ");
    }

    public function down(): void
    {
        $this->db->exec("
            ALTER TABLE velocms_sites
                DROP COLUMN www_alias,
                DROP COLUMN status
        ");
    }
}
