<?php

namespace Tests\Unit;

use App\Models\Plan;
use PHPUnit\Framework\TestCase;

class PlanTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function it_converts_monthly_cost_from_cents_to_dollars()
    {
        $plan = new Plan(['monthly_cost' => 1234]); // 12345 cents = 123.45 dollars

        $this->assertEquals('12.34', $plan->monthly_cost_in_dollars);
    }
}
