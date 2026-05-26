<?php

declare(strict_types=1);

namespace VeloCMS\Core\Services;

/**
 * Thrown when all configured translation providers fail.
 */
class TranslationException extends \RuntimeException {}

/**
 * Low-level translation service.
 * Tries DeepL first, falls back to Anthropic Claude.
 *
 * Configuration (in .env):
 *   DEEPL_API_KEY   — DeepL free or pro API key
 *   ANTHROPIC_API_KEY — Anthropic API key (fallback)
 *
 * For pro DeepL accounts, change the endpoint in translateWithDeepL()
 * from api-free.deepl.com to api.deepl.com.
 */
class TranslationService
{
    /**
     * Translate multiple texts in a single API call.
     *
     * @param  string[] $texts      Texts to translate (preserves order)
     * @param  string   $targetLang e.g. 'EN', 'FR'
     * @param  string   $sourceLang e.g. 'DE'
     * @return string[]             Translated texts in the same order
     * @throws TranslationException When all providers fail
     */
    public function translateBatch(
        array $texts,
        string $targetLang,
        string $sourceLang = 'DE'
    ): array {
        if (empty($texts)) {
            return [];
        }

        $deeplError = null;

        try {
            return $this->translateBatchWithDeepL($texts, $targetLang, $sourceLang);
        } catch (TranslationException $e) {
            $deeplError = $e->getMessage();
            error_log('[TranslationService] DeepL batch failed, trying Anthropic. Reason: ' . $deeplError);
        }

        try {
            return $this->translateBatchWithAnthropic($texts, $targetLang, $sourceLang);
        } catch (TranslationException $e) {
            throw new TranslationException(
                'All translation providers failed. DeepL: ' . $deeplError
                . ' | Anthropic: ' . $e->getMessage()
            );
        }
    }

    // ── DeepL ────────────────────────────────────────────────────────────────

    private function translateBatchWithDeepL(
        array $texts,
        string $targetLang,
        string $sourceLang
    ): array {
        $envKey = $_ENV['DEEPL_API_KEY'] ?? $_ENV['DEEPL_KEY'] ?? '';
        $key    = $envKey !== '' ? $envKey : setting('deepl_api_key', '');

        if (empty($key)) {
            throw new TranslationException('No DeepL API key configured. Add DEEPL_API_KEY to .env or enter it in Translation Settings.');
        }

        $target = strtoupper($targetLang);
        if ($target === 'EN') {
            $target = 'EN-GB';
        }

        $params = [
            'text'        => $texts,
            'source_lang' => strtoupper($sourceLang),
            'target_lang' => $target,
        ];

        $response = $this->httpPost(
            'https://api-free.deepl.com/v2/translate',
            (string) json_encode($params),
            [
                'Authorization: DeepL-Auth-Key ' . $key,
                'Content-Type: application/json',
            ]
        );

        $data = json_decode($response, true);

        if (!isset($data['translations']) || !is_array($data['translations'])) {
            throw new TranslationException('Unexpected DeepL response: ' . $response);
        }

        return array_map(fn(array $t): string => (string) $t['text'], $data['translations']);
    }

    private function translateBatchWithAnthropic(
        array $texts,
        string $targetLang,
        string $sourceLang
    ): array {
        $envKey = $_ENV['ANTHROPIC_API_KEY'] ?? $_ENV['ANTHROPIC_KEY'] ?? '';
        $key    = $envKey !== '' ? $envKey : setting('anthropic_api_key', '');

        if (empty($key)) {
            throw new TranslationException('No Anthropic API key configured. Add ANTHROPIC_API_KEY to .env or enter it in Translation Settings.');
        }

        $langNames = [
            'DE' => 'German',     'EN' => 'English',    'FR' => 'French',
            'ES' => 'Spanish',    'IT' => 'Italian',    'NL' => 'Dutch',
            'PL' => 'Polish',     'PT' => 'Portuguese', 'RU' => 'Russian',
            'JA' => 'Japanese',   'ZH' => 'Chinese',    'TR' => 'Turkish',
        ];

        $from = $langNames[strtoupper($sourceLang)] ?? strtoupper($sourceLang);
        $to   = $langNames[strtoupper($targetLang)] ?? strtoupper($targetLang);

        $prompt = "Translate each string in this JSON array from {$from} to {$to}. "
                . "Return ONLY a valid JSON array with the translated strings in the same order. "
                . "No explanations, no markdown, just the JSON array:\n\n"
                . json_encode($texts);

        $payload = (string) json_encode([
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 4096,
            'messages'   => [[
                'role'    => 'user',
                'content' => $prompt,
            ]],
        ]);

        $response = $this->httpPost(
            'https://api.anthropic.com/v1/messages',
            $payload,
            [
                'x-api-key: '          . $key,
                'anthropic-version: 2023-06-01',
                'content-type: application/json',
            ]
        );

        $data = json_decode($response, true);

        if (!isset($data['content'][0]['text'])) {
            throw new TranslationException('Unexpected Anthropic response: ' . $response);
        }

        $result = json_decode((string) $data['content'][0]['text'], true);

        if (!is_array($result) || count($result) !== count($texts)) {
            throw new TranslationException('Anthropic batch response did not return matching array');
        }

        return array_map('strval', $result);
    }

    // ── HTTP helper ───────────────────────────────────────────────────────────

    private function httpPost(string $url, string $body, array $headers = []): string
    {
        if (!function_exists('curl_init')) {
            throw new TranslationException('cURL extension is not available');
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new TranslationException("cURL request failed: {$error}");
        }

        if ($httpCode >= 400) {
            throw new TranslationException(
                "HTTP {$httpCode} from " . parse_url($url, PHP_URL_HOST) . ": {$response}"
            );
        }

        return (string) $response;
    }
}
