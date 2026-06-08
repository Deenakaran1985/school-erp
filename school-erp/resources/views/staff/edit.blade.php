@extends('layouts.app')
@section('title', 'Edit Staff')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.staff.show', $staff) }}" class="text-slate-400 hover:text-slate-600">← Back</a>
    <h3 class="text-lg font-semibold text-slate-700">Edit Staff — {{ $staff->name }}</h3>
  </div>

  <form method="POST" action="{{ route('admin.staff.update', $staff) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Personal Details</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Full Name *</label>
          <input name="name" value="{{ old('name', $staff->name) }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Gender *</label>
          <select name="gender" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50">
            <option value="M" @selected(old('gender',$staff->gender)==='M')>Male</option>
            <option value="F" @selected(old('gender',$staff->gender)==='F')>Female</option>
            <option value="O" @selected(old('gender',$staff->gender)==='O')>Other</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Date of Birth *</label>
          <input name="date_of_birth" type="date" value="{{ old('date_of_birth', $staff->date_of_birth?->format('Y-m-d')) }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Qualification</label>
          <input name="qualification" value="{{ old('qualification', $staff->qualification) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Aadhar Number</label>
          <input name="aadhar_number" value="{{ old('aadhar_number', $staff->aadhar_number) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">PAN Number</label>
          <input name="pan_number" value="{{ old('pan_number', $staff->pan_number) }}" style="text-transform:uppercase"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Status *</label>
          <select name="status" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50">
            <option value="active"     @selected(old('status',$staff->status)==='active')>Active</option>
            <option value="inactive"   @selected(old('status',$staff->status)==='inactive')>Inactive</option>
            <option value="resigned"   @selected(old('status',$staff->status)==='resigned')>Resigned</option>
            <option value="terminated" @selected(old('status',$staff->status)==='terminated')>Terminated</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Photo (leave blank to keep)</label>
          <input name="photo" type="file" accept="image/*"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Job Details</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Department *</label>
          <select name="department_id" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50">
            @foreach($departments as $dept)
              <option value="{{ $dept->id }}" @selected(old('department_id',$staff->department_id)==$dept->id)>{{ $dept->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Designation *</label>
          <input name="designation" value="{{ old('designation', $staff->designation) }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Staff Type *</label>
          <select name="staff_type" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50">
            <option value="teaching"     @selected(old('staff_type',$staff->staff_type)==='teaching')>Teaching</option>
            <option value="non_teaching" @selected(old('staff_type',$staff->staff_type)==='non_teaching')>Non-Teaching</option>
            <option value="admin"        @selected(old('staff_type',$staff->staff_type)==='admin')>Admin</option>
          </select>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Salary Structure</h4>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Basic Salary (₹) *</label>
          <input name="basic_salary" type="number" min="0" step="0.01" value="{{ old('basic_salary', $staff->basic_salary) }}" required
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">DA %</label>
          <input name="da_percent" type="number" min="0" max="100" step="0.01" value="{{ old('da_percent', $staff->da_percent) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">HRA %</label>
          <input name="hra_percent" type="number" min="0" max="100" step="0.01" value="{{ old('hra_percent', $staff->hra_percent) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Other Allowance (₹)</label>
          <input name="other_allowance" type="number" min="0" step="0.01" value="{{ old('other_allowance', $staff->other_allowance) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">PF %</label>
          <input name="pf_percent" type="number" min="0" max="100" step="0.01" value="{{ old('pf_percent', $staff->pf_percent) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 class="font-semibold text-slate-700 text-sm uppercase tracking-wide">Bank Details</h4>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Bank Name</label>
          <input name="bank_name" value="{{ old('bank_name', $staff->bank_name) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Account Number</label>
          <input name="bank_account" value="{{ old('bank_account', $staff->bank_account) }}"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">IFSC Code</label>
          <input name="bank_ifsc" value="{{ old('bank_ifsc', $staff->bank_ifsc) }}" style="text-transform:uppercase"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50"/>
        </div>
      </div>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl">Update Staff</button>
      <a href="{{ route('admin.staff.show', $staff) }}" class="px-6 py-2 bg-slate-100 text-slate-600 text-sm rounded-xl hover:bg-slate-200">Cancel</a>
    </div>
  </form>
</div>
@endsection
