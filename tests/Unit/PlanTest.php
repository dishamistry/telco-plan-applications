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
    /** @test */
    public function plan_monthly_cost_is_formatted_to_dollars()
    {
        $plan = new Plan(['monthly_cost' => 1234]);

        $this->assertEquals('12.34', $plan->monthly_cost_in_dollars);
    }
}
