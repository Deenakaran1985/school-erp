@extends('layouts.app')
@section('title', 'Exam Results')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="resultsPortal()" x-init="init()">
  <div class="flex items-center gap-3">
    <a href="{{ route('parent.home') }}" class="text-slate-400 hover:text-slate-600">← Back</a>
    <h3 class="text-lg font-semibold text-slate-700">Exam Results</h3>
  </div>

  <div x-show="loading" class="text-center py-10 text-slate-400">Loading results…</div>

  <template x-if="!loading && data">
    <div class="space-y-4">
      <!-- Student Info -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg">
          <span x-text="data.student.name.charAt(0)"></span>
        </div>
        <div>
          <h4 class="font-semibold text-slate-800" x-text="data.student.name"></h4>
          <p class="text-xs text-slate-400" x-text="`Class ${data.student.class} · Roll ${data.student.roll_number}`"></p>
        </div>
      </div>

      <!-- Results by exam type -->
      <template x-for="group in data.data" :key="group.exam_type">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
          <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h4 class="font-semibold text-slate-700" x-text="group.exam_type"></h4>
            <div class="flex items-center gap-3">
              <span class="text-sm font-bold text-blue-600" x-text="`${group.percentage}%`"></span>
              <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                :class="group.passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                x-text="group.passed ? 'PASS' : 'FAIL'"></span>
            </div>
          </div>
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs">
              <tr>
                <th class="px-4 py-2 text-left">Subject</th>
                <th class="px-4 py-2 text-center">Marks</th>
                <th class="px-4 py-2 text-center">Grade</th>
                <th class="px-4 py-2 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <template x-for="sub in group.subjects" :key="sub.subject">
                <tr>
                  <td class="px-4 py-3 text-slate-700" x-text="sub.subject"></td>
                  <td class="px-4 py-3 text-center font-medium text-slate-800"
                    x-text="sub.absent ? 'AB' : `${sub.marks_obtained}/${sub.max_marks}`"></td>
                  <td class="px-4 py-3 text-center font-bold text-blue-600" x-text="sub.grade ?? '—'"></td>
                  <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs"
                      :class="sub.passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                      x-text="sub.passed ? 'Pass' : 'Fail'"></span>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </template>
    </div>
  </template>
</div>

@push('scripts')
<script>
function resultsPortal() {
  return {
    loading: true,
    data: null,
    async init() {
      const res  = await fetch('/api/results', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
      const json = await res.json();
      this.data    = json.success ? json : null;
      this.loading = false;
    }
  }
}
</script>
@endpush
@endsection
