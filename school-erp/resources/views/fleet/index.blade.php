@extends('layouts.app')
@section('title','Fleet')
@section('page_title','Fleet Management')

@section('content')
<div class="space-y-6">

  <!-- Expiry warnings -->
  @if($expiringSoon->count())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
      <p class="text-sm font-semibold text-red-700 mb-2">
        ⚠️ {{ $expiringSoon->count() }} vehicle(s) with documents expiring within 30 days:
      </p>
      <div class="flex flex-wrap gap-2">
        @foreach($expiringSoon as $v)
          <span class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded-full font-medium">
            🚌 {{ $v->vehicle_number }}
            @if($v->is_insurance_expired) · Insurance expired @endif
          </span>
        @endforeach
      </div>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Vehicles -->
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-slate-700">🚌 Vehicles ({{ $vehicles->count() }})</h3>
        @can('fleet.manage')
          <a href="{{ route('admin.fleet.create-vehicle') }}"
            class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg">
            + Add Vehicle
          </a>
        @endcan
      </div>
      @forelse($vehicles as $vehicle)
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
          <div class="flex items-start justify-between mb-2">
            <div>
              <p class="font-bold text-slate-800 text-base">🚌 {{ $vehicle->vehicle_number }}</p>
              <p class="text-xs text-slate-400">
                {{ $vehicle->make_model ?? $vehicle->vehicle_type }} ·
                Capacity: {{ $vehicle->capacity }}
              </p>
            </div>
            <span class="px-2 py-0.5 text-xs rounded-full capitalize
              {{ $vehicle->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
              {{ $vehicle->status }}
            </span>
          </div>
          <div class="grid grid-cols-2 gap-2 text-xs text-slate-500">
            <div>🛣 Route: {{ $vehicle->route?->route_name ?? 'Not assigned' }}</div>
            <div>👤 Driver: {{ $vehicle->driver?->name ?? 'Not assigned' }}</div>
            <div class="{{ $vehicle->is_insurance_expired ? 'text-red-500 font-medium' : '' }}">
              🛡 Insurance: {{ $vehicle->insurance_expiry?->format('d M Y') ?? '—' }}
            </div>
            <div>🔧 Fitness: {{ $vehicle->fitness_expiry?->format('d M Y') ?? '—' }}</div>
          </div>
        </div>
      @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center text-slate-400">
          No vehicles added yet.
        </div>
      @endforelse
    </div>

    <!-- Routes + Add Route form -->
    <div class="space-y-3">
      <h3 class="font-semibold text-slate-700">🛣 Routes ({{ $routes->count() }})</h3>

      @can('fleet.manage')
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
          <p class="text-sm font-medium text-slate-600 mb-3">➕ Add New Route</p>
          <form method="POST" action="{{ route('admin.fleet.store-route') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
              <div>
                <input type="text" name="route_name" required
                  placeholder="Route Name *"
                  class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
              </div>
              <div>
                <input type="text" name="route_number" required
                  placeholder="Route No. *"
                  class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
              </div>
              <div>
                <input type="time" name="pickup_start_time"
                  placeholder="Pickup Time"
                  class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
              </div>
              <div>
                <input type="number" name="monthly_fee"
                  placeholder="Monthly Fee ₹" min="0"
                  class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
              </div>
            </div>
            <input type="text" name="stops"
              placeholder="Stops (comma-separated): Stop A, Stop B, Stop C"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
            <button type="submit"
              class="w-full py-2 bg-blue-600 text-white text-sm font-medium rounded-xl">
              Add Route
            </button>
          </form>
        </div>
      @endcan

      @foreach($routes as $route)
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
          <div class="flex items-center justify-between mb-2">
            <div>
              <p class="font-semibold text-slate-700">
                Route {{ $route->route_number }} — {{ $route->route_name }}
              </p>
              <p class="text-xs text-slate-400">
                {{ $route->student_transports_count }} students ·
                Monthly fee: ₹{{ number_format($route->monthly_fee) }}
              </p>
            </div>
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
              🕐 {{ $route->pickup_start_time ?? '—' }}
            </span>
          </div>
          @if($route->stops && count($route->stops))
            <div class="flex flex-wrap gap-1 mt-2">
              @foreach($route->stops as $stop)
                <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">
                  📍 {{ $stop }}
                </span>
              @endforeach
            </div>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection