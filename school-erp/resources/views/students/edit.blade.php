@extends('layouts.app')
@section('title', 'Edit Student')
@section('page_title', 'Edit: ' . $student->name)

@section('content')
<form method="POST" action="{{ route('admin.students.update', $student) }}"
  enctype="multipart/form-data" class="space-y-6 max-w-4xl">
  @csrf
  @method('PUT')

  <!-- Admission No (read-only) -->
  <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-700">
    ℹ️ Editing: <strong>{{ $student->name }}</strong> · Admission: {{ $student->admission_no }}
  </div>

  <!-- Same fields as create, use old() with $student fallback -->
  <!-- e.g. value="{{ old('name', $student->name) }}" -->
  <!-- Include status field for edit (not present in create) -->

  <div class="flex gap-3">
    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl text-sm">
      Update Student
    </button>
    <a href="{{ route('admin.students.show', $student) }}"
      class="px-6 py-2.5 bg-slate-100 text-slate-600 font-medium rounded-xl text-sm">
      Cancel
    </a>
  </div>
</form>
@endsection