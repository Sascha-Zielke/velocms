<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Nav\Models;

use VeloCMS\Core\Model;

class NavModel extends Model
{
    protected string $table = 'velocms_nav_items';

    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM velocms_nav_items WHERE deleted_at IS NULL ORDER BY position ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    public function getActive(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM velocms_nav_items WHERE active = 1 AND deleted_at IS NULL ORDER BY position ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM velocms_nav_items WHERE id = :id AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $maxPos = (int) $this->db->query(
            'SELECT COALESCE(MAX(position), 0) FROM velocms_nav_items WHERE deleted_at IS NULL'
        )->fetchColumn();

        $stmt = $this->db->prepare(
            'INSERT INTO velocms_nav_items (label, label_en, url, target, active, position)
             VALUES (:label, :label_en, :url, :target, :active, :position)'
        );
        $stmt->execute([
            ':label'    => $data['label'],
            ':label_en' => $data['label_en'] ?? null,
            ':url'      => $data['url'],
            ':target'   => $data['target'] ?? '_self',
            ':active'   => (int) ($data['active'] ?? 1),
            ':position' => $maxPos + 10,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE velocms_nav_items
             SET label = :label, label_en = :label_en, url = :url,
                 target = :target, active = :active
             WHERE id = :id AND deleted_at IS NULL'
        );
        $stmt->execute([
            ':label'    => $data['label'],
            ':label_en' => $data['label_en'] ?? null,
            ':url'      => $data['url'],
            ':target'   => $data['target'] ?? '_self',
            ':active'   => (int) ($data['active'] ?? 1),
            ':id'       => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->softDelete($id);
    }

    public function moveUp(int $id): void
    {
        $current = $this->getById($id);
        if (!$current) {
            return;
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM velocms_nav_items
             WHERE position < :pos AND deleted_at IS NULL
             ORDER BY position DESC LIMIT 1'
        );
        $stmt->execute([':pos' => $current['position']]);
        $prev = $stmt->fetch();

        if ($prev) {
            $this->swapPositions((int) $current['id'], (int) $current['position'], (int) $prev['id'], (int) $prev['position']);
        }
    }

    public function moveDown(int $id): void
    {
        $current = $this->getById($id);
        if (!$current) {
            return;
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM velocms_nav_items
             WHERE position > :pos AND deleted_at IS NULL
             ORDER BY position ASC LIMIT 1'
        );
        $stmt->execute([':pos' => $current['position']]);
        $next = $stmt->fetch();

        if ($next) {
            $this->swapPositions((int) $current['id'], (int) $current['position'], (int) $next['id'], (int) $next['position']);
        }
    }

    private function swapPositions(int $idA, int $posA, int $idB, int $posB): void
    {
        $stmt = $this->db->prepare(
            'UPDATE velocms_nav_items SET position = :pos WHERE id = :id'
        );
        $stmt->execute([':pos' => $posB, ':id' => $idA]);
        $stmt->execute([':pos' => $posA, ':id' => $idB]);
    }
}
