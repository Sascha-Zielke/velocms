<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class MigrationRunner
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->ensureMigrationsTable();
    }

    public function run(): void
    {
        $pending = $this->getPending();

        if (empty($pending)) {
            echo "Nothing to migrate.\n";
            return;
        }

        $batch = $this->getNextBatch();

        foreach ($pending as $file) {
            $name      = pathinfo($file, PATHINFO_FILENAME);
            $migration = $this->loadMigration($file);
            $migration->up();

            $stmt = $this->db->prepare(
                "INSERT INTO velocms_migrations (migration, batch) VALUES (:m, :b)"
            );
            $stmt->execute([':m' => $name, ':b' => $batch]);

            echo "Migrated: {$name}\n";
        }

        echo "Done. Batch {$batch} completed.\n";
    }

    public function rollback(): void
    {
        $batch = $this->getCurrentBatch();

        if ($batch === 0) {
            echo "Nothing to roll back.\n";
            return;
        }

        $stmt = $this->db->prepare(
            "SELECT migration FROM velocms_migrations WHERE batch = :b ORDER BY id DESC"
        );
        $stmt->execute([':b' => $batch]);
        $migrations = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($migrations as $name) {
            $file = $this->findFile((string) $name);

            if ($file !== null) {
                $migration = $this->loadMigration($file);
                $migration->down();
            }

            $stmt = $this->db->prepare(
                "DELETE FROM velocms_migrations WHERE migration = :m"
            );
            $stmt->execute([':m' => $name]);

            echo "Rolled back: {$name}\n";
        }

        echo "Batch {$batch} rolled back.\n";
    }

    public function status(): void
    {
        $all  = $this->getAllFiles();
        $ran  = $this->getRanMigrations();

        echo str_pad('Migration', 60) . " Status\n";
        echo str_repeat('-', 70) . "\n";

        foreach ($all as $file) {
            $name   = pathinfo($file, PATHINFO_FILENAME);
            $status = in_array($name, $ran) ? 'Ran' : 'Pending';
            echo str_pad($name, 60) . " {$status}\n";
        }
    }

    private function getPending(): array
    {
        $all = $this->getAllFiles();
        $ran = $this->getRanMigrations();

        return array_filter($all, function (string $file) use ($ran): bool {
            return !in_array(pathinfo($file, PATHINFO_FILENAME), $ran);
        });
    }

    private function getAllFiles(): array
    {
        $files = [];

        $globalPath = BASE_PATH . '/migrations';
        if (is_dir($globalPath)) {
            $found = glob($globalPath . '/*.php');
            if ($found !== false) {
                $files = array_merge($files, $found);
            }
        }

        $modulePaths = glob(BASE_PATH . '/modules/*/migrations/*.php');
        if ($modulePaths !== false) {
            $files = array_merge($files, $modulePaths);
        }

        sort($files);
        return $files;
    }

    private function getRanMigrations(): array
    {
        $stmt = $this->db->query("SELECT migration FROM velocms_migrations ORDER BY id");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function getNextBatch(): int
    {
        $stmt = $this->db->query("SELECT MAX(batch) FROM velocms_migrations");
        return ((int) $stmt->fetchColumn()) + 1;
    }

    private function getCurrentBatch(): int
    {
        $stmt = $this->db->query("SELECT MAX(batch) FROM velocms_migrations");
        return (int) $stmt->fetchColumn();
    }

    private function loadMigration(string $file): Migration
    {
        require_once $file;

        $basename  = pathinfo($file, PATHINFO_FILENAME);
        $className = preg_replace('/^\d+_/', '', $basename);
        $className = str_replace('_', '', ucwords($className, '_'));

        if (!class_exists($className)) {
            throw new \RuntimeException("Migration class not found: {$className}");
        }

        return new $className($this->db);
    }

    private function findFile(string $name): ?string
    {
        foreach ($this->getAllFiles() as $file) {
            if (pathinfo($file, PATHINFO_FILENAME) === $name) {
                return $file;
            }
        }

        return null;
    }

    private function ensureMigrationsTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS velocms_migrations (
                id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                migration  VARCHAR(255) NOT NULL UNIQUE,
                batch      INT UNSIGNED NOT NULL DEFAULT 1,
                ran_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
