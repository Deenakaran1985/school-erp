@extends('layouts.app')
@section('title', $student->name . ' — Fees')
@section('page_title', 'Fee Summary')

@section('content')
<div class="max-w-3xl space-y-5">

  <!-- Student info + summary -->
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    <div class="flex items-center gap-4 mb-5">
      <img src="{{ $student->photo_url }}" class="w-14 h-14 rounded-full object-cover">
      <div>
        <p class="text-lg font-bold text-slate-800">{{ $student->name }}</p>
        <p class="text-sm text-slate-400">{{ $student->admission_no }} · Class {{ $student->class_section }}</p>
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      @foreach([
        ['Total Fee',   '₹'.number_format($totalDue),     'blue'],
        ['Paid',        '₹'.number_format($totalPaid),    'green'],
        ['Pending',     '₹'.number_format($totalPending), $totalPending > 0 ? 'red' : 'green'],
      ] as [$label, $val, $color])
        <div class="bg-{{ $color }}-50 rounded-xl p-3 text-center">
          <p class="text-xl font-bold text-{{ $color }}-600">{{ $val }}</p>
          <p class="text-xs text-slate-400">{{ $label }}</p>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Fee heads table -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center">
      <h3 class="font-semibold text-slate-700">Fee Details — {{ $student->academicYear->name }}</h3>
      <a href="{{ route('admin.fees.collect') }}?search={{ $student->admission_no }}"
        class="px-4 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg">
        + Collect Fee
      </a>
    </div>
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b">
        <tr>
          @foreach(['Fee Head','Amount','Term','Status','Paid On','Receipt'] as $h)
            <th class="text-left px-4 py-2 text-xs font-semibold text-slate-500">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($feeStatus as $row)
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-2.5 font-medium text-slate-700">{{ $row['structure']->fee_head }}</td>
            <td class="px-4 py-2.5 font-semibold text-slate-700">₹{{ number_format($row['structure']->amount) }}</td>
            <td class="px-4 py-2.5 text-slate-500 capitalize">{{ str_replace('_',' ',$row['structure']->term) }}</td>
            <td class="px-4 py-2.5">
              @if($row['paid'])
                <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">✅ Paid</span>
              @else
                <span class="px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">⏳ Pending</span>
              @endif
            </td>
            <td class="px-4 py-2.5 text-slate-400 text-xs">
              {{ $row['payment']?->payment_date?->format('d M Y') ?? '—' }}
            </td>
            <td class="px-4 py-2.5">
              @if($row['payment'] && $row['paid'])
                <a href="{{ route('admin.fees.receipt', $row['payment']) }}"
                  target="_blank"
                  class="text-xs text-blue-600 hover:underline">
                  🖨 {{ $row['payment']->receipt_no }}
                </a>
              @else
                <span class="text-slate-300 text-xs">—</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection