<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Translation\Models;

use VeloCMS\Core\Database;

class TranslationModel
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    public function get(string $table, int $rowId, string $field, string $lang): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM velocms_translations
             WHERE table_name = :t AND row_id = :r AND field = :f AND language = :l
             LIMIT 1'
        );
        $stmt->execute([':t' => $table, ':r' => $rowId, ':f' => $field, ':l' => $lang]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function upsert(
        string $table,
        int    $rowId,
        string $field,
        string $lang,
        string $value,
        string $source = 'auto',
        string $hash   = ''
    ): void {
        $stmt = $this->db->prepare(
            'INSERT INTO velocms_translations
             (table_name, row_id, field, language, value, source, stale, content_hash, translated_at)
             VALUES (:t, :r, :f, :l, :v, :s, 0, :h, NOW())
             ON DUPLICATE KEY UPDATE
                 value         = VALUES(value),
                 source        = VALUES(source),
                 stale         = 0,
                 content_hash  = VALUES(content_hash),
                 translated_at = NOW()'
        );
        $stmt->execute([
            ':t' => $table,
            ':r' => $rowId,
            ':f' => $field,
            ':l' => $lang,
            ':v' => $value,
            ':s' => $source,
            ':h' => $hash,
        ]);
    }

    /** Returns field => value map for a row in the given language. */
    public function getForRow(string $table, int $rowId, string $lang): array
    {
        $stmt = $this->db->prepare(
            'SELECT field, value FROM velocms_translations
             WHERE table_name = :t AND row_id = :r AND language = :l'
        );
        $stmt->execute([':t' => $table, ':r' => $rowId, ':l' => $lang]);
        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
    }

    /** Count rows that are missing or stale for a given language. */
    public function countMissing(string $lang): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM velocms_translations WHERE language = :l AND stale = 1'
        );
        $stmt->execute([':l' => $lang]);
        return (int) $stmt->fetchColumn();
    }
}
