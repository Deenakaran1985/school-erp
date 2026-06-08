<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,  // 1st — roles needed before users
            AcademicYearSeeder::class,          // 2nd — year needed before classes
            ClassAndSectionSeeder::class,       // 3rd — classes before students
            ExamTypeSeeder::class,              // 4th — independent
            GradeConfigSeeder::class,           // 5th — needs academic year
            ExpenseHeadSeeder::class,           // 6th — independent
            AdminUserSeeder::class,             // 7th — needs roles + classes
        ]);
    }
}
