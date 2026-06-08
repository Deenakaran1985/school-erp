@extends('layouts.app')
@section('title', 'Fee Structures')
@section('page_title', 'Fee Structures')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <!-- Add Form -->
  <div class="lg:col-span-1">
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
      <h3 class="font-semibold text-slate-700 mb-4">➕ Add Fee Head</h3>
      <form method="POST" action="{{ route('admin.fees.structures.store') }}" class="space-y-3">
        @csrf
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Academic Year *</label>
          <select name="academic_year_id" required
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
            @foreach(\App\Models\AcademicYear::orderByDesc('start_date')->get() as $yr)
              <option value="{{ $yr->id }}" @selected($yr->is_current)>{{ $yr->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Class *</label>
          <select name="school_class_id" required
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
            <option value="">Select Class</option>
            @foreach($classes as $class)
              <option value="{{ $class->id }}">Class {{ $class->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fee Head *</label>
          <input type="text" name="fee_head" required
            placeholder="e.g. Tuition Fee, Transport, Lab"
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Amount (₹) *</label>
            <input type="number" name="amount" min="0" step="0.01" required
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Term *</label>
            <select name="term" required
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
              @foreach(['term1'=>'Term 1','term2'=>'Term 2','term3'=>'Term 3','annual'=>'Annual','monthly'=>'Monthly'] as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Due Date</label>
          <input type="date" name="due_date"
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        </div>
        <button type="submit"
          class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm">
          Add Fee Head
        </button>
      </form>
    </div>
  </div>

  <!-- Existing Structures -->
  <div class="lg:col-span-2 space-y-4">
    @forelse($structures as $classId => $feeList)
      @php $cls = $feeList->first()->schoolClass; @endphp
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
          <h3 class="font-semibold text-slate-700">Class {{ $cls->name }}</h3>
          <span class="text-sm font-semibold text-blue-600">
            Total: ₹{{ number_format($feeList->sum('amount')) }}
          </span>
        </div>
        <table class="w-full text-sm">
          <thead class="border-b border-slate-100">
            <tr>
              @foreach(['Fee Head','Amount','Term','Due Date',''] as $h)
                <th class="text-left px-4 py-2 text-xs font-semibold text-slate-500">{{ $h }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            @foreach($feeList as $fee)
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-2 font-medium text-slate-700">{{ $fee->fee_head }}</td>
                <td class="px-4 py-2 text-green-600 font-semibold">₹{{ number_format($fee->amount) }}</td>
                <td class="px-4 py-2 capitalize text-slate-500">{{ str_replace('_',' ',$fee->term) }}</td>
                <td class="px-4 py-2 text-slate-400 text-xs">{{ $fee->due_date?->format('d M Y') ?? '—' }}</td>
                <td class="px-4 py-2 text-right">
                  @can('fee.structure.manage')
                    <form method="POST" action="{{ route('admin.fees.structures.destroy', $fee) }}"
                      onsubmit="return confirm('Delete this fee head?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                    </form>
                  @endcan
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @empty
      <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400">
        No fee structures defined yet. Add one from the form.
      </div>
    @endforelse
  </div>
</div>
@endsection