@extends('layouts.app')
@section('title', 'My Attendance')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="myAttendance()" x-init="init()">
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ route('student.home') }}" class="text-slate-400 hover:text-slate-600">← Back</a>
      <h3 class="text-lg font-semibold text-slate-700">My Attendance</h3>
    </div>
    <input type="month" x-model="month" @change="load()"
      class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
  </div>

  <div x-show="loading" class="text-center py-10 text-slate-400">Loading…</div>

  <template x-if="!loading">
    <div class="space-y-4">
      <!-- Summary Cards -->
      <div class="grid grid-cols-4 gap-3">
        <template x-for="[label, key, color] in [['Present','present','green'],['Absent','absent','red'],['Late','late','amber'],['Holiday','holiday','blue']]">
          <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
            <p class="text-2xl font-bold" :class="`text-${color}-600`" x-text="summary[key] ?? 0"></p>
            <p class="text-xs text-slate-500" x-text="label"></p>
          </div>
        </template>
      </div>

      <!-- Calendar-style List -->
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 text-sm font-semibold text-slate-700">
          Attendance Records
        </div>
        <div class="divide-y divide-slate-100">
          <template x-for="r in records" :key="r.date">
            <div class="flex items-center justify-between px-5 py-3 text-sm">
              <span class="text-slate-700 font-medium" x-text="new Date(r.date).toLocaleDateString('en-IN', { weekday:'short', day:'numeric', month:'short' })"></span>
              <span class="px-3 py-0.5 rounded-full text-xs font-medium"
                :class="{
                  'bg-green-100 text-green-700':  r.status === 'present',
                  'bg-red-100 text-red-600':      r.status === 'absent',
                  'bg-amber-100 text-amber-600':  r.status === 'late',
                  'bg-blue-100 text-blue-600':    r.status === 'holiday',
                  'bg-slate-100 text-slate-500':  !['present','absent','late','holiday'].includes(r.status),
                }"
                x-text="r.status.charAt(0).toUpperCase() + r.status.slice(1)"></span>
            </div>
          </template>
          <template x-if="records.length === 0">
            <div class="px-5 py-10 text-center text-slate-400 text-sm">No records for this month.</div>
          </template>
        </div>
      </div>
    </div>
  </template>
</div>

@push('scripts')
<script>
function myAttendance() {
  return {
    loading: true,
    month: new Date().toISOString().slice(0, 7),
    summary: {},
    records: [],
    studentId: null,
    async init() {
      const p = await fetch('/api/profile', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json());
      const student = p.success ? (Array.isArray(p.data) ? p.data[0] : p.data) : null;
      if (student) { this.studentId = student.id; await this.load(); }
      this.loading = false;
    },
    async load() {
      if (!this.studentId) return;
      this.loading = true;
      const r = await fetch(`/api/student/${this.studentId}/attendance?month=${this.month}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      }).then(r => r.json());
      this.summary = r.success ? r.summary : {};
      this.records = r.success ? r.records : [];
      this.loading = false;
    }
  }
}
</script>
@endpush
@endsection
