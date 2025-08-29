<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{

    protected $builder;

    public function __construct(protected Request $request) {}

    /**
     * Applies filters by matching request parameters to methods in the filter class and updating the query.
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $builder;
    }
}
