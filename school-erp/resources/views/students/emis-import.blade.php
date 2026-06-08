@extends('layouts.app')
@section('title', 'EMIS Import')
@section('page_title', 'EMIS Excel Import')

@section('content')
<div class="max-w-2xl space-y-6">

  <!-- Result summary -->
  @if(session('import_summary'))
    @php $s = session('import_summary'); @endphp
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
      <h3 class="font-semibold text-slate-700 mb-4">✅ Import Complete</h3>
      <div class="grid grid-cols-4 gap-3 mb-4">
        @foreach([
          ['Imported', $s['imported'], 'green'],
          ['Updated', $s['updated'], 'blue'],
          ['Skipped', $s['skipped'], 'amber'],
          ['Failed', $s['failed'], 'red'],
        ] as [$label, $val, $color])
          <div class="bg-{{ $color }}-50 rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-{{ $color }}-600">{{ $val }}</p>
            <p class="text-xs text-slate-400">{{ $label }}</p>
          </div>
        @endforeach
      </div>
      @if(count($s['errors']))
        <div class="mt-3 border border-red-200 rounded-xl overflow-hidden">
          <div class="px-4 py-2 bg-red-50 text-sm font-medium text-red-700">❌ Error Details</div>
          @foreach($s['errors'] as $err)
            <div class="px-4 py-2 text-xs border-t border-red-100">
              <span class="font-mono text-red-600">{{ $err['emis'] }}</span> —
              {{ $err['name'] }}: <span class="text-slate-500">{{ $err['error'] }}</span>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @endif

  <!-- Upload form -->
  <form method="POST" action="{{ route('admin.students.emis.import') }}"
    enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
    @csrf

    <div>
      <label class="block text-sm font-medium text-slate-600 mb-1">EMIS Excel File *</label>
      <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
        class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
      <p class="text-xs text-slate-400 mt-1">Supported: .xlsx .xls .csv · Max 5MB</p>
      @error('excel_file')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Academic Year *</label>
        <select name="academic_year_id" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          @foreach(\App\Models\AcademicYear::orderByDesc('start_date')->get() as $yr)
            <option value="{{ $yr->id }}" @selected($yr->is_current)>{{ $yr->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">If EMIS duplicate</label>
        <select name="duplicate_mode"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          <option value="skip">Skip (keep existing)</option>
          <option value="update">Update existing record</option>
        </select>
      </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-700">
      <p class="font-semibold mb-1">⚠️ Expected EMIS column headers (row 1):</p>
      <p class="font-mono">EMIS_NO, STU_NAME, FATHER_NAME, MOTHER_NAME, DOB, GENDER, COMMUNITY, MOBILE_NO, CLASS, SECTION, AADHAR_NO</p>
      <p class="mt-1">Tip: Export directly from the EMIS portal. Do NOT add/remove columns.</p>
    </div>

    <button type="submit"
      class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl">
      📥 Start Import
    </button>
  </form>

</div>
@endsection