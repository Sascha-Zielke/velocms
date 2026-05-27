<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Translation\Models;

use VeloCMS\Core\Database;

class GlossaryModel
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /** All terms for a language pair, ordered by source term. */
    public function getAll(string $sourceLang, string $targetLang): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM velocms_glossary
             WHERE source_lang = :s AND target_lang = :t
             ORDER BY source_term ASC'
        );
        $stmt->execute([':s' => $sourceLang, ':t' => $targetLang]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /** All terms across all language pairs (for listing in admin). */
    public function getAllGrouped(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM velocms_glossary ORDER BY source_lang, target_lang, source_term'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM velocms_glossary WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function save(string $sourceLang, string $targetLang, string $sourceTerm, string $targetTerm): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO velocms_glossary (source_lang, target_lang, source_term, target_term)
             VALUES (:s, :t, :st, :tt)
             ON DUPLICATE KEY UPDATE target_term = VALUES(target_term)'
        );
        $stmt->execute([':s' => $sourceLang, ':t' => $targetLang, ':st' => $sourceTerm, ':tt' => $targetTerm]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM velocms_glossary WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
