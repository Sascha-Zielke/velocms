<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Media\Models;

use VeloCMS\Core\Model;

class MediaModel extends Model
{
    protected string $table = 'velocms_media';

    public function getAll(int $limit = 60, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function insert(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
                (filename, original, mime, size, width, height, path, alt_de, alt_en, uploaded_by)
            VALUES
                (:filename, :original, :mime, :size, :width, :height, :path, :alt_de, :alt_en, :uploaded_by)
        ");
        $stmt->execute([
            ':filename'    => $data['filename'],
            ':original'    => $data['original'],
            ':mime'        => $data['mime'],
            ':size'        => $data['size'],
            ':width'       => $data['width'] ?? null,
            ':height'      => $data['height'] ?? null,
            ':path'        => $data['path'],
            ':alt_de'      => $data['alt_de'] ?? '',
            ':alt_en'      => $data['alt_en'] ?? '',
            ':uploaded_by' => $data['uploaded_by'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateAlt(int $id, string $altDe, string $altEn): void
    {
        $this->db->prepare(
            "UPDATE {$this->table} SET alt_de=:alt_de, alt_en=:alt_en WHERE id=:id"
        )->execute([':id' => $id, ':alt_de' => $altDe, ':alt_en' => $altEn]);
    }

    public function delete(int $id): ?string
    {
        $row = $this->getById($id);
        if (!$row) return null;
        $this->db->prepare("DELETE FROM {$this->table} WHERE id=:id")->execute([':id' => $id]);
        return $row['path'];
    }
}
