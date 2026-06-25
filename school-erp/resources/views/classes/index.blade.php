@extends('layouts.app')
@section('title', 'Classes & Sections')

@section('content')
<div class="page-enter space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Classes &amp; Sections</h1>
      <p class="text-sm text-slate-400 mt-0.5">{{ $year->label }} &middot; {{ $classes->count() }} classes configured</p>
    </div>
    @can('class.create')
    <a href="{{ route('admin.classes.create') }}" class="btn-primary flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
      Add Class
    </a>
    @endcan
  </div>

  @if($classes->isEmpty())
  <div class="card text-center py-16">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl gradient-brand mb-4"
      style="box-shadow:0 8px 24px rgba(99,102,241,0.3)">
      <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
      </svg>
    </div>
    <h3 class="text-slate-700 font-semibold mb-1">No classes yet</h3>
    <p class="text-slate-400 text-sm mb-5">Create your first class to get started with sections and subjects.</p>
    @can('class.create')
    <a href="{{ route('admin.classes.create') }}" class="btn-primary inline-flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
      Add First Class
    </a>
    @endcan
  </div>
  @else

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($classes as $class)
    <div class="card card-hover space-y-4" x-data="{ addingSection: false, addingSubject: false }">

      {{-- Class header --}}
      <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl gradient-brand flex items-center justify-center flex-shrink-0"
            style="box-shadow:0 4px 12px rgba(99,102,241,0.3)">
            <span class="text-white font-bold text-sm">{{ $class->level }}</span>
          </div>
          <div>
            <h4 class="font-bold text-slate-800 leading-tight">{{ $class->display_name ?: $class->name }}</h4>
            <p class="text-xs text-slate-400">{{ $class->students_count ?? $class->students()->count() }} students enrolled</p>
          </div>
        </div>
        <div class="flex items-center gap-1.5">
          @can('class.edit')
          <a href="{{ route('admin.classes.edit', $class) }}"
            class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 transition-colors" title="Edit">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
          </a>
          @endcan
          @can('class.delete')
          <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
            onsubmit="return confirm('Delete class {{ $class->name }}? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-colors" title="Delete">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
              </svg>
            </button>
          </form>
          @endcan
        </div>
      </div>

      {{-- Sections --}}
      <div>
        <div class="flex items-center justify-between mb-2">
          <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Sections</p>
          @can('class.edit')
          <button type="button" @click="addingSection = !addingSection"
            class="text-[10px] font-semibold text-indigo-500 hover:text-indigo-700 uppercase tracking-wide">
            <span x-text="addingSection ? '✕ Cancel' : '+ Add'"></span>
          </button>
          @endcan
        </div>

        <div class="flex flex-wrap gap-2 mb-2">
          @forelse($class->sections as $sec)
          <div class="flex items-center gap-1 badge badge-blue">
            <span>{{ $sec->name }}</span>
            @can('class.edit')
            <form method="POST" action="{{ route('admin.classes.sections.remove', [$class, $sec]) }}" class="inline">
              @csrf @method('DELETE')
              <button type="submit" class="hover:text-red-400 ml-0.5 leading-none transition-colors"
                onclick="return confirm('Remove section {{ $sec->name }}?')" title="Remove">×</button>
            </form>
            @endcan
          </div>
          @empty
          <span class="text-xs text-slate-400 italic">No sections yet</span>
          @endforelse
        </div>

        @can('class.edit')
        <div x-show="addingSection" x-transition class="mt-2">
          <form method="POST" action="{{ route('admin.classes.sections.add', $class) }}" class="flex gap-2">
            @csrf
            <input name="name" placeholder="Section name (A, B…)" maxlength="5" required
              class="form-input flex-1 text-xs py-1.5"/>
            <button type="submit" class="btn-primary text-xs py-1.5 px-3">Add</button>
          </form>
        </div>
        @endcan
      </div>

      {{-- Subjects --}}
      <div class="border-t border-slate-100 pt-3">
        <div class="flex items-center justify-between mb-2">
          <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
            Subjects ({{ $class->subjects->count() }})
          </p>
          @can('class.edit')
          <button type="button" @click="addingSubject = !addingSubject"
            class="text-[10px] font-semibold text-emerald-500 hover:text-emerald-700 uppercase tracking-wide">
            <span x-text="addingSubject ? '✕ Cancel' : '+ Add'"></span>
          </button>
          @endcan
        </div>

        <ul class="space-y-1">
          @foreach($class->subjects->take(4) as $sub)
          <li class="flex items-center justify-between text-xs">
            <span class="text-slate-600">{{ $sub->name }}</span>
            <span class="text-slate-400 text-[11px]">{{ $sub->code ? "[$sub->code] " : '' }}{{ $sub->max_marks }}M / {{ $sub->pass_marks }}P</span>
          </li>
          @endforeach
          @if($class->subjects->count() > 4)
          <li class="text-xs text-indigo-500">+{{ $class->subjects->count() - 4 }} more subjects</li>
          @endif
        </ul>

        @can('class.edit')
        <div x-show="addingSubject" x-transition class="mt-3 space-y-2">
          <form method="POST" action="{{ route('admin.classes.subjects.add', $class) }}" class="space-y-2">
            @csrf
            <input name="name" placeholder="Subject name" required class="form-input w-full text-xs py-1.5"/>
            <div class="flex gap-2">
              <input name="code" placeholder="Code" maxlength="10" class="form-input flex-1 text-xs py-1.5"/>
              <input name="max_marks" type="number" placeholder="Max" value="100" class="form-input w-20 text-xs py-1.5"/>
              <input name="pass_marks" type="number" placeholder="Pass" value="35" class="form-input w-20 text-xs py-1.5"/>
            </div>
            <button type="submit" class="w-full text-xs py-1.5 rounded-xl font-medium text-white"
              style="background:linear-gradient(135deg,#10B981,#059669)">Save Subject</button>
          </form>
        </div>
        @endcan
      </div>

    </div>
    @endforeach
  </div>
  @endif

</div>
@endsection
