<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Staff;
use App\Models\FeePayment;
use App\Models\Exam;

class DashboardController extends Controller
{
    public function index()
    {
        $year = AcademicYear::current();

        $stats = [
            'students'     => Student::currentYear()->active()->count(),
            'staff'        => Staff::active()->count(),
            'fee_collected' => FeePayment::paid()
                                ->whereMonth('payment_date', now()->month)
                                ->sum('amount_paid'),
            'fee_pending'  => FeePayment::pending()->sum('amount_due'),
            'exams_today'  => Exam::forToday()->count(),
        ];

        $recentPayments = FeePayment::with(['student', 'feeStructure'])
            ->paid()
            ->latest('payment_date')
            ->limit(8)
            ->get();

        $upcomingExams = Exam::with(['subject', 'schoolClass', 'examType'])
            ->upcoming()
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentPayments', 'upcomingExams', 'year'
        ));
    }
}