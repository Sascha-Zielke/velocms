<?php

declare(strict_types=1);

namespace VeloCMS\Core\Services;

class TranslationService
{
    public function translate(string $text, string $from = 'DE', string $to = 'EN'): string
    {
        try {
            return $this->translateWithDeepL($text, $from, $to);
        } catch (\Exception $e) {
            error_log('DeepL translation failed: ' . $e->getMessage());
        }

        return $this->translateWithAnthropic($text, $from, $to);
    }

    private function translateWithDeepL(string $text, string $from, string $to): string
    {
        $key = $_ENV['DEEPL_KEY'] ?? '';

        if (empty($key)) {
            throw new \RuntimeException('No DeepL API key configured');
        }

        $response = $this->httpPost(
            'https://api-free.deepl.com/v2/translate',
            http_build_query([
                'auth_key'    => $key,
                'text'        => $text,
                'source_lang' => $from,
                'target_lang' => $to,
            ]),
            ['Content-Type: application/x-www-form-urlencoded']
        );

        $data = json_decode($response, true);

        if (!isset($data['translations'][0]['text'])) {
            throw new \RuntimeException('Invalid DeepL response: ' . $response);
        }

        return $data['translations'][0]['text'];
    }

    private function translateWithAnthropic(string $text, string $from, string $to): string
    {
        $key = $_ENV['ANTHROPIC_KEY'] ?? '';

        if (empty($key)) {
            throw new \RuntimeException('No Anthropic API key configured');
        }

        $payload = json_encode([
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 1024,
            'messages'   => [[
                'role'    => 'user',
                'content' => "Translate the following text from {$from} to {$to}. "
                           . "Return only the translation, nothing else:\n\n{$text}",
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
            throw new \RuntimeException('Invalid Anthropic response: ' . $response);
        }

        return $data['content'][0]['text'];
    }

    private function httpPost(string $url, string $body, array $headers = []): string
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("HTTP request failed: {$error}");
        }

        return (string) $response;
    }
}
