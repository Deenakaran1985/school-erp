@extends('layouts.app')
@section('title', 'Student Portal')

@section('content')
<div class="space-y-5" x-data="studentPortal()" x-init="init()">

  <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white">
    <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
    <p class="text-emerald-100 text-sm mt-1" x-text="student ? `Class ${student.class} · Roll No: ${student.roll_number}` : 'Student Dashboard'"></p>
  </div>

  <div x-show="loading" class="text-center py-10 text-slate-400">Loading…</div>

  <template x-if="!loading && student">
    <div class="space-y-4">
      <!-- Profile Card -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-2xl flex-shrink-0">
          <span x-text="student.name.charAt(0)"></span>
        </div>
        <div>
          <h3 class="font-semibold text-slate-800 text-lg" x-text="student.name"></h3>
          <p class="text-sm text-slate-500" x-text="`Admission No: ${student.admission_no}`"></p>
          <p class="text-xs text-slate-400" x-text="`Class ${student.class_section} · ${student.status}`"></p>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
          ['/student/results',    '📊', 'My Results',    'blue'],
          ['/student/attendance', '📅', 'Attendance',    'amber'],
          ['/student/homework',   '📚', 'Homework',      'purple'],
          ['#',                   '🔔', 'Notifications', 'green'],
        ] as [$url, $icon, $label, $color])
        <a href="{{ $url }}"
          class="flex flex-col items-center gap-2 p-5 bg-white rounded-2xl border border-slate-200 hover:border-{{ $color }}-300 hover:bg-{{ $color }}-50 transition">
          <span class="text-3xl">{{ $icon }}</span>
          <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
        </a>
        @endforeach
      </div>

      <!-- Attendance Summary -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h4 class="font-semibold text-slate-700 mb-4">This Month's Attendance</h4>
        <div class="grid grid-cols-3 gap-3 text-center">
          <div class="p-3 bg-green-50 rounded-xl">
            <p class="text-2xl font-bold text-green-600" x-text="attendance.present ?? 0"></p>
            <p class="text-xs text-slate-500">Present</p>
          </div>
          <div class="p-3 bg-red-50 rounded-xl">
            <p class="text-2xl font-bold text-red-500" x-text="attendance.absent ?? 0"></p>
            <p class="text-xs text-slate-500">Absent</p>
          </div>
          <div class="p-3 bg-amber-50 rounded-xl">
            <p class="text-2xl font-bold text-amber-500" x-text="attendance.late ?? 0"></p>
            <p class="text-xs text-slate-500">Late</p>
          </div>
        </div>
      </div>

      <!-- Notifications -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h4 class="font-semibold text-slate-700 mb-3">Recent Notifications</h4>
        <template x-for="n in notifications.slice(0,4)" :key="n.id">
          <div class="flex gap-3 py-3 border-b border-slate-100 last:border-0">
            <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0" :class="n.is_read ? 'bg-slate-200' : 'bg-blue-500'"></div>
            <div>
              <p class="text-sm font-medium text-slate-800" x-text="n.title"></p>
              <p class="text-xs text-slate-400" x-text="n.body.substring(0,80) + (n.body.length > 80 ? '…' : '')"></p>
            </div>
          </div>
        </template>
        <template x-if="!notifications.length">
          <p class="text-sm text-slate-400 py-2">No notifications.</p>
        </template>
      </div>
    </div>
  </template>
</div>

@push('scripts')
<script>
function studentPortal() {
  return {
    loading: true,
    student: null,
    attendance: {},
    notifications: [],
    async init() {
      try {
        const [profileRes, attendRes, notifRes] = await Promise.all([
          fetch('/api/profile', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }),
          fetch('/api/notifications', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }),
        ]);
        const profile = await profileRes.json();
        const notif   = await notifRes.json();

        if (profile.success) {
          this.student = Array.isArray(profile.data) ? profile.data[0] : profile.data;
          // Load attendance for this month
          if (this.student) {
            const attRes = await fetch(`/api/student/${this.student.id}/attendance`, {
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const att = await attRes.json();
            this.attendance = att.success ? att.summary : {};
          }
        }
        this.notifications = notif.success ? notif.data : [];
      } catch(e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
@endpush
@endsection
