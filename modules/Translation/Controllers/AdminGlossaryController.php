<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Translation\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Translation\Models\GlossaryModel;

class AdminGlossaryController extends Controller
{
    private GlossaryModel $model;
    /** @var string[] */
    private array $activeLangs;
    private string $defaultLang;
    /** @var string[] */
    private array $targetLangs;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');

        $this->model       = new GlossaryModel();
        $this->activeLangs = json_decode(setting('active_languages', '["de","en"]'), true) ?: ['de', 'en'];
        $this->defaultLang = setting('default_language', 'de');
        $this->targetLangs = array_values(array_filter(
            $this->activeLangs,
            fn(string $l) => $l !== $this->defaultLang
        ));
    }

    public function index(): void
    {
        $this->render('admin/translation/glossary', [
            'entries'     => $this->model->getAllGrouped(),
            'defaultLang' => $this->defaultLang,
            'targetLangs' => $this->targetLangs,
        ]);
    }

    public function save(): void
    {
        Auth::verifyCsrf();

        $sourceLang = trim((string) $this->input('source_lang', $this->defaultLang));
        $targetLang = trim((string) $this->input('target_lang', $this->targetLangs[0] ?? 'en'));
        $sourceTerm = trim((string) $this->input('source_term', ''));
        $targetTerm = trim((string) $this->input('target_term', ''));

        if ($sourceTerm === '' || $targetTerm === '') {
            $this->redirectWithError('/admin/apps/translation/glossary', t('error.required'));
        }

        $this->model->save($sourceLang, $targetLang, $sourceTerm, $targetTerm);
        $this->redirectWithSuccess('/admin/apps/translation/glossary', t('success.saved'));
    }

    public function delete(string $id): void
    {
        Auth::verifyCsrf();
        $this->model->delete((int) $id);
        $this->redirectWithSuccess('/admin/apps/translation/glossary', t('success.deleted'));
    }
}
