<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Blog\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Blog\Models\BlogModel;
use VeloCMS\Modules\Translation\Models\TranslationModel;
use VeloCMS\Modules\Translation\Services\TranslationEngine;

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
        $activeLangs = json_decode(setting('active_languages', '["de","en"]'), true) ?: ['de', 'en'];
        $defaultLang = setting('default_language', 'de');
        $targetLangs = array_values(array_filter($activeLangs, fn(string $l) => $l !== $defaultLang));

        $this->view->extend('admin');
        $this->render('admin/edit', [
            'post'         => null,
            'translations' => [],
            'targetLangs'  => $targetLangs,
            'defaultLang'  => $defaultLang,
        ]);
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

        $id     = $this->model->insert($this->collectData());
        $fields = $this->collectTranslatableFields();
        $engine = new TranslationEngine();
        $this->redirectWithSuccessAndBackground(
            '/admin/blog/edit/' . $id,
            t('success.saved'),
            fn() => $engine->translateRow('velocms_blog_posts', $id, $fields)
        );
    }

    public function edit(string $id): void
    {
        $post = $this->model->getById((int) $id);
        if (!$post) $this->redirectWithError('/admin/blog', t('error.not_found'));

        [$targetLangs, $defaultLang, $translations] = $this->loadTranslations((int) $id);

        $this->view->extend('admin');
        $this->render('admin/edit', [
            'post'         => $post,
            'translations' => $translations,
            'targetLangs'  => $targetLangs,
            'defaultLang'  => $defaultLang,
        ]);
    }

    public function update(string $id): void
    {
        Auth::verifyCsrf();
        $post = $this->model->getById((int) $id);
        if (!$post) $this->redirectWithError('/admin/blog', t('error.not_found'));

        $this->model->update((int) $id, $this->collectData($post));
        $this->saveManualTranslations((int) $id);

        $fields = $this->collectTranslatableFields();
        $engine = new TranslationEngine();
        $this->redirectWithSuccessAndBackground(
            '/admin/blog/edit/' . $id,
            t('success.saved'),
            fn() => $engine->translateRow('velocms_blog_posts', (int) $id, $fields)
        );
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

    /** @return array{0: string[], 1: string, 2: array<string, array<string,string>>} */
    private function loadTranslations(int $postId): array
    {
        $activeLangs = json_decode(setting('active_languages', '["de","en"]'), true) ?: ['de', 'en'];
        $defaultLang = setting('default_language', 'de');
        $targetLangs = array_values(array_filter(
            $activeLangs,
            fn(string $l) => $l !== $defaultLang
        ));

        $transModel   = new TranslationModel();
        $translations = [];
        foreach ($targetLangs as $lang) {
            $translations[$lang] = $transModel->getForRow('velocms_blog_posts', $postId, $lang);
        }

        return [$targetLangs, $defaultLang, $translations];
    }

    private function saveManualTranslations(int $postId): void
    {
        $submitted = $_POST['trans'] ?? [];
        if (empty($submitted) || !is_array($submitted)) {
            return;
        }

        $transModel = new TranslationModel();

        foreach ($submitted as $lang => $fields) {
            if (!is_array($fields) || !preg_match('/^[a-z]{2}$/', (string) $lang)) {
                continue;
            }
            foreach (['title', 'excerpt', 'content'] as $field) {
                $val = trim((string) ($fields[$field] ?? ''));
                if ($val !== '') {
                    $transModel->upsert(
                        'velocms_blog_posts', $postId, $field, $lang,
                        $val, 'manual', md5($val)
                    );
                }
            }
        }
    }

    /** @return array<string,string> */
    private function collectTranslatableFields(): array
    {
        return [
            'title'   => (string) $this->input('title', ''),
            'excerpt' => (string) $this->input('excerpt', ''),
            'content' => (string) $this->input('content', ''),
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
