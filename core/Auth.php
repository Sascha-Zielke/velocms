<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class Auth
{
    /** Role weight: higher = more permissions. */
    private const ROLE_WEIGHT = [
        'editor'     => 1,
        'admin'      => 2,
        'superadmin' => 3,
    ];

    public static function verifyCsrf(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            throw new \RuntimeException('CSRF token mismatch');
        }
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'] ?? 'editor';
        $_SESSION['user_name'] = $user['name'] ?? '';
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    public static function name(): ?string
    {
        return $_SESSION['user_name'] ?? null;
    }

    /**
     * Returns true if the current user's role weight >= the required role weight.
     * e.g. hasRole('admin') → true for admin AND superadmin
     */
    public static function hasRole(string $required): bool
    {
        $current  = self::role() ?? 'editor';
        $reqLevel = self::ROLE_WEIGHT[$required]  ?? 99;
        $curLevel = self::ROLE_WEIGHT[$current]   ?? 0;
        return $curLevel >= $reqLevel;
    }
}
