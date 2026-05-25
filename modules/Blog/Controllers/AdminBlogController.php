<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Blog\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Blog\Models\BlogModel;

class AdminBlogController extends Controller
{
    private BlogModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->model = new BlogModel();
    }

    public function index(): void
    {
        $this->view->extend('admin');
        $this->render('admin/index', ['posts' => $this->model->getAll()]);
    }

    public function new(): void
    {
        $this->view->extend('admin');
        $this->render('admin/edit', ['post' => null]);
    }

    public function save(): void
    {
        Auth::verifyCsrf();
        $slug = $this->sanitizeSlug($this->input('slug', ''));
        if (empty($slug)) {
            $this->redirectWithError('/admin/blog/new', t('error.required'));
        }
        if (empty($this->input('title', ''))) {
            $this->redirectWithError('/admin/blog/new', t('error.title_required'));
        }

        $id = $this->model->insert($this->collectData());
        $this->redirectWithSuccess('/admin/blog/edit/' . $id, t('success.saved'));
    }

    public function edit(string $id): void
    {
        $post = $this->model->getById((int) $id);
        if (!$post) $this->redirectWithError('/admin/blog', t('error.not_found'));
        $this->view->extend('admin');
        $this->render('admin/edit', ['post' => $post]);
    }

    public function update(string $id): void
    {
        Auth::verifyCsrf();
        $post = $this->model->getById((int) $id);
        if (!$post) $this->redirectWithError('/admin/blog', t('error.not_found'));

        $this->model->update((int) $id, $this->collectData($post));
        $this->redirectWithSuccess('/admin/blog/edit/' . $id, t('success.saved'));
    }

    public function delete(string $id): void
    {
        Auth::verifyCsrf();
        $this->model->delete((int) $id);
        $this->redirectWithSuccess('/admin/blog', t('success.deleted'));
    }

    private function collectData(array $existing = []): array
    {
        $slug = $this->sanitizeSlug($this->input('slug', $existing['slug'] ?? ''));
        return [
            'title'            => $this->input('title', ''),
            'title_en'         => $this->input('title_en', ''),
            'slug'             => $slug,
            'excerpt'          => $this->input('excerpt', null),
            'excerpt_en'       => $this->input('excerpt_en', null),
            'content'          => $this->input('content', ''),
            'content_en'       => $this->input('content_en', ''),
            'cover_image'      => $this->input('cover_image', ''),
            'status'           => $this->input('status', 'draft'),
            'meta_title'       => $this->input('meta_title', ''),
            'meta_description' => $this->input('meta_description', ''),
            'author_id'        => Auth::id() ?? 1,
            'published_at'     => $existing['published_at'] ?? null,
        ];
    }

    private function sanitizeSlug(string $raw): string
    {
        $slug = mb_strtolower(trim($raw));
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}
