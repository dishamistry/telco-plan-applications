<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    /**
     * Get the applications for the Plan
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
