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
