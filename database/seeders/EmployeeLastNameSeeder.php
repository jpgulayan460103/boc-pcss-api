<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeLastNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = Employee::get();
        foreach ($employees as $key => $employee) {
            $employee->full_name = trim($employee->last_name.", ".$employee->first_name." ".$employee->middle_name);
            $employee->save();
        }
    }
}
