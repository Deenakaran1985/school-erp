@extends('layouts.app')
@section('title', 'Add Staff')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.staff.index') }}" class="text-slate-400 hover:text-slate-600">← Back</a>
    <h3 class="text-lg font-semibold text-slate-700">Add New Staff</h3>
  </div>

  <form method="POST" action="{{ route('admin.staff.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- Personal Details --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Personal Details</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Full Name *</label>
          <input name="name" value="{{ old('name') }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
          @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Phone (Login ID) *</label>
          <input name="phone" value="{{ old('phone') }}" required maxlength="10" pattern="\d{10}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
          @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Gender *</label>
          <select name="gender" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50">
            <option value="">Select</option>
            <option value="M" @selected(old('gender')==='M')>Male</option>
            <option value="F" @selected(old('gender')==='F')>Female</option>
            <option value="O" @selected(old('gender')==='O')>Other</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Date of Birth *</label>
          <input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Qualification</label>
          <input name="qualification" value="{{ old('qualification') }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Aadhar Number</label>
          <input name="aadhar_number" value="{{ old('aadhar_number') }}" maxlength="16"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">PAN Number</label>
          <input name="pan_number" value="{{ old('pan_number') }}" maxlength="10" style="text-transform:uppercase"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Photo</label>
          <input name="photo" type="file" accept="image/*"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
      </div>
    </div>

    {{-- Job Details --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Job Details</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Department *</label>
          <select name="department_id" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50">
            <option value="">Select Department</option>
            @foreach($departments as $dept)
              <option value="{{ $dept->id }}" @selected(old('department_id') == $dept->id)>{{ $dept->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Designation *</label>
          <input name="designation" value="{{ old('designation') }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Staff Type *</label>
          <select name="staff_type" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50">
            <option value="">Select</option>
            <option value="teaching" @selected(old('staff_type')==='teaching')>Teaching</option>
            <option value="non_teaching" @selected(old('staff_type')==='non_teaching')>Non-Teaching</option>
            <option value="admin" @selected(old('staff_type')==='admin')>Admin</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Joining Date *</label>
          <input name="joining_date" type="date" value="{{ old('joining_date') }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
      </div>
    </div>

    {{-- Salary --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Salary Structure</h4>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Basic Salary (₹) *</label>
          <input name="basic_salary" type="number" min="0" step="0.01" value="{{ old('basic_salary') }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">DA %</label>
          <input name="da_percent" type="number" min="0" max="100" step="0.01" value="{{ old('da_percent', 0) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">HRA %</label>
          <input name="hra_percent" type="number" min="0" max="100" step="0.01" value="{{ old('hra_percent', 0) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Other Allowance (₹)</label>
          <input name="other_allowance" type="number" min="0" step="0.01" value="{{ old('other_allowance', 0) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">PF %</label>
          <input name="pf_percent" type="number" min="0" max="100" step="0.01" value="{{ old('pf_percent', 12) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
      </div>
    </div>

    {{-- Bank Details --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Bank Details</h4>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Bank Name</label>
          <input name="bank_name" value="{{ old('bank_name') }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Account Number</label>
          <input name="bank_account" value="{{ old('bank_account') }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">IFSC Code</label>
          <input name="bank_ifsc" value="{{ old('bank_ifsc') }}" style="text-transform:uppercase"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
      </div>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">
        Save Staff
      </button>
      <a href="{{ route('admin.staff.index') }}" class="px-6 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">
        Cancel
      </a>
    </div>
  </form>
</div>
@endsection
