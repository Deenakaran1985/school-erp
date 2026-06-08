@extends('layouts.app')
@section('title', 'Homework')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="myHomework()" x-init="init()">
  <div class="flex items-center gap-3">
    <a href="{{ route('student.home') }}" class="text-slate-400 hover:text-slate-600">← Back</a>
    <h3 class="text-lg font-semibold text-slate-700">My Homework</h3>
  </div>

  <div x-show="loading" class="text-center py-10 text-slate-400">Loading…</div>

  <template x-if="!loading">
    <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100">
      <template x-for="hw in homework" :key="hw.id">
        <div class="p-5 flex items-start gap-4">
          <div class="mt-1 w-5 h-5 rounded-full flex-shrink-0"
            :class="hw.submitted ? 'bg-green-500' : hw.overdue ? 'bg-red-400' : 'bg-amber-400'">
          </div>
          <div class="flex-1">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="font-semibold text-slate-800" x-text="hw.title"></p>
                <p class="text-xs text-slate-500 mt-0.5" x-text="`${hw.subject} · Due: ${hw.due_date}`"></p>
              </div>
              <span class="px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0"
                :class="hw.submitted ? 'bg-green-100 text-green-700' : hw.overdue ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700'"
                x-text="hw.submitted ? 'Submitted' : hw.overdue ? 'Overdue' : 'Pending'">
              </span>
            </div>
            <p x-show="hw.description" class="text-sm text-slate-600 mt-2" x-text="hw.description"></p>
          </div>
        </div>
      </template>
      <template x-if="homework.length === 0">
        <div class="p-10 text-center text-slate-400">No homework assigned.</div>
      </template>
    </div>
  </template>
</div>

@push('scripts')
<script>
function myHomework() {
  return {
    loading: true,
    homework: [],
    async init() {
      const p = await fetch('/api/profile', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json());
      const student = p.success ? (Array.isArray(p.data) ? p.data[0] : p.data) : null;
      if (student) {
        const h = await fetch(`/api/student/${student.id}/homework`, {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json());
        this.homework = h.success ? h.data : [];
      }
      this.loading = false;
    }
  }
}
</script>
@endpush
@endsection
