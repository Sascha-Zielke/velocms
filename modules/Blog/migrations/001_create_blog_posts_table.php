<?php
declare(strict_types=1);

class CreateBlogPostsTable
{
    public function __construct(private \PDO $db) {}

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_blog_posts (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title           VARCHAR(255) NOT NULL,
                title_en        VARCHAR(255) NOT NULL DEFAULT '',
                slug            VARCHAR(255) NOT NULL,
                excerpt         TEXT NULL,
                excerpt_en      TEXT NULL,
                content         LONGTEXT NOT NULL,
                content_en      LONGTEXT NOT NULL,
                cover_image     VARCHAR(500) NOT NULL DEFAULT '',
                status          ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
                meta_title      VARCHAR(255) NOT NULL DEFAULT '',
                meta_description VARCHAR(320) NOT NULL DEFAULT '',
                author_id       INT UNSIGNED NOT NULL DEFAULT 1,
                published_at    DATETIME NULL,
                created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_slug (slug),
                INDEX idx_status_pub (status, published_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
