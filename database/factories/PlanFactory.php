<?php

namespace Database\Factories;

use App\Enums\PlanType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = $this->faker->randomElement(PlanType::values());
        $plans = ['Basic', 'Standard', 'Premium', 'Unlimited', 'Family', 'Business', 'Starter', 'Pro', 'Ultra'];
        $plan = ucfirst($type) . ' ' . $this->faker->randomElement($plans) . ' Plan';

        return [
            'name' => $plan,
            'type' => $type,
            'monthly_cost' => $this->faker->numerify('####'),
        ];
    }
}
