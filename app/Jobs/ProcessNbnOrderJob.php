<?php

namespace App\Jobs;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessNbnOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Application $application)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $payload = [
                'address_1' => $this->application->address_1,
                'address_2' => $this->application->address_2,
                'city' => $this->application->city,
                'state' => $this->application->state,
                'postcode' => $this->application->postcode,
                'plan_name' => $this->application->plan->name,
            ];
            $fakeResponse = $this->getFakeB2BResponse();
            Http::fake([env('NBN_ENDPOINT') => Http::response($fakeResponse, 200)]);
            Http::post(env('NBN_ENDPOINT'), $payload)->json();

            if (filled(trim($fakeResponse['id'] ?? ''))) {
                $this->application->update([
                    'order_id' => $fakeResponse['id'],
                    'status' => ApplicationStatus::Complete
                ]);
            } else {
                $this->application->update(['status' => ApplicationStatus::OrderFailed]);
            }
        } catch (\Exception $e) {
            Log::error('NBN order job failed', ['error' => $e->getMessage()]);
            $this->application->update(['status' => ApplicationStatus::OrderFailed]);
        }
    }

    private function getFakeB2BResponse(): array
    {
        $filename = $this->application->id % 2 === 0 ? 'nbn-successful-response.json' : 'nbn-fail-response.json';
        $path = base_path("tests/stubs/{$filename}");
        return json_decode(File::get($path), true);
    }
}
