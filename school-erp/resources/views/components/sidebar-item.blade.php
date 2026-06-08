<a href="{{ route($route) }}"
  class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-150
         {{ request()->routeIs(str_replace('.index','',$route).'*')
             ? 'bg-blue-600 text-white font-medium'
             : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
  <span class="text-base flex-shrink-0">{{ $icon }}</span>
  <span x-show="sidebarOpen" class="truncate">{{ $label }}</span>
</a>