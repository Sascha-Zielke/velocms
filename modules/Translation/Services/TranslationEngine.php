<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Translation\Services;

use VeloCMS\Core\Services\TranslationService;
use VeloCMS\Modules\Translation\Models\TranslationModel;

class TranslationEngine
{
    private TranslationModel $model;
    private TranslationService $service;
    private string $defaultLang;
    /** @var string[] */
    private array $targetLangs;

    public function __construct()
    {
        $this->model   = new TranslationModel();
        $this->service = new TranslationService();

        $activeLangs       = json_decode(setting('active_languages', '["de","en"]'), true) ?: ['de', 'en'];
        $this->defaultLang = setting('default_language', 'de');
        $this->targetLangs = array_values(array_filter(
            $activeLangs,
            fn(string $l) => $l !== $this->defaultLang
        ));
    }

    /**
     * Translate all UI strings from lang/en.php into every active non-default language
     * that has no static lang/{lang}.php file. Results are cached in velocms_translations
     * with table_name='velocms_ui' and row_id=0.
     */
    public function translateUiStrings(): void
    {
        $enPath = BASE_PATH . '/lang/en.php';
        if (!file_exists($enPath)) {
            return;
        }

        $strings = require $enPath;
        if (!is_array($strings)) {
            return;
        }

        foreach ($this->targetLangs as $lang) {
            if (file_exists(BASE_PATH . "/lang/{$lang}.php")) {
                continue;
            }
            try {
                $this->translateUiIntoLang($strings, $lang);
            } catch (\Throwable $e) {
                error_log('[TranslationEngine] UI lang=' . $lang . ': ' . $e->getMessage());
            }
        }
    }

    /** @param array<string,string> $strings */
    private function translateUiIntoLang(array $strings, string $lang): void
    {
        $pending     = [];
        $pendingKeys = [];

        foreach ($strings as $key => $text) {
            if (!is_string($key) || !is_string($text) || trim($text) === '') {
                continue;
            }

            $existing = $this->model->get('velocms_ui', 0, $key, $lang);
            if ($existing && $existing['source'] === 'manual') {
                continue;
            }

            $hash = md5($text);
            if ($existing && $existing['content_hash'] === $hash) {
                continue;
            }

            $pending[]     = $text;
            $pendingKeys[] = [$key, $hash];
        }

        if (empty($pending)) {
            return;
        }

        // Translate in chunks of 50 to stay within API limits
        foreach (array_chunk($pending, 50) as $ci => $chunk) {
            $keyChunk   = array_slice($pendingKeys, $ci * 50, 50);
            $translated = $this->service->translateBatch($chunk, strtoupper($lang), 'EN');

            foreach ($keyChunk as $i => [$key, $hash]) {
                $this->model->upsert('velocms_ui', 0, $key, $lang, $translated[$i], 'auto', $hash);
            }
        }
    }

    /**
     * Translate all given fields for a DB row into every active non-default language.
     *
     * @param string            $table  DB table name (e.g. 'velocms_blog_posts')
     * @param int               $rowId  Primary key of the row
     * @param array<string,string> $fields field => source-text map
     */
    public function translateRow(string $table, int $rowId, array $fields): void
    {
        foreach ($this->targetLangs as $lang) {
            try {
                $this->translateRowIntoLang($table, $rowId, $fields, $lang);
            } catch (\Throwable $e) {
                error_log('[TranslationEngine] lang=' . $lang . ' table=' . $table
                    . ' id=' . $rowId . ': ' . $e->getMessage());
            }
        }
    }

    /** @param array<string,string> $fields */
    private function translateRowIntoLang(string $table, int $rowId, array $fields, string $lang): void
    {
        $pending     = [];
        $pendingKeys = [];

        foreach ($fields as $field => $text) {
            if (trim($text) === '') {
                continue;
            }

            $existing = $this->model->get($table, $rowId, $field, $lang);

            if ($existing && $existing['source'] === 'manual') {
                continue;
            }

            $hash = md5($text);
            if ($existing && $existing['content_hash'] === $hash) {
                continue;
            }

            $pending[]     = $text;
            $pendingKeys[] = [$field, $hash];
        }

        if (empty($pending)) {
            return;
        }

        $translated = $this->service->translateBatch(
            $pending,
            strtoupper($lang),
            strtoupper($this->defaultLang)
        );

        foreach ($pendingKeys as $i => [$field, $hash]) {
            $this->model->upsert($table, $rowId, $field, $lang, $translated[$i], 'auto', $hash);
        }
    }
}
