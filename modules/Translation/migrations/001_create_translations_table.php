<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

/**
 * Central translation store.
 *
 * One row per (table, row_id, field, language) tuple.
 * - source='auto'   → KI-generated, can be re-translated automatically
 * - source='manual' → human-edited, excluded from automatic re-translation
 * - stale=1         → source text changed after translation, needs review
 * - content_hash    → MD5 of original text at translation time (stale detection)
 */
class CreateTranslationsTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_translations (
                id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
                table_name    VARCHAR(64)   NOT NULL               COMMENT 'e.g. velocms_blog_posts',
                row_id        INT UNSIGNED  NOT NULL               COMMENT 'PK of the source row',
                field         VARCHAR(64)   NOT NULL               COMMENT 'e.g. title, content',
                language      VARCHAR(5)    NOT NULL               COMMENT 'e.g. en, fr, es',
                value         LONGTEXT      NOT NULL,
                source        ENUM('auto','manual') NOT NULL DEFAULT 'auto',
                stale         TINYINT(1)    NOT NULL DEFAULT 0     COMMENT '1 = original changed after translation',
                content_hash  VARCHAR(32)   NULL                   COMMENT 'MD5 of source text at translation time',
                translated_at DATETIME      NULL,
                created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at    DATETIME      NULL     ON UPDATE CURRENT_TIMESTAMP,

                PRIMARY KEY (id),
                UNIQUE  KEY uq_translation (table_name, row_id, field, language),
                        KEY idx_lookup     (table_name, row_id, language),
                        KEY idx_stale      (stale, language)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_translations");
    }
}
