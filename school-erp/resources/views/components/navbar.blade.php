<header class="flex-shrink-0 flex items-center justify-between px-6 py-3.5 glass-light"
  style="border-bottom:1px solid rgba(226,232,240,0.7);">

  {{-- Left --}}
  <div class="flex items-center gap-3">
    <button @click="sidebarOpen = !sidebarOpen"
      class="hidden lg:flex p-2 rounded-xl hover:bg-slate-100 text-slate-400 transition">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8M4 18h16"/>
      </svg>
    </button>
    <div>
      <h2 class="text-sm font-bold text-slate-800 leading-tight">@yield('page_title', 'Dashboard')</h2>
      <p class="text-[11px] text-slate-400 leading-tight mt-0.5">
        {{ config('school.name', 'School ERP') }}
        @php try { $ay = \App\Models\AcademicYear::current(); } catch(\Exception $e) { $ay = null; } @endphp
        @if($ay) &middot; {{ $ay->name }} @endif
      </p>
    </div>
  </div>

  {{-- Right --}}
  <div class="flex items-center gap-2" x-data="{ userOpen: false }">

    @if($ay ?? false)
      <span class="hidden sm:inline-flex badge badge-blue">{{ $ay->name }}</span>
    @endif

    <a href="{{ route('admin.notifications.index') }}"
      class="p-2 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-indigo-500 transition">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
      </svg>
    </a>

    <div class="w-px h-6 bg-slate-200 mx-1"></div>

    <div class="relative">
      <button @click="userOpen = !userOpen"
        class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition">
        @php
          $initials = collect(explode(' ', auth()->user()->name ?? 'U'))->map(fn($w)=>mb_substr($w,0,1))->take(2)->join('');
        @endphp
        <div class="w-8 h-8 rounded-xl gradient-brand flex items-center justify-center text-white text-xs font-bold">
          {{ strtoupper($initials) }}
        </div>
        <div class="hidden sm:block text-left">
          <p class="text-sm font-semibold text-slate-700 leading-none">{{ auth()->user()->name ?? '' }}</p>
          <p class="text-[11px] text-slate-400 mt-0.5 capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'Admin' }}</p>
        </div>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>

      <div x-show="userOpen" @click.away="userOpen = false"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute right-0 mt-2 w-52 bg-white border border-slate-200/80 rounded-2xl shadow-xl z-50 overflow-hidden">

        <div class="px-4 py-3" style="background:linear-gradient(135deg,#F5F3FF,#EEF2FF)">
          <p class="text-xs text-slate-500">Signed in as</p>
          <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->email ?? '' }}</p>
          <span class="badge badge-violet mt-1">{{ auth()->user()->getRoleNames()->first() ?? '' }}</span>
        </div>

        <div class="py-1.5 px-2">
          <a href="#" class="flex items-center gap-2.5 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-xl transition">
            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            My Profile
          </a>
          <a href="{{ route('admin.settings') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-xl transition">
            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Settings
          </a>
        </div>

        <div class="px-2 pb-2">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
              class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-500 hover:bg-red-50 rounded-xl transition font-medium">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              Sign Out
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</header>