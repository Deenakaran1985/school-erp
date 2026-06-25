@extends('layouts.app')
@section('title', 'Add Class')

@section('content')
<div class="page-enter max-w-3xl mx-auto space-y-6" x-data="{
  sections: ['A'],
  subjects: [{ name: '', code: '', max_marks: 100, pass_marks: 35 }],
  addSection() { this.sections.push('') },
  removeSection(i) { if (this.sections.length > 1) this.sections.splice(i, 1) },
  addSubject() { this.subjects.push({ name: '', code: '', max_marks: 100, pass_marks: 35 }) },
  removeSubject(i) { this.subjects.splice(i, 1) },
}">

  {{-- Header --}}
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.classes.index') }}"
      class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
    </a>
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Add New Class</h1>
      <p class="text-sm text-slate-400">Create a class with sections and subjects</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-5">
    @csrf

    {{-- Class Information --}}
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

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="form-label">Class Name *</label>
          <input name="name" value="{{ old('name') }}" placeholder="e.g. I, VI, X, XI" required class="form-input"/>
          @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="form-label">Display Name</label>
          <input name="display_name" value="{{ old('display_name') }}" placeholder="e.g. Class I" class="form-input"/>
        </div>
        <div>
          <label class="form-label">Level (1–13) *</label>
          <input name="level" type="number" min="1" max="13" value="{{ old('level') }}" required class="form-input"/>
          @error('level')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
      <div class="md:w-1/3">
        <label class="form-label">Sort Order</label>
        <input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="form-input"/>
      </div>
    </div>

    {{-- Sections --}}
    <div class="card space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg gradient-sky flex items-center justify-center"
            style="box-shadow:0 4px 12px rgba(6,182,212,0.3)">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <h2 class="font-semibold text-slate-700">Sections</h2>
        </div>
        <button type="button" @click="addSection()"
          class="flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          Add Section
        </button>
      </div>

      <div class="flex flex-wrap gap-3">
        <template x-for="(sec, i) in sections" :key="i">
          <div class="flex items-center gap-2">
            <input :name="`sections[${i}]`" x-model="sections[i]" placeholder="A" maxlength="5"
              class="w-24 form-input text-center font-semibold"/>
            <button type="button" @click="removeSection(i)"
              x-show="sections.length > 1"
              class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </template>
      </div>
      <p class="text-xs text-slate-400">Enter section labels like A, B, C or I, II, III</p>
    </div>

    {{-- Subjects --}}
    <div class="card space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg gradient-success flex items-center justify-center"
            style="box-shadow:0 4px 12px rgba(16,185,129,0.3)">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>
          <h2 class="font-semibold text-slate-700">Subjects</h2>
        </div>
        <button type="button" @click="addSubject()"
          class="flex items-center gap-1.5 text-sm font-medium text-emerald-600 hover:text-emerald-800">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          Add Subject
        </button>
      </div>

      <div class="space-y-0">
        {{-- Column headers --}}
        <div class="grid grid-cols-12 gap-2 mb-2 px-1">
          <div class="col-span-5 text-[10px] font-bold text-slate-500 uppercase tracking-wide">Subject Name</div>
          <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-wide">Code</div>
          <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-wide">Max</div>
          <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-wide">Pass</div>
          <div class="col-span-1"></div>
        </div>

        <div class="space-y-2">
          <template x-for="(sub, i) in subjects" :key="i">
            <div class="grid grid-cols-12 gap-2 items-center">
              <div class="col-span-5">
                <input :name="`subjects[${i}][name]`" x-model="sub.name" placeholder="e.g. Mathematics"
                  class="form-input w-full" required/>
              </div>
              <div class="col-span-2">
                <input :name="`subjects[${i}][code]`" x-model="sub.code" placeholder="MATH" maxlength="10"
                  class="form-input w-full text-center"/>
              </div>
              <div class="col-span-2">
                <input :name="`subjects[${i}][max_marks]`" x-model.number="sub.max_marks" type="number" min="0"
                  class="form-input w-full text-center"/>
              </div>
              <div class="col-span-2">
                <input :name="`subjects[${i}][pass_marks]`" x-model.number="sub.pass_marks" type="number" min="0"
                  class="form-input w-full text-center"/>
              </div>
              <div class="col-span-1 flex justify-center">
                <button type="button" @click="removeSubject(i)"
                  class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
      <button type="submit" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        Create Class
      </button>
      <a href="{{ route('admin.classes.index') }}" class="btn-secondary">Cancel</a>
    </div>

  </form>
</div>
@endsection
