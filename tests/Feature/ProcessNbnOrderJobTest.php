<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Enums\ApplicationStatus;
use App\Jobs\ProcessNbnOrderJob;
use App\Models\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessNbnOrderJobTest extends TestCase
{
    protected array $successResponse;
    protected array $failResponse;

    public function setUp(): void
    {
        parent::setUp();
        $this->successResponse = $this->loadStub('successful-response.json');
        $this->failResponse = $this->loadStub('fail-response.json');
    }

    private function loadStub(string $filename): array
    {
        $path = base_path("tests/stubs/{$filename}");
        return json_decode(File::get($path), true);
    }

    /** @test */
    public function it_updates_status_to_complete_when_response_is_success()
    {
        $application = Application::factory()->create(['id' => 2, 'status' => ApplicationStatus::OrderFailed]);
        File::shouldReceive('get')->once()->andReturn(json_encode($this->successResponse));
        Http::fake([env('NBN_B2B_ENDPOINT') => Http::response($this->successResponse, 200)]);

        $job = new ProcessNbnOrderJob($application);
        $job->handle();
        $application->refresh();

        $this->assertEquals(ApplicationStatus::Complete, $application->status);
        $this->assertEquals($this->successResponse['id'], $application->order_id);
    }

    /** @test */
    public function it_updates_status_to_order_failed_when_response_is_failed()
    {
        $application = Application::factory()->create(['id' => 1, 'status' => ApplicationStatus::Complete]);
        File::shouldReceive('get')->once()->andReturn(json_encode($this->failResponse));
        Http::fake([env('NBN_B2B_ENDPOINT') => Http::response($this->failResponse, 200)]);

        $job = new ProcessNbnOrderJob($application);
        $job->handle();
        $application->refresh();

        $this->assertEquals(ApplicationStatus::OrderFailed, $application->status);
        $this->assertNull($application->order_id);
    }

    /** @test */
    public function it_updates_status_to_order_failed_when_exception_occurs()
    {
        $application = Application::factory()->create(['id' => 1, 'status' => ApplicationStatus::Complete]);
        File::shouldReceive('get')->once()->andThrow(new \Exception('File not found'));
        Log::shouldReceive('error')->once()->withArgs(function ($message, $context) {
            return $message === 'NBN order job failed' && isset($context['error']);
        });

        $job = new ProcessNbnOrderJob($application);
        $job->handle();
        $application->refresh();

        $this->assertEquals(ApplicationStatus::OrderFailed, $application->status);
    }
}
