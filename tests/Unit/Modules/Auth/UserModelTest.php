<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Auth;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Auth\Models\UserModel;

class UserModelTest extends TestCase
{
    private UserModel $model;
    private MockObject $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->model  = new UserModel($this->dbMock);
    }

    public function testGetByEmail_returnsUser_whenFound(): void
    {
        $expected = ['id' => 1, 'email' => 'user@example.test', 'active' => 1];

        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn($expected);
        $this->dbMock->method('prepare')->willReturn($stmtMock);

        $result = $this->model->getByEmail('user@example.test');

        $this->assertSame($expected, $result);
    }

    public function testGetByEmail_returnsNull_whenNotFound(): void
    {
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false);
        $this->dbMock->method('prepare')->willReturn($stmtMock);

        $result = $this->model->getByEmail('nobody@example.test');

        $this->assertNull($result);
    }

    public function testCreate_returnsNewId_andHashesPassword(): void
    {
        $capturedParams = [];

        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturnCallback(
            function (array $params) use (&$capturedParams): bool {
                $capturedParams = $params;
                return true;
            }
        );
        $this->dbMock->method('prepare')->willReturn($stmtMock);
        $this->dbMock->method('lastInsertId')->willReturn('7');

        $id = $this->model->create([
            'name'     => 'Test User',
            'email'    => 'create@example.test',
            'password' => 'test-plain-password',
            'role'     => 'editor',
        ]);

        $this->assertSame(7, $id);
        $this->assertArrayHasKey(':password_hash', $capturedParams);
        $this->assertNotSame('test-plain-password', $capturedParams[':password_hash']);
        $this->assertTrue(password_verify('test-plain-password', $capturedParams[':password_hash']));
    }

    public function testCreate_throws_whenPasswordEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password is required');

        $this->model->create([
            'name'     => 'Test User',
            'email'    => 'empty-pw@example.test',
            'password' => '',
        ]);
    }

    public function testCreate_throws_whenRoleInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid role');

        $this->model->create([
            'name'     => 'Test User',
            'email'    => 'bad-role@example.test',
            'password' => 'test-pw',
            'role'     => 'god',
        ]);
    }

    public function testCreate_usesDefaultRole_whenRoleOmitted(): void
    {
        $capturedParams = [];

        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturnCallback(
            function (array $params) use (&$capturedParams): bool {
                $capturedParams = $params;
                return true;
            }
        );
        $this->dbMock->method('prepare')->willReturn($stmtMock);
        $this->dbMock->method('lastInsertId')->willReturn('2');

        $this->model->create([
            'name'     => 'No Role User',
            'email'    => 'norole@example.test',
            'password' => 'test-pw',
        ]);

        $this->assertSame('editor', $capturedParams[':role']);
    }
}
