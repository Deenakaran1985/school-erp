<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'fee_structure_id', 'collected_by', 'receipt_no',
        'amount_due', 'amount_paid', 'discount', 'fine',
        'payment_mode', 'transaction_id', 'razorpay_order_id',
        'razorpay_signature', 'status', 'payment_date',
        'cheque_no', 'bank_name', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount_due'  => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'discount'    => 'decimal:2',
            'fine'        => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function student()       { return $this->belongsTo(Student::class); }
    public function feeStructure() { return $this->belongsTo(FeeStructure::class); }
    public function collectedBy()  { return $this->belongsTo(User::class, 'collected_by'); }

    public function scopePaid($query)    { return $query->where('status', 'paid'); }
    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeOnline($query)  { return $query->where('payment_mode', 'online'); }

    public function getNetAmountAttribute(): float
    {
        return $this->amount_due - $this->discount + $this->fine;
    }

    // Auto-generate receipt number before creating
    protected static function booted(): void
    {
        static::creating(function ($payment) {
            if (empty($payment->receipt_no)) {
                $payment->receipt_no = 'RCP-' . strtoupper(uniqid());
            }
        });
    }
}