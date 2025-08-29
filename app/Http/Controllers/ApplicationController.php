<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Http\Resources\ApplicationResource;
use App\Http\Requests\ListApplicationsRequest;

class ApplicationController extends Controller
{
    public function index(ListApplicationsRequest $request)
    {
        $planType = $request->query('plan_type', null);
        $perPage = $request->query('per_page', 10);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'asc');

        $paginatedApplications = Application::with(['customer', 'plan'])
        ->when(
            $request->has('plan_type'),
            fn ($applications) =>
            $applications->whereHas(
                'plan',
                fn ($plan) =>
                $plan->where('type', $planType)
            )
        )
        ->orderBy($sortBy, $sortOrder)
        ->paginate($perPage);

        return ApplicationResource::collection($paginatedApplications);
    }
}
