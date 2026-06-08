<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ExamType;

class ExamTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // [name, code, weightage%, max_marks, pass_marks, counts_for_promo, sort]
            [
                'name'                  => 'Unit Test',
                'code'                  => 'UT',
                'weightage_percent'     => 10.00,
                'max_marks'             => 20,
                'pass_marks'            => 7,
                'counts_for_promotion'  => true,
                'sort_order'            => 1,
            ],
            [
                'name'                  => 'Quarterly Exam',
                'code'                  => 'QE',
                'weightage_percent'     => 25.00,
                'max_marks'             => 100,
                'pass_marks'            => 35,
                'counts_for_promotion'  => true,
                'sort_order'            => 2,
            ],
            [
                'name'                  => 'Half Yearly Exam',
                'code'                  => 'HY',
                'weightage_percent'     => 25.00,
                'max_marks'             => 100,
                'pass_marks'            => 35,
                'counts_for_promotion'  => true,
                'sort_order'            => 3,
            ],
            [
                'name'                  => 'Annual Exam',
                'code'                  => 'AE',
                'weightage_percent'     => 40.00,
                'max_marks'             => 100,
                'pass_marks'            => 35,
                'counts_for_promotion'  => true,
                'sort_order'            => 4,
            ],
            [
                'name'                  => 'Supplementary Exam',
                'code'                  => 'SUPP',
                'weightage_percent'     => 0.00,
                'max_marks'             => 100,
                'pass_marks'            => 35,
                'counts_for_promotion'  => true,
                'sort_order'            => 5,
            ],
            [
                'name'                  => 'Practical Exam',
                'code'                  => 'PR',
                'weightage_percent'     => 0.00,
                'max_marks'             => 50,
                'pass_marks'            => 18,
                'counts_for_promotion'  => false,
                'sort_order'            => 6,
            ],
            [
                'name'                  => 'Activity / Project',
                'code'                  => 'ACT',
                'weightage_percent'     => 0.00,
                'max_marks'             => 20,
                'pass_marks'            => 0,
                'counts_for_promotion'  => false,
                'sort_order'            => 7,
            ],
        ];

        foreach ($types as $type) {
            ExamType::firstOrCreate(['code' => $type['code']], $type);
        }

        $this->command->info('✅ 7 exam types seeded. UT(10%)+QE(25%)+HY(25%)+AE(40%)=100%');
    }
}
