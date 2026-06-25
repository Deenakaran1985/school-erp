@extends('layouts.app')
@section('title', 'Edit Class — ' . $class->name)

@section('content')
<div class="page-enter max-w-2xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.classes.index') }}"
      class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
    </a>
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Edit Class</h1>
      <p class="text-sm text-slate-400">{{ $class->display_name ?: $class->name }}</p>
    </div>
  </div>

  {{-- Class form --}}
  <div class="card space-y-4">
    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
      <div class="w-8 h-8 rounded-lg gradient-brand flex items-center justify-center"
        style="box-shadow:0 4px 12px rgba(99,102,241,0.3)">
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
      </div>
      <h2 class="font-semibold text-slate-700">Class Information</h2>
    </div>

    <form method="POST" action="{{ route('admin.classes.update', $class) }}" class="space-y-4">
      @csrf @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="form-label">Class Name *</label>
          <input name="name" value="{{ old('name', $class->name) }}" required class="form-input"/>
          @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Display Name</label>
          <input name="display_name" value="{{ old('display_name', $class->display_name) }}" class="form-input"/>
        </div>
        <div>
          <label class="form-label">Level (1–13) *</label>
          <input name="level" type="number" min="1" max="13" value="{{ old('level', $class->level) }}" required class="form-input"/>
          @error('level')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Sort Order</label>
          <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $class->sort_order) }}" class="form-input"/>
        </div>
        <div class="flex items-end pb-1">
          <label class="flex items-center gap-2.5 cursor-pointer group">
            <div class="relative">
              <input name="is_active" type="checkbox" value="1" id="is_active"
                @checked(old('is_active', $class->is_active)) class="sr-only peer"/>
              <div class="w-10 h-5 bg-slate-200 peer-checked:bg-indigo-500 rounded-full transition-colors"></div>
              <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
            </div>
            <span class="text-sm font-medium text-slate-700">Active</span>
          </label>
        </div>
      </div>

      <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="btn-primary flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
          Update Class
        </button>
        <a href="{{ route('admin.classes.index') }}" class="btn-secondary">Cancel</a>
      </div>
    </form>
  </div>

  {{-- Sections (live edit) --}}
  <div class="card space-y-4">
    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
      <div class="w-8 h-8 rounded-lg gradient-sky flex items-center justify-center"
        style="box-shadow:0 4px 12px rgba(6,182,212,0.3)">
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </div>
      <h2 class="font-semibold text-slate-700">Sections</h2>
    </div>

    <div class="flex flex-wrap gap-2">
      @forelse($class->sections as $sec)
      <div class="flex items-center gap-1.5 badge badge-blue text-sm py-1.5 px-3">
        <svg class="w-3.5 h-3.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span class="font-semibold">{{ $sec->name }}</span>
        <span class="opacity-50 text-xs">&middot; {{ $sec->students()->count() }} students</span>
        @can('class.edit')
        <form method="POST" action="{{ route('admin.classes.sections.remove', [$class, $sec]) }}" class="inline ml-1">
          @csrf @method('DELETE')
          <button type="submit" onclick="return confirm('Remove section {{ $sec->name }}?')"
            class="hover:text-red-400 leading-none transition-colors" title="Remove">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </form>
        @endcan
      </div>
      @empty
      <p class="text-sm text-slate-400 italic">No sections added yet.</p>
      @endforelse
    </div>

    @can('class.edit')
    <form method="POST" action="{{ route('admin.classes.sections.add', $class) }}"
      class="flex gap-3 items-end pt-1 border-t border-slate-100">
      @csrf
      <div class="flex-1">
        <label class="form-label">New Section Name</label>
        <input name="name" placeholder="e.g. B, C, II" maxlength="5" required class="form-input"/>
      </div>
      <div class="w-32">
        <label class="form-label">Max Strength</label>
        <input name="max_strength" type="number" min="1" value="40" class="form-input"/>
      </div>
      <button type="submit" class="btn-primary flex items-center gap-2 pb-[9px]">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Add Section
      </button>
    </form>
    @endcan
  </div>

  {{-- Subjects (live edit) --}}
  <div class="card space-y-4">
    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
      <div class="w-8 h-8 rounded-lg gradient-success flex items-center justify-center"
        style="box-shadow:0 4px 12px rgba(16,185,129,0.3)">
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
      </div>
      <h2 class="font-semibold text-slate-700">Subjects ({{ $class->subjects->count() }})</h2>
    </div>

    @if($class->subjects->isNotEmpty())
    <div class="table-modern">
      <table class="w-full">
        <thead><tr>
          <th>Subject</th><th>Code</th><th>Max</th><th>Pass</th>
        </tr></thead>
        <tbody>
          @foreach($class->subjects as $sub)
          <tr>
            <td class="font-medium text-slate-700">{{ $sub->name }}</td>
            <td><span class="badge badge-gray">{{ $sub->code ?: '—' }}</span></td>
            <td class="text-slate-600">{{ $sub->max_marks }}</td>
            <td class="text-slate-600">{{ $sub->pass_marks }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    @can('class.edit')
    <form method="POST" action="{{ route('admin.classes.subjects.add', $class) }}"
      class="grid grid-cols-12 gap-2 items-end pt-1 border-t border-slate-100">
      @csrf
      <div class="col-span-5">
        <label class="form-label">Subject Name</label>
        <input name="name" placeholder="e.g. Mathematics" required class="form-input"/>
      </div>
      <div class="col-span-2">
        <label class="form-label">Code</label>
        <input name="code" placeholder="MATH" maxlength="10" class="form-input text-center"/>
      </div>
      <div class="col-span-2">
        <label class="form-label">Max Marks</label>
        <input name="max_marks" type="number" value="100" min="0" class="form-input text-center"/>
      </div>
      <div class="col-span-2">
        <label class="form-label">Pass Marks</label>
        <input name="pass_marks" type="number" value="35" min="0" class="form-input text-center"/>
      </div>
      <div class="col-span-1">
        <button type="submit" class="w-full h-[38px] rounded-xl text-white font-medium text-sm flex items-center justify-center"
          style="background:linear-gradient(135deg,#10B981,#059669);box-shadow:0 4px 12px rgba(16,185,129,0.3)">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
        </button>
      </div>
    </form>
    @endcan
  </div>

</div>
@endsection
