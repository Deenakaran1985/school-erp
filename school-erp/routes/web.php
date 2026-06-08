<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Exam\ExamController;
use App\Http\Controllers\Exam\MarksController;
use App\Http\Controllers\Exam\ResultController;
use App\Http\Controllers\Fees\FeeStructureController;
use App\Http\Controllers\Fees\FeeCollectionController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Expense\ExpenseController;
use App\Http\Controllers\Fleet\FleetController;
use App\Http\Controllers\EMIS\EmisImportController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;

// ── Guest routes ───────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ── Admin / Staff routes ───────────────────────────────────
Route::prefix('admin')
    ->middleware(['auth', 'role:super_admin|correspondent|principal|teacher|accountant'])
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students
    Route::get('students/emis-import',  [EmisImportController::class, 'showForm'])->name('students.emis.form');
    Route::post('students/emis-import', [EmisImportController::class, 'import'])->name('students.emis.import');
    Route::get('students/{student}/sections', [StudentController::class, 'getSections'])->name('students.sections');
    Route::resource('students', StudentController::class);

    // Staff
    Route::resource('staff', StaffController::class);

    // Classes & Sections
    Route::resource('classes', ClassController::class);
    Route::post('classes/{class}/sections',         [ClassController::class, 'addSection'])->name('classes.sections.add');
    Route::delete('classes/{class}/sections/{section}', [ClassController::class, 'removeSection'])->name('classes.sections.remove');
    Route::post('classes/{class}/subjects',         [ClassController::class, 'addSubject'])->name('classes.subjects.add');

    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/',                            [ExamController::class, 'index'])->name('index');
        Route::get('create',                       [ExamController::class, 'create'])->name('create');
        Route::post('/',                           [ExamController::class, 'store'])->name('store');
        Route::get('subjects',                     [ExamController::class, 'getSubjects'])->name('subjects');
        Route::get('{exam}',                       [ExamController::class, 'show'])->name('show');
        Route::get('{exam}/marks',                 [MarksController::class, 'index'])->name('marks.index');
        Route::post('{exam}/marks',                [MarksController::class, 'store'])->name('marks.store');
        Route::post('{exam}/publish',              [ResultController::class, 'publish'])->name('publish');
        Route::get('{exam}/results',               [ResultController::class, 'show'])->name('results');
        Route::post('{exam}/resend-notifications', [ResultController::class, 'resendNotifications'])->name('resend');
    });

    // Fees
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::resource('structures', FeeStructureController::class);
        Route::get('collect',              [FeeCollectionController::class, 'index'])->name('collect');
        Route::post('collect',             [FeeCollectionController::class, 'store'])->name('collect.store');
        Route::get('student/{student}',    [FeeCollectionController::class, 'studentFees'])->name('student');
        Route::get('receipt/{payment}',    [FeeCollectionController::class, 'receipt'])->name('receipt');
    });

    // Payroll
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/',                    [PayrollController::class, 'index'])->name('index');
        Route::get('generate',             [PayrollController::class, 'showGenerate'])->name('generate');
        Route::post('generate',            [PayrollController::class, 'generate']);
        Route::post('approve-all',         [PayrollController::class, 'approveAll'])->name('approve-all');
        Route::get('{payroll}',            [PayrollController::class, 'show'])->name('show');
        Route::put('{payroll}',            [PayrollController::class, 'update'])->name('update');
        Route::post('{payroll}/approve',   [PayrollController::class, 'approve'])->name('approve');
        Route::post('{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('mark-paid');
        Route::get('{payroll}/payslip',    [PayrollController::class, 'payslip'])->name('payslip');
    });

    // Expenses
    Route::resource('expenses', ExpenseController::class)->except(['show', 'edit', 'update']);
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/reject',  [ExpenseController::class, 'reject'])->name('expenses.reject');

    // Fleet
    Route::prefix('fleet')->name('fleet.')->group(function () {
        Route::get('/',              [FleetController::class, 'index'])->name('index');
        Route::get('create-vehicle', [FleetController::class, 'createVehicle'])->name('create-vehicle');
        Route::post('store-vehicle', [FleetController::class, 'storeVehicle'])->name('store-vehicle');
        Route::post('store-route',   [FleetController::class, 'storeRoute'])->name('store-route');
        Route::post('assign-student',[FleetController::class, 'assignStudent'])->name('assign-student');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',      [NotificationController::class, 'index'])->name('index');
        Route::post('send',  [NotificationController::class, 'send'])->name('send');
    });

    // Settings
    Route::get('settings',                              [SettingsController::class, 'index'])->name('settings');
    Route::post('settings/school-info',                 [SettingsController::class, 'updateSchoolInfo'])->name('settings.school-info');
    Route::post('settings/academic-year',               [SettingsController::class, 'storeAcademicYear'])->name('settings.academic-year');
    Route::put('settings/academic-year/{year}/current', [SettingsController::class, 'setCurrentYear'])->name('settings.academic-year.current');
    Route::post('settings/departments',                 [SettingsController::class, 'storeDepartment'])->name('settings.departments');
    Route::post('settings/grade-config',                [SettingsController::class, 'storeGradeConfig'])->name('settings.grade-config');
    Route::post('settings/exam-types',                  [SettingsController::class, 'storeExamType'])->name('settings.exam-types');
});

// ── Parent portal ──────────────────────────────────────────
Route::prefix('parent')
    ->middleware(['auth', 'role:parent'])
    ->name('parent.')
    ->group(function () {
    Route::get('home',    fn() => view('parent.home'))->name('home');
    Route::get('fees',    fn() => view('parent.fees'))->name('fees');
    Route::get('results', fn() => view('parent.results'))->name('results');
});

// ── Student portal ─────────────────────────────────────────
Route::prefix('student')
    ->middleware(['auth', 'role:student'])
    ->name('student.')
    ->group(function () {
    Route::get('home',       fn() => view('student.home'))->name('home');
    Route::get('results',    fn() => view('student.results'))->name('results');
    Route::get('attendance', fn() => view('student.attendance'))->name('attendance');
    Route::get('homework',   fn() => view('student.homework'))->name('homework');
});
