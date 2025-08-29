<?php

namespace App\Console\Commands;

use App\Jobs\ProcessNbnOrderJob;
use App\Models\Application;
use Illuminate\Console\Command;

class DispatchNbnOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:dispatch-nbn-applications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch NBN applications with order status for ordering every 5 minutes';

    /**
     * Execute the console command.
     * Fetch all applications with status 'order' and associated plan type 'nbn',
     * then dispatch a job to process each application.
     */
    public function handle()
    {
        $applications = Application::where('status', 'order')
            ->whereHas('plan', function ($query) {
                $query->where('type', 'nbn');
            })->get();

        foreach ($applications as $application) {
            ProcessNbnOrderJob::dispatch($application);
        }

        $this->info('NBN applications dispatched to queue.');
    }
}
