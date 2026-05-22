<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct(
        string $host,
        int    $port,
        string $name,
        string $user,
        string $pass
    ) {
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        $this->pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    public static function connect(
        string $host,
        int    $port,
        string $name,
        string $user,
        string $pass
    ): self {
        self::$instance = new self($host, $port, $name, $user, $pass);
        return self::$instance;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('Database not connected');
        }
        return self::$instance;
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public static function getTestConnection(): \PDO
    {
        $host = $_ENV['TEST_DB_HOST'] ?? '127.0.0.1';
        $port = (int) ($_ENV['TEST_DB_PORT'] ?? 3306);
        $name = $_ENV['TEST_DB_NAME'] ?? 'velocms_test';
        $user = $_ENV['TEST_DB_USER'] ?? 'velocms';
        $pass = $_ENV['TEST_DB_PASS'] ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        return new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}
