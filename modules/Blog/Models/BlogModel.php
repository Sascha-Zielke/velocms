<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Blog\Models;

use VeloCMS\Core\Model;

class BlogModel extends Model
{
    protected string $table = 'velocms_blog_posts';

    public function getAll(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :l OFFSET :o"
        );
        $stmt->bindValue(':l', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPublished(int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE status = 'published'
             ORDER BY published_at DESC
             LIMIT :l OFFSET :o"
        );
        $stmt->bindValue(':l', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE slug = :slug AND status = 'published' LIMIT 1"
        );
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function count(bool $publishedOnly = false): int
    {
        $where = $publishedOnly ? "WHERE status = 'published'" : '';
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table} $where")->fetchColumn();
    }

    public function insert(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
                (title, title_en, slug, excerpt, excerpt_en, content, content_en,
                 cover_image, status, meta_title, meta_description, author_id, published_at)
            VALUES
                (:title, :title_en, :slug, :excerpt, :excerpt_en, :content, :content_en,
                 :cover_image, :status, :meta_title, :meta_description, :author_id, :published_at)
        ");
        $stmt->execute($this->prepareData($data));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET
                title=:title, title_en=:title_en, slug=:slug,
                excerpt=:excerpt, excerpt_en=:excerpt_en,
                content=:content, content_en=:content_en,
                cover_image=:cover_image, status=:status,
                meta_title=:meta_title, meta_description=:meta_description,
                published_at=:published_at
            WHERE id=:id
        ");
        $stmt->execute(array_merge($this->prepareData($data), [':id' => $id]));
    }

    public function delete(int $id): void
    {
        $this->db->prepare("DELETE FROM {$this->table} WHERE id=:id")->execute([':id' => $id]);
    }

    private function prepareData(array $d): array
    {
        $status = in_array($d['status'] ?? '', ['draft', 'published', 'archived'], true)
                  ? $d['status'] : 'draft';
        return [
            ':title'            => substr($d['title'] ?? '', 0, 255),
            ':title_en'         => substr($d['title_en'] ?? '', 0, 255),
            ':slug'             => substr($d['slug'] ?? '', 0, 255),
            ':excerpt'          => $d['excerpt'] ?? null,
            ':excerpt_en'       => $d['excerpt_en'] ?? null,
            ':content'          => $d['content'] ?? '',
            ':content_en'       => $d['content_en'] ?? '',
            ':cover_image'      => substr($d['cover_image'] ?? '', 0, 500),
            ':status'           => $status,
            ':meta_title'       => substr($d['meta_title'] ?? '', 0, 255),
            ':meta_description' => substr($d['meta_description'] ?? '', 0, 320),
            ':author_id'        => (int) ($d['author_id'] ?? 1),
            ':published_at'     => ($status === 'published' && empty($d['published_at']))
                                   ? date('Y-m-d H:i:s')
                                   : ($d['published_at'] ?? null),
        ];
    }
}
