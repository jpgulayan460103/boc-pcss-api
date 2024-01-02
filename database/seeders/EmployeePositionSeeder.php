<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;

class EmployeePositionSeeder extends Seeder
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
            $position = Position::whereName($employee->position)->first();

            if($position){
                $employee->position_id = $position->id;
                $employee->save();
            }
        }
    }
}
