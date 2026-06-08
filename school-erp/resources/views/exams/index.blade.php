@extends('layouts.app')
@section('title', 'Exams')
@section('page_title', 'Exams')

@section('content')
<div class="space-y-4">

  <div class="flex items-center justify-between">
    <div>
      <h3 class="text-lg font-semibold text-slate-700">Exam Schedule — {{ $year->name }}</h3>
      <p class="text-sm text-slate-400">{{ $exams->total() }} exams total</p>
    </div>
    @can('exam.create')
      <a href="{{ route('admin.exams.create') }}"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">
        + Schedule Exam
      </a>
    @endcan
  </div>

  <!-- Filters -->
  <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-wrap gap-3">
    <select name="class_id"
      class="px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl">
      <option value="">All Classes</option>
      @foreach($classes as $class)
        <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>Class {{ $class->name }}</option>
      @endforeach
    </select>
    <select name="exam_type"
      class="px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl">
      <option value="">All Types</option>
      @foreach($examTypes as $et)
        <option value="{{ $et->id }}" @selected(request('exam_type') == $et->id)>{{ $et->name }}</option>
      @endforeach
    </select>
    <select name="status"
      class="px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl">
      <option value="">All Status</option>
      @foreach(['scheduled','marks_entry','published','cancelled'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-xl">Filter</button>
    <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl">Reset</a>
  </form>

  <!-- Table -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          @foreach(['Exam Name','Type','Class','Subject','Date','Marks','Status',''] as $h)
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($exams as $exam)
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 font-medium text-slate-700">{{ $exam->exam_name }}</td>
            <td class="px-4 py-3">
              <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">
                {{ $exam->examType->code }}
              </span>
            </td>
            <td class="px-4 py-3 text-slate-600">Class {{ $exam->schoolClass->name }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $exam->subject->name }}</td>
            <td class="px-4 py-3 text-slate-600">{{ $exam->exam_date->format('d M Y') }}</td>
            <td class="px-4 py-3 font-mono text-slate-600">{{ $exam->pass_marks }}/{{ $exam->max_marks }}</td>
            <td class="px-4 py-3">
              @php
                $colors = ['scheduled'=>'blue','marks_entry'=>'amber','published'=>'green','cancelled'=>'red'];
                $c = $colors[$exam->status] ?? 'slate';
              @endphp
              <span class="px-2 py-0.5 text-xs bg-{{ $c }}-100 text-{{ $c }}-700 rounded-full capitalize">
                {{ str_replace('_',' ',$exam->status) }}
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex gap-2 justify-end">
                @if($exam->status !== 'published' && $exam->status !== 'cancelled')
                  @can('exam.marks.entry')
                    <a href="{{ route('admin.exams.marks.index', $exam) }}"
                      class="text-amber-600 hover:text-amber-800 text-xs font-medium">
                      Enter Marks
                    </a>
                  @endcan
                @endif
                <a href="{{ route('admin.exams.results', $exam) }}"
                  class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                  Results
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">No exams scheduled yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($exams->hasPages())
      <div class="px-4 py-3 border-t">{{ $exams->links() }}</div>
    @endif
  </div>
</div>
@endsection