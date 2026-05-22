#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Creates the first superadmin user.
 * Usage: php scripts/seed-admin.php [email] [name]
 * Password will be prompted interactively.
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

// Load .env
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
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

// Connect DB
$config = require BASE_PATH . '/config/config.php';
$db     = \VeloCMS\Core\Database::connect(
    $config['db_host'],
    $config['db_port'],
    $config['db_name'],
    $config['db_user'],
    $config['db_pass']
)->getPdo();

// Check if admin already exists
$stmt = $db->query("SELECT COUNT(*) FROM velocms_users WHERE role = 'superadmin'");
if ((int) $stmt->fetchColumn() > 0) {
    echo "A superadmin already exists. Aborting.\n";
    exit(1);
}

// Collect input
$email = $argv[1] ?? readline('Email: ');
$name  = $argv[2] ?? readline('Name:  ');

if (empty($email) || empty($name)) {
    echo "Email and name are required.\n";
    exit(1);
}

if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    echo "Invalid email address.\n";
    exit(1);
}

// Prompt password securely (no echo)
echo 'Password: ';
system('stty -echo');
$password = trim((string) fgets(STDIN));
system('stty echo');
echo "\n";

if (strlen($password) < 12) {
    echo "Password must be at least 12 characters.\n";
    exit(1);
}

// Insert
$stmt = $db->prepare(
    "INSERT INTO velocms_users (name, email, password_hash, role, active)
     VALUES (:name, :email, :password_hash, 'superadmin', 1)"
);
$stmt->execute([
    ':name'          => $name,
    ':email'         => $email,
    ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
]);

$id = (int) $db->lastInsertId();
echo "Superadmin created (id={$id}): {$name} <{$email}>\n";
