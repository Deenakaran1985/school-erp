<?php

use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\CorrespondentApiController;
use App\Http\Controllers\API\ExamApiController;
use App\Http\Controllers\API\NotificationApiController;
use App\Http\Controllers\API\PaymentApiController;
use App\Http\Controllers\API\StaffApiController;
use App\Http\Controllers\API\StudentApiController;
use Illuminate\Support\Facades\Route;

// ── Public: Auth ───────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login',  [AuthApiController::class, 'login']);
    Route::post('logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
});

// ── Authenticated routes ───────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profile
    Route::get('auth/me',                       [AuthApiController::class, 'me']);
    Route::post('auth/change-password',         [AuthApiController::class, 'changePassword']);
    Route::post('profile/update-fcm',           [NotificationApiController::class, 'updateFcmToken']);
    Route::post('profile/update-avatar',        [StudentApiController::class, 'updateAvatar']);

    // ── Notifications (all roles) ──────────────────────────
    Route::get('notifications',                 [NotificationApiController::class, 'index']);
    Route::post('notifications/{id}/read',      [NotificationApiController::class, 'markRead']);

    // ── Student & Parent routes ────────────────────────────
    Route::get('profile',                       [StudentApiController::class, 'profile']);
    Route::get('student/{student}/attendance',  [StudentApiController::class, 'attendance']);
    Route::get('student/{student}/homework',    [StudentApiController::class, 'homework']);

    // Exams & Results (student/parent)
    Route::get('exams',                         [ExamApiController::class, 'index']);
    Route::get('results',                       [ExamApiController::class, 'results']);
    Route::get('results/report-card',           [ExamApiController::class, 'reportCard']);

    // Fees (parent)
    Route::get('fees/pending',                  [PaymentApiController::class, 'pendingFees']);
    Route::get('fees/history',                  [PaymentApiController::class, 'history']);
    Route::post('fees/create-order',            [PaymentApiController::class, 'createOrder']);
    Route::post('fees/verify',                  [PaymentApiController::class, 'verifyPayment']);

    // ── Staff routes ───────────────────────────────────────
    Route::prefix('staff')->group(function () {
        Route::get('profile',                   [StaffApiController::class, 'profile']);
        Route::get('payslips',                  [StaffApiController::class, 'payslips']);
        Route::get('attendance',                [StaffApiController::class, 'attendance']);
        Route::get('my-classes',               [StaffApiController::class, 'myClasses']);
        Route::get('students',                  [StaffApiController::class, 'students']);
        Route::post('attendance/mark',          [StaffApiController::class, 'markStudentAttendance']);
        Route::post('homework',                 [StaffApiController::class, 'createHomework']);
    });

    // ── Correspondent / Principal / Admin routes ───────────
    Route::prefix('correspondent')->group(function () {
        Route::get('dashboard',                 [CorrespondentApiController::class, 'dashboard']);
        Route::get('fee-summary',               [CorrespondentApiController::class, 'feeSummary']);
        Route::get('payroll-summary',           [CorrespondentApiController::class, 'payrollSummary']);
        Route::get('staff',                     [CorrespondentApiController::class, 'staffList']);
        Route::get('expenses',                  [CorrespondentApiController::class, 'expenses']);
        Route::post('notifications/send',       [CorrespondentApiController::class, 'sendNotification']);
    });
});
