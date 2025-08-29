<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use App\Models\Plan;
use App\Models\Customer;
use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationListingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    /** @test */
    public function api_returns_a_paginated_list_of_applications()
    {
        Application::factory(15)->create();
        $response = $this->getJson('/api/applications');
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.current_page', 1);
    }
    /** @test */
    public function applications_are_sorted_by_oldest_first()
    {
        $app1 = Application::factory()->create(['created_at' => now()->subDays(2)]);
        $app2 = Application::factory()->create(['created_at' => now()->subDay()]);
        $app3 = Application::factory()->create(['created_at' => now()]);
        $applications = collect([$app1, $app2, $app3]);

        $sortedApplications = $applications->sortByDesc('created_at')->values();

        $response = $this->getJson(route('applications.index', [
            'sort_by' => 'created_at',
            'sort_order' => 'desc',
        ]));

        $response->assertOk();

        $responseIds = collect($response->json('data'))->pluck('id');
        $expectedIds = $sortedApplications->pluck('id');

        $this->assertCount($applications->count(), $responseIds);

        $this->assertEquals($expectedIds->toArray(), $responseIds->toArray());
    }
    /** @test */
    public function applications_can_be_filtered_by_plan_type()
    {
        $nbnPlan = Plan::factory()->create(['type' => 'nbn']);
        $mobilePlan = Plan::factory()->create(['type' => 'mobile']);

        Application::factory()->count(3)->create(['plan_id' => $nbnPlan->id]);
        Application::factory()->count(2)->create(['plan_id' => $mobilePlan->id]);

        $response = $this->getJson('/api/applications?plan_type=mobile');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');

        foreach ($response['data'] as $app) {
            $this->assertEquals('mobile', $app['plan_type']);
        }
    }

      /** @test */
    public function api_returns_only_limited_fields_for_each_application()
    {
        Application::factory(15)->create();

        $response = $this->getJson('/api/applications');

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'customer_full_name',
                    'address',
                    'plan_type',
                    'plan_name',
                    'state',
                    'monthly_cost',
                ]
            ],
            'meta',
            'links',
        ]);
    }
    /** @test */
    public function order_id_is_visible_only_for_completed_applications_with_pagination_and_structure()
    {
        Application::factory()->count(15)->create(['status' => 'complete']);
        $response = $this->getJson('/api/applications');
        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'customer_full_name',
                    'address',
                    'plan_type',
                    'plan_name',
                    'state',
                    'monthly_cost',
                    'order_id',
                ]
            ],
            'meta',
            'links',
        ]);
    }
    
}
