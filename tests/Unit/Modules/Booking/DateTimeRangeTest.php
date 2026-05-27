<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Modules\Booking;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use VeloCMS\Modules\Booking\Core\ValueObjects\DateTimeRange;

class DateTimeRangeTest extends TestCase
{
    public function testFromStrings_createsRange(): void
    {
        $range = DateTimeRange::fromStrings('2026-06-01 09:00:00', '2026-06-01 10:00:00', 'UTC');
        $this->assertSame('2026-06-01 09:00:00', $range->startUtc());
        $this->assertSame('2026-06-01 10:00:00', $range->endUtc());
    }

    public function testFromStrings_throwsWhenEndBeforeStart(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DateTimeRange::fromStrings('2026-06-01 10:00:00', '2026-06-01 09:00:00', 'UTC');
    }

    public function testFromStrings_throwsWhenEndEqualsStart(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DateTimeRange::fromStrings('2026-06-01 09:00:00', '2026-06-01 09:00:00', 'UTC');
    }

    public function testDurationMinutes_returnsCorrectValue(): void
    {
        $range = DateTimeRange::fromStrings('2026-06-01 09:00:00', '2026-06-01 10:30:00', 'UTC');
        $this->assertSame(90, $range->durationMinutes());
    }

    public function testOverlaps_returnsTrueForOverlappingRanges(): void
    {
        $a = DateTimeRange::fromStrings('2026-06-01 09:00:00', '2026-06-01 11:00:00', 'UTC');
        $b = DateTimeRange::fromStrings('2026-06-01 10:00:00', '2026-06-01 12:00:00', 'UTC');
        $this->assertTrue($a->overlaps($b));
        $this->assertTrue($b->overlaps($a));
    }

    public function testOverlaps_returnsFalseForAdjacentRanges(): void
    {
        $a = DateTimeRange::fromStrings('2026-06-01 09:00:00', '2026-06-01 10:00:00', 'UTC');
        $b = DateTimeRange::fromStrings('2026-06-01 10:00:00', '2026-06-01 11:00:00', 'UTC');
        $this->assertFalse($a->overlaps($b));
        $this->assertFalse($b->overlaps($a));
    }

    public function testOverlaps_returnsFalseForNonOverlappingRanges(): void
    {
        $a = DateTimeRange::fromStrings('2026-06-01 09:00:00', '2026-06-01 10:00:00', 'UTC');
        $b = DateTimeRange::fromStrings('2026-06-01 11:00:00', '2026-06-01 12:00:00', 'UTC');
        $this->assertFalse($a->overlaps($b));
    }

    public function testOverlaps_returnsTrueWhenContained(): void
    {
        $outer = DateTimeRange::fromStrings('2026-06-01 08:00:00', '2026-06-01 18:00:00', 'UTC');
        $inner = DateTimeRange::fromStrings('2026-06-01 10:00:00', '2026-06-01 11:00:00', 'UTC');
        $this->assertTrue($outer->overlaps($inner));
        $this->assertTrue($inner->overlaps($outer));
    }

    public function testStartUtc_returnsUtcString(): void
    {
        $range = DateTimeRange::fromStrings('2026-06-01 09:00:00', '2026-06-01 10:00:00', 'Europe/Berlin');
        // Berlin is UTC+2 in summer — so 09:00 Berlin = 07:00 UTC
        $this->assertSame('2026-06-01 07:00:00', $range->startUtc());
    }
}
