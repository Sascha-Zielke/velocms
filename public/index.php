<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

use VeloCMS\Core\App;
use VeloCMS\Core\Router;

require_once __DIR__ . '/../vendor/autoload.php';

define('BASE_PATH', dirname(__DIR__));

// Routen laden
// Wir laden hier die routes.php aus dem Pages-Modul
require_once BASE_PATH . '/modules/Pages/routes.php';

App::boot();