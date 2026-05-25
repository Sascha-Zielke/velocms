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

    public function show(string $slug): void
    {
        // Homepage: empty slug -> 'home'
        $slug = $slug === '' ? 'home' : $slug;

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
