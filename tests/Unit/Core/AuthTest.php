<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use VeloCMS\Core\Auth;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public function testCheck_returnsFalse_whenNotLoggedIn(): void
    {
        $this->assertFalse(Auth::check());
    }

    public function testCheck_returnsTrue_afterLogin(): void
    {
        Auth::login(['id' => 1, 'role' => 'admin', 'name' => 'Test User']);
        $this->assertTrue(Auth::check());
    }

    public function testId_returnsUserId_afterLogin(): void
    {
        Auth::login(['id' => 42, 'role' => 'editor', 'name' => 'Jane']);
        $this->assertSame(42, Auth::id());
    }

    public function testRole_returnsRole_afterLogin(): void
    {
        Auth::login(['id' => 1, 'role' => 'superadmin', 'name' => 'Boss']);
        $this->assertSame('superadmin', Auth::role());
    }

    public function testId_returnsNull_whenNotLoggedIn(): void
    {
        $this->assertNull(Auth::id());
    }

    public function testVerifyCsrf_throws_onMismatch(): void
    {
        $_SESSION['csrf_token'] = 'valid-token';
        $_POST['_csrf']         = 'wrong-token';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CSRF token mismatch');

        Auth::verifyCsrf();
    }

    public function testVerifyCsrf_passes_onValidToken(): void
    {
        $_SESSION['csrf_token'] = 'my-valid-csrf-token';
        $_POST['_csrf']         = 'my-valid-csrf-token';

        Auth::verifyCsrf();
        $this->assertTrue(true);
    }
}
