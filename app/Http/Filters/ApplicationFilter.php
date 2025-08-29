<?php

namespace App\Http\Filters;

class ApplicationFilter extends QueryFilter
{
    public function plan_type($planType)
    {
        return $this->builder->whereHas('plan', function ($applications) use ($planType) {
            $applications->where('type', $planType);
        });
    }
}
