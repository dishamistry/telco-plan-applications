<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    /**
     * Get the applications for the Customer
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
