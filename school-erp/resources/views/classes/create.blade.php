@extends('layouts.app')
@section('title', 'Add Class')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{
  sections: ['A'],
  subjects: [{ name: '', code: '', max_marks: 100, pass_marks: 35 }],
  addSection() { this.sections.push('') },
  removeSection(i) { this.sections.splice(i, 1) },
  addSubject() { this.subjects.push({ name: '', code: '', max_marks: 100, pass_marks: 35 }) },
  removeSubject(i) { this.subjects.splice(i, 1) },
}">

  <div class="flex items-center gap-3">
    <a href="{{ route('admin.classes.index') }}" class="text-slate-400 hover:text-slate-600">← Back</a>
    <h3 class="text-lg font-semibold text-slate-700">Add New Class</h3>
  </div>

  <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-5">
    @csrf

    <!-- Class Info -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Class Information</h4>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Class Name *</label>
          <input name="name" value="{{ old('name') }}" placeholder="e.g. I, II, X" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
          @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Display Name</label>
          <input name="display_name" value="{{ old('display_name') }}" placeholder="e.g. Class I"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Level (1-13) *</label>
          <input name="level" type="number" min="1" max="13" value="{{ old('level') }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
      </div>
    </div>

    <!-- Sections -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-3">
      <div class="flex items-center justify-between">
        <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Sections</h4>
        <button type="button" @click="addSection()"
          class="text-xs text-blue-600 hover:underline">+ Add Section</button>
      </div>
      <div class="flex flex-wrap gap-3">
        <template x-for="(sec, i) in sections" :key="i">
          <div class="flex items-center gap-2">
            <input :name="`sections[${i}]`" x-model="sections[i]" placeholder="A" maxlength="5"
              class="w-24 px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
            <button type="button" @click="removeSection(i)" class="text-red-400 hover:text-red-600 text-lg leading-none">×</button>
          </div>
        </template>
      </div>
    </div>

    <!-- Subjects -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-3">
      <div class="flex items-center justify-between">
        <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Subjects</h4>
        <button type="button" @click="addSubject()"
          class="text-xs text-blue-600 hover:underline">+ Add Subject</button>
      </div>
      <div class="space-y-3">
        <template x-for="(sub, i) in subjects" :key="i">
          <div class="flex items-center gap-2">
            <input :name="`subjects[${i}][name]`" x-model="sub.name" placeholder="Subject name" required
              class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
            <input :name="`subjects[${i}][code]`" x-model="sub.code" placeholder="Code" maxlength="10"
              class="w-24 px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
            <input :name="`subjects[${i}][max_marks]`" x-model.number="sub.max_marks" type="number" placeholder="Max"
              class="w-20 px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
            <input :name="`subjects[${i}][pass_marks]`" x-model.number="sub.pass_marks" type="number" placeholder="Pass"
              class="w-20 px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
            <button type="button" @click="removeSubject(i)" class="text-red-400 hover:text-red-600 text-lg leading-none">×</button>
          </div>
        </template>
      </div>
      <p class="text-xs text-slate-400">Max Marks / Pass Marks for each subject</p>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">Create Class</button>
      <a href="{{ route('admin.classes.index') }}" class="px-6 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">Cancel</a>
    </div>
  </form>
</div>
@endsection
