<?php

declare(strict_types=1);

/**
 * Escape output — always use for user data in views
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Get translation string — Layer 1 UI strings
 */
function t(string $key, array $params = []): string
{
    static $strings = null;

    if ($strings === null) {
        $isAdmin = str_starts_with($_SERVER['REQUEST_URI'] ?? '/', '/admin');
        $lang    = $isAdmin
            ? ($_COOKIE['vcms_admin_lang'] ?? $_COOKIE['vcms_lang'] ?? 'de')
            : ($_COOKIE['vcms_lang'] ?? 'de');
        $lang = preg_match('/^[a-z]{2}$/', $lang) ? $lang : 'de';

        $dePath   = BASE_PATH . '/lang/de.php';
        $enPath   = BASE_PATH . '/lang/en.php';
        $langPath = BASE_PATH . "/lang/{$lang}.php";

        $de = file_exists($dePath) ? require $dePath : [];
        $en = file_exists($enPath) ? require $enPath : [];

        if ($lang === 'de') {
            $strings = $de;
        } elseif ($lang === 'en') {
            $strings = array_merge($de, $en);
        } elseif (file_exists($langPath)) {
            $strings = array_merge($de, require $langPath);
        } else {
            $strings = array_merge($de, $en);
        }
    }

    $text = $strings[$key] ?? $key;

    foreach ($params as $param => $value) {
        $text = str_replace(':' . $param, (string) $value, $text);
    }

    return $text;
}

/**
 * Get localized content field — Layer 2 DB content.
 * Pass $table to look up velocms_translations first (Phase 3+).
 * Falls back to legacy _en columns for backward compatibility.
 */
function localized(array $row, string $field, string $table = ''): string
{
    static $cache = [];

    $isAdmin     = str_starts_with($_SERVER['REQUEST_URI'] ?? '/', '/admin');
    $lang        = $isAdmin
        ? ($_COOKIE['vcms_admin_lang'] ?? $_COOKIE['vcms_lang'] ?? 'de')
        : ($_COOKIE['vcms_lang'] ?? 'de');
    $defaultLang = setting('default_language', 'de');

    if ($lang === $defaultLang) {
        return (string) ($row[$field] ?? '');
    }

    if ($table !== '' && !empty($row['id'])) {
        $key = $table . ':' . $row['id'] . ':' . $field . ':' . $lang;
        if (!array_key_exists($key, $cache)) {
            try {
                $db   = \VeloCMS\Core\Database::getInstance()->getPdo();
                $stmt = $db->prepare(
                    'SELECT value FROM velocms_translations
                     WHERE table_name = :t AND row_id = :r AND field = :f AND language = :l AND stale = 0
                     LIMIT 1'
                );
                $stmt->execute([':t' => $table, ':r' => (int) $row['id'], ':f' => $field, ':l' => $lang]);
                $val          = $stmt->fetchColumn();
                $cache[$key]  = ($val !== false && $val !== '') ? (string) $val : null;
            } catch (\Throwable) {
                $cache[$key] = null;
            }
        }
        if ($cache[$key] !== null) {
            return $cache[$key];
        }
    }

    $enField = $field . '_en';
    if (!empty($row[$enField])) {
        return (string) $row[$enField];
    }

    return (string) ($row[$field] ?? '');
}

/**
 * CSRF token hidden field
 */
function csrf_field(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $token = e($_SESSION['csrf_token']);
    return "<input type=\"hidden\" name=\"_csrf\" value=\"{$token}\">";
}

/**
 * Strip dangerous HTML tags — for rich text output
 */
function safe_html(string $html): string
{
    return strip_tags($html, '<p><br><strong><em><ul><ol><li><a><h2><h3><h4><blockquote>');
}

/**
 * Get a tenant setting value — cached per request.
 */
function setting(string $key, string $default = ''): string
{
    static $cache = null;

    if ($cache === null) {
        try {
            $db    = \VeloCMS\Core\Database::getInstance()->getPdo();
            $stmt  = $db->query('SELECT `key`, value FROM velocms_settings');
            $cache = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
        } catch (\Throwable) {
            $cache = [];
        }
    }

    return (string) ($cache[$key] ?? $default);
}

/**
 * Get active navigation items — cached per request.
 *
 * @return array<int, array<string, mixed>>
 */
function nav(): array
{
    static $cache = null;

    if ($cache === null) {
        try {
            $db    = \VeloCMS\Core\Database::getInstance()->getPdo();
            $stmt  = $db->query(
                'SELECT * FROM velocms_nav_items WHERE active = 1 AND deleted_at IS NULL ORDER BY position ASC, id ASC'
            );
            $cache = $stmt->fetchAll() ?: [];
        } catch (\Throwable) {
            $cache = [];
        }
    }

    return $cache;
}
