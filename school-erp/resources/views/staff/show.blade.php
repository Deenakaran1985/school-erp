@extends('layouts.app')
@section('title', $staff->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

  <div class="flex items-center gap-3">
    <a href="{{ route('admin.staff.index') }}" class="text-slate-400 hover:text-slate-600">← Back</a>
    <h3 class="text-lg font-semibold text-slate-700">Staff Profile</h3>
    <div class="ml-auto flex gap-2">
      @can('staff.edit')
        <a href="{{ route('admin.staff.edit', $staff) }}"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">Edit</a>
      @endcan
      <a href="{{ route('admin.payroll.index', ['month_year' => now()->format('Y-m')]) }}"
        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl">Payroll</a>
    </div>
  </div>

  <!-- Profile Card -->
  <div class="bg-white rounded-2xl border border-slate-200 p-6">
    <div class="flex flex-wrap gap-6 items-start">
      <div class="w-20 h-20 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-3xl flex-shrink-0">
        {{ strtoupper(substr($staff->name, 0, 1)) }}
      </div>
      <div class="flex-1 min-w-0">
        <h2 class="text-xl font-bold text-slate-800">{{ $staff->name }}</h2>
        <p class="text-slate-500 text-sm">{{ $staff->designation }} · {{ $staff->department?->name }}</p>
        <p class="text-slate-400 text-xs mt-1">{{ $staff->employee_id }} · Joined {{ $staff->joining_date?->format('d M Y') }}</p>
        <div class="mt-2">
          <span class="px-3 py-1 rounded-full text-xs font-medium
            {{ $staff->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
            {{ ucfirst($staff->status) }}
          </span>
          <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
            {{ ucfirst(str_replace('_',' ',$staff->staff_type)) }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Personal Info -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
      <h4 class="font-semibold text-slate-700 text-sm">Personal Information</h4>
      @foreach([
        ['Gender', $staff->gender === 'M' ? 'Male' : ($staff->gender === 'F' ? 'Female' : 'Other')],
        ['Date of Birth', $staff->date_of_birth?->format('d M Y')],
        ['Phone', $staff->user?->phone],
        ['Qualification', $staff->qualification],
        ['Aadhar', $staff->aadhar_number],
        ['PAN', $staff->pan_number],
      ] as [$label, $val])
      <div class="flex justify-between text-sm">
        <span class="text-slate-500">{{ $label }}</span>
        <span class="text-slate-800 font-medium">{{ $val ?? '—' }}</span>
      </div>
      @endforeach
    </div>

    <!-- Salary Info -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
      <h4 class="font-semibold text-slate-700 text-sm">Salary Structure</h4>
      @foreach([
        ['Basic Salary', '₹' . number_format($staff->basic_salary, 2)],
        ['DA', $staff->da_percent . '%'],
        ['HRA', $staff->hra_percent . '%'],
        ['Other Allowance', '₹' . number_format($staff->other_allowance, 2)],
        ['PF', $staff->pf_percent . '%'],
        ['Gross Salary', '₹' . number_format($staff->gross_salary, 2)],
      ] as [$label, $val])
      <div class="flex justify-between text-sm">
        <span class="text-slate-500">{{ $label }}</span>
        <span class="text-slate-800 font-medium {{ $label === 'Gross Salary' ? 'text-blue-600' : '' }}">{{ $val }}</span>
      </div>
      @endforeach
    </div>

    <!-- Bank Details -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
      <h4 class="font-semibold text-slate-700 text-sm">Bank Details</h4>
      @foreach([
        ['Bank Name', $staff->bank_name],
        ['Account No', $staff->bank_account],
        ['IFSC Code', $staff->bank_ifsc],
      ] as [$label, $val])
      <div class="flex justify-between text-sm">
        <span class="text-slate-500">{{ $label }}</span>
        <span class="text-slate-800 font-medium">{{ $val ?? '—' }}</span>
      </div>
      @endforeach
    </div>

    <!-- Recent Payrolls -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
      <h4 class="font-semibold text-slate-700 text-sm mb-3">Recent Payrolls</h4>
      @forelse($staff->payrolls as $p)
      <div class="flex justify-between items-center py-2 border-b border-slate-100 last:border-0 text-sm">
        <div>
          <p class="font-medium text-slate-700">
            {{ \Carbon\Carbon::createFromFormat('Y-m', $p->month_year)->format('M Y') }}
          </p>
          <p class="text-xs text-slate-400">{{ $p->present_days }}/{{ $p->working_days }} days</p>
        </div>
        <div class="text-right">
          <p class="font-medium text-slate-800">₹{{ number_format($p->net_salary, 0) }}</p>
          <span class="text-xs px-2 py-0.5 rounded-full
            {{ $p->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
            {{ ucfirst($p->status) }}
          </span>
        </div>
      </div>
      @empty
      <p class="text-sm text-slate-400 py-4 text-center">No payroll records.</p>
      @endforelse
    </div>

  </div>
</div>
@endsection
