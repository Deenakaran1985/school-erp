@extends('layouts.app')
@section('title', 'Staff')

@section('content')
<div class="space-y-4">

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h3 class="text-lg font-semibold text-slate-700">Staff Members</h3>
      <p class="text-sm text-slate-400">{{ $staff->total() }} records</p>
    </div>
    @can('staff.create')
      <a href="{{ route('admin.staff.create') }}"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">
        + Add Staff
      </a>
    @endcan
  </div>

  <!-- Filter -->
  <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 grid grid-cols-2 md:grid-cols-4 gap-3">
    <input name="search" value="{{ request('search') }}" placeholder="Name / Employee ID / Phone"
      class="col-span-2 px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"/>

    <select name="department_id" class="px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl">
      <option value="">All Departments</option>
      @foreach($departments as $dept)
        <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
      @endforeach
    </select>

    <div class="flex gap-2">
      <select name="staff_type" class="flex-1 px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl">
        <option value="">All Types</option>
        <option value="teaching" @selected(request('staff_type')==='teaching')>Teaching</option>
        <option value="non_teaching" @selected(request('staff_type')==='non_teaching')>Non-Teaching</option>
        <option value="admin" @selected(request('staff_type')==='admin')>Admin</option>
      </select>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-xl hover:bg-blue-700">Filter</button>
      <a href="{{ route('admin.staff.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">Reset</a>
    </div>
  </form>

  <!-- Table -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
        <tr>
          <th class="px-4 py-3 text-left">Staff</th>
          <th class="px-4 py-3 text-left">Department</th>
          <th class="px-4 py-3 text-left">Designation</th>
          <th class="px-4 py-3 text-left">Type</th>
          <th class="px-4 py-3 text-left">Phone</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($staff as $s)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">
                {{ strtoupper(substr($s->name, 0, 1)) }}
              </div>
              <div>
                <p class="font-medium text-slate-800">{{ $s->name }}</p>
                <p class="text-xs text-slate-400">{{ $s->employee_id }}</p>
              </div>
            </div>
          </td>
          <td class="px-4 py-3 text-slate-600">{{ $s->department?->name ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $s->designation }}</td>
          <td class="px-4 py-3">
            <span class="px-2 py-0.5 rounded-full text-xs font-medium
              {{ $s->staff_type === 'teaching' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
              {{ ucfirst(str_replace('_', ' ', $s->staff_type)) }}
            </span>
          </td>
          <td class="px-4 py-3 text-slate-600">{{ $s->user?->phone ?? '—' }}</td>
          <td class="px-4 py-3">
            <span class="px-2 py-0.5 rounded-full text-xs font-medium
              {{ $s->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
              {{ ucfirst($s->status) }}
            </span>
          </td>
          <td class="px-4 py-3 text-right">
            <div class="flex justify-end gap-2">
              <a href="{{ route('admin.staff.show', $s) }}" class="px-3 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg">View</a>
              @can('staff.edit')
                <a href="{{ route('admin.staff.edit', $s) }}" class="px-3 py-1 text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg">Edit</a>
              @endcan
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">No staff records found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">{{ $staff->withQueryString()->links() }}</div>
  </div>
</div>
@endsection
