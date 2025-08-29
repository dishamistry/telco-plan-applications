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
             'customer_full_name' => $this->customer->first_name . ' ' . $this->customer->last_name,
             'address' => $this->address_1 . ($this->address_2 ? ', ' . $this->address_2 : '') . ', ' . $this->city . ', ' . $this->state . ' ' . $this->postcode,
             'plan_type' => $this->plan->type,
             'plan_name' => $this->plan->name,
             'state' => $this->state,
             'monthly_cost' => number_format($this->plan->monthly_cost / 100, 2),
             'order_id' => $this->when($this->status === ApplicationStatus::Complete, $this->order_id)
         ];
    }
}
