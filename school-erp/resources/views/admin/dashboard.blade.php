@extends('layouts.app')
@section('title', 'Dashboard — School ERP')
@section('page_title', 'Dashboard')

@section('content')

{{-- Greeting --}}
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-slate-800">
      Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
      {{ explode(' ', auth()->user()->name ?? 'Admin')[0] }} 👋
    </h1>
    <p class="text-sm text-slate-400 mt-0.5">Here's what's happening at your school today.</p>
  </div>
  <a href="{{ route('admin.students.create') }}" class="btn-primary hidden sm:inline-flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
    </svg>
    Add Student
  </a>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

  <div class="card kpi-card kpi-brand card-hover p-5">
    <div class="flex items-start justify-between mb-4">
      <div class="w-10 h-10 rounded-xl gradient-brand flex items-center justify-center" style="box-shadow:0 4px 10px rgba(99,102,241,0.3)">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
      </div>
      <span class="badge badge-blue">Active</span>
    </div>
    <p class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($stats['students']) }}</p>
    <p class="text-sm text-slate-400 mt-1 font-medium">Total Students</p>
    <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-1 text-xs text-indigo-500 font-semibold mt-2 hover:gap-2 transition-all">
      View all <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
  </div>

  <div class="card kpi-card kpi-success card-hover p-5">
    <div class="flex items-start justify-between mb-4">
      <div class="w-10 h-10 rounded-xl gradient-success flex items-center justify-center" style="box-shadow:0 4px 10px rgba(16,185,129,0.3)">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </div>
      <span class="badge badge-green">Active</span>
    </div>
    <p class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($stats['staff']) }}</p>
    <p class="text-sm text-slate-400 mt-1 font-medium">Staff Members</p>
    <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-1 text-xs text-emerald-500 font-semibold mt-2 hover:gap-2 transition-all">
      View all <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
  </div>

  <div class="card kpi-card kpi-sky card-hover p-5">
    <div class="flex items-start justify-between mb-4">
      <div class="w-10 h-10 rounded-xl gradient-sky flex items-center justify-center" style="box-shadow:0 4px 10px rgba(6,182,212,0.3)">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
      </div>
      <span class="badge badge-sky">This month</span>
    </div>
    <p class="text-2xl font-extrabold text-slate-800 tracking-tight">₹{{ number_format($stats['fee_collected']) }}</p>
    <p class="text-sm text-slate-400 mt-1 font-medium">Fees Collected</p>
    <a href="{{ route('admin.fees.collect') }}" class="inline-flex items-center gap-1 text-xs text-cyan-500 font-semibold mt-2 hover:gap-2 transition-all">
      Collect fees <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
  </div>

  <div class="card kpi-card kpi-danger card-hover p-5">
    <div class="flex items-start justify-between mb-4">
      <div class="w-10 h-10 rounded-xl gradient-danger flex items-center justify-center" style="box-shadow:0 4px 10px rgba(244,63,94,0.3)">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
      </div>
      <span class="badge badge-red">Overdue</span>
    </div>
    <p class="text-2xl font-extrabold text-slate-800 tracking-tight">₹{{ number_format($stats['fee_pending']) }}</p>
    <p class="text-sm text-slate-400 mt-1 font-medium">Fees Pending</p>
    <a href="{{ route('admin.fees.collect') }}" class="inline-flex items-center gap-1 text-xs text-rose-500 font-semibold mt-2 hover:gap-2 transition-all">
      View pending <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
  </div>

</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
  @php
    $actions = [
      ['label'=>'Collect Fee',  'route'=>'admin.fees.collect',    'icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'color'=>'#6366F1'],
      ['label'=>'Add Exam',     'route'=>'admin.exams.create',     'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 8h6m-6 4h6', 'color'=>'#10B981'],
      ['label'=>'Add Staff',    'route'=>'admin.staff.create',     'icon'=>'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'color'=>'#06B6D4'],
      ['label'=>'Run Payroll',  'route'=>'admin.payroll.generate', 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color'=>'#F59E0B'],
    ];
  @endphp
  @foreach($actions as $act)
    <a href="{{ route($act['route']) }}"
      class="card card-hover flex items-center gap-3 px-4 py-3.5 group">
      <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110"
        style="background:{{ $act['color'] }}18;">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:{{ $act['color'] }}">
          <path stroke-linecap="round" stroke-linejoin="round" d="{{ $act['icon'] }}"/>
        </svg>
      </div>
      <span class="text-sm font-semibold text-slate-700">{{ $act['label'] }}</span>
    </a>
  @endforeach
</div>

{{-- Two columns --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

  {{-- Recent Payments --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid #F1F5F9">
      <div class="flex items-center gap-2">
        <div class="w-7 h-7 rounded-lg gradient-sky flex items-center justify-center">
          <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
        </div>
        <h3 class="font-bold text-slate-800 text-sm">Recent Fee Payments</h3>
      </div>
      <a href="{{ route('admin.fees.collect') }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700 transition">View all →</a>
    </div>
    <div>
      @forelse ($recentPayments as $payment)
        <div class="px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/60 transition" style="border-bottom:1px solid #F8FAFC">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
              <span class="text-xs font-bold text-indigo-500">{{ mb_substr($payment->student->name ?? '?', 0, 1) }}</span>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-700">{{ $payment->student->name }}</p>
              <p class="text-xs text-slate-400">{{ $payment->feeStructure->fee_head ?? '—' }} · {{ $payment->payment_date?->format('d M') }}</p>
            </div>
          </div>
          <span class="text-sm font-bold text-emerald-600">₹{{ number_format($payment->amount_paid) }}</span>
        </div>
      @empty
        <div class="px-5 py-10 text-center">
          <svg class="w-10 h-10 text-slate-200 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          <p class="text-sm text-slate-400">No payments yet this month</p>
        </div>
      @endforelse
    </div>
  </div>

  {{-- Upcoming Exams --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid #F1F5F9">
      <div class="flex items-center gap-2">
        <div class="w-7 h-7 rounded-lg gradient-brand flex items-center justify-center">
          <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
        </div>
        <h3 class="font-bold text-slate-800 text-sm">Upcoming Exams</h3>
      </div>
      <a href="{{ route('admin.exams.index') }}" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700 transition">View all →</a>
    </div>
    <div>
      @forelse ($upcomingExams as $exam)
        <div class="px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/60 transition" style="border-bottom:1px solid #F8FAFC">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-700">{{ $exam->subject->name }} — Class {{ $exam->schoolClass->name }}</p>
              <p class="text-xs text-slate-400">{{ $exam->examType->name }} · {{ $exam->exam_date->format('d M Y') }}</p>
            </div>
          </div>
          <span class="badge badge-violet">{{ $exam->max_marks }}m</span>
        </div>
      @empty
        <div class="px-5 py-10 text-center">
          <svg class="w-10 h-10 text-slate-200 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <p class="text-sm text-slate-400">No upcoming exams scheduled</p>
        </div>
      @endforelse
    </div>
  </div>

</div>
@endsection