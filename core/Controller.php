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
        $parts = explode('\\', static::class);
        $moduleIdx = array_search('Modules', $parts);

        if ($moduleIdx !== false && isset($parts[$moduleIdx + 1])) {
            $moduleName = $parts[$moduleIdx + 1];
            $viewPath = BASE_PATH . "/modules/{$moduleName}/views/{$view}.php";
        } else {
            $viewPath = BASE_PATH . "/views/{$view}.php";
        }

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        $this->view->renderFile($viewPath, $data);
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}