@extends('layouts.app')
@section('title', 'Generate Payroll')
@section('page_title', 'Generate Monthly Payroll')

@section('content')
<div class="max-w-lg">
  <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">

    <div class="p-4 bg-purple-50 border border-purple-200 rounded-xl">
      <p class="text-sm font-medium text-purple-700">
        ⚡ This will batch-generate payroll for all
        <strong>{{ $staffCount }} active staff</strong>.
      </p>
      <p class="text-xs text-purple-500 mt-1">
        Staff who already have a record for the selected month will be skipped automatically.
        You can edit individual records after generation.
      </p>
    </div>

    <form method="POST" action="{{ route('admin.payroll.generate') }}" class="space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Month & Year *</label>
        <input type="month" name="month_year"
          value="{{ now()->format('Y-m') }}" required
          class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none"/>
        @error('month_year')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Working Days This Month *</label>
        <input type="number" name="working_days"
          value="26" min="1" max="31" required
          class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none"/>
        <p class="text-xs text-slate-400 mt-1">
          Default 26. Adjust for holidays. LOP = absent days × (gross / working days).
        </p>
        @error('working_days')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700">
        ⚠️ All staff will default to <strong>full attendance (no LOP)</strong>.
        After generating, edit each payslip individually to apply leave deductions or TDS/Loan adjustments.
      </div>

      <button type="submit"
        class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl text-sm">
        ⚡ Generate Payroll for All Staff
      </button>
    </form>
  </div>
</div>
@endsection