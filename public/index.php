<?php
declare(strict_types=1);

use VeloCMS\Core\App;

require_once __DIR__ . '/../vendor/autoload.php';

define('BASE_PATH', dirname(__DIR__));

App::boot();
