<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions (important!)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define all permissions ──────────────────────────────
        $permissions = [

            // Students
            'student.view', 'student.create', 'student.edit', 'student.delete',
            'student.import',       // EMIS Excel import
            'student.promote',      // grade upgrade

            // Staff
            'staff.view', 'staff.create', 'staff.edit', 'staff.delete',

            // Classes & Subjects
            'class.view', 'class.manage',
            'subject.view', 'subject.manage',
            'timetable.view', 'timetable.manage',

            // Attendance
            'attendance.view', 'attendance.mark', 'attendance.edit',

            // Homework
            'homework.view', 'homework.create', 'homework.edit',

            // Exams
            'exam.view', 'exam.create', 'exam.edit', 'exam.delete',
            'exam.marks.entry',     // teacher enters marks
            'exam.results.publish', // admin/principal publishes results + FCM
            'exam.results.view',

            // Fees
            'fee.view', 'fee.collect', 'fee.structure.manage',
            'fee.report', 'fee.discount',

            // Payroll
            'payroll.view', 'payroll.generate',
            'payroll.approve', 'payroll.mark_paid',

            // Expenses
            'expense.view', 'expense.create',
            'expense.approve', 'expense.delete',

            // Fleet
            'fleet.view', 'fleet.manage',

            // Notifications
            'notification.send', 'notification.view',

            // Reports
            'report.academic', 'report.financial', 'report.all',

            // Settings
            'settings.manage', 'roles.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Create roles & assign permissions ──────────────────

        // SUPER ADMIN — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // CORRESPONDENT
        $correspondent = Role::firstOrCreate(['name' => 'correspondent']);
        $correspondent->syncPermissions([
            'student.view', 'student.promote',
            'staff.view', 'staff.create', 'staff.edit',
            'fee.view', 'fee.structure.manage', 'fee.report', 'fee.discount',
            'payroll.view', 'payroll.approve', 'payroll.mark_paid',
            'expense.view', 'expense.approve',
            'fleet.view', 'fleet.manage',
            'report.academic', 'report.financial', 'report.all',
            'notification.send', 'notification.view',
            'exam.results.view',
        ]);

        // PRINCIPAL
        $principal = Role::firstOrCreate(['name' => 'principal']);
        $principal->syncPermissions([
            'student.view', 'student.promote',
            'staff.view',
            'class.view', 'class.manage',
            'subject.view', 'subject.manage',
            'timetable.view', 'timetable.manage',
            'attendance.view',
            'homework.view',
            'exam.view', 'exam.create', 'exam.edit',
            'exam.results.publish', 'exam.results.view',
            'fee.view', 'fee.report',
            'report.academic',
            'notification.send', 'notification.view',
        ]);

        // TEACHER
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacher->syncPermissions([
            'student.view',
            'timetable.view',
            'attendance.view', 'attendance.mark', 'attendance.edit',
            'homework.view', 'homework.create', 'homework.edit',
            'exam.view',
            'exam.marks.entry',
            'exam.results.view',
            'notification.view',
        ]);

        // ACCOUNTANT
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->syncPermissions([
            'student.view',
            'fee.view', 'fee.collect', 'fee.report',
            'payroll.view', 'payroll.generate',
            'expense.view', 'expense.create',
            'report.financial',
            'notification.view',
        ]);

        // PARENT — mobile app user
        $parent = Role::firstOrCreate(['name' => 'parent']);
        $parent->syncPermissions([
            'exam.results.view',
            'fee.view',
            'notification.view',
            'homework.view',
            'attendance.view',
        ]);

        // STUDENT — mobile app user
        $student = Role::firstOrCreate(['name' => 'student']);
        $student->syncPermissions([
            'exam.results.view',
            'homework.view',
            'timetable.view',
            'notification.view',
            'attendance.view',
        ]);

        $this->command->info('✅ Roles and permissions seeded.');
    }
}
