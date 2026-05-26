<?php
declare(strict_types=1);

namespace VeloCMS\Core;

/**
 * Tenant — Domain-based multi-tenancy resolver
 *
 * Two modes, selected automatically by the presence of MASTER_DB in .env:
 *
 *   Single-site mode  (MASTER_DB not set):
 *     → Direct connect to DB_NAME, no master-DB lookup.
 *       Tenant::current() returns a synthetic row with id=0.
 *       Safe to use forever — no config changes required on the server.
 *
 *   Multi-site mode   (MASTER_DB set):
 *     → Connects to velocms_master.velocms_sites, resolves domain → tenant DB.
 *       If the master DB is unreachable, falls back to single-site mode.
 *       If the domain is not registered: 503.
 *
 * tenant_id is set once per boot and never changes (immutable per request).
 */
class Tenant
{
    private static ?array $current = null;

    /**
     * Resolve the current tenant and establish the DB connection.
     * Called exactly once per request from App::boot().
     */
    public static function resolve(array $config): void
    {
        $host = strtolower(trim($_SERVER['HTTP_HOST'] ?? ''));
        $host = (string) preg_replace('/:\d+$/', '', $host);

        // ── CLI / unit tests ────────────────────────────────────────────────
        if ($host === '' || php_sapi_name() === 'cli') {
            self::bootSingleSite($config, 'cli');
            return;
        }

        // ── Single-site mode (no MASTER_DB configured) ───────────────────────
        if (empty($config['master_db'])) {
            self::bootSingleSite($config, $host);
            return;
        }

        // ── Multi-site mode: resolve via master DB ────────────────────────────
        $masterDsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['db_host'],
            $config['db_port'],
            $config['master_db']
        );

        try {
            $master = new \PDO($masterDsn, $config['db_user'], $config['db_pass'], [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (\PDOException $e) {
            // Master DB unreachable → graceful degradation to single-site
            error_log('[VeloCMS] Master DB unreachable, falling back to single-site: ' . $e->getMessage());
            self::bootSingleSite($config, $host);
            return;
        }

        // Look up by domain OR www_alias — only active sites
        $stmt = $master->prepare(
            "SELECT * FROM velocms_sites
             WHERE (domain = :h1 OR www_alias = :h2)
               AND status = 'active'
             LIMIT 1"
        );
        $stmt->execute([':h1' => $host, ':h2' => $host]);
        $site = $stmt->fetch();

        if (!$site) {
            http_response_code(503);
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Site unavailable</title></head>'
               . '<body><h1>503 — Site not found</h1>'
               . '<p>This domain is not registered in VeloCMS.</p>'
               . '</body></html>';
            exit;
        }

        self::$current = $site;

        Database::connect(
            $config['db_host'],
            $config['db_port'],
            $site['db_name'],
            $config['db_user'],
            $config['db_pass']
        );
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

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

    /** True when running with a master-DB multi-site setup. */
    public static function isMultiSite(): bool
    {
        return isset(self::$current['id']) && self::$current['id'] > 0;
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private static function bootSingleSite(array $config, string $domain): void
    {
        self::$current = [
            'id'      => 0,
            'domain'  => $domain,
            'db_name' => $config['db_name'],
            'name'    => 'default',
            'status'  => 'active',
        ];

        Database::connect(
            $config['db_host'],
            $config['db_port'],
            $config['db_name'],
            $config['db_user'],
            $config['db_pass']
        );
    }
}
