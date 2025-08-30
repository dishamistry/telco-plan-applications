<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessNbnOrderJob;
use App\Models\Application;
use App\Models\Plan;
use App\Enums\ApplicationStatus;

class DispatchNbnOrdersTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function dispatch_nbn_order_jobs_for_order_status_applications()
    {
        Queue::fake();
        $plan = Plan::factory()->create(['type' => 'nbn']);
        $nbnApplication1 = Application::factory()->create(['status' => ApplicationStatus::Order, 'plan_id' => $plan->id]);
        $nbnApplication2 = Application::factory()->create(['status' => ApplicationStatus::Order, 'plan_id' => $plan->id]);

        $noneNbnApplication2 = Application::factory()->create(['status' => ApplicationStatus::Prelim, 'plan_id' => $plan->id]);

        $this->artisan('orders:dispatch-nbn-applications')
            ->expectsOutput('NBN applications dispatched to queue.')
            ->assertExitCode(0);

        Queue::assertPushed(ProcessNbnOrderJob::class, 2);
        Queue::assertPushed(ProcessNbnOrderJob::class, function ($job) use ($nbnApplication1) {
            return $job->application->id === $nbnApplication1->id;
        });
        Queue::assertPushed(ProcessNbnOrderJob::class, function ($job) use ($nbnApplication2) {
            return $job->application->id === $nbnApplication2->id;
        });
        Queue::assertNotPushed(ProcessNbnOrderJob::class, function ($job) use ($noneNbnApplication2) {
            return $job->application->id === $noneNbnApplication2->id;
        });
    }
}
