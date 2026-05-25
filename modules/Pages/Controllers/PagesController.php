<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Pages\Controllers;

use VeloCMS\Core\Controller;
use VeloCMS\Modules\Pages\Models\PagesModel;

class PagesController extends Controller
{
    private PagesModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new PagesModel();
    }

    /**
     * Root URL (/) — redirect to the configured homepage slug.
     * Falls back to the first published page, or shows 404 if none exist.
     */
    public function home(): void
    {
        $slug = setting('homepage_slug', 'startseite');
        $page = $this->model->getBySlug($slug);

        if ($page && $page['status'] === 'published') {
            $this->redirect('/' . $slug);
        }

        // Fallback: first published page
        $first = $this->model->getFirstPublished();
        if ($first) {
            $this->redirect('/' . $first['slug']);
        }

        // Nothing published at all
        http_response_code(404);
        $this->render('frontend/404', []);
    }

    public function show(string $slug): void
    {
        $page = $this->model->getBySlug($slug);

        if ($page === null || $page['status'] !== 'published') {
            http_response_code(404);
            $this->render('frontend/404', []);
            return;
        }

        $sections = $this->model->getFullPage((int) $page['id']);
        $this->render('frontend/page', ['page' => $page, 'sections' => $sections]);
    }
}
