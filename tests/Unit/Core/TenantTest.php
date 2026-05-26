<?php
declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use VeloCMS\Core\Tenant;

class TenantTest extends TestCase
{
    public function testDomain_returnsEmpty_initially(): void
    {
        $this->assertSame('', Tenant::domain());
    }

    public function testId_returnsZero_initially(): void
    {
        $this->assertSame(0, Tenant::id());
    }

    public function testCurrent_returnsNull_initially(): void
    {
        $this->assertNull(Tenant::current());
    }

    public function testDbName_returnsEmpty_initially(): void
    {
        $this->assertSame('', Tenant::dbName());
    }

    public function testIsMultiSite_returnsFalse_initially(): void
    {
        $this->assertFalse(Tenant::isMultiSite());
    }
}
