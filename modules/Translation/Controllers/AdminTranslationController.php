<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Translation\Controllers;

use VeloCMS\Core\Controller;

/**
 * Translation-App admin controller.
 * Dashboard, settings, and editor — fully implemented in Phase 4.
 * Phase 1 provides a working placeholder so the menu link is live.
 */
class AdminTranslationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    /** Phase 1 placeholder — replaced in Phase 4. */
    public function dashboard(): void
    {
        $this->render('admin/translation/dashboard', []);
    }

    /** Phase 1 placeholder — replaced in Phase 4. */
    public function settings(): void
    {
        $this->render('admin/translation/dashboard', []);
    }

    /** Phase 1 placeholder — replaced in Phase 4. */
    public function saveSettings(): void
    {
        \VeloCMS\Core\Auth::verifyCsrf();
        $this->redirect('/admin/apps/translation/settings');
    }
}
