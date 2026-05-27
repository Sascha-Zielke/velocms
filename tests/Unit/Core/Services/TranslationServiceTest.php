<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Core\Services;

use PHPUnit\Framework\TestCase;
use VeloCMS\Core\Services\TranslationException;
use VeloCMS\Core\Services\TranslationService;

class TranslationServiceTest extends TestCase
{
    private TranslationService $service;

    protected function setUp(): void
    {
        $this->service = new TranslationService();
    }

    public function testTranslateBatch_returnsEmptyArray_whenInputIsEmpty(): void
    {
        $result = $this->service->translateBatch([], 'EN', 'DE');
        $this->assertSame([], $result);
    }

    public function testTranslateBatch_throwsTranslationException_whenNoApiKeyConfigured(): void
    {
        $savedDeepL     = $_ENV['DEEPL_API_KEY'] ?? null;
        $savedDeepLKey  = $_ENV['DEEPL_KEY']     ?? null;
        $savedAnthropic = $_ENV['ANTHROPIC_API_KEY'] ?? null;
        $savedAnthKey   = $_ENV['ANTHROPIC_KEY']     ?? null;

        // Clear all env-based keys so both providers fail fast
        unset($_ENV['DEEPL_API_KEY'], $_ENV['DEEPL_KEY'], $_ENV['ANTHROPIC_API_KEY'], $_ENV['ANTHROPIC_KEY']);

        // Also suppress the setting() fallback by ensuring it returns empty
        // (setting() reads from DB — not available in unit tests; it will return the default '')
        $this->expectException(TranslationException::class);

        try {
            $this->service->translateBatch(['Hello'], 'FR', 'EN');
        } finally {
            // Restore env state
            if ($savedDeepL    !== null) { $_ENV['DEEPL_API_KEY']      = $savedDeepL; }
            if ($savedDeepLKey !== null) { $_ENV['DEEPL_KEY']          = $savedDeepLKey; }
            if ($savedAnthropic!== null) { $_ENV['ANTHROPIC_API_KEY']  = $savedAnthropic; }
            if ($savedAnthKey  !== null) { $_ENV['ANTHROPIC_KEY']      = $savedAnthKey; }
        }
    }
}
