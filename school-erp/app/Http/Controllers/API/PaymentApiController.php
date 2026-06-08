<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Services\FCMService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function __construct(
        private RazorpayService $razorpay,
        private FCMService      $fcm,
    ) {}

    // ── GET /api/fees/pending ──────────────────────────────
    // Returns all pending fees for the logged-in parent's children
    public function pendingFees(Request $request)
    {
        $user     = $request->user();
        $year     = AcademicYear::current();
        $children = Student::where('parent_user_id', $user->id)
            ->active()->with('schoolClass')->get();

        $pending = [];

        foreach ($children as $student) {
            $structures = FeeStructure::where('school_class_id', $student->school_class_id)
                ->where('academic_year_id', $year->id)
                ->get();

            foreach ($structures as $structure) {
                $paid = FeePayment::where('student_id', $student->id)
                    ->where('fee_structure_id', $structure->id)
                    ->where('status', 'paid')->exists();

                if (!$paid) {
                    $pending[] = [
                        'student_id'       => $student->id,
                        'student_name'     => $student->name,
                        'class'            => $student->schoolClass->name,
                        'fee_structure_id' => $structure->id,
                        'fee_head'         => $structure->fee_head,
                        'amount'           => (float) $structure->amount,
                        'term'             => $structure->term,
                        'due_date'         => $structure->due_date?->format('Y-m-d'),
                        'overdue'          => $structure->due_date?->isPast(),
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $pending,
            'total'   => array_sum(array_column($pending, 'amount')),
        ]);
    }

    // ── POST /api/fees/create-order ────────────────────────
    // Flutter calls this → gets Razorpay order_id
    public function createOrder(Request $request)
    {
        $request->validate([
            'student_id'       => 'required|exists:students,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
        ]);

        $student   = Student::findOrFail($request->student_id);
        $structure = FeeStructure::findOrFail($request->fee_structure_id);

        // Prevent duplicate payment
        if (FeePayment::where('student_id', $student->id)
            ->where('fee_structure_id', $structure->id)
            ->where('status', 'paid')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This fee is already paid.',
            ], 422);
        }

        // Create pending payment record
        $payment = FeePayment::create([
            'student_id'       => $student->id,
            'fee_structure_id' => $structure->id,
            'amount_due'       => $structure->amount,
            'amount_paid'      => 0,
            'payment_mode'     => 'online',
            'status'           => 'pending',
        ]);

        // Create Razorpay order
        $order = $this->razorpay->createOrder(
            amount:  (float) $structure->amount,
            receipt: $payment->receipt_no,
            notes: [
                'student_name' => $student->name,
                'fee_head'     => $structure->fee_head,
                'payment_id'   => (string) $payment->id,
            ]
        );

        if (!$order['success']) {
            $payment->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway error. Try again.',
            ], 500);
        }

        // Save Razorpay order ID to payment record
        $payment->update(['razorpay_order_id' => $order['order_id']]);

        return response()->json([
            'success'    => true,
            'payment_id' => $payment->id,
            'order_id'   => $order['order_id'],
            'amount'     => $order['amount'],     // in paise
            'currency'   => $order['currency'],
            'key'        => $order['key'],
            'prefill'    => [
                'name'    => $student->father_name,
                'contact' => $student->parent_mobile,
            ],
            'description' => $structure->fee_head . ' — ' . $student->name,
        ]);
    }

    // ── POST /api/fees/verify ──────────────────────────────
    // Flutter sends back Razorpay response for verification
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'payment_id'          => 'required|exists:fee_payments,id',
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $payment = FeePayment::findOrFail($request->payment_id);

        // Security: verify order_id matches
        if ($payment->razorpay_order_id !== $request->razorpay_order_id) {
            return response()->json(['success' => false, 'message' => 'Order ID mismatch.'], 422);
        }

        // Verify cryptographic signature
        $valid = $this->razorpay->verifyPayment(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        if (!$valid) {
            $payment->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Contact school.',
            ], 422);
        }

        // Mark as paid
        $payment->update([
            'amount_paid'          => $payment->amount_due,
            'transaction_id'       => $request->razorpay_payment_id,
            'razorpay_signature'   => $request->razorpay_signature,
            'status'               => 'paid',
            'payment_mode'         => 'online',
            'payment_date'         => now(),
        ]);

        // Send FCM receipt notification to parent
        $parent = $request->user();
        if ($parent?->fcm_token) {
            $this->fcm->send(
                token: $parent->fcm_token,
                title: '✅ Fee Payment Successful',
                body:  "₹{$payment->amount_paid} paid for {$payment->feeStructure->fee_head}. Receipt: {$payment->receipt_no}",
                data:  [
                    'type'         => 'fee_paid',
                    'payment_id'   => (string) $payment->id,
                    'receipt_no'   => $payment->receipt_no,
                    'amount'       => (string) $payment->amount_paid,
                    'click_action' => 'FEE_RECEIPT_SCREEN',
                ]
            );
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Payment successful!',
            'receipt_no' => $payment->receipt_no,
            'amount'     => $payment->amount_paid,
        ]);
    }

    // ── GET /api/fees/history ──────────────────────────────
    public function history(Request $request)
    {
        $user     = $request->user();
        $children = Student::where('parent_user_id', $user->id)->pluck('id');

        $payments = FeePayment::with(['student', 'feeStructure'])
            ->whereIn('student_id', $children)
            ->paid()
            ->latest('payment_date')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $payments->map(fn($p) => [
                'id'           => $p->id,
                'receipt_no'   => $p->receipt_no,
                'student'      => $p->student->name,
                'fee_head'     => $p->feeStructure->fee_head,
                'amount'       => (float) $p->amount_paid,
                'mode'         => $p->payment_mode,
                'date'         => $p->payment_date?->format('d M Y'),
                'transaction_id' => $p->transaction_id,
            ]),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
            ],
        ]);
    }
}