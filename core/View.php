<?php

declare(strict_types=1);

namespace VeloCMS\Core;

class View
{
    private ?string $layout = null;
    private array $sections = [];
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
            // Wir bauen den Pfad explizit aus dem Layout-Namen zusammen
            $layoutPath = BASE_PATH . '/views/layouts/' . $this->layout . '.php';

            if (!file_exists($layoutPath)) {
                // Zur Sicherheit: Zeig uns, wo genau gesucht wird
                throw new \RuntimeException("Layout not found: {$this->layout}. Expected file at: {$layoutPath}");
            }

            include $layoutPath;
        }
    }
}