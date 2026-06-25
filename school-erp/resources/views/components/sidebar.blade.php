<aside :class="sidebarOpen ? 'w-64' : 'w-[72px]'"
  class="flex-shrink-0 flex flex-col transition-all duration-300 ease-in-out overflow-y-auto overflow-x-hidden"
  style="background:#0C1228;">

  {{-- Logo --}}
  <div class="flex items-center gap-3 px-4 py-5 flex-shrink-0" style="border-bottom:1px solid rgba(255,255,255,0.06)">
    <div class="w-9 h-9 rounded-xl gradient-brand flex items-center justify-center flex-shrink-0" style="box-shadow:0 4px 12px rgba(99,102,241,0.4)">
      <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
      </svg>
    </div>
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-75" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
      <p class="text-white font-bold text-sm leading-tight truncate">{{ config('app.name', 'School ERP') }}</p>
      <p class="text-indigo-400/60 text-[11px] truncate">Management System</p>
    </div>
  </div>

  {{-- Nav --}}
  <nav class="flex-1 px-3 py-4 space-y-0.5">

    @include('components.sidebar-item', ['route'=>'admin.dashboard','label'=>'Dashboard','icon_path'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'])

    @canany(['student.view', 'student.create'])
      <div class="pt-5 pb-1.5" x-show="sidebarOpen"><p class="px-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Students</p></div>
      <div class="border-t border-white/5 my-2" x-show="!sidebarOpen"></div>
      @include('components.sidebar-item', ['route'=>'admin.students.index','label'=>'All Students','icon_path'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'])
      @include('components.sidebar-item', ['route'=>'admin.classes.index','label'=>'Classes','icon_path'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'])
      @can('student.import')
        @include('components.sidebar-item', ['route'=>'admin.students.emis.form','label'=>'EMIS Import','icon_path'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'])
      @endcan
    @endcanany

    @can('staff.view')
      <div class="pt-5 pb-1.5" x-show="sidebarOpen"><p class="px-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Staff</p></div>
      <div class="border-t border-white/5 my-2" x-show="!sidebarOpen"></div>
      @include('components.sidebar-item', ['route'=>'admin.staff.index','label'=>'All Staff','icon_path'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'])
    @endcan

    @can('exam.view')
      <div class="pt-5 pb-1.5" x-show="sidebarOpen"><p class="px-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Academics</p></div>
      <div class="border-t border-white/5 my-2" x-show="!sidebarOpen"></div>
      @include('components.sidebar-item', ['route'=>'admin.exams.index','label'=>'Exams & Results','icon_path'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'])
    @endcan

    @canany(['fee.view', 'payroll.view', 'expense.view'])
      <div class="pt-5 pb-1.5" x-show="sidebarOpen"><p class="px-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Finance</p></div>
      <div class="border-t border-white/5 my-2" x-show="!sidebarOpen"></div>
      @can('fee.view')
        @include('components.sidebar-item', ['route'=>'admin.fees.collect','label'=>'Fee Collection','icon_path'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'])
      @endcan
      @can('payroll.view')
        @include('components.sidebar-item', ['route'=>'admin.payroll.index','label'=>'Payroll','icon_path'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'])
      @endcan
      @can('expense.view')
        @include('components.sidebar-item', ['route'=>'admin.expenses.index','label'=>'Expenses','icon_path'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'])
      @endcan
    @endcanany

    @can('fleet.view')
      <div class="pt-5 pb-1.5" x-show="sidebarOpen"><p class="px-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Fleet</p></div>
      <div class="border-t border-white/5 my-2" x-show="!sidebarOpen"></div>
      @include('components.sidebar-item', ['route'=>'admin.fleet.index','label'=>'Vehicles','icon_path'=>'M8 17a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0zM3 5h11M5 5v3h11V5M3 9h11m-5 8H8m5 0h2'])
    @endcan

    @include('components.sidebar-item', ['route'=>'admin.notifications.index','label'=>'Notifications','icon_path'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'])

    @can('settings.manage')
      <div class="pt-5 pb-1.5" x-show="sidebarOpen"><p class="px-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest">System</p></div>
      <div class="border-t border-white/5 my-2" x-show="!sidebarOpen"></div>
      @include('components.sidebar-item', ['route'=>'admin.settings','label'=>'Settings','icon_path'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'])
    @endcan

  </nav>

  {{-- Collapse toggle --}}
  <div class="p-3 flex-shrink-0" style="border-top:1px solid rgba(255,255,255,0.06)">
    <button @click="sidebarOpen = !sidebarOpen"
      class="w-full flex items-center justify-center gap-2 p-2.5 rounded-xl text-slate-500 hover:text-white hover:bg-white/5 transition-all">
      <svg :class="sidebarOpen ? '' : 'rotate-180'" class="w-4 h-4 transition-transform duration-300"
        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
      </svg>
      <span x-show="sidebarOpen" class="text-xs font-medium">Collapse</span>
    </button>
  </div>
</aside>