@extends('layouts.app')
@section('title', 'Add Student')
@section('page_title', 'Add New Student')

@section('content')
<form method="POST" action="{{ route('admin.students.store') }}"
  enctype="multipart/form-data" class="space-y-6 max-w-4xl">
  @csrf

  <!-- Personal Info -->
  <div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-700 mb-4">👤 Personal Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

      <!-- Name -->
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-600 mb-1">Student Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" required
          class="w-full px-3 py-2 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                 @error('name') border-red-400 @else border-slate-200 @enderror"/>
        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <!-- Gender -->
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Gender *</label>
        <select name="gender" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="M" @selected(old('gender')==='M')>Male</option>
          <option value="F" @selected(old('gender')==='F')>Female</option>
          <option value="O" @selected(old('gender')==='O')>Other</option>
        </select>
      </div>

      <!-- Father name -->
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Father's Name *</label>
        <input type="text" name="father_name" value="{{ old('father_name') }}" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        @error('father_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <!-- Mother name -->
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Mother's Name</label>
        <input type="text" name="mother_name" value="{{ old('mother_name') }}"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
      </div>

      <!-- DOB -->
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Date of Birth *</label>
        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        @error('date_of_birth')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <!-- Community -->
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Community</label>
        <select name="community"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          <option value="">Select</option>
          @foreach(['OC','BC','MBC','SC','ST'] as $c)
            <option value="{{ $c }}" @selected(old('community')===$c)>{{ $c }}</option>
          @endforeach
        </select>
      </div>

      <!-- Blood Group -->
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Blood Group</label>
        <select name="blood_group"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          <option value="">Unknown</option>
          @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
            <option value="{{ $bg }}" @selected(old('blood_group')===$bg)>{{ $bg }}</option>
          @endforeach
        </select>
      </div>

      <!-- Aadhar -->
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Aadhar Number</label>
        <input type="text" name="aadhar_number" value="{{ old('aadhar_number') }}"
          maxlength="12" pattern="[0-9]{12}" placeholder="12 digits"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        @error('aadhar_number')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <!-- EMIS -->
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">EMIS Number</label>
        <input type="text" name="emis_number" value="{{ old('emis_number') }}"
          placeholder="e.g. 33010120001"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        @error('emis_number')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

    </div>
  </div>

  <!-- Academic Info -->
  <div class="bg-white rounded-2xl border border-slate-200 p-6"
    x-data="{ classId: '{{ old('school_class_id') }}', sections: [] }"
    x-init="$watch('classId', val => {
      if (!val) { sections = []; return; }
      fetch('/admin/students/sections?class_id=' + val)
        .then(r => r.json()).then(d => sections = d);
    })">

    <h3 class="font-semibold text-slate-700 mb-4">🏫 Academic Details</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Academic Year *</label>
        <select name="academic_year_id" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          @foreach($years as $yr)
            <option value="{{ $yr->id }}" @selected($yr->is_current)>{{ $yr->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Class *</label>
        <select name="school_class_id" x-model="classId" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          <option value="">Select Class</option>
          @foreach($classes as $class)
            <option value="{{ $class->id }}">Class {{ $class->name }}</option>
          @endforeach
        </select>
        @error('school_class_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Section</label>
        <select name="section_id"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          <option value="">Select Section</option>
          <template x-for="sec in sections" :key="sec.id">
            <option :value="sec.id" x-text="sec.name"></option>
          </template>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Roll Number</label>
        <input type="number" name="roll_number" value="{{ old('roll_number') }}" min="1"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      </div>

    </div>
  </div>

  <!-- Contact -->
  <div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-700 mb-4">📱 Contact Details</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Parent Mobile *</label>
        <input type="tel" name="parent_mobile" value="{{ old('parent_mobile') }}"
          required maxlength="10" pattern="[0-9]{10}"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        @error('parent_mobile')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Alt Mobile</label>
        <input type="tel" name="alt_mobile" value="{{ old('alt_mobile') }}"
          maxlength="10"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Pincode</label>
        <input type="text" name="pincode" value="{{ old('pincode') }}"
          maxlength="6"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      </div>
      <div class="md:col-span-3">
        <label class="block text-sm font-medium text-slate-600 mb-1">Address</label>
        <textarea name="address" rows="2"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">{{ old('address') }}</textarea>
      </div>
    </div>
  </div>

  <!-- Photo -->
  <div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-700 mb-4">📷 Photo</h3>
    <input type="file" name="photo" accept="image/*"
      class="text-sm text-slate-600"/>
    <p class="text-xs text-slate-400 mt-1">Max 2MB. JPG, PNG accepted.</p>
  </div>

  <!-- Actions -->
  <div class="flex gap-3">
    <button type="submit"
      class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm">
      Save Student
    </button>
    <a href="{{ route('admin.students.index') }}"
      class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium rounded-xl text-sm">
      Cancel
    </a>
  </div>

</form>

@push('scripts')
<script>
  // Add sections GET route to web.php for Alpine.js fetch:
  // Route::get('students/sections', [StudentController::class, 'getSections'])->name('students.sections');
</script>
@endpush
@endsection