<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>419 — Session Expired</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-[#F0F2F9] min-h-screen flex items-center justify-center px-4">

  <div class="text-center max-w-md">

    {{-- Icon --}}
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl gradient-sky mb-6"
      style="box-shadow:0 12px 32px rgba(6,182,212,0.35)">
      <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>

    <p class="text-8xl font-extrabold mb-2" style="background:linear-gradient(135deg,#06B6D4,#0284C7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">419</p>

    <h1 class="text-2xl font-bold text-slate-800 mb-3">Session Expired</h1>
    <p class="text-slate-400 text-sm leading-relaxed mb-8">
      Your session has expired for security reasons.<br>
      Please refresh the page and try again.
    </p>

    <div class="flex items-center justify-center gap-3">
      <a href="javascript:location.reload()" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Refresh Page
      </a>
    </div>

    <p class="text-xs text-slate-300 mt-8">School ERP &middot; Error 419</p>
  </div>

</body>
</html>
