<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class App
{
    public static function boot(): void
    {
        self::loadEnv(BASE_PATH . '/.env');
        self::configureErrors();
        self::registerExceptionHandler();
        self::startSession();

        $config = require BASE_PATH . '/config/config.php';

        if (!empty($config['db_host'])) {
            Tenant::resolve($config);
        }

        ModuleLoader::boot();
        self::handleMaintenanceMode();
        Router::dispatch();
    }

    private static function loadEnv(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }

    private static function registerExceptionHandler(): void
    {
        set_exception_handler(function (\Throwable $e): void {
            // CSRF mismatch — show simple message, no stack trace
            if ($e instanceof \RuntimeException && str_contains($e->getMessage(), 'CSRF')) {
                http_response_code(403);
                echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>403</title></head>'
                   . '<body><h1>403 – Ungültiges Sicherheitstoken</h1>'
                   . '<p><a href="javascript:history.back()">Zurück</a></p></body></html>';
                exit;
            }

            $code   = $e->getCode();
            $status = (is_int($code) && $code >= 400 && $code < 600) ? $code : 500;
            http_response_code($status);

            error_log('[VeloCMS] Uncaught: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            $env = $_ENV['APP_ENV'] ?? 'production';
            if ($env === 'development') {
                echo '<pre style="padding:20px">' . htmlspecialchars($e->getMessage()) . "\n\n"
                   . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                exit;
            }

            // Production: render 500 error page
            $errorPage = BASE_PATH . '/views/errors/500.php';
            if (file_exists($errorPage)) {
                include $errorPage;
            } else {
                echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>500</title></head>'
                   . '<body><h1>500 – Interner Serverfehler</h1>'
                   . '<p>Bitte versuche es später erneut.</p></body></html>';
            }
            exit;
        });
    }

    /**
     * If maintenance_mode is active: block all non-admin frontend requests with 503.
     *
     * Rules:
     *  - /admin/* routes always pass (needed to reach the setting that turns this off)
     *  - Logged-in users with role admin or superadmin bypass the maintenance screen
     *  - Everyone else (guests + editors) gets the 503 maintenance page
     */
    private static function handleMaintenanceMode(): void
    {
        if (setting('maintenance_mode') !== '1') {
            return;
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Admin panel always stays accessible
        if (str_starts_with($uri, '/admin')) {
            return;
        }

        // Admins and superadmins may still browse the frontend
        if (Auth::check() && Auth::hasRole('admin')) {
            return;
        }

        http_response_code(503);
        header('Retry-After: 3600');

        $page = BASE_PATH . '/views/errors/maintenance.php';
        if (file_exists($page)) {
            include $page;
        } else {
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Wartung</title></head>'
               . '<body><h1>Wartungsarbeiten</h1>'
               . '<p>Die Website wird gerade gewartet. Bitte versuche es später erneut.</p>'
               . '</body></html>';
        }
        exit;
    }

    private static function configureErrors(): void
    {
        $env = $_ENV['APP_ENV'] ?? 'production';

        error_reporting(E_ALL);

        if ($env === 'development') {
            ini_set('display_errors', '1');
        } else {
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        }
    }

    private static function startSession(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        session_start([
            'cookie_httponly' => true,
            'cookie_secure'   => isset($_SERVER['HTTPS']),
            'cookie_samesite' => 'Strict',
            'use_strict_mode' => true,
        ]);

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
