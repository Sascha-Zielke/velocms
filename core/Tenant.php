<?php
declare(strict_types=1);

namespace VeloCMS\Core;

/**
 * Tenant — Domain-based multi-tenancy resolver
 *
 * Architecture: velocms_master.velocms_sites is the registry.
 * On each request the domain is resolved → tenant DB is selected.
 * Admin routes always use the resolved tenant's DB.
 *
 * tenant_id is set once per boot and never changes (immutable per request).
 */
class Tenant
{
    private static ?array $current = null;

    /**
     * Resolve current tenant from HTTP_HOST.
     * Connects to master DB, looks up domain, then connects to tenant DB.
     *
     * @throws \RuntimeException if domain is unknown or site is suspended
     */
    public static function resolve(array $config): void
    {
        $host = strtolower(trim($_SERVER['HTTP_HOST'] ?? ''));
        // Strip port if present
        $host = preg_replace('/:\d+$/', '', $host);

        // CLI / unit tests: fall back to .env DB_NAME
        if ($host === '' || php_sapi_name() === 'cli') {
            self::$current = [
                'id'      => 0,
                'domain'  => 'localhost',
                'db_name' => $config['db_name'],
                'name'    => 'CLI',
                'status'  => 'active',
            ];
            Database::connect(
                $config['db_host'],
                $config['db_port'],
                $config['db_name'],
                $config['db_user'],
                $config['db_pass']
            );
            return;
        }

        // Connect to master DB to look up domain
        $masterDsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['master_db']};charset=utf8mb4";
        try {
            $master = new \PDO($masterDsn, $config['db_user'], $config['db_pass'], [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (\PDOException $e) {
            // If master DB unreachable, fall through to direct DB_NAME (dev mode)
            error_log('[VeloCMS] Master DB unreachable: ' . $e->getMessage());
            Database::connect($config['db_host'], $config['db_port'], $config['db_name'], $config['db_user'], $config['db_pass']);
            self::$current = ['id' => 0, 'domain' => $host, 'db_name' => $config['db_name'], 'name' => 'fallback', 'status' => 'active'];
            return;
        }

        // Look up by domain OR www_alias
        $stmt = $master->prepare(
            "SELECT * FROM velocms_sites WHERE (domain = :h1 OR www_alias = :h2) AND status = 'active' LIMIT 1"
        );
        $stmt->execute([':h1' => $host, ':h2' => $host]);
        $site = $stmt->fetch();

        if (!$site) {
            http_response_code(503);
            echo '<!DOCTYPE html><html><head><title>Site unavailable</title></head><body><h1>503 — Site not found</h1><p>This domain is not registered.</p></body></html>';
            exit;
        }

        self::$current = $site;

        // Connect to tenant's own DB
        Database::connect(
            $config['db_host'],
            $config['db_port'],
            $site['db_name'],
            $config['db_user'],
            $config['db_pass']
        );
    }

    public static function current(): ?array
    {
        return self::$current;
    }

    public static function id(): int
    {
        return (int) (self::$current['id'] ?? 0);
    }

    public static function dbName(): string
    {
        return self::$current['db_name'] ?? '';
    }

    public static function domain(): string
    {
        return self::$current['domain'] ?? '';
    }
}
