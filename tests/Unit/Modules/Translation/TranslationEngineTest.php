<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Translation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Translation\Models\GlossaryModel;
use VeloCMS\Modules\Translation\Models\TranslationModel;
use VeloCMS\Modules\Translation\Services\TranslationEngine;

/**
 * Tests the HTML-safe glossary substitution logic without touching DB or external APIs.
 * Uses reflection to invoke private methods on an engine wired with mock dependencies.
 */
class TranslationEngineTest extends TestCase
{
    private TranslationEngine $engine;
    private MockObject $glossaryMock;

    protected function setUp(): void
    {
        // Stub out DB-dependent models and the external service
        $dbMock = $this->createMock(\PDO::class);

        $this->glossaryMock = $this->createMock(GlossaryModel::class);
        $modelMock          = $this->createMock(TranslationModel::class);

        // Build the engine, then inject mocked dependencies via reflection
        // (constructor calls Database::getInstance(), so we patch after instantiation)
        $this->engine = $this->getMockBuilder(TranslationEngine::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $ref = new \ReflectionClass($this->engine);

        $glossaryProp = $ref->getProperty('glossary');
        $glossaryProp->setAccessible(true);
        $glossaryProp->setValue($this->engine, $this->glossaryMock);

        $modelProp = $ref->getProperty('model');
        $modelProp->setAccessible(true);
        $modelProp->setValue($this->engine, $modelMock);

        // defaultLang and targetLangs needed for public methods
        $defProp = $ref->getProperty('defaultLang');
        $defProp->setAccessible(true);
        $defProp->setValue($this->engine, 'de');

        $tgtProp = $ref->getProperty('targetLangs');
        $tgtProp->setAccessible(true);
        $tgtProp->setValue($this->engine, ['en']);
    }

    // ── Helper: call private method via reflection ────────────────────────────

    private function callApplyGlossary(string $text, string $src, string $tgt): array
    {
        $m = new \ReflectionMethod($this->engine, 'applyGlossary');
        $m->setAccessible(true);
        return $m->invoke($this->engine, $text, $src, $tgt);
    }

    private function callRestoreGlossary(string $text, array $map): string
    {
        $m = new \ReflectionMethod($this->engine, 'restoreGlossary');
        $m->setAccessible(true);
        return $m->invoke($this->engine, $text, $map);
    }

    // ── applyGlossary() ───────────────────────────────────────────────────────

    public function testApplyGlossary_returnsUnchanged_whenNoTerms(): void
    {
        $this->glossaryMock->method('getAll')->willReturn([]);

        [$result, $map] = $this->callApplyGlossary('Hello world', 'de', 'en');

        $this->assertSame('Hello world', $result);
        $this->assertSame([], $map);
    }

    public function testApplyGlossary_replacesTermInPlainText(): void
    {
        $this->glossaryMock->method('getAll')->willReturn([
            ['id' => 1, 'source_term' => 'VeloCMS', 'target_term' => 'VeloCMS'],
        ]);

        [$result, $map] = $this->callApplyGlossary('Welcome to VeloCMS today.', 'de', 'en');

        $this->assertStringContainsString('[[VCMS_TERM_1]]', $result);
        $this->assertSame('VeloCMS', $map['[[VCMS_TERM_1]]']);
    }

    public function testApplyGlossary_doesNotReplaceInsideHtmlAttribute(): void
    {
        $this->glossaryMock->method('getAll')->willReturn([
            ['id' => 2, 'source_term' => 'home', 'target_term' => 'Startseite'],
        ]);

        $html = '<a href="/home/page">Go to home</a>';
        [$result, $map] = $this->callApplyGlossary($html, 'de', 'en');

        // The href attribute must not be corrupted
        $this->assertStringContainsString('href="/home/page"', $result);
        // The text node "Go to home" should have the placeholder
        $this->assertStringContainsString('[[VCMS_TERM_2]]', $result);
    }

    public function testApplyGlossary_replacesInTextNodeButNotInTag(): void
    {
        $this->glossaryMock->method('getAll')->willReturn([
            ['id' => 3, 'source_term' => 'foo', 'target_term' => 'bar'],
        ]);

        $html = '<span class="foo-class">foo text</span>';
        [$result, $map] = $this->callApplyGlossary($html, 'de', 'en');

        // class attribute must be untouched
        $this->assertStringContainsString('class="foo-class"', $result);
        // text node contains placeholder
        $this->assertStringContainsString('[[VCMS_TERM_3]]', $result);
    }

    public function testApplyGlossary_usesTermIdAsPlaceholder(): void
    {
        $this->glossaryMock->method('getAll')->willReturn([
            ['id' => 99, 'source_term' => 'Alpha', 'target_term' => 'Alpha_EN'],
        ]);

        [$result, $map] = $this->callApplyGlossary('Alpha release', 'de', 'en');

        $this->assertArrayHasKey('[[VCMS_TERM_99]]', $map);
    }

    public function testApplyGlossary_doesNotReplacePartialWord(): void
    {
        $this->glossaryMock->method('getAll')->willReturn([
            ['id' => 4, 'source_term' => 'art', 'target_term' => 'art_EN'],
        ]);

        [$result, $map] = $this->callApplyGlossary('artist artwork art', 'de', 'en');

        // "artist" and "artwork" must NOT be replaced — only standalone "art"
        $this->assertStringNotContainsString('[[VCMS_TERM_4]]ist', $result);
        $this->assertStringNotContainsString('[[VCMS_TERM_4]]work', $result);
        $this->assertStringContainsString('[[VCMS_TERM_4]]', $result);
    }

    // ── restoreGlossary() ─────────────────────────────────────────────────────

    public function testRestoreGlossary_returnsUnchanged_whenMapEmpty(): void
    {
        $result = $this->callRestoreGlossary('Hello [[VCMS_TERM_1]]', []);
        $this->assertSame('Hello [[VCMS_TERM_1]]', $result);
    }

    public function testRestoreGlossary_replacesPlaceholdersWithTargetTerms(): void
    {
        $map = [
            '[[VCMS_TERM_1]]' => 'VeloCMS',
            '[[VCMS_TERM_2]]' => 'Startseite',
        ];
        $result = $this->callRestoreGlossary('Welcome to [[VCMS_TERM_1]]. Go to [[VCMS_TERM_2]].', $map);
        $this->assertSame('Welcome to VeloCMS. Go to Startseite.', $result);
    }

    public function testApplyAndRestoreGlossary_roundtrip(): void
    {
        $this->glossaryMock->method('getAll')->willReturn([
            ['id' => 7, 'source_term' => 'VeloCMS', 'target_term' => 'VeloCMS_EN'],
        ]);

        $original = 'Check out VeloCMS today.';
        [$withPlaceholders, $map] = $this->callApplyGlossary($original, 'de', 'en');
        $restored = $this->callRestoreGlossary($withPlaceholders, $map);

        $this->assertSame('Check out VeloCMS_EN today.', $restored);
    }
}
