<header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between flex-shrink-0">

  <!-- Left: mobile toggle + breadcrumb -->
  <div class="flex items-center gap-4">
    <button @click="sidebarOpen = !sidebarOpen"
      class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 lg:hidden">
      ☰
    </button>
    <div>
      <h2 class="text-sm font-semibold text-slate-800">
        @yield('page_title', 'Dashboard')
      </h2>
      <p class="text-xs text-slate-400">
        {{ config('school.name', 'School ERP') }} ·
        {{ \App\Models\AcademicYear::current()->name ?? '' }}
      </p>
    </div>
  </div>

  <!-- Right: user menu -->
  <div class="flex items-center gap-4" x-data="{ open: false }">

    <!-- Notification bell -->
    <button class="relative p-2 rounded-lg hover:bg-slate-100 text-slate-500">
      🔔
    </button>

    <!-- User dropdown -->
    <div class="relative">
      <button @click="open = !open"
        class="flex items-center gap-2 p-1 rounded-lg hover:bg-slate-100">
        <img
          src="{{ auth()->user()->avatar_url }}"
          class="w-8 h-8 rounded-full object-cover bg-slate-200"
          alt="avatar"
        />
        <div class="hidden sm:block text-left">
          <p class="text-sm font-medium text-slate-700 leading-none">
            {{ auth()->user()->name }}
          </p>
          <p class="text-xs text-slate-400 mt-0.5">
            {{ auth()->user()->getRoleNames()->first() }}
          </p>
        </div>
        <span class="text-slate-400 text-xs">▾</span>
      </button>

      <!-- Dropdown menu -->
      <div x-show="open" @click.away="open = false"
        x-transition
        class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-lg z-50 py-1">
        <div class="px-4 py-2 border-b border-slate-100">
          <p class="text-xs text-slate-400">Signed in as</p>
          <p class="text-sm font-medium text-slate-700 truncate">{{ auth()->user()->email }}</p>
        </div>
        <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
          👤 My Profile
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-left">
            🚪 Sign Out
          </button>
        </form>
      </div>
    </div>
  </div>
</header>