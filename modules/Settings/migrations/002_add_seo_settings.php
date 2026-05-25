<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class AddSeoSettings extends Migration
{
    public function up(): void
    {
        // Insert new SEO keys — INSERT IGNORE keeps existing values
        $stmt = $this->db->prepare(
            'INSERT IGNORE INTO velocms_settings (`key`, value) VALUES (:key, :value)'
        );

        $defaults = [
            'app_url'    => 'https://webzite-newmedia.com',
            'robots_txt' => '',
        ];

        foreach ($defaults as $key => $value) {
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
    }

    public function down(): void
    {
        $this->db->exec(
            "DELETE FROM velocms_settings WHERE `key` IN ('app_url', 'robots_txt')"
        );
    }
}
