<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateSettingsTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_settings (
                `key`       VARCHAR(100) NOT NULL,
                value       TEXT NOT NULL DEFAULT '',
                updated_at  DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $defaults = [
            'site_name'                => 'Meine Website',
            'site_tagline'             => '',
            'site_email'               => '',
            'logo_path'                => '',
            'favicon_path'             => '',
            'meta_title_suffix'        => ' | Meine Website',
            'meta_description_default' => '',
            'meta_keywords_default'    => '',
            'social_facebook'          => '',
            'social_instagram'         => '',
            'social_linkedin'          => '',
            'social_twitter'           => '',
            'footer_text'              => '&copy; ' . date('Y'),
            'footer_impressum_url'     => '/impressum',
            'footer_datenschutz_url'   => '/datenschutz',
            'homepage_slug'            => 'startseite',
            'maintenance_mode'         => '0',
        ];

        $stmt = $this->db->prepare(
            'INSERT IGNORE INTO velocms_settings (`key`, value) VALUES (:key, :value)'
        );
        foreach ($defaults as $key => $value) {
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
    }

    public function down(): void
    {
        $this->db->exec('DROP TABLE IF EXISTS velocms_settings');
    }
}
