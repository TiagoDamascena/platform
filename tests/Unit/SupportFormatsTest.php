<?php

declare(strict_types=1);

namespace Orchid\Tests\Unit;

use Orchid\Support\Formats;
use Orchid\Tests\TestUnitCase;

class SupportFormatsTest extends TestUnitCase
{
    public function testUnixDateToTimeString(): void
    {
        $this->assertEquals(Formats::toDateTimeString(1525735820), '2018-05-07 23:30:20');
    }

    public function testFormatBytes(): void
    {
        $this->assertEquals(Formats::formatBytes(0), 0);
        $this->assertEquals(Formats::formatBytes(1000), '1000 bytes');
        $this->assertEquals(Formats::formatBytes(2000), '1.95 KB');
        $this->assertEquals(Formats::formatBytes(10000000), '9.54 MB');
        $this->assertEquals(Formats::formatBytes(1000000000000), '931.32 GB');
        $this->assertEquals(Formats::formatBytes(10000000000000), '9.09 TB');
    }
}
