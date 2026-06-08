<?php
namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('expense.view');

        $expenses = Expense::with(['expenseHead', 'createdBy', 'approvedBy'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->head_id, fn($q, $v) => $q->where('expense_head_id', $v))
            ->when($request->from, fn($q, $v) => $q->whereDate('expense_date', '>=', $v))
            ->when($request->to,   fn($q, $v) => $q->whereDate('expense_date', '<=', $v))
            ->orderByDesc('expense_date')
            ->paginate(20)
            ->withQueryString();

        // Monthly total for current month
        $monthlyTotal = Expense::approved()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date',  now()->year)
            ->sum('amount');

        $pendingCount = Expense::pending()->count();
        $heads        = ExpenseHead::where('is_active', true)->get();

        return view('expenses.index', compact(
            'expenses', 'monthlyTotal', 'pendingCount', 'heads'
        ));
    }

    public function create()
    {
        $this->authorize('expense.create');
        $heads = ExpenseHead::where('is_active', true)->orderBy('name')->get();
        return view('expenses.create', compact('heads'));
    }

    public function store(Request $request)
    {
        $this->authorize('expense.create');

        $validated = $request->validate([
            'expense_head_id' => 'required|exists:expense_heads,id',
            'title'           => 'required|string|max:200',
            'description'     => 'nullable|string',
            'amount'          => 'required|numeric|min:1',
            'vendor_name'     => 'nullable|string|max:100',
            'bill_no'         => 'nullable|string|max:50',
            'expense_date'    => 'required|date',
            'attachment'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')
                ->store('expense-bills', 'public');
        }

        Expense::create(array_merge($validated, [
            'created_by' => auth()->id(),
            'status'     => 'pending',
        ]));

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense submitted for approval.');
    }

    public function approve(Expense $expense)
    {
        $this->authorize('expense.approve');

        if ($expense->status !== 'pending') {
            return back()->with('error', 'Only pending expenses can be approved.');
        }

        $expense->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Expense approved.');
    }

    public function reject(Expense $expense)
    {
        $this->authorize('expense.approve');
        $expense->update(['status' => 'rejected']);
        return back()->with('success', 'Expense rejected.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('expense.delete');

        if ($expense->attachment) {
            Storage::disk('public')->delete($expense->attachment);
        }
        $expense->delete();

        return back()->with('success', 'Expense deleted.');
    }
}