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
            // Start with EN fallback, then overlay DB-cached UI translations
            $strings = array_merge($de, $en);
            try {
                $db   = \VeloCMS\Core\Database::getInstance()->getPdo();
                $stmt = $db->prepare(
                    "SELECT field, value FROM velocms_translations
                     WHERE table_name = 'velocms_ui' AND row_id = 0
                       AND language = :l AND stale = 0"
                );
                $stmt->execute([':l' => $lang]);
                $ui = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
                if (!empty($ui)) {
                    $strings = array_merge($strings, $ui);
                }
            } catch (\Throwable) {
                // DB not yet available — English fallback stays
            }
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

// ─── Visual Editor helpers ──────────────────────────────────────────────────

/**
 * Returns Gridstack position attributes for a box array.
 * Use on every [data-ve-box] element so Gridstack can read its grid position.
 * Falls back to auto-position with full-width (w=24) when no position is saved yet.
 */
function ve_gs_attrs(array $box): string
{
    if (array_key_exists('grid_x', $box) && $box['grid_x'] !== null) {
        return sprintf(
            'data-gs-x="%d" data-gs-y="%d" data-gs-w="%d" data-gs-h="%d"',
            (int) $box['grid_x'],
            (int) $box['grid_y'],
            max(1, (int) ($box['grid_w'] ?? 24)),
            max(1, (int) ($box['grid_h'] ?? 4))
        );
    }

    return 'data-gs-auto-position="1" data-gs-w="24" data-gs-h="4"';
}

/**
 * Outputs VE CSS assets + meta tags into <head>.
 * Any tenant layout calls <?= ve_head($pageId) ?> — no per-tenant config needed.
 *
 * @param int $pageId  Current page's DB id (used by JS to POST grid changes).
 */
function ve_head(int $pageId = 0): string
{
    if (!\VeloCMS\Core\Auth::check()) {
        return '';
    }
    if (!isset($_GET['ve_edit']) && !isset($_GET['ve_embedded'])) {
        return '';
    }

    $token = e($_SESSION['csrf_token'] ?? '');

    return implode("\n    ", [
        '<meta name="csrf-token"  content="' . $token . '">',
        '<meta name="ve-page-id" content="' . $pageId . '">',
        '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@10/dist/gridstack.min.css">',
        '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@10/dist/gridstack-extra.min.css">',
        '<link rel="stylesheet" href="/assets/css/visual-editor.css">',
    ]);
}

/**
 * Outputs VE JavaScript before </body>.
 * Any tenant layout calls <?= ve_scripts() ?> — no per-tenant config needed.
 */
function ve_scripts(): string
{
    if (!\VeloCMS\Core\Auth::check()) {
        return '';
    }
    if (!isset($_GET['ve_edit']) && !isset($_GET['ve_embedded'])) {
        return '';
    }

    return implode("\n", [
        '<script src="https://cdn.jsdelivr.net/npm/gridstack@10/dist/gridstack-all.js"></script>',
        '<script src="https://cdn.jsdelivr.net/npm/sortablejs@1/Sortable.min.js"></script>',
        '<script src="/assets/js/visual-editor.js"></script>',
    ]);
}

// ─── Navigation ─────────────────────────────────────────────────────────────

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
