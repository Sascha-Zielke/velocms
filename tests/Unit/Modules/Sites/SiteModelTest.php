<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Sites;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Sites\Models\SiteModel;

class SiteModelTest extends TestCase
{
    private SiteModel $model;
    private MockObject $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->model  = new SiteModel($this->dbMock);
    }

    // ── isValidDbName ─────────────────────────────────────────────────────────

    public function testIsValidDbName_returnsTrue_forValidName(): void
    {
        $this->assertTrue(SiteModel::isValidDbName('velocms_site_a'));
        $this->assertTrue(SiteModel::isValidDbName('mysite123'));
        $this->assertTrue(SiteModel::isValidDbName('a'));
    }

    public function testIsValidDbName_returnsFalse_forInvalidName(): void
    {
        $this->assertFalse(SiteModel::isValidDbName(''));
        $this->assertFalse(SiteModel::isValidDbName('site-name'));        // hyphen not allowed
        $this->assertFalse(SiteModel::isValidDbName('site name'));        // space not allowed
        $this->assertFalse(SiteModel::isValidDbName(str_repeat('a', 65))); // too long
        $this->assertFalse(SiteModel::isValidDbName('../../etc/passwd')); // path traversal
    }

    // ── create ────────────────────────────────────────────────────────────────

    public function testCreate_returnsInsertedId(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);
        $this->dbMock->method('lastInsertId')->willReturn('3');

        $id = $this->model->create([
            'domain'    => 'example.com',
            'www_alias' => 'www.example.com',
            'name'      => 'Example Site',
            'db_name'   => 'velocms_example',
            'status'    => 'provisioning',
        ]);

        $this->assertSame(3, $id);
    }

    public function testCreate_throwsOnZeroLastInsertId(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);
        $this->dbMock->method('lastInsertId')->willReturn('0');

        $this->expectException(\RuntimeException::class);
        $this->model->create([
            'domain'    => 'example.com',
            'www_alias' => '',
            'name'      => 'Test',
            'db_name'   => 'testdb',
            'status'    => 'provisioning',
        ]);
    }

    // ── domainExists ──────────────────────────────────────────────────────────

    public function testDomainExists_returnsTrue_whenFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(['id' => 1]);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertTrue($this->model->domainExists('example.com'));
    }

    public function testDomainExists_returnsFalse_whenNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertFalse($this->model->domainExists('unknown.com'));
    }

    // ── softDelete ────────────────────────────────────────────────────────────

    public function testSoftDelete_executesUpdate(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([':id' => 5])
            ->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->model->softDelete(5);
        $this->assertTrue(true);
    }

    // ── markActive ────────────────────────────────────────────────────────────

    public function testMarkActive_executesUpdate(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([':id' => 2])
            ->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->model->markActive(2);
        $this->assertTrue(true);
    }
}
