<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Nav\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Nav\Models\NavModel;
use VeloCMS\Modules\Translation\Services\TranslationEngine;

class AdminNavController extends Controller
{
    private NavModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->model = new NavModel();
    }

    public function index(): void
    {
        $this->view->extend('admin');
        $this->render('admin/nav/index', [
            'items' => $this->model->getAll(),
        ]);
    }

    public function create(): void
    {
        $this->view->extend('admin');
        $this->render('admin/nav/form', [
            'item'   => null,
            'action' => '/admin/nav/store',
        ]);
    }

    public function store(): void
    {
        Auth::verifyCsrf();

        $label = trim($this->input('label', ''));
        $url   = trim($this->input('url', ''));

        if ($label === '' || $url === '') {
            $this->redirectWithError('/admin/nav/create', t('error.required'));
        }

        $newId  = $this->model->create([
            'label'    => $label,
            'label_en' => trim($this->input('label_en', '')),
            'url'      => $url,
            'target'   => $this->input('target', '_self'),
            'active'   => $this->input('active', '1'),
        ]);
        $engine = new TranslationEngine();
        $this->redirectWithSuccessAndBackground(
            '/admin/nav',
            t('success.saved'),
            fn() => $engine->translateRow('velocms_nav_items', $newId, ['label' => $label])
        );
    }

    public function edit(string $id): void
    {
        $item = $this->loadItem((int) $id);

        $this->view->extend('admin');
        $this->render('admin/nav/form', [
            'item'   => $item,
            'action' => '/admin/nav/' . $id . '/update',
        ]);
    }

    public function update(string $id): void
    {
        Auth::verifyCsrf();
        $this->loadItem((int) $id);

        $label = trim($this->input('label', ''));
        $url   = trim($this->input('url', ''));

        if ($label === '' || $url === '') {
            $this->redirectWithError('/admin/nav/' . $id . '/edit', t('error.required'));
        }

        $this->model->update((int) $id, [
            'label'    => $label,
            'label_en' => trim($this->input('label_en', '')),
            'url'      => $url,
            'target'   => $this->input('target', '_self'),
            'active'   => $this->input('active', '0'),
        ]);
        $engine = new TranslationEngine();
        $this->redirectWithSuccessAndBackground(
            '/admin/nav',
            t('success.saved'),
            fn() => $engine->translateRow('velocms_nav_items', (int) $id, ['label' => $label])
        );
    }

    public function delete(string $id): void
    {
        Auth::verifyCsrf();
        $this->loadItem((int) $id);
        $this->model->delete((int) $id);
        $this->redirectWithSuccess('/admin/nav', t('success.deleted'));
    }

    public function moveUp(string $id): void
    {
        Auth::verifyCsrf();
        $this->loadItem((int) $id);
        $this->model->moveUp((int) $id);
        $this->redirect('/admin/nav');
    }

    public function moveDown(string $id): void
    {
        Auth::verifyCsrf();
        $this->loadItem((int) $id);
        $this->model->moveDown((int) $id);
        $this->redirect('/admin/nav');
    }

    private function loadItem(int $id): array
    {
        $item = $this->model->getById($id);
        if (!$item) {
            $this->redirectWithError('/admin/nav', t('error.not_found'));
        }
        return $item;
    }
}
