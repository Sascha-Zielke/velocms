<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class AddContactSettings extends Migration
{
    public function up(): void
    {
        $defaults = [
            'contact_recipient_email' => '',
            'contact_from_name'       => 'Kontaktformular',
            'contact_subject_prefix'  => '[Kontakt]',
            'contact_rate_limit'      => '3',
            'contact_store_messages'  => '1',
            'contact_retention_days'  => '90',
            'contact_privacy_url'     => '/datenschutz',
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
        $keys = [
            'contact_recipient_email',
            'contact_from_name',
            'contact_subject_prefix',
            'contact_rate_limit',
            'contact_store_messages',
            'contact_retention_days',
            'contact_privacy_url',
        ];
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $this->db->prepare("DELETE FROM velocms_settings WHERE `key` IN ({$placeholders})")
                 ->execute($keys);
    }
}
