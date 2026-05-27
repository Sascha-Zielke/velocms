<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Translation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Translation\Models\TranslationModel;

class TranslationModelTest extends TestCase
{
    private TranslationModel $model;
    private MockObject $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->model  = new TranslationModel($this->dbMock);
    }

    // ── get() ─────────────────────────────────────────────────────────────────

    public function testGet_returnsNull_whenNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertNull($this->model->get('velocms_blog_posts', 1, 'title', 'en'));
    }

    public function testGet_returnsRow_whenFound(): void
    {
        $expected = ['id' => 5, 'table_name' => 'velocms_blog_posts', 'language' => 'en', 'value' => 'Hello'];
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($expected);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertSame($expected, $this->model->get('velocms_blog_posts', 1, 'title', 'en'));
    }

    // ── upsert() ──────────────────────────────────────────────────────────────

    public function testUpsert_executesWithAllParams(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([
                 ':t' => 'velocms_blog_posts',
                 ':r' => 42,
                 ':f' => 'title',
                 ':l' => 'en',
                 ':v' => 'Hello',
                 ':s' => 'auto',
                 ':h' => md5('Hello'),
             ])
             ->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->model->upsert('velocms_blog_posts', 42, 'title', 'en', 'Hello', 'auto', md5('Hello'));
        $this->assertTrue(true);
    }

    // ── getForRow() ───────────────────────────────────────────────────────────

    public function testGetForRow_returnsKeyValuePairs(): void
    {
        $expected = ['title' => 'Hello', 'content' => 'World'];
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->with(\PDO::FETCH_KEY_PAIR)->willReturn($expected);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertSame($expected, $this->model->getForRow('velocms_blog_posts', 1, 'en'));
    }

    // ── updateManual() ────────────────────────────────────────────────────────

    public function testUpdateManual_executesUpdate(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([':v' => 'Updated text', ':id' => 7])
             ->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->model->updateManual(7, 'Updated text');
        $this->assertTrue(true);
    }

    // ── unlock() ──────────────────────────────────────────────────────────────

    public function testUnlock_executesUpdate(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([':id' => 3])
             ->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->model->unlock(3);
        $this->assertTrue(true);
    }

    // ── getById() ─────────────────────────────────────────────────────────────

    public function testGetById_returnsNull_whenNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertNull($this->model->getById(999));
    }
}
