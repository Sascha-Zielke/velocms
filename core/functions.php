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
        $lang = $_COOKIE['vcms_lang'] ?? 'de';
        $lang = in_array($lang, ['de', 'en']) ? $lang : 'de';

        $dePath   = BASE_PATH . '/lang/de.php';
        $langPath = BASE_PATH . "/lang/{$lang}.php";

        $de      = file_exists($dePath) ? require $dePath : [];
        $strings = $lang === 'de' ? $de : array_merge($de, file_exists($langPath) ? require $langPath : []);
    }

    $text = $strings[$key] ?? $key;

    foreach ($params as $param => $value) {
        $text = str_replace(':' . $param, (string) $value, $text);
    }

    return $text;
}

/**
 * Get localized content field — Layer 2 DB content
 */
function localized(array $row, string $field): string
{
    $lang    = $_COOKIE['vcms_lang'] ?? 'de';
    $enField = $field . '_en';
    return ($lang === 'en' && !empty($row[$enField])) ? $row[$enField] : ($row[$field] ?? '');
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
