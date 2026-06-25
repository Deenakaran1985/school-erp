<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'School ERP')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body class="bg-[#F0F2F9] font-sans antialiased" x-data="{ sidebarOpen: true, sidebarMobile: false }">

  <div class="flex h-screen overflow-hidden">

    {{-- Mobile overlay --}}
    <div x-show="sidebarMobile" @click="sidebarMobile=false"
      x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20 lg:hidden"></div>

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Main content --}}
    <div class="flex flex-col flex-1 overflow-hidden min-w-0">

      {{-- Topbar --}}
      @include('components.navbar')

      {{-- Page content --}}
      <main class="flex-1 overflow-y-auto px-6 py-5">

        {{-- Flash messages --}}
        @if (session('success'))
          <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="mb-5 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm">
            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="flex-1 font-medium">{{ session('success') }}</span>
            <button @click="show=false" class="text-emerald-400 hover:text-emerald-600">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        @endif

        @if (session('error'))
          <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
          </div>
        @endif

        <div class="page-enter">
          @yield('content')
        </div>
      </main>
    </div>
  </div>

  @stack('scripts')
</body>
</html>