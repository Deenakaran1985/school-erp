@extends('layouts.app')
@section('title', 'My Results')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="myResults()" x-init="init()">
  <div class="flex items-center gap-3">
    <a href="{{ route('student.home') }}" class="text-slate-400 hover:text-slate-600">← Back</a>
    <h3 class="text-lg font-semibold text-slate-700">My Exam Results</h3>
  </div>

  <div x-show="loading" class="text-center py-10 text-slate-400">Loading results…</div>

  <template x-if="!loading">
    <div class="space-y-4">
      <template x-for="group in results" :key="group.exam_type">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
          <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50">
            <h4 class="font-semibold text-slate-700" x-text="group.exam_type"></h4>
            <div class="flex items-center gap-3">
              <span class="text-sm font-bold" :class="group.percentage >= 75 ? 'text-green-600' : group.percentage >= 35 ? 'text-amber-500' : 'text-red-500'"
                x-text="`${group.percentage}%`"></span>
              <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                :class="group.passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                x-text="group.passed ? 'PASS' : 'FAIL'"></span>
            </div>
          </div>
          <table class="min-w-full text-sm">
            <thead class="text-slate-500 text-xs uppercase">
              <tr class="border-b border-slate-100">
                <th class="px-4 py-2 text-left">Subject</th>
                <th class="px-4 py-2 text-center">Marks</th>
                <th class="px-4 py-2 text-center">Grade</th>
                <th class="px-4 py-2 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <template x-for="s in group.subjects" :key="s.subject">
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3 font-medium text-slate-700" x-text="s.subject"></td>
                  <td class="px-4 py-3 text-center text-slate-800" x-text="s.absent ? 'AB' : `${s.marks_obtained}/${s.max_marks}`"></td>
                  <td class="px-4 py-3 text-center font-bold text-blue-600" x-text="s.grade ?? '—'"></td>
                  <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs"
                      :class="s.passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                      x-text="s.passed ? 'Pass' : 'Fail'"></span>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </template>
      <template x-if="results.length === 0">
        <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-400">
          No results published yet.
        </div>
      </template>
    </div>
  </template>
</div>

@push('scripts')
<script>
function myResults() {
  return {
    loading: true,
    results: [],
    async init() {
      const res  = await fetch('/api/results', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
      const json = await res.json();
      this.results = json.success ? json.data : [];
      this.loading = false;
    }
  }
}
</script>
@endpush
@endsection
