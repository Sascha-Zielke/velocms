<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Blog\Controllers;

use VeloCMS\Core\Controller;
use VeloCMS\Modules\Blog\Models\BlogModel;

class BlogController extends Controller
{
    private BlogModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new BlogModel();
    }

    public function index(): void
    {
        $page    = max(1, (int) ($this->input('page', 1)));
        $perPage = 10;
        $posts   = $this->model->getPublished($perPage, ($page - 1) * $perPage);
        $total   = $this->model->count(true);
        $this->view->extend('frontend');
        $this->render('frontend/index', ['posts' => $posts, 'total' => $total, 'page' => $page, 'perPage' => $perPage]);
    }

    public function show(string $slug): void
    {
        $post = $this->model->getBySlug($slug);
        if (!$post) {
            http_response_code(404);
            include BASE_PATH . '/views/errors/404.php';
            exit;
        }
        $this->view->extend('frontend');
        $this->render('frontend/show', ['post' => $post]);
    }
}
