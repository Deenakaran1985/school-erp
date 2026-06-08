@extends('layouts.app')
@section('title', 'Payroll Detail')
@section('page_title', 'Payroll Detail')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl">

  <!-- LEFT: Staff card -->
  <div class="space-y-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-lg">
          {{ strtoupper(substr($payroll->staff->name, 0, 1)) }}
        </div>
        <div>
          <p class="font-semibold text-slate-800">{{ $payroll->staff->name }}</p>
          <p class="text-xs text-slate-400">{{ $payroll->staff->employee_id }}</p>
        </div>
      </div>
      @foreach([
        ['Designation', $payroll->staff->designation],
        ['Department', $payroll->staff->department?->name ?? '—'],
        ['Month',  \Carbon\Carbon::createFromFormat('Y-m', $payroll->month_year)->format('F Y')],
        ['Status', ucfirst($payroll->status)],
        ['Approved By', $payroll->approvedBy?->name ?? '—'],
      ] as [$label, $val])
        <div class="flex justify-between text-sm py-2 border-b border-slate-50">
          <span class="text-slate-400">{{ $label }}</span>
          <span class="text-slate-700 font-medium">{{ $val }}</span>
        </div>
      @endforeach

      <div class="flex gap-2 mt-4">
        <a href="{{ route('admin.payroll.payslip', $payroll) }}" target="_blank"
          class="flex-1 py-2 text-center bg-purple-600 text-white text-xs font-medium rounded-xl hover:bg-purple-700">
          🖨 Payslip PDF
        </a>
      </div>

      @if($payroll->status === 'approved')
        @can('payroll.mark_paid')
          <form method="POST" action="{{ route('admin.payroll.mark-paid', $payroll) }}"
            class="mt-3 space-y-2">
            @csrf
            <select name="payment_mode"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
              <option value="bank_transfer">Bank Transfer</option>
              <option value="cash">Cash</option>
              <option value="cheque">Cheque</option>
            </select>
            <input type="date" name="paid_on" value="{{ now()->format('Y-m-d') }}"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs"/>
            <button type="submit"
              class="w-full py-2 bg-green-600 text-white text-xs font-medium rounded-xl">
              ✅ Mark as Paid
            </button>
          </form>
        @endcan
      @endif
    </div>
  </div>

  <!-- RIGHT: Salary breakdown + edit -->
  <div class="lg:col-span-2 space-y-4">

    <!-- Salary breakdown -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
        <h3 class="font-semibold text-slate-700">Salary Breakdown</h3>
      </div>
      <div class="p-5 grid grid-cols-2 gap-x-8 gap-y-1">
        <!-- Earnings -->
        <div>
          <p class="text-xs font-semibold text-green-600 uppercase mb-2">Earnings</p>
          @foreach([
            ['Basic Salary',    $payroll->basic_salary],
            ['DA',             $payroll->da_amount],
            ['HRA',            $payroll->hra_amount],
            ['Other Allowance',$payroll->other_allowance],
          ] as [$label, $val])
            <div class="flex justify-between py-1.5 border-b border-slate-50 text-sm">
              <span class="text-slate-500">{{ $label }}</span>
              <span class="text-slate-700">₹{{ number_format($val, 2) }}</span>
            </div>
          @endforeach
          <div class="flex justify-between py-2 text-sm font-bold border-t border-slate-200 mt-1">
            <span>Gross Salary</span>
            <span class="text-blue-600">₹{{ number_format($payroll->gross_salary, 2) }}</span>
          </div>
        </div>
        <!-- Deductions -->
        <div>
          <p class="text-xs font-semibold text-red-500 uppercase mb-2">Deductions</p>
          @foreach([
            ['LOP ('.$payroll->leave_days.' days)', $payroll->pf_deduction], {{-- Note: use lop_amount if stored separately --}}
            ['PF (12%)',        $payroll->pf_deduction],
            ['ESI',             $payroll->esi_deduction],
            ['TDS',             $payroll->tds_deduction],
            ['Loan',            $payroll->loan_deduction],
            ['Other',           $payroll->other_deduction],
          ] as [$label, $val])
            @if($val > 0)
              <div class="flex justify-between py-1.5 border-b border-slate-50 text-sm">
                <span class="text-slate-500">{{ $label }}</span>
                <span class="text-red-500">− ₹{{ number_format($val, 2) }}</span>
              </div>
            @endif
          @endforeach
          <div class="flex justify-between py-2 text-sm font-bold border-t border-slate-200 mt-1">
            <span>Total Deductions</span>
            <span class="text-red-500">₹{{ number_format($payroll->total_deduction, 2) }}</span>
          </div>
        </div>
      </div>
      <div class="px-5 py-4 bg-green-50 border-t border-green-100 flex justify-between items-center">
        <span class="font-semibold text-green-700">Net Salary Payable</span>
        <span class="text-2xl font-bold text-green-600">₹{{ number_format($payroll->net_salary, 2) }}</span>
      </div>
    </div>

    <!-- Edit form (draft only) -->
    @if($payroll->status === 'draft')
      @can('payroll.generate')
        <div class="bg-white rounded-2xl border border-amber-200 p-5">
          <h3 class="font-semibold text-amber-700 mb-4">✏️ Adjust & Recalculate</h3>
          <form method="POST" action="{{ route('admin.payroll.update', $payroll) }}"
            class="grid grid-cols-2 gap-4">
            @csrf @method('PUT')

            @foreach([
              ['present_days',   'Present Days',   $payroll->present_days, 'number'],
              ['tds_deduction',  'TDS Deduction',  $payroll->tds_deduction,  'number'],
              ['loan_deduction', 'Loan Deduction', $payroll->loan_deduction, 'number'],
              ['other_deduction','Other Deduction',$payroll->other_deduction,'number'],
            ] as [$name, $label, $val, $type])
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ $label }}</label>
                <input type="{{ $type }}" name="{{ $name }}"
                  value="{{ old($name, $val) }}"
                  min="0" step="0.01"
                  class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
              </div>
            @endforeach

            <div class="col-span-2">
              <label class="block text-xs font-medium text-slate-500 mb-1">Remarks</label>
              <input type="text" name="remarks"
                value="{{ old('remarks', $payroll->remarks) }}"
                placeholder="e.g. Loan EMI deduction"
                class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
            </div>

            <div class="col-span-2">
              <button type="submit"
                class="px-6 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl">
                🔄 Recalculate & Save
              </button>
            </div>
          </form>
        </div>
      @endcan
    @endif
  </div>
</div>
@endsection