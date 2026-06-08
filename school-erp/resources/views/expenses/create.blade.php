@extends('layouts.app')
@section('title','Add Expense')
@section('page_title','Add New Expense')

@section('content')
<form method="POST" action="{{ route('admin.expenses.store') }}"
  enctype="multipart/form-data" class="max-w-2xl space-y-5">
  @csrf

  <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
    <h3 class="font-semibold text-slate-700">💸 Expense Details</h3>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Category *</label>
        <select name="expense_head_id" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          <option value="">Select category</option>
          @foreach($heads as $h)
            <option value="{{ $h->id }}" @selected(old('expense_head_id')==$h->id)>
              {{ $h->name }}
            </option>
          @endforeach
        </select>
        @error('expense_head_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Expense Date *</label>
        <input type="date" name="expense_date"
          value="{{ old('expense_date', now()->format('Y-m-d')) }}" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        @error('expense_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-600 mb-1">Title *</label>
      <input type="text" name="title" value="{{ old('title') }}" required
        placeholder="e.g. Stationery purchase for exams"
        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Amount (₹) *</label>
        <input type="number" name="amount" value="{{ old('amount') }}"
          min="1" step="0.01" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        @error('amount')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Vendor / Supplier</label>
        <input type="text" name="vendor_name" value="{{ old('vendor_name') }}"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Bill No.</label>
        <input type="text" name="bill_no" value="{{ old('bill_no') }}"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Bill / Receipt (PDF/Image)</label>
        <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
          class="w-full text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700"/>
        @error('attachment')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-600 mb-1">Description</label>
      <textarea name="description" rows="2"
        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">{{ old('description') }}</textarea>
    </div>
  </div>

  <div class="flex gap-3">
    <button type="submit"
      class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm">
      Submit for Approval
    </button>
    <a href="{{ route('admin.expenses.index') }}"
      class="px-6 py-2.5 bg-slate-100 text-slate-600 font-medium rounded-xl text-sm">Cancel</a>
  </div>
</form>
@endsection