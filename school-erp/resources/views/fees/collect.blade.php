@extends('layouts.app')
@section('title', 'Fee Collection')
@section('page_title', 'Fee Collection')

@section('content')
<div class="max-w-4xl space-y-5">

  <!-- Student search -->
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    <h3 class="font-semibold text-slate-700 mb-3">🔍 Find Student</h3>
    <form method="GET" class="flex gap-3">
      <input name="search" value="{{ request('search') }}"
        placeholder="Admission No / EMIS No / Mobile / Name"
        class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"/>
      <button type="submit"
        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm">
        Search
      </button>
    </form>
  </div>

  @if($student)
    <!-- Student card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
      <img src="{{ $student->photo_url }}"
        class="w-14 h-14 rounded-full object-cover border-2 border-slate-100">
      <div class="flex-1">
        <p class="text-lg font-bold text-slate-800">{{ $student->name }}</p>
        <p class="text-sm text-slate-400">{{ $student->admission_no }} · Class {{ $student->class_section }}</p>
        <p class="text-sm text-slate-400">Father: {{ $student->father_name }} · Mobile: {{ $student->parent_mobile }}</p>
      </div>
      <a href="{{ route('admin.fees.student', $student) }}"
        class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200">
        Full Fee Summary →
      </a>
    </div>

    <!-- Collection form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6"
      x-data="{ mode: 'cash', structureAmount: 0 }">
      <h3 class="font-semibold text-slate-700 mb-4">💳 Record Payment</h3>

      <form method="POST" action="{{ route('admin.fees.collect.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="student_id" value="{{ $student->id }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Fee Head *</label>
            <select name="fee_structure_id" required
              @change="structureAmount = $event.target.selectedOptions[0]?.dataset?.amount ?? 0"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
              <option value="">Select fee head</option>
              @foreach(\App\Models\FeeStructure::where('school_class_id', $student->school_class_id)
                ->where('academic_year_id', \App\Models\AcademicYear::current()->id)
                ->get() as $fs)
                <option value="{{ $fs->id }}" data-amount="{{ $fs->amount }}">
                  {{ $fs->fee_head }} — ₹{{ number_format($fs->amount) }} ({{ ucfirst($fs->term) }})
                </option>
              @endforeach
            </select>
            @error('fee_structure_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Amount Paid (₹) *</label>
            <input type="number" name="amount_paid"
              :value="structureAmount"
              min="1" step="0.01" required
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
            @error('amount_paid')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Payment Mode *</label>
            <select name="payment_mode" x-model="mode" required
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
              <option value="cash">Cash</option>
              <option value="cheque">Cheque</option>
              <option value="dd">Demand Draft</option>
              <option value="bank_transfer">Bank Transfer / NEFT</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Payment Date *</label>
            <input type="date" name="payment_date"
              value="{{ now()->format('Y-m-d') }}" required
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
          </div>

          <!-- Cheque fields -->
          <div x-show="mode === 'cheque' || mode === 'dd'">
            <label class="block text-sm font-medium text-slate-600 mb-1">Cheque / DD No.</label>
            <input type="text" name="cheque_no"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
          </div>
          <div x-show="mode === 'cheque' || mode === 'dd' || mode === 'bank_transfer'">
            <label class="block text-sm font-medium text-slate-600 mb-1">Bank Name</label>
            <input type="text" name="bank_name"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Discount (₹)</label>
            <input type="number" name="discount" value="0" min="0"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Fine (₹)</label>
            <input type="number" name="fine" value="0" min="0"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-600 mb-1">Notes</label>
            <input type="text" name="notes"
              placeholder="Optional remarks"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
          </div>
        </div>

        <button type="submit"
          class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl text-sm">
          ✅ Collect & Generate Receipt
        </button>
      </form>
    </div>

  @elseif(request('search'))
    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400">
      No student found for "{{ request('search') }}". Try admission no, EMIS no, or mobile.
    </div>
  @endif

</div>
@endsection