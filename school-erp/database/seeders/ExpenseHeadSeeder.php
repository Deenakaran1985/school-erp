<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ExpenseHead;

class ExpenseHeadSeeder extends Seeder
{
    public function run(): void
    {
        $heads = [
            ['name' => 'Staff Salary',        'code' => 'SALARY'],
            ['name' => 'Stationery',          'code' => 'STAT'],
            ['name' => 'Electricity Bill',    'code' => 'ELEC'],
            ['name' => 'Water Bill',          'code' => 'WATER'],
            ['name' => 'Vehicle Fuel',        'code' => 'FUEL'],
            ['name' => 'Vehicle Maintenance', 'code' => 'VMAINT'],
            ['name' => 'Building Maintenance','code' => 'BMAINT'],
            ['name' => 'Laboratory',          'code' => 'LAB'],
            ['name' => 'Library Books',       'code' => 'LIB'],
            ['name' => 'Sports & Games',     'code' => 'SPORT'],
            ['name' => 'Event / Function',    'code' => 'EVENT'],
            ['name' => 'Computer / IT',       'code' => 'IT'],
            ['name' => 'Postage / Courier',   'code' => 'POST'],
            ['name' => 'Miscellaneous',       'code' => 'MISC'],
        ];

        foreach ($heads as $head) {
            ExpenseHead::firstOrCreate(['code' => $head['code']], array_merge($head, ['is_active' => true]));
        }

        $this->command->info('✅ 14 expense heads seeded.');
    }
}