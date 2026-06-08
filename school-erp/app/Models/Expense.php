<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_head_id', 'created_by', 'approved_by',
        'title', 'description', 'amount', 'vendor_name',
        'bill_no', 'attachment', 'expense_date', 'status',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'expense_date' => 'date'];
    }

    public function expenseHead() { return $this->belongsTo(ExpenseHead::class); }
    public function createdBy()   { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy()  { return $this->belongsTo(User::class, 'approved_by'); }

    public function scopePending($query)  { return $query->where('status', 'pending'); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
}