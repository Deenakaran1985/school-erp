@extends('layouts.app')
@section('title', 'Results')
@section('page_title', 'Exam Results')

@section('content')
<div class="space-y-5 max-w-5xl">

  <!-- Header + Publish button -->
  <div class="flex flex-wrap gap-3 items-start justify-between">
    <div>
      <h2 class="text-lg font-bold text-slate-800">{{ $exam->exam_name }}</h2>
      <p class="text-sm text-slate-400">
        {{ $exam->examType->name }} · Class {{ $exam->schoolClass->name }} ·
        {{ $exam->subject->name }} · {{ $exam->exam_date->format('d M Y') }}
      </p>
    </div>
    <div class="flex gap-2">
      @if($exam->status !== 'published')
        @can('exam.marks.entry')
          <a href="{{ route('admin.exams.marks.index', $exam) }}"
            class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 text-sm font-medium rounded-xl">
            ✍️ Edit Marks
          </a>
        @endcan
        @can('exam.results.publish')
          <form method="POST" action="{{ route('admin.exams.publish', $exam) }}"
            onsubmit="return confirm('Publish results and send FCM notifications to all parents?')">
            @csrf
            <button type="submit"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl">
              🚀 Publish & Notify Parents
            </button>
          </form>
        @endcan
      @else
        <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-xl">
          ✅ Published {{ $exam->published_at?->format('d M Y, h:i A') }}
        </span>
        @can('exam.results.publish')
          <form method="POST" action="{{ route('admin.exams.resend', $exam) }}">
            @csrf
            <button type="submit"
              class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-xl">
              🔔 Re-send Notifications
            </button>
          </form>
        @endcan
      @endif
    </div>
  </div>

  <!-- Summary cards -->
  <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
    @foreach([
      ['Total',   $summary['total'],        'slate'],
      ['Appeared',$summary['appeared'],     'blue'],
      ['Absent',  $summary['absent'],       'red'],
      ['Passed',  $summary['passed'],       'green'],
      ['Failed',  $summary['failed'],       'red'],
      ['Pass %',  $summary['pass_percent'].'%', 'emerald'],
      ['Avg',     $summary['average'],      'purple'],
      ['Highest', $summary['highest'],      'amber'],
    ] as [$label, $val, $color])
      <div class="bg-white rounded-xl border border-slate-200 p-3 text-center">
        <p class="text-lg font-bold text-{{ $color }}-600">{{ $val }}</p>
        <p class="text-xs text-slate-400">{{ $label }}</p>
      </div>
    @endforeach
  </div>

  <!-- Results table -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          @foreach(['Rank','Student','Section','Marks','%','Grade','GP','Result','Notified'] as $h)
            <th class="text-left px-3 py-3 text-xs font-semibold text-slate-500 uppercase">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($results as $result)
          <tr class="hover:bg-slate-50 {{ !$result->is_absent && !$result->is_pass ? 'bg-red-50/50' : '' }}">
            <td class="px-3 py-2.5 font-mono text-slate-400 text-xs">
              {{ $result->is_absent ? '—' : ($result->rank ?? '—') }}
            </td>
            <td class="px-3 py-2.5">
              <p class="font-medium text-slate-700">{{ $result->student->name }}</p>
              <p class="text-xs text-slate-400">{{ $result->student->admission_no }}</p>
            </td>
            <td class="px-3 py-2.5 text-slate-500 text-xs">{{ $result->student->section?->name ?? '—' }}</td>
            <td class="px-3 py-2.5 font-mono">
              {{ $result->is_absent ? 'AB' : ($result->marks_obtained . '/' . $exam->max_marks) }}
            </td>
            <td class="px-3 py-2.5 font-mono text-slate-600">{{ $result->percentage ? number_format($result->percentage,1).'%' : '—' }}</td>
            <td class="px-3 py-2.5 font-bold text-lg"
              style="color:{{ match($result->grade) { 'A+','A' => '#16a34a', 'F' => '#ef4444', default => '#2563eb' } }}">
              {{ $result->grade ?? '—' }}
            </td>
            <td class="px-3 py-2.5 text-slate-600 text-xs font-mono">{{ $result->grade_point ?? '—' }}</td>
            <td class="px-3 py-2.5">
              @if($result->is_absent)
                <span class="px-2 py-0.5 text-xs bg-slate-100 text-slate-500 rounded-full">Absent</span>
              @elseif($result->is_pass)
                <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">✅ Pass</span>
              @else
                <span class="px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">❌ Fail</span>
              @endif
            </td>
            <td class="px-3 py-2.5 text-xs">
              {{ $result->notified_at ? '✅ ' . $result->notified_at->format('d M, h:i A') : '⏳ Pending' }}
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">
            No marks entered yet.
            @can('exam.marks.entry')
              <a href="{{ route('admin.exams.marks.index', $exam) }}" class="text-blue-600 underline">Enter marks now</a>
            @endcan
          </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection