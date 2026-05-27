<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Translation\Models;

use VeloCMS\Core\Database;

class TranslationModel
{
    private \PDO $db;

    public function __construct(?\PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getPdo();
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

    /** Stats for the dashboard: total, auto_ok, manual_ok, stale. */
    public function getStats(string $lang): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                COUNT(*) AS total,
                SUM(source = "auto"   AND stale = 0) AS auto_ok,
                SUM(source = "manual" AND stale = 0) AS manual_ok,
                SUM(stale = 1) AS stale
             FROM velocms_translations WHERE language = :l'
        );
        $stmt->execute([':l' => $lang]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)
            ?: ['total' => 0, 'auto_ok' => 0, 'manual_ok' => 0, 'stale' => 0];
    }

    /** Paginated list with optional filters. */
    public function getList(
        string $lang,
        string $table  = '',
        string $source = '',
        int    $limit  = 25,
        int    $offset = 0
    ): array {
        [$where, $params] = $this->buildWhere($lang, $table, $source);
        $stmt = $this->db->prepare(
            "SELECT * FROM velocms_translations {$where}
             ORDER BY stale DESC, translated_at ASC
             LIMIT :lim OFFSET :off"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function countList(string $lang, string $table = '', string $source = ''): int
    {
        [$where, $params] = $this->buildWhere($lang, $table, $source);
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM velocms_translations {$where}"
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /** Distinct table names that have translation rows. */
    public function getTables(): array
    {
        return $this->db->query(
            'SELECT DISTINCT table_name FROM velocms_translations ORDER BY table_name'
        )->fetchAll(\PDO::FETCH_COLUMN) ?: [];
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM velocms_translations WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /** Save a manually edited translation and lock it against auto-overwrite. */
    public function updateManual(int $id, string $value): void
    {
        $stmt = $this->db->prepare(
            'UPDATE velocms_translations
             SET value = :v, source = "manual", stale = 0, translated_at = NOW()
             WHERE id = :id'
        );
        $stmt->execute([':v' => $value, ':id' => $id]);
    }

    /** Release a manual lock so the next content save will re-translate it. */
    public function unlock(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE velocms_translations SET source = "auto", stale = 1 WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    /** @return array{0: string, 1: array<string,mixed>} */
    private function buildWhere(string $lang, string $table, string $source): array
    {
        $where  = 'WHERE language = :l';
        $params = [':l' => $lang];

        if ($table !== '') {
            $where       .= ' AND table_name = :t';
            $params[':t'] = $table;
        }

        match ($source) {
            'stale'  => $where .= ' AND stale = 1',
            'manual' => $where .= ' AND source = "manual"',
            'auto'   => $where .= ' AND source = "auto" AND stale = 0',
            default  => null,
        };

        return [$where, $params];
    }
}
