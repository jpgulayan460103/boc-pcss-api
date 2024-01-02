<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $positions = [
            'Acting Appraiser',
            'Acting Examiner',
            'Appraiser',
            'Cashier',
            'Collecting Officer',
            'Document Processor',
            'Examiner',
            'Flight Supervisor',
            'Lane Examiner',
            'Warehouseman',
            'Warehouseman III',
        ];

        foreach ($positions as $position) {
            Position::create([
                'name' => $position,
            ]);
        }
    }
}
