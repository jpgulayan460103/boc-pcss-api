<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->name,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'position' => $this->faker->jobTitle,
            'full_name' => $this->faker->name,
            'is_overtimer' => $this->faker->boolean,
        ];
    }

    protected $casts = [
        'is_overtimer' => 'boolean',
    ];
}
