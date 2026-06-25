<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 — Access Denied</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-[#F0F2F9] min-h-screen flex items-center justify-center px-4">

  <div class="text-center max-w-md">

    {{-- Icon --}}
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl gradient-danger mb-6"
      style="box-shadow:0 12px 32px rgba(244,63,94,0.35)">
      <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
    </div>

    {{-- Code --}}
    <p class="text-8xl font-extrabold text-gradient mb-2">403</p>

    {{-- Title --}}
    <h1 class="text-2xl font-bold text-slate-800 mb-3">Access Denied</h1>
    <p class="text-slate-400 text-sm leading-relaxed mb-8">
      {{ $exception->getMessage() ?: "You don't have permission to view this page. Contact your administrator if you think this is a mistake." }}
    </p>

    {{-- Actions --}}
    <div class="flex items-center justify-center gap-3">
      <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
        class="btn-secondary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Go Back
      </a>
      <a href="{{ route('admin.dashboard') }}" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
      </a>
    </div>

    <p class="text-xs text-slate-300 mt-8">School ERP &middot; Error 403</p>
  </div>

</body>
</html>
