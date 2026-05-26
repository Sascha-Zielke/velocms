<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Auth;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Auth\Models\PasswordResetModel;

class PasswordResetModelTest extends TestCase
{
    private PasswordResetModel $model;
    private MockObject $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->model  = new PasswordResetModel($this->dbMock);
    }

    // ── createToken ──────────────────────────────────────────────────────────

    public function testCreateToken_returnsRawToken_of128HexChars(): void
    {
        $delStmt    = $this->createMock(\PDOStatement::class);
        $insertStmt = $this->createMock(\PDOStatement::class);

        $delStmt->method('execute')->willReturn(true);
        $insertStmt->method('execute')->willReturn(true);

        $this->dbMock
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($delStmt, $insertStmt);

        $raw = $this->model->createToken(1);

        $this->assertSame(128, strlen($raw));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{128}$/', $raw);
    }

    public function testCreateToken_storesHashNotRawToken(): void
    {
        $delStmt    = $this->createMock(\PDOStatement::class);
        $insertStmt = $this->createMock(\PDOStatement::class);

        $delStmt->method('execute')->willReturn(true);

        $capturedParams = null;
        $insertStmt->method('execute')
            ->willReturnCallback(function (array $params) use (&$capturedParams): bool {
                $capturedParams = $params;
                return true;
            });

        $this->dbMock
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($delStmt, $insertStmt);

        $raw = $this->model->createToken(42);

        $expectedHash = hash('sha256', $raw);
        $this->assertSame($expectedHash, $capturedParams[':token_hash']);
        // Raw token must NOT be stored
        $this->assertNotSame($raw, $capturedParams[':token_hash']);
    }

    public function testCreateToken_deletesExistingTokensFirst(): void
    {
        $delStmt    = $this->createMock(\PDOStatement::class);
        $insertStmt = $this->createMock(\PDOStatement::class);

        $delStmt->expects($this->once())
            ->method('execute')
            ->with([':uid' => 5])
            ->willReturn(true);

        $insertStmt->method('execute')->willReturn(true);

        $this->dbMock
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($delStmt, $insertStmt);

        $this->model->createToken(5);
        $this->assertTrue(true); // assertion is the expectation above
    }

    // ── findValidToken ────────────────────────────────────────────────────────

    public function testFindValidToken_returnsRow_whenTokenValid(): void
    {
        $row  = ['id' => 1, 'user_id' => 2, 'token_hash' => 'x', 'expires_at' => '2099-01-01 00:00:00', 'used_at' => null];
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($row);

        $this->dbMock->method('prepare')->willReturn($stmt);

        $result = $this->model->findValidToken('somerawtoken');
        $this->assertSame($row, $result);
    }

    public function testFindValidToken_returnsNull_whenTokenNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);

        $this->dbMock->method('prepare')->willReturn($stmt);

        $result = $this->model->findValidToken('invalidtoken');
        $this->assertNull($result);
    }

    // ── markUsed ──────────────────────────────────────────────────────────────

    public function testMarkUsed_executesUpdateWithId(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([':id' => 7])
            ->willReturn(true);

        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->model->markUsed(7);
        $this->assertTrue(true);
    }

    // ── purgeExpired ──────────────────────────────────────────────────────────

    public function testPurgeExpired_returnsRowCount(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(3);

        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertSame(3, $this->model->purgeExpired());
    }

    public function testPurgeExpired_returnsZero_whenNothingDeleted(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(0);

        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertSame(0, $this->model->purgeExpired());
    }
}
