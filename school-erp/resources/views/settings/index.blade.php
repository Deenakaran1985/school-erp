@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
  <h3 class="text-lg font-semibold text-slate-700">System Settings</h3>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- School Information --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">School Information</h4>
      <form method="POST" action="{{ route('admin.settings.school-info') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        @foreach([
          ['school_name',    'School Name *',    'text',  '', true],
          ['school_address', 'Address',          'text',  '', false],
          ['school_phone',   'Phone',            'text',  '', false],
          ['school_email',   'Email',            'email', '', false],
        ] as [$n, $l, $t, $p, $req])
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">{{ $l }}</label>
          <input name="{{ $n }}" type="{{ $t }}" value="{{ old($n, $schoolInfo[$n] ?? '') }}"
            {{ $req ? 'required' : '' }}
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        @endforeach
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">School Logo</label>
          <input name="school_logo" type="file" accept="image/*"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">
          Save School Info
        </button>
      </form>
    </div>

    {{-- Academic Years --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Academic Years</h4>
      <div class="space-y-2 max-h-40 overflow-y-auto">
        @foreach($academicYears as $ay)
        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0 text-sm">
          <div>
            <span class="font-medium text-slate-800">{{ $ay->label }}</span>
            @if($ay->is_current)
              <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Current</span>
            @endif
          </div>
          @if(!$ay->is_current)
          <form method="POST" action="{{ route('admin.settings.academic-year.current', $ay) }}">
            @csrf @method('PUT')
            <button type="submit" class="text-xs text-blue-600 hover:underline">Set Current</button>
          </form>
          @endif
        </div>
        @endforeach
      </div>
      <form method="POST" action="{{ route('admin.settings.academic-year') }}" class="space-y-3 pt-2 border-t border-slate-100">
        @csrf
        <div class="grid grid-cols-3 gap-2">
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Label *</label>
            <input name="label" placeholder="2025-26" required
              class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg bg-slate-50"/>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Start Date *</label>
            <input name="start_date" type="date" required
              class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg bg-slate-50"/>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">End Date *</label>
            <input name="end_date" type="date" required
              class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg bg-slate-50"/>
          </div>
        </div>
        <button type="submit" class="w-full py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-xl">
          Add Academic Year
        </button>
      </form>
    </div>

    {{-- Departments --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Departments</h4>
      <div class="space-y-2 max-h-40 overflow-y-auto">
        @foreach($departments as $dept)
        <div class="flex justify-between items-center text-sm py-1.5 border-b border-slate-100 last:border-0">
          <span class="text-slate-800 font-medium">{{ $dept->name }}</span>
          <span class="text-xs text-slate-400">{{ $dept->staff_count }} staff · {{ $dept->code }}</span>
        </div>
        @endforeach
      </div>
      <form method="POST" action="{{ route('admin.settings.departments') }}" class="flex gap-2 pt-2 border-t border-slate-100">
        @csrf
        <input name="name" placeholder="Department name" required
          class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        <input name="code" placeholder="Code" maxlength="10"
          class="w-24 px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-xl hover:bg-blue-700">Add</button>
      </form>
    </div>

    {{-- Grade Configuration --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Grade Configuration</h4>
      <div class="overflow-x-auto">
        <table class="min-w-full text-xs">
          <thead class="bg-slate-50 text-slate-500">
            <tr>
              <th class="px-3 py-2 text-left">Grade</th>
              <th class="px-3 py-2 text-left">Label</th>
              <th class="px-3 py-2 text-left">Min %</th>
              <th class="px-3 py-2 text-left">Max %</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($gradeConfig as $g)
            <tr>
              <td class="px-3 py-2 font-bold text-blue-700">{{ $g->grade }}</td>
              <td class="px-3 py-2">{{ $g->label }}</td>
              <td class="px-3 py-2">{{ $g->min_percent }}%</td>
              <td class="px-3 py-2">{{ $g->max_percent }}%</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <form method="POST" action="{{ route('admin.settings.grade-config') }}"
        class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
        @csrf
        @foreach([
          ['grade',       'Grade', 'text'],
          ['label',       'Label', 'text'],
          ['min_percent', 'Min %', 'number'],
          ['max_percent', 'Max %', 'number'],
        ] as [$n, $l, $t])
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">{{ $l }} *</label>
          <input name="{{ $n }}" type="{{ $t }}" required
            class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg bg-slate-50"/>
        </div>
        @endforeach
        <div class="col-span-2">
          <button type="submit" class="w-full py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700">
            Add Grade
          </button>
        </div>
      </form>
    </div>

    {{-- Exam Types --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Exam Types</h4>
      <div class="space-y-2 max-h-40 overflow-y-auto">
        @foreach($examTypes as $et)
        <div class="flex justify-between items-center text-sm py-1.5 border-b border-slate-100 last:border-0">
          <span class="text-slate-800 font-medium">{{ $et->name }}</span>
          <span class="text-xs text-slate-400">{{ $et->short_name }} · {{ $et->weightage }}% weight</span>
        </div>
        @endforeach
      </div>
      <form method="POST" action="{{ route('admin.settings.exam-types') }}" class="space-y-2 pt-2 border-t border-slate-100">
        @csrf
        <div class="grid grid-cols-2 gap-2">
          <input name="name" placeholder="Exam type name" required
            class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
          <input name="short_name" placeholder="Short (e.g. UT1)"
            class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
          <input name="weightage" type="number" min="0" max="100" placeholder="Weightage %"
            class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
          <input name="sort_order" type="number" min="0" placeholder="Sort order"
            class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <button type="submit" class="w-full py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700">
          Add Exam Type
        </button>
      </form>
    </div>

  </div>
</div>
@endsection
