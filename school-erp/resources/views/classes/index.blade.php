@extends('layouts.app')
@section('title', 'Classes & Sections')

@section('content')
<div class="space-y-4">
  <div class="flex items-center justify-between">
    <div>
      <h3 class="text-lg font-semibold text-slate-700">Classes & Sections</h3>
      <p class="text-sm text-slate-400">{{ $year->label }} · {{ $classes->count() }} classes</p>
    </div>
    @can('class.create')
      <a href="{{ route('admin.classes.create') }}"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">+ Add Class</a>
    @endcan
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($classes as $class)
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="font-bold text-slate-800">{{ $class->name }}</h4>
          <p class="text-xs text-slate-400">{{ $class->student_count }} students</p>
        </div>
        <div class="flex gap-2">
          @can('class.edit')
            <a href="{{ route('admin.classes.edit', $class) }}"
              class="px-3 py-1 text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg">Edit</a>
          @endcan
          @can('class.delete')
            <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
              onsubmit="return confirm('Delete this class?')">
              @csrf @method('DELETE')
              <button type="submit" class="px-3 py-1 text-xs bg-red-50 hover:bg-red-100 text-red-600 rounded-lg">Del</button>
            </form>
          @endcan
        </div>
      </div>

      <!-- Sections -->
      <div>
        <p class="text-xs font-semibold text-slate-500 mb-2">SECTIONS</p>
        <div class="flex flex-wrap gap-2">
          @forelse($class->sections as $sec)
          <div class="flex items-center gap-1 bg-slate-100 rounded-lg px-2 py-1">
            <span class="text-xs font-medium text-slate-700">{{ $sec->name }}</span>
            @can('class.edit')
            <form method="POST" action="{{ route('admin.classes.sections.remove', [$class, $sec]) }}">
              @csrf @method('DELETE')
              <button type="submit" class="text-slate-400 hover:text-red-500 text-xs leading-none ml-1">×</button>
            </form>
            @endcan
          </div>
          @empty
          <span class="text-xs text-slate-400">No sections</span>
          @endforelse
        </div>

        @can('class.edit')
        <form method="POST" action="{{ route('admin.classes.sections.add', $class) }}" class="flex gap-2 mt-3">
          @csrf
          <input name="name" placeholder="New section (A/B)" maxlength="5"
            class="flex-1 px-2 py-1 text-xs border border-slate-200 rounded-lg bg-slate-50"/>
          <button type="submit" class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700">Add</button>
        </form>
        @endcan
      </div>

      <!-- Subjects -->
      <div>
        <p class="text-xs font-semibold text-slate-500 mb-2">SUBJECTS ({{ $class->subjects->count() }})</p>
        <ul class="space-y-1">
          @foreach($class->subjects->take(5) as $sub)
          <li class="text-xs text-slate-600 flex justify-between">
            <span>{{ $sub->name }}</span>
            <span class="text-slate-400">{{ $sub->max_marks }}M</span>
          </li>
          @endforeach
          @if($class->subjects->count() > 5)
          <li class="text-xs text-slate-400">+{{ $class->subjects->count() - 5 }} more</li>
          @endif
        </ul>

        @can('class.edit')
        <details class="mt-3">
          <summary class="text-xs text-blue-600 cursor-pointer hover:underline">+ Add Subject</summary>
          <form method="POST" action="{{ route('admin.classes.subjects.add', $class) }}" class="mt-2 space-y-2">
            @csrf
            <input name="name" placeholder="Subject name" required
              class="w-full px-2 py-1 text-xs border border-slate-200 rounded-lg bg-slate-50"/>
            <div class="flex gap-2">
              <input name="code" placeholder="Code" class="flex-1 px-2 py-1 text-xs border border-slate-200 rounded-lg bg-slate-50"/>
              <input name="max_marks" type="number" placeholder="Max" value="100" class="w-20 px-2 py-1 text-xs border border-slate-200 rounded-lg bg-slate-50"/>
              <input name="pass_marks" type="number" placeholder="Pass" value="35" class="w-20 px-2 py-1 text-xs border border-slate-200 rounded-lg bg-slate-50"/>
            </div>
            <button type="submit" class="w-full px-3 py-1 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700">Save Subject</button>
          </form>
        </details>
        @endcan
      </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-400">
      No classes found. <a href="{{ route('admin.classes.create') }}" class="text-blue-600 underline">Add one.</a>
    </div>
    @endforelse
  </div>
</div>
@endsection
