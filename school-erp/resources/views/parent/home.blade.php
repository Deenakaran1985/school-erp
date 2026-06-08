@extends('layouts.app')
@section('title', 'Parent Portal')

@section('content')
<div class="space-y-5" x-data="parentPortal()" x-init="init()">

  <!-- Welcome Header -->
  <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white">
    <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
    <p class="text-blue-100 text-sm mt-1">Parent Dashboard · School ERP</p>
  </div>

  <!-- Children Cards -->
  <div x-show="loading" class="text-center py-10 text-slate-400">Loading your children's data…</div>

  <template x-if="!loading">
    <div class="space-y-5">
      <template x-for="child in children" :key="child.id">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
          <!-- Child Header -->
          <div class="flex items-center gap-4 p-5 border-b border-slate-100">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg">
              <span x-text="child.name.charAt(0)"></span>
            </div>
            <div>
              <h3 class="font-semibold text-slate-800" x-text="child.name"></h3>
              <p class="text-xs text-slate-400" x-text="`Class ${child.class} ${child.section} · Adm: ${child.admission_no}`"></p>
            </div>
            <div class="ml-auto text-right">
              <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full" x-text="child.status"></span>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
            <div class="p-4 text-center">
              <p class="text-lg font-bold text-blue-600" x-text="`${child.attendance_pct ?? '--'}%`"></p>
              <p class="text-xs text-slate-400">Attendance</p>
            </div>
            <div class="p-4 text-center">
              <p class="text-lg font-bold text-amber-500" x-text="child.pending_fees ? `₹${Number(child.pending_fees).toLocaleString('en-IN')}` : '₹0'"></p>
              <p class="text-xs text-slate-400">Pending Fees</p>
            </div>
            <div class="p-4 text-center">
              <p class="text-lg font-bold text-emerald-600" x-text="child.last_grade ?? '--'"></p>
              <p class="text-xs text-slate-400">Last Grade</p>
            </div>
          </div>

          <!-- Quick Links -->
          <div class="p-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="/parent/fees" class="flex flex-col items-center gap-1 p-3 bg-slate-50 hover:bg-blue-50 rounded-xl transition">
              <span class="text-2xl">💳</span>
              <span class="text-xs text-slate-600 font-medium">Pay Fees</span>
            </a>
            <a href="/parent/results" class="flex flex-col items-center gap-1 p-3 bg-slate-50 hover:bg-blue-50 rounded-xl transition">
              <span class="text-2xl">📊</span>
              <span class="text-xs text-slate-600 font-medium">Results</span>
            </a>
            <a href="#" @click.prevent="viewAttendance(child.id)" class="flex flex-col items-center gap-1 p-3 bg-slate-50 hover:bg-blue-50 rounded-xl transition">
              <span class="text-2xl">📅</span>
              <span class="text-xs text-slate-600 font-medium">Attendance</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 p-3 bg-slate-50 hover:bg-blue-50 rounded-xl transition">
              <span class="text-2xl">📚</span>
              <span class="text-xs text-slate-600 font-medium">Homework</span>
            </a>
          </div>
        </div>
      </template>

      <template x-if="children.length === 0">
        <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-400">
          No children linked to your account. Contact school administration.
        </div>
      </template>
    </div>
  </template>

  <!-- Notifications panel -->
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    <h4 class="font-semibold text-slate-700 mb-3">Recent Notifications</h4>
    <template x-for="n in notifications.slice(0,5)" :key="n.id">
      <div class="flex gap-3 py-3 border-b border-slate-100 last:border-0">
        <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0"
          :class="n.is_read ? 'bg-slate-300' : 'bg-blue-500'"></div>
        <div>
          <p class="text-sm font-medium text-slate-800" x-text="n.title"></p>
          <p class="text-xs text-slate-500" x-text="n.body"></p>
          <p class="text-xs text-slate-400 mt-1" x-text="n.sent_at"></p>
        </div>
      </div>
    </template>
    <template x-if="notifications.length === 0">
      <p class="text-sm text-slate-400 py-3">No notifications.</p>
    </template>
  </div>

</div>

@push('scripts')
<script>
function parentPortal() {
  return {
    loading: true,
    children: [],
    notifications: [],
    async init() {
      try {
        const [profileRes, notifRes] = await Promise.all([
          fetch('/api/profile', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }),
          fetch('/api/notifications', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }),
        ]);
        const profile = await profileRes.json();
        const notif   = await notifRes.json();
        this.children      = profile.success ? profile.data : [];
        this.notifications = notif.success   ? notif.data   : [];
      } catch(e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },
    viewAttendance(id) {
      window.location.href = `/parent/attendance?student_id=${id}`;
    }
  }
}
</script>
@endpush
@endsection
