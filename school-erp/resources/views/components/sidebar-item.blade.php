@php
  $isActive = request()->routeIs(str_replace('.index', '', $route) . '*');
@endphp
<a href="{{ route($route) }}"
  class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 group relative
         {{ $isActive
             ? 'nav-active font-semibold'
             : 'text-slate-500 hover:text-slate-200 hover:bg-white/5' }}"
  title="{{ $label }}">
  {{-- Icon --}}
  <svg class="w-[18px] h-[18px] flex-shrink-0 transition-colors {{ $isActive ? 'text-indigo-300' : 'text-slate-500 group-hover:text-slate-300' }}"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon_path }}"/>
  </svg>
  {{-- Label --}}
  <span x-show="sidebarOpen" class="truncate leading-none">{{ $label }}</span>
  {{-- Tooltip when collapsed --}}
  <span x-show="!sidebarOpen"
    class="absolute left-full ml-3 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded-lg whitespace-nowrap
           opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50 shadow-lg">
    {{ $label }}
  </span>
</a>