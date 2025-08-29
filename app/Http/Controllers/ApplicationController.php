<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Http\Resources\ApplicationResource;
use App\Http\Requests\ListApplicationsRequest;
use App\Http\Filters\ApplicationFilter;

class ApplicationController extends Controller
{
    public function index(ListApplicationsRequest $request, ApplicationFilter $filters)
    {
        $perPage = $request->query('per_page', 10);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'asc');

        $paginatedApplications = Application::with(['customer', 'plan'])
            ->filter($filters)
        ->orderBy($sortBy, $sortOrder)
        ->paginate($perPage);

        return ApplicationResource::collection($paginatedApplications);
    }
}
