<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Staff;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $year  = AcademicYear::current();
        $class6 = SchoolClass::where('name', 'VI')
                    ->where('academic_year_id', $year->id)
                    ->first();

        // ── 1. Super Admin ─────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.local'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('Admin@1234'),
                'phone'     => '9000000001',
                'user_type' => 'admin',
                'status'    => 'active',
            ]
        );
        $admin->assignRole('super_admin');

        // ── 2. Correspondent ───────────────────────────────
        $corr = User::firstOrCreate(
            ['email' => 'correspondent@school.local'],
            [
                'name'      => 'Dr. Correspondent',
                'password'  => Hash::make('Corr@1234'),
                'phone'     => '9000000002',
                'user_type' => 'admin',
                'status'    => 'active',
            ]
        );
        $corr->assignRole('correspondent');

        // ── 3. Principal ───────────────────────────────────
        $principal = User::firstOrCreate(
            ['email' => 'principal@school.local'],
            [
                'name'      => 'Mrs. Principal',
                'password'  => Hash::make('Principal@1234'),
                'phone'     => '9000000003',
                'user_type' => 'staff',
                'status'    => 'active',
            ]
        );
        $principal->assignRole('principal');

        // ── 4. Teacher ─────────────────────────────────────
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@school.local'],
            [
                'name'      => 'Mr. Maths Teacher',
                'password'  => Hash::make('Teacher@1234'),
                'phone'     => '9000000004',
                'user_type' => 'staff',
                'status'    => 'active',
            ]
        );
        $teacherUser->assignRole('teacher');

        // Create staff record for teacher
        Staff::firstOrCreate(
            ['user_id' => $teacherUser->id],
            [
                'employee_id'  => 'EMP-001',
                'name'         => $teacherUser->name,
                'designation'  => 'PGT Mathematics',
                'staff_type'   => 'teaching',
                'gender'       => 'M',
                'joining_date' => now()->subYears(2),
                'basic_salary' => 25000.00,
                'da_percent'   => 10.00,
                'hra_percent'  => 8.00,
                'pf_percent'   => 12.00,
                'status'       => 'active',
            ]
        );

        // ── 5. Accountant ──────────────────────────────────
        $acc = User::firstOrCreate(
            ['email' => 'accountant@school.local'],
            [
                'name'      => 'Mrs. Accountant',
                'password'  => Hash::make('Account@1234'),
                'phone'     => '9000000005',
                'user_type' => 'staff',
                'status'    => 'active',
            ]
        );
        $acc->assignRole('accountant');

        // ── 6. Parent + Student (demo) ─────────────────────
        $parentUser = User::firstOrCreate(
            ['email' => 'parent@school.local'],
            [
                'name'      => 'Rajan (Parent)',
                'password'  => Hash::make('9876543210'), // mobile as password
                'phone'     => '9876543210',
                'user_type' => 'parent',
                'status'    => 'active',
            ]
        );
        $parentUser->assignRole('parent');

        $studentUser = User::firstOrCreate(
            ['email' => 'student@school.local'],
            [
                'name'      => 'Arun Kumar',
                'password'  => Hash::make('Student@1234'),
                'phone'     => '9876543210',
                'user_type' => 'student',
                'status'    => 'active',
            ]
        );
        $studentUser->assignRole('student');

        // Create student record only if Class VI exists
        if ($class6) {
            $section = $class6->sections()->first();

            Student::firstOrCreate(
                ['user_id' => $studentUser->id],
                [
                    'academic_year_id' => $year->id,
                    'school_class_id'  => $class6->id,
                    'section_id'       => $section?->id,
                    'parent_user_id'   => $parentUser->id,
                    'admission_no'     => 'ADM-2025-001',
                    'emis_number'      => '33010120001',
                    'name'             => 'Arun Kumar',
                    'father_name'      => 'Rajan',
                    'date_of_birth'    => '2013-06-15',
                    'gender'           => 'M',
                    'community'        => 'BC',
                    'parent_mobile'    => '9876543210',
                    'roll_number'      => 1,
                    'status'           => 'active',
                ]
            );
        }

        $this->command->info('✅ All test users seeded with roles.');
    }
}