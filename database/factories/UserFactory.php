<?php

namespace Database\Factories;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->userName,
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'role' => $this->faker->randomElement(['user', 'admin']),
            'first_name' => $this->faker->name,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
            'position' => $this->faker->jobTitle,
            'full_name' => $this->faker->name,
            'office_id' => $this->faker->randomElement(Office::all()->pluck('id')),
        ];
    }
}
