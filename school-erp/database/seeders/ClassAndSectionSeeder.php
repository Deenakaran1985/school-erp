<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;

class ClassAndSectionSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::current();

        $classes = [
            // [name, display_name, level, sort_order]
            ['I',    'Class 1',   'primary',          1],
            ['II',   'Class 2',   'primary',          2],
            ['III',  'Class 3',   'primary',          3],
            ['IV',   'Class 4',   'primary',          4],
            ['V',    'Class 5',   'primary',          5],
            ['VI',   'Class 6',   'middle',           6],
            ['VII',  'Class 7',   'middle',           7],
            ['VIII', 'Class 8',   'middle',           8],
            ['IX',   'Class 9',   'secondary',        9],
            ['X',    'Class 10',  'secondary',        10],
            ['XI',   'Class 11',  'higher_secondary', 11],
            ['XII',  'Class 12',  'higher_secondary', 12],
        ];

        foreach ($classes as [$name, $display, $level, $sort]) {

            $class = SchoolClass::firstOrCreate(
                ['name' => $name, 'academic_year_id' => $year->id],
                [
                    'display_name'     => $display,
                    'level'            => $level,
                    'sort_order'       => $sort,
                    'is_active'        => true,
                ]
            );

            // Create sections A and B for each class
            foreach (['A', 'B'] as $section) {
                Section::firstOrCreate(
                    ['school_class_id' => $class->id, 'name' => $section],
                    [
                        'medium'       => 'Tamil',
                        'max_strength' => 40,
                    ]
                );
            }
        }

        $this->command->info('✅ 12 classes with A & B sections seeded.');
    }
}