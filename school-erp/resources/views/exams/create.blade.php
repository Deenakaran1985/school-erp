@extends('layouts.app')
@section('title', 'Schedule Exam')
@section('page_title', 'Schedule New Exam')

@section('content')
<form method="POST" action="{{ route('admin.exams.store') }}"
  class="max-w-2xl space-y-5"
  x-data="{ classId: '', subjects: [] }"
  x-init="$watch('classId', val => {
    subjects = [];
    if (!val) return;
    fetch('/admin/exams/subjects?class_id=' + val)
      .then(r => r.json()).then(d => subjects = d);
  })">
  @csrf

  <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">

    <!-- Exam Type -->
    <div>
      <label class="block text-sm font-medium text-slate-600 mb-1">Exam Type *</label>
      <select name="exam_type_id" required
        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <option value="">Select Type</option>
        @foreach($examTypes as $et)
          <option value="{{ $et->id }}">{{ $et->name }} ({{ $et->code }} — {{ $et->weightage_percent }}%)</option>
        @endforeach
      </select>
      @error('exam_type_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- Class -->
    <div>
      <label class="block text-sm font-medium text-slate-600 mb-1">Class *</label>
      <select name="school_class_id" x-model="classId" required
        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <option value="">Select Class</option>
        @foreach($classes as $class)
          <option value="{{ $class->id }}">Class {{ $class->name }}</option>
        @endforeach
      </select>
    </div>

    <!-- Subject (dynamic) -->
    <div>
      <label class="block text-sm font-medium text-slate-600 mb-1">Subject *</label>
      <select name="subject_id" required
        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <option value="">Select subject first</option>
        <template x-for="sub in subjects" :key="sub.id">
          <option :value="sub.id" x-text="sub.name + ' (' + sub.code + ')'"></option>
        </template>
      </select>
      @error('subject_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- Exam Name -->
    <div>
      <label class="block text-sm font-medium text-slate-600 mb-1">Exam Name *</label>
      <input type="text" name="exam_name" value="{{ old('exam_name') }}" required
        placeholder="e.g. Unit Test 1 — Mathematics — Class VI"
        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"/>
    </div>

    <!-- Date + Time -->
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Exam Date *</label>
        <input type="date" name="exam_date" value="{{ old('exam_date') }}" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        @error('exam_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Start Time</label>
        <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      </div>
    </div>

    <!-- Marks -->
    <div class="grid grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Max Marks *</label>
        <input type="number" name="max_marks" value="{{ old('max_marks', 100) }}"
          min="1" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        @error('max_marks')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Pass Marks *</label>
        <input type="number" name="pass_marks" value="{{ old('pass_marks', 35) }}"
          min="1" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        @error('pass_marks')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Duration (mins)</label>
        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 180) }}"
          min="10"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      </div>
    </div>

    <!-- Hall no -->
    <div>
      <label class="block text-sm font-medium text-slate-600 mb-1">Hall No.</label>
      <input type="text" name="hall_no" value="{{ old('hall_no') }}"
        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
    </div>

    <!-- Instructions -->
    <div>
      <label class="block text-sm font-medium text-slate-600 mb-1">Instructions (shown on app)</label>
      <textarea name="instructions" rows="2"
        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">{{ old('instructions') }}</textarea>
    </div>
  </div>

  <div class="flex gap-3">
    <button type="submit"
      class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm">
      📅 Schedule Exam
    </button>
    <a href="{{ route('admin.exams.index') }}"
      class="px-6 py-2.5 bg-slate-100 text-slate-600 font-medium rounded-xl text-sm hover:bg-slate-200">
      Cancel
    </a>
  </div>
</form>
@endsection