@extends('layouts.app')
@section('title','Expenses')
@section('page_title','Expense Management')

@section('content')
<div class="space-y-5">

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h3 class="text-lg font-semibold text-slate-700">Expenses</h3>
      @if($pendingCount > 0)
        <p class="text-sm text-amber-500 font-medium">⏳ {{ $pendingCount }} pending approval</p>
      @endif
    </div>
    <div class="flex gap-2">
      <div class="bg-white rounded-xl border border-slate-200 px-4 py-2 text-sm">
        This month: <strong class="text-blue-600">₹{{ number_format($monthlyTotal) }}</strong>
      </div>
      @can('expense.create')
        <a href="{{ route('admin.expenses.create') }}"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">
          + Add Expense
        </a>
      @endcan
    </div>
  </div>

  <!-- Filters -->
  <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-wrap gap-3">
    <select name="head_id" class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50">
      <option value="">All Categories</option>
      @foreach($heads as $h)
        <option value="{{ $h->id }}" @selected(request('head_id')==$h->id)>{{ $h->name }}</option>
      @endforeach
    </select>
    <select name="status" class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50">
      <option value="">All Status</option>
      @foreach(['pending','approved','rejected'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <input type="date" name="from" value="{{ request('from') }}"
      class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
    <input type="date" name="to" value="{{ request('to') }}"
      class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-xl">Filter</button>
    <a href="{{ route('admin.expenses.index') }}"
      class="px-4 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl">Reset</a>
  </form>

  <!-- Table -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          @foreach(['Title','Category','Amount','Date','Submitted By','Status',''] as $h)
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($expenses as $exp)
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3">
              <p class="font-medium text-slate-700">{{ $exp->title }}</p>
              @if($exp->vendor_name)
                <p class="text-xs text-slate-400">{{ $exp->vendor_name }}</p>
              @endif
            </td>
            <td class="px-4 py-3 text-slate-500">{{ $exp->expenseHead->name }}</td>
            <td class="px-4 py-3 font-semibold text-slate-700">₹{{ number_format($exp->amount) }}</td>
            <td class="px-4 py-3 text-slate-500">{{ $exp->expense_date->format('d M Y') }}</td>
            <td class="px-4 py-3 text-slate-500 text-xs">{{ $exp->createdBy->name }}</td>
            <td class="px-4 py-3">
              @php
                $c = match($exp->status){'approved'=>'green','rejected'=>'red',default=>'amber'};
              @endphp
              <span class="px-2 py-0.5 text-xs bg-{{ $c }}-100 text-{{ $c }}-700 rounded-full capitalize">
                {{ $exp->status }}
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex gap-2 justify-end items-center">
                @if($exp->attachment)
                  <a href="{{ asset('storage/'.$exp->attachment) }}"
                    target="_blank" class="text-blue-600 text-xs hover:underline">📎 Bill</a>
                @endif
                @can('expense.approve')
                  @if($exp->status === 'pending')
                    <form method="POST" action="{{ route('admin.expenses.approve', $exp) }}">
                      @csrf
                      <button type="submit"
                        class="text-green-600 text-xs hover:underline">✅ Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.expenses.reject', $exp) }}">
                      @csrf
                      <button type="submit"
                        class="text-red-500 text-xs hover:underline">Reject</button>
                    </form>
                  @endif
                @endcan
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">No expenses found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($expenses->hasPages())
      <div class="px-4 py-3 border-t">{{ $expenses->links() }}</div>
    @endif
  </div>
</div>
@endsection