<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'School ERP')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body class="bg-slate-100 font-sans antialiased" x-data="{ sidebarOpen: true }">

  <div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main content area -->
    <div class="flex flex-col flex-1 overflow-hidden">

      <!-- Topbar -->
      @include('components.navbar')

      <!-- Page content -->
      <main class="flex-1 overflow-y-auto p-6">

        <!-- Flash messages -->
        @if (session('success'))
          <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-xl text-sm flex items-center justify-between">
            <span>✅ {{ session('success') }}</span>
            <button @click="show=false" class="text-green-600 hover:text-green-800">✕</button>
          </div>
        @endif

        @if (session('error'))
          <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded-xl text-sm">
            ❌ {{ session('error') }}
          </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  @stack('scripts')
</body>
</html>