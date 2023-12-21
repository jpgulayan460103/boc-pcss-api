<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Office;
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
            'office_id' => $this->faker->randomElement(Office::all()->pluck('id')),
        ];
    }

    protected $casts = [
        'is_overtimer' => 'boolean',
    ];
}
