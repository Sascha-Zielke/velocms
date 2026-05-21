<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class View
{
    private ?string $layout       = null;
    private array   $sections     = [];
    private ?string $currentSection = null;

    public function extend(string $layout): void
    {
        $this->layout = $layout;
    }

    public function section(string $name): void
    {
        $this->currentSection = $name;
        ob_start();
    }

    public function endSection(): void
    {
        if ($this->currentSection === null) {
            throw new \RuntimeException('endSection() called without section()');
        }

        $this->sections[$this->currentSection] = ob_get_clean() ?: '';
        $this->currentSection = null;
    }

    public function yield(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    public function renderFile(string $viewPath, array $data = []): void
    {
        extract($data);
        include $viewPath;

        if ($this->layout !== null) {
            $layoutPath = BASE_PATH . '/views/layouts/' . $this->layout . '.php';

            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout not found: {$this->layout}");
            }

            include $layoutPath;
        }
    }
}
