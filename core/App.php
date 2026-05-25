<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class App
{
    public static function boot(): void
    {
        // Hier wird dein Routing gestartet
        $router = new Router();
        $router->run();
    }
}