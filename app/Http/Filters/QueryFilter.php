<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{

    protected $builder;

    public function __construct(protected Request $request) {}

    /**
     * List of allowed filters
     */
    protected array $filters = [];

    /**
     * Applies filters by matching request parameters to methods in the filter class and updating the query.
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->only($this->filters) as $filterName => $filterValue) {
            if (method_exists($this, $filterName)) {
                $this->$filterName($filterValue);
            }
        }

        return $builder;
    }
}
