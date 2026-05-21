<?php

declare(strict_types=1);

namespace VeloCMS\Core;

abstract class Module
{
    protected object $router;
    protected AdminMenu $admin;

    public function __construct()
    {
        $this->admin  = new AdminMenu();
        $this->router = new class {
            public function get(string $path, string $handler): void
            {
                Router::get($path, $handler);
            }

            public function post(string $path, string $handler): void
            {
                Router::post($path, $handler);
            }
        };
    }

    abstract public function boot(): void;

    public function install(): void {}

    protected function runMigrations(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $db    = Database::getInstance()->getPdo();
        $files = glob($path . '/*.php');

        if ($files === false) {
            return;
        }

        sort($files);

        foreach ($files as $file) {
            require_once $file;

            $basename  = pathinfo($file, PATHINFO_FILENAME);
            $className = preg_replace('/^\d+_/', '', $basename);
            $className = str_replace('_', '', ucwords($className, '_'));

            if (class_exists($className)) {
                $migration = new $className($db);
                $migration->up();
            }
        }
    }
}
