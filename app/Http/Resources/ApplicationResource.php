<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Enums\ApplicationStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             'id' => $this->id,
             'customer_full_name' => $this->customer?->first_name . ' ' . $this->customer?->last_name,
             'address' => $this->full_address,
             'plan_type' => $this->plan->type,
             'plan_name' => $this->plan->name,
             'state' => $this->state,
             'monthly_cost' => $this->plan->monthly_cost_in_dollars,
             'order_id' => $this->when($this->status === ApplicationStatus::Complete, $this->order_id)
         ];
    }
}
