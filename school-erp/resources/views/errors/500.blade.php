<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 — Server Error</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-[#F0F2F9] min-h-screen flex items-center justify-center px-4">

  <div class="text-center max-w-md">

    {{-- Icon --}}
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl gradient-warning mb-6"
      style="box-shadow:0 12px 32px rgba(245,158,11,0.35)">
      <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
      </svg>
    </div>

    {{-- Code --}}
    <p class="text-8xl font-extrabold mb-2" style="background:linear-gradient(135deg,#F59E0B,#EF4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">500</p>

    {{-- Title --}}
    <h1 class="text-2xl font-bold text-slate-800 mb-3">Server Error</h1>
    <p class="text-slate-400 text-sm leading-relaxed mb-8">
      Something went wrong on our end. Our team has been notified.<br>
      Please try again in a moment.
    </p>

    {{-- Actions --}}
    <div class="flex items-center justify-center gap-3">
      <a href="javascript:location.reload()" class="btn-secondary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Retry
      </a>
      <a href="{{ route('admin.dashboard') }}" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
      </a>
    </div>

    <p class="text-xs text-slate-300 mt-8">School ERP &middot; Error 500</p>
  </div>

</body>
</html>
