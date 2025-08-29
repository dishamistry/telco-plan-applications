<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Filters\QueryFilter;

class Application extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => ApplicationStatus::class,
    ];

    protected $fillable = [
        'status',
        'order_id'
    ];

    /**
     * Get the customer that owns the Application.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the plan that owns the Application.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the full address as a single formatted string.
     */
    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => collect([
                    $this->address_1,
                    $this->address_2,
                    $this->city,
                    $this->state,
                    $this->postcode,
                ])
                ->filter()
                ->implode(', ')
        );
    }

    /**
     * Scope a query to filter applications based on query parameters
     */
    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }
}
