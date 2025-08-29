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
    protected array $sortable = [];

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
        $this->sort();

        return $builder;
    }

    protected function sort()
    {
        $defaultSortBy = 'created_at';
        $defaultSortOrder = 'asc';

        $sortBy = $this->request->query('sort_by', $defaultSortBy);
        $sortOrder = strtolower($this->request->query('sort_order', $defaultSortOrder));

        $sortBy = in_array($sortBy, $this->sortable, true) ? $sortBy : $defaultSortBy;
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true) ? $sortOrder : $defaultSortOrder;

        return $this->builder->orderBy($sortBy, $sortOrder);
    }
}
