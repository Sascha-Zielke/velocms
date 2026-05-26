<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Translation;

use VeloCMS\Core\Module;

class TranslationModule extends Module
{
    public string $name    = 'Translation';
    public string $version = '1.0.0';

    public function boot(): void
    {
        // ── Admin routes (dashboard + settings — controllers built in Phase 4) ──
        // Placeholder route so the menu link doesn't 404 before Phase 4
        $this->router->get('/admin/apps/translation', 'Translation\Controllers\AdminTranslationController@dashboard');
        $this->router->get('/admin/apps/translation/editor', 'Translation\Controllers\AdminTranslationController@editor');
        $this->router->post('/admin/apps/translation/editor/[i:id]/save', 'Translation\Controllers\AdminTranslationController@saveTranslation');
        $this->router->post('/admin/apps/translation/editor/[i:id]/unlock', 'Translation\Controllers\AdminTranslationController@unlockTranslation');
        $this->router->get('/admin/apps/translation/settings', 'Translation\Controllers\AdminTranslationController@settings');
        $this->router->post('/admin/apps/translation/settings', 'Translation\Controllers\AdminTranslationController@saveSettings');

        // ── Sidebar: "Apps" section header ────────────────────────────────────
        // type='section' items render as non-clickable group labels.
        // Deduplicated by AdminMenu — safe to register from multiple app-modules.
        $this->admin->addMenuItem([
            'type'     => 'section',
            'label'    => t('nav.apps'),
            'position' => 95,
            'min_role' => 'admin',
        ]);

        // ── Translation app link ───────────────────────────────────────────────
        $this->admin->addMenuItem([
            'label'    => t('nav.translation'),
            'url'      => '/admin/apps/translation',
            'position' => 96,
            'min_role' => 'admin',
        ]);
    }

    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/migrations');
    }
}
