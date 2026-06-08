<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // Only seed if no academic year exists yet
        if (AcademicYear::count() > 0) {
            $this->command->warn('Academic years already exist, skipping.');
            return;
        }

        AcademicYear::create([
            'name'       => '2025-26',
            'start_date' => '2025-06-01',
            'end_date'   => '2026-03-31',
            'is_current' => true,
        ]);

        // Previous year (for historical data)
        AcademicYear::create([
            'name'       => '2024-25',
            'start_date' => '2024-06-01',
            'end_date'   => '2025-03-31',
            'is_current' => false,
        ]);

        $this->command->info('✅ Academic years seeded. Current: 2025-26');
    }
}
