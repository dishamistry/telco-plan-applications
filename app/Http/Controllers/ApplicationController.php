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

        $paginatedApplications = Application::with(['customer', 'plan'])
            ->filter($filters)
        ->paginate($perPage);

        return ApplicationResource::collection($paginatedApplications);
    }
}
