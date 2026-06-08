@extends('layouts.app')
@section('title', 'Payroll')
@section('page_title', 'Payroll Management')

@section('content')
<div class="space-y-5">

  <!-- Header + actions -->
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h3 class="text-lg font-semibold text-slate-700">
        Payroll — {{ \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->format('F Y') }}
      </h3>
      <p class="text-sm text-slate-400">{{ $summary['staff_count'] }} staff records</p>
    </div>
    <div class="flex gap-2">
      @can('payroll.generate')
        <a href="{{ route('admin.payroll.generate') }}"
          class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl">
          ⚡ Generate Payroll
        </a>
      @endcan
      @can('payroll.approve')
        @if($summary['draft_count'] > 0)
          <form method="POST" action="{{ route('admin.payroll.approve-all') }}">
            @csrf
            <input type="hidden" name="month_year" value="{{ $monthYear }}">
            <button type="submit"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl">
              ✅ Approve All ({{ $summary['draft_count'] }})
            </button>
          </form>
        @endif
      @endcan
    </div>
  </div>

  <!-- Month selector -->
  <form method="GET" class="flex gap-3 items-center bg-white rounded-2xl border border-slate-200 p-4">
    <label class="text-sm font-medium text-slate-600">Month:</label>
    <input type="month" name="month_year" value="{{ $monthYear }}"
      class="px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
    <button type="submit"
      class="px-4 py-2 bg-blue-600 text-white text-sm rounded-xl">Go</button>
  </form>

  <!-- Summary cards -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    @foreach([
      ['Gross Payable', '₹'.number_format($summary['total_gross']),      'blue'],
      ['Total Deductions', '₹'.number_format($summary['total_deductions']), 'red'],
      ['Net Payable',  '₹'.number_format($summary['total_net']),          'green'],
      ['PF Liability',  '₹'.number_format($summary['total_pf']),          'amber'],
    ] as [$label, $val, $color])
      <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <p class="text-xl font-bold text-{{ $color }}-600">{{ $val }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $label }}</p>
      </div>
    @endforeach
  </div>

  <!-- Payroll Table -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            @foreach(['Employee','Dept','Days','Gross','Deductions','Net Salary','Status',''] as $h)
              <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">{{ $h }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($payrolls as $p)
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3">
                <p class="font-medium text-slate-700">{{ $p->staff->name }}</p>
                <p class="text-xs text-slate-400">{{ $p->staff->employee_id }} · {{ $p->staff->designation }}</p>
              </td>
              <td class="px-4 py-3 text-slate-500 text-xs">{{ $p->staff->department?->name ?? '—' }}</td>
              <td class="px-4 py-3 font-mono text-xs text-slate-500">
                {{ $p->present_days }}/{{ $p->working_days }}
              </td>
              <td class="px-4 py-3 font-semibold text-slate-700">₹{{ number_format($p->gross_salary) }}</td>
              <td class="px-4 py-3 text-red-600">− ₹{{ number_format($p->total_deduction) }}</td>
              <td class="px-4 py-3 font-bold text-green-600">₹{{ number_format($p->net_salary) }}</td>
              <td class="px-4 py-3">
                @php
                  $c = match($p->status) { 'paid' => 'green', 'approved' => 'blue', default => 'amber' };
                @endphp
                <span class="px-2 py-0.5 text-xs bg-{{ $c }}-100 text-{{ $c }}-700 rounded-full capitalize">
                  {{ $p->status }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex gap-2 justify-end">
                  <a href="{{ route('admin.payroll.show', $p) }}"
                    class="text-blue-600 text-xs hover:underline">Detail</a>
                  <a href="{{ route('admin.payroll.payslip', $p) }}"
                    target="_blank"
                    class="text-purple-600 text-xs hover:underline">PDF</a>
                  @can('payroll.approve')
                    @if($p->status === 'draft')
                      <form method="POST" action="{{ route('admin.payroll.approve', $p) }}">
                        @csrf
                        <button type="submit" class="text-green-600 text-xs hover:underline">Approve</button>
                      </form>
                    @endif
                  @endcan
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-4 py-10 text-center text-slate-400">
                No payroll for this month.
                @can('payroll.generate')
                  <a href="{{ route('admin.payroll.generate') }}" class="text-purple-600 underline">Generate now</a>
                @endcan
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($payrolls->hasPages())
      <div class="px-4 py-3 border-t">{{ $payrolls->links() }}</div>
    @endif
  </div>
</div>
@endsection