<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Contact;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Contact\Models\ContactModel;

class ContactModelTest extends TestCase
{
    private ContactModel $model;
    private MockObject $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->model  = new ContactModel($this->dbMock);
    }

    // ── countRecentByIp ───────────────────────────────────────────────────────

    public function testCountRecentByIp_returnsZero_whenNoMessages(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchColumn')->willReturn('0');
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertSame(0, $this->model->countRecentByIp('127.0.0.1'));
    }

    public function testCountRecentByIp_returnsCorrectCount(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchColumn')->willReturn('3');
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertSame(3, $this->model->countRecentByIp('192.168.1.1'));
    }

    // ── create ────────────────────────────────────────────────────────────────

    public function testCreate_returnsInsertedId(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);
        $this->dbMock->method('lastInsertId')->willReturn('7');

        $id = $this->model->create(
            'Max Mustermann',
            'max@example.com',
            'Anfrage',
            'Hallo, ich habe eine Frage.',
            '127.0.0.1',
            'Mozilla/5.0'
        );

        $this->assertSame(7, $id);
    }

    public function testCreate_truncatesLongUserAgent(): void
    {
        $longUa  = str_repeat('A', 600);
        $captured = null;

        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')
             ->willReturnCallback(function (array $params) use (&$captured): bool {
                 $captured = $params;
                 return true;
             });
        $this->dbMock->method('prepare')->willReturn($stmt);
        $this->dbMock->method('lastInsertId')->willReturn('1');

        $this->model->create('A', 'a@b.de', 'S', 'M', '1.2.3.4', $longUa);

        $this->assertLessThanOrEqual(500, mb_strlen($captured[':ua']));
    }

    // ── markRead ──────────────────────────────────────────────────────────────

    public function testMarkRead_executesUpdate(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([':id' => 5])
             ->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->model->markRead(5);
        $this->assertTrue(true);
    }

    // ── markSpam ──────────────────────────────────────────────────────────────

    public function testMarkSpam_executesUpdate(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([':id' => 3])
             ->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->model->markSpam(3);
        $this->assertTrue(true);
    }

    // ── countNew ──────────────────────────────────────────────────────────────

    public function testCountNew_returnsInteger(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('fetchColumn')->willReturn('4');
        $this->dbMock->method('query')->willReturn($stmt);

        $this->assertSame(4, $this->model->countNew());
    }

    // ── purgeOlderThan ────────────────────────────────────────────────────────

    public function testPurgeOlderThan_returnsRowCount(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(12);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertSame(12, $this->model->purgeOlderThan(90));
    }
}
