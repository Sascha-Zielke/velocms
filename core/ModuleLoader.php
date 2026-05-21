<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class ModuleLoader
{
    public static function boot(): void
    {
        $modulePath = BASE_PATH . '/modules';

        if (!is_dir($modulePath)) {
            return;
        }

        $dirs = glob($modulePath . '/*', GLOB_ONLYDIR);

        if ($dirs === false) {
            return;
        }

        foreach ($dirs as $dir) {
            $name       = basename($dir);
            $moduleFile = $dir . '/' . $name . 'Module.php';
            $className  = "VeloCMS\\Modules\\{$name}\\{$name}Module";

            if (!file_exists($moduleFile)) {
                continue;
            }

            if (!class_exists($className)) {
                require_once $moduleFile;
            }

            if (!class_exists($className)) {
                continue;
            }

            $module = new $className();
            $module->boot();
        }
    }
}
