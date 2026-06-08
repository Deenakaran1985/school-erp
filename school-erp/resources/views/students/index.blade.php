@extends('layouts.app')
@section('title', 'Students')
@section('page_title', 'Students')

@section('content')
<div class="space-y-4">

  <!-- Header row -->
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h3 class="text-lg font-semibold text-slate-700">All Students</h3>
      <p class="text-sm text-slate-400">{{ $students->total() }} students · {{ $year->name }}</p>
    </div>
    <div class="flex gap-2">
      @can('student.import')
        <a href="{{ route('admin.students.emis.form') }}"
          class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl flex items-center gap-2">
          📥 EMIS Import
        </a>
      @endcan
      @can('student.create')
        <a href="{{ route('admin.students.create') }}"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl flex items-center gap-2">
          + Add Student
        </a>
      @endcan
    </div>
  </div>

  <!-- Filter bar -->
  <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 grid grid-cols-2 md:grid-cols-5 gap-3">
    <input name="search" value="{{ request('search') }}"
      placeholder="Name / Admission No / EMIS / Mobile"
      class="col-span-2 md:col-span-2 px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"/>

    <select name="class_id" id="filter_class"
      class="px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl">
      <option value="">All Classes</option>
      @foreach($classes as $class)
        <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>
          Class {{ $class->name }}
        </option>
      @endforeach
    </select>

    <select name="gender"
      class="px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl">
      <option value="">All Gender</option>
      <option value="M" @selected(request('gender')==='M')>Male</option>
      <option value="F" @selected(request('gender')==='F')>Female</option>
    </select>

    <div class="flex gap-2">
      <button type="submit" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded-xl hover:bg-blue-700">
        Filter
      </button>
      <a href="{{ route('admin.students.index') }}"
        class="px-3 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">
        Reset
      </a>
    </div>
  </form>

  <!-- Table -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Student</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Admission No</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">EMIS No</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Class</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Parent Mobile</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($students as $student)
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <img src="{{ $student->photo_url }}"
                    class="w-8 h-8 rounded-full object-cover bg-slate-200">
                  <div>
                    <p class="font-medium text-slate-700">{{ $student->name }}</p>
                    <p class="text-xs text-slate-400">{{ $student->father_name }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 font-mono text-slate-600 text-xs">{{ $student->admission_no }}</td>
              <td class="px-4 py-3 font-mono text-slate-600 text-xs">
                {{ $student->emis_number ?? '—' }}
              </td>
              <td class="px-4 py-3">
                <span class="text-sm text-slate-600">{{ $student->class_section }}</span>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ $student->parent_mobile }}</td>
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 text-xs rounded-full font-medium
                  {{ $student->status === 'active'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-slate-100 text-slate-500' }}">
                  {{ ucfirst($student->status) }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2 justify-end">
                  <a href="{{ route('admin.students.show', $student) }}"
                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</a>
                  @can('student.edit')
                    <a href="{{ route('admin.students.edit', $student) }}"
                      class="text-amber-600 hover:text-amber-800 text-xs font-medium">Edit</a>
                  @endcan
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                No students found. <a href="{{ route('admin.students.create') }}" class="text-blue-600 underline">Add one</a> or
                <a href="{{ route('admin.students.emis.form') }}" class="text-amber-600 underline">import from EMIS</a>.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($students->hasPages())
      <div class="px-4 py-3 border-t border-slate-100">
        {{ $students->links() }}
      </div>
    @endif
  </div>

</div>
@endsection