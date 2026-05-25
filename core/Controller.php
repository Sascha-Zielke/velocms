<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class Controller
{
    protected ?View $view = null;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function render(string $view, array $data = []): void
    {
        $parts     = explode('\\', static::class);
        $moduleIdx = array_search('Modules', $parts);

        if ($moduleIdx !== false && isset($parts[$moduleIdx + 1])) {
            $moduleName = $parts[$moduleIdx + 1];
            $viewPath   = BASE_PATH . "/modules/{$moduleName}/views/{$view}.php";
        } else {
            $viewPath = BASE_PATH . "/views/{$view}.php";
        }

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        $this->view->renderFile($viewPath, $data);
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/admin/login');
        }
    }

    protected function requireRole(string $role): void
    {
        $this->requireAuth();
        if (($_SESSION['user_role'] ?? '') !== $role) {
            http_response_code(403);
            die('Forbidden');
        }
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

    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}