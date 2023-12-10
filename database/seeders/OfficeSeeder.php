<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $offices = [
            'District Collector',
            'Administrative Division',
            'Assessment Division',
            'Air Express Cargo Division ',
            'Bonds Unit',
            'Collection Division',
            'Operations Division',
            'Customs Clearance Area',
            'Aircraft Operations',
            'Export Unit',
            'Passenger Service',
            'Office for Strategy Management',
            'Customer Care Center',
            'ESS',
            'CIIS',
            'XIP',
            'MISTG',
        ];

        foreach ($offices as $office) {
            Office::create([
                'name' => $office
            ]);
        }
    }
}
