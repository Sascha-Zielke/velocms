<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class Router
{
    private static array $routes = [];

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

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = self::buildPattern($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                self::callHandler($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private static function buildPattern(string $path): string
    {
        $pattern = preg_replace('/\[[\*a-z]+:([a-z_]+)\]/', '([^/]+)', $path);
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
            throw new \RuntimeException("Controller {$class} not found");
        }

        $controller = new $namespace();
        $controller->$method(...$params);
    }
}
