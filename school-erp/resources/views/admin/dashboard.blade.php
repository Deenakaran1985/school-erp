@extends('layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

<!-- KPI Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

  <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <span class="text-2xl">🎓</span>
      <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Active</span>
    </div>
    <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['students']) }}</p>
    <p class="text-sm text-slate-400 mt-1">Total Students</p>
  </div>

  <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <span class="text-2xl">👩‍🏫</span>
      <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Active</span>
    </div>
    <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['staff']) }}</p>
    <p class="text-sm text-slate-400 mt-1">Staff Members</p>
  </div>

  <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <span class="text-2xl">💳</span>
      <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">This month</span>
    </div>
    <p class="text-3xl font-bold text-slate-800">₹{{ number_format($stats['fee_collected']) }}</p>
    <p class="text-sm text-slate-400 mt-1">Fees Collected</p>
  </div>

  <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <span class="text-2xl">⚠️</span>
      <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Overdue</span>
    </div>
    <p class="text-3xl font-bold text-slate-800">₹{{ number_format($stats['fee_pending']) }}</p>
    <p class="text-sm text-slate-400 mt-1">Fees Pending</p>
  </div>

</div>

<!-- Two column: recent payments + upcoming exams -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  <!-- Recent Payments -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-700">💳 Recent Fee Payments</h3>
      <a href="{{ route('admin.fees.collect') }}" class="text-xs text-blue-600 hover:underline">View all</a>
    </div>
    <div class="divide-y divide-slate-50">
      @forelse ($recentPayments as $payment)
        <div class="px-5 py-3 flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-700">{{ $payment->student->name }}</p>
            <p class="text-xs text-slate-400">
              {{ $payment->feeStructure->fee_head }} ·
              {{ $payment->payment_date?->format('d M Y') }}
            </p>
          </div>
          <span class="text-sm font-semibold text-green-600">₹{{ number_format($payment->amount_paid) }}</span>
        </div>
      @empty
        <div class="px-5 py-6 text-center text-slate-400 text-sm">No payments yet this month.</div>
      @endforelse
    </div>
  </div>

  <!-- Upcoming Exams -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-700">📋 Upcoming Exams</h3>
      <a href="{{ route('admin.exams.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
    </div>
    <div class="divide-y divide-slate-50">
      @forelse ($upcomingExams as $exam)
        <div class="px-5 py-3 flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-700">
              {{ $exam->subject->name }} — Class {{ $exam->schoolClass->name }}
            </p>
            <p class="text-xs text-slate-400">
              {{ $exam->examType->name }} · {{ $exam->exam_date->format('d M Y') }}
            </p>
          </div>
          <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
            {{ $exam->max_marks }} marks
          </span>
        </div>
      @empty
        <div class="px-5 py-6 text-center text-slate-400 text-sm">No upcoming exams.</div>
      @endforelse
    </div>
  </div>

</div>
@endsection