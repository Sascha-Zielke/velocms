<?php
declare(strict_types=1);

namespace VeloCMS\Modules\Pages\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Pages\Models\PagesModel;
use VeloCMS\Modules\Translation\Services\TranslationEngine;

class AdminPagesController extends Controller
{
    private PagesModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->model = new PagesModel();
    }

    public function index(): void
    {
        $this->render('admin/index', ['pages' => $this->model->getAll()]);
    }

    public function visualEditor(string $slug = ''): void
    {
        $pages = $this->model->getAll();
        if ($slug === '') {
            $slug = setting('homepage_slug', $pages[0]['slug'] ?? '');
        }
        $this->render('admin/visual-editor', ['pages' => $pages, 'currentSlug' => $slug]);
    }

    public function new(): void
    {
        $this->render('admin/edit', ['page' => null, 'sections' => []]);
    }

    public function edit(string $id): void
    {
        $page = $this->model->getById((int) $id);
        if ($page === null) {
            $this->redirectWithError('/admin/pages', t('error.not_found'));
        }
        $sections = $this->model->getFullPage((int) $id);
        $this->render('admin/edit', ['page' => $page, 'sections' => $sections]);
    }

    public function save(): void
    {
        Auth::verifyCsrf();

        $id    = (int) $this->input('id', 0);
        $title = trim((string) $this->input('title', ''));
        $slug  = preg_replace('/[^a-z0-9\-]/', '', strtolower(trim((string) $this->input('slug', ''))));

        if ($title === '' || $slug === '') {
            $this->redirectWithError('/admin/pages', t('error.required'));
        }

        $data = [
            'slug'             => $slug,
            'title'            => $title,
            'title_en'         => trim((string) $this->input('title_en', '')) ?: null,
            'status'           => $this->input('status', 'draft') === 'published' ? 'published' : 'draft',
            'meta_title'       => trim((string) $this->input('meta_title', '')) ?: null,
            'meta_description' => trim((string) $this->input('meta_description', '')) ?: null,
        ];

        $engine = new TranslationEngine();
        $fields = array_filter(['title' => $title, 'meta_title' => $data['meta_title'] ?? '']);

        if ($id > 0) {
            $this->model->update($id, $data);
            $this->redirectWithSuccessAndBackground(
                '/admin/pages/edit/' . $id,
                t('success.saved'),
                fn() => $engine->translateRow('velocms_pages', $id, $fields)
            );
        } else {
            $newId = $this->model->create($data);
            $this->redirectWithSuccessAndBackground(
                '/admin/pages/edit/' . $newId,
                t('success.saved'),
                fn() => $engine->translateRow('velocms_pages', $newId, $fields)
            );
        }
    }

    public function delete(string $id): void
    {
        Auth::verifyCsrf();
        $this->model->softDelete((int) $id);
        $this->redirectWithSuccess('/admin/pages', t('success.deleted'));
    }

    // --- Visual Editor API endpoints (return JSON) ---

    public function boxData(string $id): void
    {
        $box = $this->model->getBox((int) $id);
        if (!$box) {
            $this->json(['ok' => false]);
            return;
        }
        $data = is_array($box['data'] ?? null) ? $box['data'] : [];
        $this->json([
            'ok'      => true,
            'type'    => $box['type'] ?? 'text',
            'content' => $data['content'] ?? [],
            'data'    => $data,
        ]);
    }

    public function addSection(string $id): void
    {
        Auth::verifyCsrf();
        $sectionId = $this->model->createSection((int) $id);
        $this->json(['ok' => true, 'section_id' => $sectionId]);
    }

    public function addRow(string $id): void
    {
        Auth::verifyCsrf();
        $rowId = $this->model->createRow((int) $id);
        $this->json(['ok' => true, 'row_id' => $rowId]);
    }

    public function addBox(string $id): void
    {
        Auth::verifyCsrf();
        $type  = $this->input('type', 'text');
        $allowed = ['text', 'image', 'video', 'button', 'spacer'];
        $type  = in_array($type, $allowed, true) ? $type : 'text';
        $boxId = $this->model->addBox((int) $id, $type);
        $this->json(['ok' => true, 'box_id' => $boxId]);
    }

    public function saveBox(string $id): void
    {
        Auth::verifyCsrf();
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $box     = $this->model->getBox((int) $id);
        $type    = $box['type'] ?? 'text';
        unset($payload['_csrf']);
        $this->model->saveBox((int) $id, $type, $payload);

        $text = trim((string) ($payload['text'] ?? ''));
        if ($type === 'text' && $text !== '') {
            $engine = new TranslationEngine();
            $boxId  = (int) $id;
            $this->jsonWithBackground(
                ['ok' => true],
                fn() => $engine->translateRow('velocms_boxes', $boxId, ['text' => $text])
            );
        }

        $this->json(['ok' => true]);
    }

    public function deleteBox(string $id): void
    {
        Auth::verifyCsrf();
        $this->model->deleteBox((int) $id);
        $this->json(['ok' => true]);
    }

    public function deleteSection(string $id): void
    {
        Auth::verifyCsrf();
        $this->model->deleteSection((int) $id);
        $this->json(['ok' => true]);
    }

    public function deleteRow(string $id): void
    {
        Auth::verifyCsrf();
        $this->model->deleteRow((int) $id);
        $this->json(['ok' => true]);
    }

    public function saveSectionSettings(string $id): void
    {
        Auth::verifyCsrf();
        $rawSettings = $this->input('settings', '{}');
        $settings = is_array($rawSettings) ? $rawSettings : (json_decode((string)$rawSettings, true) ?? []);
        // Whitelist: only overlay (0-100) and bg_color
        $clean = [
            'overlay'  => min(100, max(0, (int)($settings['overlay'] ?? 0))),
            'bg_color' => preg_replace('/[^#a-fA-F0-9]/', '', (string)($settings['bg_color'] ?? '')),
            'padding'  => in_array($settings['padding'] ?? '', ['none','sm','md','lg'], true)
                          ? $settings['padding'] : 'md',
        ];
        $this->model->updateSectionSettings((int) $id, $clean);
        $this->json(['ok' => true]);
    }

}
