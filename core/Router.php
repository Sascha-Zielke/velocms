<?php
declare(strict_types=1);

namespace VeloCMS\Core;

class Router
{
    private static array $routes = [];

    public function run(): void { self::dispatch(); }

    public static function get(string $path, string $handler): void
    {
        self::$routes[] = ['method' => 'GET', 'path' => $path, 'handler' => $handler];
    }

    public static function post(string $path, string $handler): void
    {
        self::$routes[] = ['method' => 'POST', 'path' => $path, 'handler' => $handler];
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri    = rtrim($uri ?: '/', '/') ?: '/';

        error_log('[VCMS-DEBUG] dispatch method=' . $method . ' uri=' . $uri . ' routes=' . count(self::$routes));

        // Sort: wildcard [*:] routes last so specific routes always win
        $routes = self::$routes;
        usort($routes, static fn($a, $b) =>
            (str_contains($a['path'], '[*:') ? 1 : 0) <=> (str_contains($b['path'], '[*:') ? 1 : 0)
        );

        foreach ($routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $pattern = self::buildPattern($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                error_log('[VCMS-DEBUG] matched path=' . $route['path'] . ' handler=' . $route['handler']);
                array_shift($matches);
                self::callHandler($route['handler'], $matches);
                return;
            }
        }

        error_log('[VCMS-DEBUG] NO MATCH for ' . $method . ' ' . $uri);
        http_response_code(404);
        $errorPage = BASE_PATH . '/views/errors/404.php';
        if (file_exists($errorPage)) {
            include $errorPage;
        } else {
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>404</title></head>'
               . '<body><h1>404 – Seite nicht gefunden</h1>'
               . '<p><a href="/">Zur Startseite</a></p></body></html>';
        }
    }

    private static function buildPattern(string $path): string
    {
        // [i:id] → integer segment, [*:slug] → any chars including slashes, [a:name] → alphanumeric
        $pattern = preg_replace_callback('/\[([a-z\*]+):([a-z_]+)\]/', function ($m) {
            return match($m[1]) {
                'i'     => '(\d+)',
                'a'     => '([a-zA-Z0-9_\-]+)',
                '*'     => '(.+)',
                default => '([^/]+)',
            };
        }, $path);
        return '#^' . $pattern . '$#';
    }

    private static function callHandler(string $handler, array $params): void
    {
        [$class, $method] = explode('@', $handler);
        $namespace = 'VeloCMS\\Modules\\' . $class;

        if (!class_exists($namespace)) {
            $namespace = 'VeloCMS\\Core\\Controllers\\' . $class;
        }

        if (!class_exists($namespace)) {
            throw new \RuntimeException("Controller not found: {$class} (tried: VeloCMS\\Modules\\{$class})");
        }

        $controller = new $namespace();
        $controller->$method(...$params);
    }
}
