<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Translation\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Core\Database;
use VeloCMS\Modules\Translation\Models\TranslationModel;

class AdminTranslationController extends Controller
{
    private TranslationModel $model;
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

        $this->model       = new TranslationModel();
        $this->activeLangs = json_decode(setting('active_languages', '["de","en"]'), true) ?: ['de', 'en'];
        $this->defaultLang = setting('default_language', 'de');
        $this->targetLangs = array_values(array_filter(
            $this->activeLangs,
            fn(string $l) => $l !== $this->defaultLang
        ));
    }

    public function dashboard(): void
    {
        $stats = [];
        foreach ($this->targetLangs as $lang) {
            $stats[$lang] = $this->model->getStats($lang);
        }

        $this->render('admin/translation/dashboard', [
            'stats'       => $stats,
            'targetLangs' => $this->targetLangs,
            'defaultLang' => $this->defaultLang,
        ]);
    }

    public function editor(): void
    {
        $lang    = $this->sanitizeLang($this->input('lang', $this->targetLangs[0] ?? 'en'));
        $table   = (string) $this->input('table', '');
        $source  = (string) $this->input('source', '');
        $editId  = (int) $this->input('edit', 0);
        $page    = max(1, (int) $this->input('page', 1));
        $perPage = 25;
        $offset  = ($page - 1) * $perPage;

        $rows   = $this->model->getList($lang, $table, $source, $perPage, $offset);
        $total  = $this->model->countList($lang, $table, $source);
        $tables = $this->model->getTables();
        $pages  = (int) ceil($total / $perPage);

        $editRow = $editId > 0 ? $this->model->getById($editId) : null;

        $this->render('admin/translation/editor', [
            'rows'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'pages'       => $pages,
            'perPage'     => $perPage,
            'tables'      => $tables,
            'targetLangs' => $this->targetLangs,
            'lang'        => $lang,
            'table'       => $table,
            'source'      => $source,
            'editRow'     => $editRow,
            'editId'      => $editId,
        ]);
    }

    public function saveTranslation(string $id): void
    {
        Auth::verifyCsrf();
        $row = $this->model->getById((int) $id);
        if (!$row) {
            $this->redirectWithError('/admin/apps/translation/editor', t('error.not_found'));
        }
        $this->model->updateManual((int) $id, (string) $this->input('value', ''));
        $qs = $this->buildEditorQs($row['language'], $row['table_name']);
        $this->redirectWithSuccess('/admin/apps/translation/editor?' . $qs, t('success.saved'));
    }

    public function unlockTranslation(string $id): void
    {
        Auth::verifyCsrf();
        $row = $this->model->getById((int) $id);
        if (!$row) {
            $this->redirectWithError('/admin/apps/translation/editor', t('error.not_found'));
        }
        $this->model->unlock((int) $id);
        $qs = $this->buildEditorQs($row['language'], $row['table_name']);
        $this->redirectWithSuccess('/admin/apps/translation/editor?' . $qs, t('translation.action_unlock'));
    }

    public function settings(): void
    {
        $this->render('admin/translation/settings', [
            'activeLangs'  => $this->activeLangs,
            'defaultLang'  => $this->defaultLang,
            'provider'     => setting('translation_provider', 'deepl'),
            'deeplKey'     => setting('deepl_api_key', ''),
            'anthropicKey' => setting('anthropic_api_key', ''),
        ]);
    }

    public function saveSettings(): void
    {
        Auth::verifyCsrf();

        $knownLangs = [
            'ar','bg','cs','da','de','el','en','es','et','fi','fr',
            'hu','id','it','ja','ko','lt','lv','nb','nl','pl',
            'pt','ro','ru','sk','sl','sr','sv','tr','uk','zh',
        ];
        $selected   = array_filter(
            (array) ($_POST['active_languages'] ?? []),
            fn(string $l) => in_array($l, $knownLangs, true)
        );
        if (empty($selected)) {
            $this->redirectWithError('/admin/apps/translation/settings', t('error.required'));
        }
        $selected = array_values($selected);

        $default = trim((string) $this->input('default_language', 'de'));
        if (!in_array($default, $selected, true)) {
            $default = $selected[0];
        }

        $provider = $this->input('translation_provider', 'deepl');
        if (!in_array($provider, ['deepl', 'anthropic'], true)) {
            $provider = 'deepl';
        }

        $this->saveSetting('active_languages', json_encode($selected));
        $this->saveSetting('default_language', $default);
        $this->saveSetting('translation_provider', $provider);

        $deeplKey = trim((string) $this->input('deepl_api_key', ''));
        if ($deeplKey !== '') {
            $this->saveSetting('deepl_api_key', $deeplKey);
        }

        $anthropicKey = trim((string) $this->input('anthropic_api_key', ''));
        if ($anthropicKey !== '') {
            $this->saveSetting('anthropic_api_key', $anthropicKey);
        }

        $engine = new \VeloCMS\Modules\Translation\Services\TranslationEngine();
        $this->redirectWithSuccessAndBackground(
            '/admin/apps/translation/settings',
            t('translation.settings_saved'),
            fn() => $engine->translateUiStrings()
        );
    }

    private function saveSetting(string $key, string $value): void
    {
        $db   = Database::getInstance()->getPdo();
        $stmt = $db->prepare(
            'INSERT INTO velocms_settings (`key`, value) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE value = :v2'
        );
        $stmt->execute([':k' => $key, ':v' => $value, ':v2' => $value]);
    }

    private function sanitizeLang(string $lang): string
    {
        return in_array($lang, $this->targetLangs, true) ? $lang : ($this->targetLangs[0] ?? 'en');
    }

    private function buildEditorQs(string $lang, string $table): string
    {
        return http_build_query(['lang' => $lang, 'table' => $table]);
    }
}
