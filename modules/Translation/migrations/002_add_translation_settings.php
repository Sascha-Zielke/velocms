<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

/**
 * Inserts Translation-App configuration keys into velocms_settings.
 * Uses INSERT IGNORE so already-configured values are never overwritten.
 *
 * API keys are intentionally NOT stored here — they belong in .env:
 *   DEEPL_API_KEY      = your-deepl-free-or-pro-key
 *   ANTHROPIC_API_KEY  = your-anthropic-key  (fallback provider)
 */
class AddTranslationSettings extends Migration
{
    public function up(): void
    {
        $settings = [
            // JSON array of active ISO-639-1 language codes.
            // 'de' is always first (source language).
            ['key' => 'active_languages',     'value' => '["de","en"]'],

            // The language shown by default (cookie fallback).
            ['key' => 'default_language',     'value' => 'de'],

            // Which AI provider to use: 'deepl' or 'anthropic'
            ['key' => 'translation_provider', 'value' => 'deepl'],

            // DeepL glossary ID (leave empty to disable).
            // Created via DeepL API or dashboard. Used in Phase 6.
            ['key' => 'deepl_glossary_id',    'value' => ''],
        ];

        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO velocms_settings (`key`, value) VALUES (:key, :value)"
        );

        foreach ($settings as $s) {
            $stmt->execute([':key' => $s['key'], ':value' => $s['value']]);
        }
    }

    public function down(): void
    {
        $this->db->exec(
            "DELETE FROM velocms_settings
             WHERE `key` IN (
                 'active_languages',
                 'default_language',
                 'translation_provider',
                 'deepl_glossary_id'
             )"
        );
    }
}
