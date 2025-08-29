<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_cost'
    ];

    /**
     * Get the applications for the Plan
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get the monthly cost formatted in dollars.
     */
    protected function monthlyCostInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->monthly_cost / 100, 2)
        );
    }
}
