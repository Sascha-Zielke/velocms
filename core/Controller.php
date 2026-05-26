<?php
declare(strict_types=1);

namespace VeloCMS\Core;

class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function render(string $view, array $data = []): void
    {
        $parts    = explode('\\', static::class);
        $moduleIdx = array_search('Modules', $parts);

        if ($moduleIdx !== false && isset($parts[$moduleIdx + 1])) {
            $moduleName = $parts[$moduleIdx + 1];
            $viewPath   = BASE_PATH . "/modules/{$moduleName}/views/{$view}.php";
        } else {
            $viewPath = BASE_PATH . "/views/{$view}.php";
        }

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view} (looked in: {$viewPath})");
        }

        $this->view->renderFile($viewPath, $data);
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    protected function redirectWithSuccess(string $url, string $message): never
    {
        $_SESSION['flash_success'] = $message;
        $this->redirect($url);
    }

    protected function redirectWithError(string $url, string $message): never
    {
        $_SESSION['flash_error'] = $message;
        $this->redirect($url);
    }

    /**
     * Redirect immediately, then run $callback in the background after the response is flushed.
     * Requires fastcgi_finish_request() (PHP-FPM) — falls back to running callback before exit.
     */
    protected function redirectWithSuccessAndBackground(
        string $url,
        string $message,
        callable $callback
    ): never {
        $_SESSION['flash_success'] = $message;
        session_write_close();
        header('Location: ' . $url);

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        set_time_limit(0);
        $callback();
        exit;
    }

    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->redirect('/admin/login');
        }
    }

    protected function requireRole(string $role): void
    {
        $this->requireAuth();
        if (!Auth::hasRole($role)) {
            http_response_code(403);
            $errorPage = BASE_PATH . '/views/errors/403.php';
            if (file_exists($errorPage)) {
                include $errorPage;
            } else {
                echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>403</title></head>'
                   . '<body><h1>403 – Zugriff verweigert</h1>'
                   . '<p><a href="/admin">Zum Dashboard</a></p></body></html>';
            }
            exit;
        }
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonWithBackground(array $data, callable $callback, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        session_write_close();
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        set_time_limit(0);
        $callback();
        exit;
    }
}
