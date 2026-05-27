<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Pages\Models;

use VeloCMS\Core\Model;

class PagesModel extends Model
{
    protected string $table = 'velocms_pages';

    public function getAll(): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id=:id AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function getFirstPublished(): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE status = 'published' AND deleted_at IS NULL ORDER BY created_at ASC LIMIT 1"
        );
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE slug=:slug AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (slug,title,title_en,status,meta_title,meta_description)
             VALUES (:slug,:title,:title_en,:status,:meta_title,:meta_description)"
        );
        $stmt->execute([
            ':slug'             => $data['slug'],
            ':title'            => $data['title'],
            ':title_en'         => $data['title_en'] ?? null,
            ':status'           => $data['status'] ?? 'draft',
            ':meta_title'       => $data['meta_title'] ?? null,
            ':meta_description' => $data['meta_description'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET slug=:slug,title=:title,title_en=:title_en,status=:status,
                 meta_title=:meta_title,meta_description=:meta_description,updated_at=NOW()
             WHERE id=:id AND deleted_at IS NULL"
        );
        $stmt->execute([
            ':id'               => $id,
            ':slug'             => $data['slug'],
            ':title'            => $data['title'],
            ':title_en'         => $data['title_en'] ?? null,
            ':status'           => $data['status'] ?? 'draft',
            ':meta_title'       => $data['meta_title'] ?? null,
            ':meta_description' => $data['meta_description'] ?? null,
        ]);
    }

    public function softDelete(int $id): void
    {
        $this->db->prepare("UPDATE {$this->table} SET deleted_at=NOW() WHERE id=:id")
                 ->execute([':id' => $id]);
    }

    public function getSections(int $pageId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM velocms_sections WHERE page_id=:pid ORDER BY sort_order ASC"
        );
        $stmt->execute([':pid' => $pageId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createSection(int $pageId): int
    {
        $ord = $this->db->prepare(
            "SELECT COALESCE(MAX(sort_order),0)+1 FROM velocms_sections WHERE page_id=:pid"
        );
        $ord->execute([':pid' => $pageId]);
        $stmt = $this->db->prepare(
            "INSERT INTO velocms_sections (page_id,sort_order,settings) VALUES (:pid,:ord,:set)"
        );
        $stmt->execute([':pid' => $pageId, ':ord' => (int)$ord->fetchColumn(), ':set' => '{}']);
        return (int) $this->db->lastInsertId();
    }

    public function updateSectionSettings(int $id, array $settings): void
    {
        $this->db->prepare("UPDATE velocms_sections SET settings=:s,updated_at=NOW() WHERE id=:id")
                 ->execute([':s' => json_encode($settings), ':id' => $id]);
    }

    public function deleteSection(int $id): void
    {
        $rows = $this->db->prepare("SELECT id FROM velocms_rows WHERE section_id=:sid");
        $rows->execute([':sid' => $id]);
        foreach ($rows->fetchAll(\PDO::FETCH_COLUMN) as $rid) {
            $this->db->prepare("DELETE FROM velocms_boxes WHERE row_id=:rid")->execute([':rid' => $rid]);
        }
        $this->db->prepare("DELETE FROM velocms_rows WHERE section_id=:sid")->execute([':sid' => $id]);
        $this->db->prepare("DELETE FROM velocms_sections WHERE id=:id")->execute([':id' => $id]);
    }

    public function getRows(int $sectionId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM velocms_rows WHERE section_id=:sid ORDER BY sort_order ASC"
        );
        $stmt->execute([':sid' => $sectionId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createRow(int $sectionId): int
    {
        $ord = $this->db->prepare(
            "SELECT COALESCE(MAX(sort_order),0)+1 FROM velocms_rows WHERE section_id=:sid"
        );
        $ord->execute([':sid' => $sectionId]);
        $stmt = $this->db->prepare(
            "INSERT INTO velocms_rows (section_id,sort_order,cols_config) VALUES (:sid,:ord,:cfg)"
        );
        $stmt->execute([':sid' => $sectionId, ':ord' => (int)$ord->fetchColumn(), ':cfg' => '{}']);
        return (int) $this->db->lastInsertId();
    }

    public function deleteRow(int $id): void
    {
        $this->db->prepare("DELETE FROM velocms_boxes WHERE row_id=:rid")->execute([':rid' => $id]);
        $this->db->prepare("DELETE FROM velocms_rows WHERE id=:id")->execute([':id' => $id]);
    }

    public function getBox(int $boxId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM velocms_boxes WHERE id = :id");
        $stmt->execute([':id' => $boxId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;
        $row['data'] = json_decode($row['data'] ?? '[]', true) ?? [];
        return $row;
    }

        public function getBoxes(int $rowId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM velocms_boxes WHERE row_id=:rid ORDER BY sort_order ASC"
        );
        $stmt->execute([':rid' => $rowId]);
        $boxes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($boxes as &$box) {
            $box['data'] = is_string($box['data']) ? (json_decode($box['data'], true) ?? []) : ($box['data'] ?? []);
        }
        return $boxes;
    }

    public function saveBox(int $boxId, string $type, array $data): void
    {
        $this->db->prepare(
            "UPDATE velocms_boxes SET type=:type,data=:data,updated_at=NOW() WHERE id=:id"
        )->execute([':id' => $boxId, ':type' => $type, ':data' => json_encode($data)]);
    }

    public function addBox(int $rowId, string $type): int
    {
        $ord = $this->db->prepare(
            "SELECT COALESCE(MAX(sort_order),0)+1 FROM velocms_boxes WHERE row_id=:rid"
        );
        $ord->execute([':rid' => $rowId]);
        $data = json_encode(['layout' => ['cols' => 12], 'content' => [], 'settings' => []]);
        $stmt = $this->db->prepare(
            "INSERT INTO velocms_boxes (row_id,sort_order,type,data) VALUES (:rid,:ord,:type,:data)"
        );
        $stmt->execute([':rid' => $rowId, ':ord' => (int)$ord->fetchColumn(), ':type' => $type, ':data' => $data]);
        return (int) $this->db->lastInsertId();
    }

    public function deleteBox(int $id): void
    {
        $this->db->prepare("DELETE FROM velocms_boxes WHERE id=:id")->execute([':id' => $id]);
    }

    /**
     * Update grid positions (x, y, w, h) for a set of boxes.
     * Only updates boxes that actually belong to $pageId — security guard.
     */
    public function saveGridLayout(int $pageId, array $grid): void
    {
        // Fetch all valid box IDs for this page
        $stmt = $this->db->prepare(
            "SELECT b.id FROM velocms_boxes b
             JOIN velocms_rows r     ON b.row_id     = r.id
             JOIN velocms_sections s ON r.section_id = s.id
             WHERE s.page_id = :pid"
        );
        $stmt->execute([':pid' => $pageId]);
        $validIds = array_flip($stmt->fetchAll(\PDO::FETCH_COLUMN));

        $update = $this->db->prepare(
            "UPDATE velocms_boxes
             SET grid_x = :x, grid_y = :y, grid_w = :w, grid_h = :h, updated_at = NOW()
             WHERE id = :id"
        );

        foreach ($grid as $item) {
            $id = (int) ($item['box_id'] ?? 0);
            if ($id <= 0 || !isset($validIds[$id])) {
                continue; // Reject boxes that don't belong to this page
            }
            $update->execute([
                ':id' => $id,
                ':x'  => max(0, (int) ($item['x'] ?? 0)),
                ':y'  => max(0, (int) ($item['y'] ?? 0)),
                ':w'  => max(1, (int) ($item['w'] ?? 24)),
                ':h'  => max(1, (int) ($item['h'] ?? 4)),
            ]);
        }
    }

    public function getFullPage(int $pageId): array
    {
        $sections = $this->getSections($pageId);
        foreach ($sections as &$section) {
            $section['settings'] = is_string($section['settings'])
                ? (json_decode($section['settings'], true) ?? []) : ($section['settings'] ?? []);
            $section['rows'] = $this->getRows((int) $section['id']);
            foreach ($section['rows'] as &$row) {
                $row['cols_config'] = is_string($row['cols_config'])
                    ? (json_decode($row['cols_config'], true) ?? []) : ($row['cols_config'] ?? []);
                $row['boxes'] = $this->getBoxes((int) $row['id']);
            }
        }
        return $sections;
    }
}
