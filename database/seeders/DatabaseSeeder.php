<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();
        Employee::factory(100)->create();
        $this->call([
            OfficeSeeder::class,
        ]);
    }
}
