<?php

declare(strict_types=1);

return [
    'app_env'    => $_ENV['APP_ENV'] ?? 'production',
    'app_url'    => $_ENV['APP_URL'] ?? '',
    'db_host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'db_port'    => (int) ($_ENV['DB_PORT'] ?? 3306),
    'db_name'    => $_ENV['DB_NAME'] ?? '',
    'db_user'    => $_ENV['DB_USER'] ?? '',
    'db_pass'    => $_ENV['DB_PASS'] ?? '',
    'master_db'  => $_ENV['MASTER_DB'] ?? '',
    'deepl_key'  => $_ENV['DEEPL_KEY'] ?? '',
];
