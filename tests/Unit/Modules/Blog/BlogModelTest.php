<?php
declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Blog;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Blog\Models\BlogModel;

class BlogModelTest extends TestCase
{
    private BlogModel $model;
    private MockObject $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->model  = new BlogModel($this->dbMock);
    }

    public function testGetBySlug_returnsNull_whenNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertNull($this->model->getBySlug('does-not-exist'));
    }

    public function testGetBySlug_returnsPost_whenFound(): void
    {
        $expected = ['id' => 1, 'title' => 'Hello', 'slug' => 'hello', 'status' => 'published'];
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($expected);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertSame($expected, $this->model->getBySlug('hello'));
    }

    public function testInsert_returnsNewId(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);
        $this->dbMock->method('lastInsertId')->willReturn('42');

        $id = $this->model->insert([
            'title' => 'Test Post', 'slug' => 'test-post',
            'status' => 'draft', 'author_id' => 1,
        ]);
        $this->assertSame(42, $id);
    }

    public function testGetById_returnsNull_whenNotFound(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->assertNull($this->model->getById(999));
    }

    public function testDelete_executesDeleteQuery(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([':id' => 7])
             ->willReturn(true);
        $this->dbMock->method('prepare')->willReturn($stmt);

        $this->model->delete(7);
        $this->assertTrue(true); // Reached = no exception
    }

    public function testCount_returnsInteger(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('fetchColumn')->willReturn('5');
        $this->dbMock->method('query')->willReturn($stmt);

        $this->assertSame(5, $this->model->count());
    }
}
