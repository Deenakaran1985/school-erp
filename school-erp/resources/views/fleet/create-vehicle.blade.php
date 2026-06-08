@extends('layouts.app')
@section('title','Add Vehicle')
@section('page_title','Add Vehicle')

@section('content')
<form method="POST" action="{{ route('admin.fleet.store-vehicle') }}"
  class="max-w-2xl">
  @csrf
  <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
    <h3 class="font-semibold text-slate-700">🚌 Vehicle Details</h3>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Vehicle Number *</label>
        <input type="text" name="vehicle_number" required
          placeholder="TN33 AB 1234"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm uppercase"/>
        @error('vehicle_number')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Type *</label>
        <select name="vehicle_type" required
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          @foreach(['bus'=>'Bus','van'=>'Van','auto'=>'Auto','tempo'=>'Tempo Traveller'] as $v=>$l)
            <option value="{{ $v }}">{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Make & Model</label>
        <input type="text" name="make_model"
          placeholder="e.g. Tata Starbus"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Seating Capacity *</label>
        <input type="number" name="capacity" min="1" required
          placeholder="40"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Route</label>
        <select name="route_id"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          <option value="">Unassigned</option>
          @foreach($routes as $r)
            <option value="{{ $r->id }}">{{ $r->route_number }} — {{ $r->route_name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Driver</label>
        <select name="driver_id"
          class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
          <option value="">Unassigned</option>
          @foreach($drivers as $d)
            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->employee_id }})</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
      @foreach(['insurance_expiry'=>'Insurance Expiry','fitness_expiry'=>'Fitness Expiry','permit_expiry'=>'Permit Expiry'] as $n=>$l)
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">{{ $l }}</label>
          <input type="date" name="{{ $n }}"
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
        </div>
      @endforeach
    </div>

    <div class="flex gap-3">
      <button type="submit"
        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm">
        Save Vehicle
      </button>
      <a href="{{ route('admin.fleet.index') }}"
        class="px-6 py-2.5 bg-slate-100 text-slate-600 font-medium rounded-xl text-sm">Cancel</a>
    </div>
  </div>
</form>
@endsection