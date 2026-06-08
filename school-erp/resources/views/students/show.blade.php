@extends('layouts.app')
@section('title', $student->name)
@section('page_title', 'Student Profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <!-- LEFT: Profile card -->
  <div class="space-y-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center">
      <img src="{{ $student->photo_url }}"
        class="w-24 h-24 rounded-full object-cover mx-auto mb-4 border-4 border-slate-100">
      <h2 class="text-xl font-bold text-slate-800">{{ $student->name }}</h2>
      <p class="text-sm text-slate-400">{{ $student->admission_no }}</p>
      <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
        {{ $student->class_section }}
      </span>

      <div class="mt-4 grid grid-cols-3 gap-2 text-center">
        <div class="bg-green-50 rounded-xl p-2">
          <p class="text-lg font-bold text-green-600">{{ $attSummary['present'] }}</p>
          <p class="text-xs text-slate-400">Present</p>
        </div>
        <div class="bg-red-50 rounded-xl p-2">
          <p class="text-lg font-bold text-red-500">{{ $attSummary['absent'] }}</p>
          <p class="text-xs text-slate-400">Absent</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-2">
          <p class="text-lg font-bold text-amber-500">{{ $attSummary['late'] }}</p>
          <p class="text-xs text-slate-400">Late</p>
        </div>
      </div>

      @can('student.edit')
        <a href="{{ route('admin.students.edit', $student) }}"
          class="mt-4 w-full block py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700">
          ✏️ Edit Profile
        </a>
      @endcan
    </div>

    <!-- Details -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
      @foreach([
        ['Father', $student->father_name],
        ['Mother', $student->mother_name ?? '—'],
        ['DOB', $student->date_of_birth->format('d M Y') . ' (' . $student->age . ' yrs)'],
        ['Gender', $student->gender === 'M' ? 'Male' : ('F' ? 'Female' : 'Other')],
        ['Community', $student->community ?? '—'],
        ['Blood', $student->blood_group ?? '—'],
        ['Mobile', $student->parent_mobile],
        ['EMIS No', $student->emis_number ?? '—'],
        ['Aadhar', $student->aadhar_number ? 'XXXX-XXXX-' . substr($student->aadhar_number,-4) : '—'],
      ] as [$label, $value])
        <div class="flex justify-between text-sm">
          <span class="text-slate-400 font-medium">{{ $label }}</span>
          <span class="text-slate-700 text-right">{{ $value }}</span>
        </div>
      @endforeach
    </div>
  </div>

  <!-- RIGHT: Tabs (Fees, Results, Homework) -->
  <div class="lg:col-span-2 space-y-4" x-data="{ tab: 'fees' }">

    <!-- Tab bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1 flex gap-1">
      @foreach(['fees' => '💳 Fees', 'results' => '📊 Results', 'transport' => '🚌 Transport'] as $key => $label)
        <button @click="tab = '{{ $key }}'"
          :class="tab === '{{ $key }}' ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-50'"
          class="flex-1 py-2 px-3 text-sm font-medium rounded-xl transition-colors">
          {{ $label }}
        </button>
      @endforeach
    </div>

    <!-- Fees tab -->
    <div x-show="tab === 'fees'" class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-semibold text-slate-700">Fee Payments</h3>
        <span class="text-sm text-red-500 font-medium">Pending: ₹{{ number_format($student->pending_fee) }}</span>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            @foreach(['Fee Head','Amount','Mode','Date','Status'] as $h)
              <th class="text-left px-4 py-2 text-xs font-semibold text-slate-500">{{ $h }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($student->feePayments as $fp)
            <tr>
              <td class="px-4 py-2">{{ $fp->feeStructure->fee_head }}</td>
              <td class="px-4 py-2 font-medium">₹{{ number_format($fp->amount_paid) }}</td>
              <td class="px-4 py-2 capitalize">{{ $fp->payment_mode }}</td>
              <td class="px-4 py-2 text-slate-400">{{ $fp->payment_date?->format('d M Y') ?? '—' }}</td>
              <td class="px-4 py-2">
                <span class="px-2 py-0.5 text-xs rounded-full {{ $fp->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                  {{ ucfirst($fp->status) }}
                </span>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No fee records.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Results tab -->
    <div x-show="tab === 'results'" class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-700">Exam Results</h3>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            @foreach(['Exam','Subject','Marks','Grade','Result'] as $h)
              <th class="text-left px-4 py-2 text-xs font-semibold text-slate-500">{{ $h }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($student->examResults as $er)
            <tr>
              <td class="px-4 py-2 text-xs text-slate-500">{{ $er->exam->examType->code }}</td>
              <td class="px-4 py-2">{{ $er->exam->subject->name }}</td>
              <td class="px-4 py-2 font-mono">
                {{ $er->is_absent ? 'AB' : ($er->marks_obtained . '/' . $er->exam->max_marks) }}
              </td>
              <td class="px-4 py-2 font-bold"
                style="color: {{ $er->grade === 'F' ? '#ef4444' : ($er->grade === 'A+' ? '#16a34a' : '#2563eb') }}">
                {{ $er->grade ?? '—' }}
              </td>
              <td class="px-4 py-2">
                <span class="px-2 py-0.5 text-xs rounded-full {{ $er->is_pass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                  {{ $er->status_label }}
                </span>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No results yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>
</div>
@endsection