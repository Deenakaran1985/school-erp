@extends('layouts.app')
@section('title', 'Edit Class')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.classes.index') }}" class="text-slate-400 hover:text-slate-600">← Back</a>
    <h3 class="text-lg font-semibold text-slate-700">Edit Class — {{ $class->name }}</h3>
  </div>

  <form method="POST" action="{{ route('admin.classes.update', $class) }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Class Name *</label>
        <input name="name" value="{{ old('name', $class->name) }}" required
          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Display Name</label>
        <input name="display_name" value="{{ old('display_name', $class->display_name) }}"
          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Level *</label>
        <input name="level" type="number" min="1" max="13" value="{{ old('level', $class->level) }}" required
          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Sort Order</label>
        <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $class->sort_order) }}"
          class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
      </div>
      <div class="flex items-end pb-1">
        <label class="flex items-center gap-2 cursor-pointer">
          <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $class->is_active))
            class="w-4 h-4 text-blue-600 rounded"/>
          <span class="text-sm text-slate-700">Active</span>
        </label>
      </div>
    </div>

    <div class="flex gap-3 pt-2">
      <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">Update Class</button>
      <a href="{{ route('admin.classes.index') }}" class="px-6 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">Cancel</a>
    </div>
  </form>
</div>
@endsection
