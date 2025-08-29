<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Application;
use App\Models\Plan;
use App\Enums\ApplicationStatus;
use App\Enums\PlanType;

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
        $response = $this->getJson(route('applications.index'));
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'links', 'path', 'per_page', 'to', 'total'],
        ]);
    }

    /** @test */
    public function applications_are_sorted_by_oldest_first()
    {
        $oldestApplication = Application::factory()->create(['created_at' => now()->subDays(2)]);
        $middleApplication = Application::factory()->create(['created_at' => now()->subDay()]);
        $newestApplication = Application::factory()->create(['created_at' => now()]);

        $response = $this->getJson(route('applications.index', [
            'sort_by' => 'created_at',
            'sort_order' => 'desc',
        ]));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $responseIds = collect($response->json('data'))->pluck('id');
        $this->assertEquals($newestApplication->id, $responseIds->first());
        $this->assertEquals($oldestApplication->id, $responseIds->last());
    }

    /** @test */
    public function applications_can_be_filtered_by_plan_type()
    {
        $nbnPlan = Plan::factory()->create(['type' => 'nbn']);
        $mobilePlan = Plan::factory()->create(['type' => 'mobile']);

        Application::factory()->count(3)->create(['plan_id' => $nbnPlan->id]);
        Application::factory()->count(2)->create(['plan_id' => $mobilePlan->id]);

        $response = $this->getJson(route('applications.index', [
            'plan_type' => 'mobile'
        ]));
        $response->assertOk();
        $response->assertJsonCount(2, 'data');

        collect($response->json('data'))->each(function ($app) {
            $this->assertEquals('mobile', $app['plan_type']);
        });
    }

    /** @test */
    public function api_returns_only_limited_fields_for_each_application_without_order_id()
    {
        Application::factory(15)->create();

        $response = $this->getJson(route('applications.index'));

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
        $response->assertJsonMissing(['order_id' => true]);
    }

    /** @test */
    public function order_id_is_visible_only_for_completed_applications_with_pagination_and_structure()
    {
        Application::factory()->count(15)->create(['status' => ApplicationStatus::Complete, 'order_id' => 'ORD000000000000',]);
        $response = $this->getJson(route('applications.index'));
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
        $this->assertNotNull($response->json('data')[0]['order_id']);
    }

    /** @test */
    public function it_validates_invalid_plan_type()
    {
        $response = $this->getJson(route('applications.index', [
            'plan_type' => 'invalid',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['plan_type']);        
        $response->assertJsonFragment([
            'plan_type' => ['The plan type must be one of: ' . implode(', ', PlanType::values())]
        ]);
    }

    /** @test */
    public function it_validates_invalid_per_page()
    {
        $response = $this->getJson(route('applications.index', [
            'per_page' => 200
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_validates_invalid_sort_by()
    {
        $response = $this->getJson(route('applications.index', [
            'sort_by' => 'name'
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sort_by']);
    }

    /** @test */
    public function it_validates_invalid_sort_order()
    {
        $response = $this->getJson(route('applications.index', [
            'sort_order' => 'ascending',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sort_order']);
    }
}