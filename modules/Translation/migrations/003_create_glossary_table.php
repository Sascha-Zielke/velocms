<?php

declare(strict_types=1);

use VeloCMS\Core\Migration;

class CreateGlossaryTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_glossary (
                id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
                source_lang  VARCHAR(5)    NOT NULL COMMENT 'e.g. de',
                target_lang  VARCHAR(5)    NOT NULL COMMENT 'e.g. en',
                source_term  VARCHAR(255)  NOT NULL COMMENT 'Term in source language',
                target_term  VARCHAR(255)  NOT NULL COMMENT 'Term in target language',
                created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at   DATETIME      NULL     ON UPDATE CURRENT_TIMESTAMP,

                PRIMARY KEY (id),
                UNIQUE  KEY uq_glossary    (source_lang, target_lang, source_term),
                        KEY idx_lang_pair  (source_lang, target_lang)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS velocms_glossary");
    }
}
