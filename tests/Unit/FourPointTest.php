<?php

namespace Tests\Unit;

use App\Services\FourPoint;
use PHPUnit\Framework\TestCase;

class FourPointTest extends TestCase
{
    public function test_points_follow_the_four_point_scale(): void
    {
        $this->assertSame(1, FourPoint::points(3));
        $this->assertSame(2, FourPoint::points(6));
        $this->assertSame(3, FourPoint::points(9));
        $this->assertSame(4, FourPoint::points(9.5));
    }
}
