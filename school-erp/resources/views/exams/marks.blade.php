@extends('layouts.app')
@section('title', 'Marks Entry')
@section('page_title', 'Marks Entry')

@section('content')
<div class="space-y-4 max-w-4xl">

  <!-- Exam info -->
  <div class="bg-white rounded-2xl border border-slate-200 p-5 grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach([
      ['Exam',    $exam->exam_name],
      ['Class',   'Class ' . $exam->schoolClass->name],
      ['Subject', $exam->subject->name],
      ['Date',    $exam->exam_date->format('d M Y')],
      ['Type',    $exam->examType->name],
      ['Max',     $exam->max_marks . ' marks'],
      ['Pass',    $exam->pass_marks . ' marks'],
      ['Students',$students->count()],
    ] as [$label, $val])
      <div>
        <p class="text-xs text-slate-400">{{ $label }}</p>
        <p class="text-sm font-semibold text-slate-700">{{ $val }}</p>
      </div>
    @endforeach
  </div>

  <form method="POST" action="{{ route('admin.exams.marks.store', $exam) }}">
    @csrf

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="px-5 py-3 bg-amber-50 border-b border-amber-200 flex items-center justify-between">
        <p class="text-sm font-medium text-amber-700">✍️ Enter marks for each student. Check "AB" for absent.</p>
        <button type="button" id="markAllPresent"
          class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
          ✓ All Present
        </button>
      </div>

      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 w-8">#</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500">Student</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500">Section</th>
            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 w-24">Absent</th>
            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 w-36">
              Marks (Max: {{ $exam->max_marks }})
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($students as $i => $student)
            @php
              $isAbsent  = in_array($student->id, $absentStudents);
              $prevMarks = $existingResults[$student->id] ?? null;
            @endphp
            <tr class="hover:bg-slate-50" x-data="{ absent: {{ $isAbsent ? 'true' : 'false' }} }">
              <td class="px-4 py-2 text-slate-400 text-xs">{{ $student->roll_number ?? $i+1 }}</td>
              <td class="px-4 py-2">
                <p class="font-medium text-slate-700">{{ $student->name }}</p>
                <p class="text-xs text-slate-400">{{ $student->admission_no }}</p>
              </td>
              <td class="px-4 py-2 text-slate-500 text-xs">{{ $student->section?->name ?? '—' }}</td>
              <td class="px-4 py-2 text-center">
                <input type="checkbox"
                  name="absent[{{ $student->id }}]"
                  value="1"
                  x-model="absent"
                  @checked($isAbsent)
                  class="rounded border-slate-300 text-red-500 focus:ring-red-400"/>
              </td>
              <td class="px-4 py-2">
                <input
                  type="number"
                  name="marks[{{ $student->id }}]"
                  :disabled="absent"
                  value="{{ $prevMarks }}"
                  min="0" max="{{ $exam->max_marks }}"
                  step="0.5"
                  :class="absent ? 'bg-slate-100 cursor-not-allowed' : 'bg-white'"
                  class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-sm text-center
                         focus:outline-none focus:ring-2 focus:ring-blue-400"/>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="flex gap-3">
      <button type="submit"
        class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl text-sm">
        💾 Save Marks
      </button>
      <a href="{{ route('admin.exams.results', $exam) }}"
        class="px-6 py-2.5 bg-slate-100 text-slate-600 font-medium rounded-xl text-sm hover:bg-slate-200">
        View Results
      </a>
    </div>
  </form>
</div>

@push('scripts')
<script>
  document.getElementById('markAllPresent')?.addEventListener('click', () => {
    document.querySelectorAll('input[name^="absent"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('input[name^="marks"]').forEach(i => i.disabled = false);
  });
</script>
@endpush
@endsection