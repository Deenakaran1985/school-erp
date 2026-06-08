<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\AcademicYear;
use App\Models\GradeConfig;

class GradeConfigSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::current();

        if (GradeConfig::where('academic_year_id', $year->id)->exists()) {
            $this->command->warn('Grade config already exists for current year.');
            return;
        }

        $grades = [
            // [min%, max%, grade, grade_point, description]
            [91.00, 100.00, 'A+', 10.00, 'Outstanding'],
            [81.00,  90.99, 'A',  9.00,  'Excellent'],
            [71.00,  80.99, 'B+', 8.00,  'Very Good'],
            [61.00,  70.99, 'B',  7.00,  'Good'],
            [51.00,  60.99, 'C+', 6.00,  'Above Average'],
            [41.00,  50.99, 'C',  5.00,  'Average'],
            [35.00,  40.99, 'D',  4.00,  'Pass'],
            [ 0.00,  34.99, 'F',  0.00,  'Fail'],
        ];

        foreach ($grades as [$min, $max, $grade, $gp, $desc]) {
            GradeConfig::create([
                'academic_year_id' => $year->id,
                'min_percent'      => $min,
                'max_percent'      => $max,
                'grade'            => $grade,
                'grade_point'      => $gp,
                'description'      => $desc,
            ]);
        }

        $this->command->info('✅ Grade config seeded for ' . $year->name);
    }
}