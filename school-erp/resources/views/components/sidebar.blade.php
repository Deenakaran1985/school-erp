<aside
  :class="sidebarOpen ? 'w-64' : 'w-16'"
  class="bg-slate-900 text-slate-300 flex flex-col transition-all duration-300 ease-in-out overflow-y-auto overflow-x-hidden flex-shrink-0">

  <!-- Logo row -->
  <div class="flex items-center gap-3 px-4 py-5 border-b border-slate-800">
    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
      <span class="text-white text-sm font-bold">S</span>
    </div>
    <span x-show="sidebarOpen" class="font-bold text-white text-sm truncate">
      {{ config('app.name', 'School ERP') }}
    </span>
  </div>

  <!-- Nav items -->
  <nav class="flex-1 px-2 py-4 space-y-1">

    <!-- Dashboard (all roles) -->
    @include('components.sidebar-item', [
      'route' => 'admin.dashboard', 'icon' => '🏠', 'label' => 'Dashboard'
    ])

    <!-- STUDENTS -->
    @canany(['student.view', 'student.create'])
      <div class="pt-3 pb-1">
        <p x-show="sidebarOpen" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
          Students
        </p>
      </div>
      @include('components.sidebar-item', [
        'route' => 'admin.students.index', 'icon' => '🎓', 'label' => 'All Students'
      ])
      @can('student.import')
        @include('components.sidebar-item', [
          'route' => 'admin.students.emis.form', 'icon' => '📥', 'label' => 'EMIS Import'
        ])
      @endcan
    @endcanany

    <!-- STAFF -->
    @can('staff.view')
      <div class="pt-3 pb-1">
        <p x-show="sidebarOpen" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
          Staff
        </p>
      </div>
      @include('components.sidebar-item', [
        'route' => 'admin.staff.index', 'icon' => '👩‍🏫', 'label' => 'All Staff'
      ])
    @endcan

    <!-- ACADEMICS -->
    @canany(['attendance.view', 'homework.view', 'timetable.view'])
      <div class="pt-3 pb-1">
        <p x-show="sidebarOpen" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
          Academics
        </p>
      </div>
      @can('attendance.view')
        @include('components.sidebar-item', [
          'route' => 'admin.dashboard', 'icon' => '✅', 'label' => 'Attendance'
        ])
      @endcan
      @can('homework.view')
        @include('components.sidebar-item', [
          'route' => 'admin.dashboard', 'icon' => '📝', 'label' => 'Homework'
        ])
      @endcan
    @endcanany

    <!-- EXAMS -->
    @can('exam.view')
      <div class="pt-3 pb-1">
        <p x-show="sidebarOpen" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
          Exams
        </p>
      </div>
      @include('components.sidebar-item', [
        'route' => 'admin.exams.index', 'icon' => '📋', 'label' => 'Exams'
      ])
    @endcan

    <!-- FEES -->
    @can('fee.view')
      <div class="pt-3 pb-1">
        <p x-show="sidebarOpen" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
          Finance
        </p>
      </div>
      @include('components.sidebar-item', [
        'route' => 'admin.fees.collect', 'icon' => '💳', 'label' => 'Fee Collection'
      ])
    @endcan

    @can('payroll.view')
      @include('components.sidebar-item', [
        'route' => 'admin.payroll.index', 'icon' => '💰', 'label' => 'Payroll'
      ])
    @endcan

    @can('expense.view')
      @include('components.sidebar-item', [
        'route' => 'admin.expenses.index', 'icon' => '💸', 'label' => 'Expenses'
      ])
    @endcan

    <!-- FLEET -->
    @can('fleet.view')
      <div class="pt-3 pb-1">
        <p x-show="sidebarOpen" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
          Fleet
        </p>
      </div>
      @include('components.sidebar-item', [
        'route' => 'admin.fleet.index', 'icon' => '🚌', 'label' => 'Vehicles'
      ])
    @endcan

    <!-- SETTINGS -->
    @can('settings.manage')
      <div class="pt-3 pb-1">
        <p x-show="sidebarOpen" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
          System
        </p>
      </div>
      @include('components.sidebar-item', [
        'route' => 'admin.settings', 'icon' => '⚙️', 'label' => 'Settings'
      ])
    @endcan

  </nav>

  <!-- Sidebar toggle button -->
  <div class="border-t border-slate-800 p-3">
    <button @click="sidebarOpen = !sidebarOpen"
      class="w-full flex items-center justify-center p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors">
      <span x-show="sidebarOpen">◀ Collapse</span>
      <span x-show="!sidebarOpen">▶</span>
    </button>
  </div>
</aside>