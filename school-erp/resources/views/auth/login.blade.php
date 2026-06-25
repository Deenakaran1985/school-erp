@extends('layouts.auth')
@section('title', 'Sign In — School ERP')

@section('content')
<div class="relative min-h-screen bg-[#060B1A] flex items-center justify-center px-4 overflow-hidden">

  {{-- Animated gradient orbs --}}
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>

  {{-- Subtle grid overlay --}}
  <div class="absolute inset-0 pointer-events-none" style="background-image:linear-gradient(rgba(255,255,255,.015) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.015) 1px,transparent 1px);background-size:40px 40px;"></div>

  <div class="relative z-10 w-full max-w-md">

    {{-- Logo + Branding --}}
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl gradient-brand shadow-lg mb-4" style="box-shadow:0 8px 32px rgba(99,102,241,0.45)">
        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-white tracking-tight">{{ config('app.name', 'School ERP') }}</h1>
      <p class="text-slate-500 text-sm mt-1">School Management System</p>
    </div>

    {{-- Glass Card --}}
    <div class="glass rounded-3xl p-8 shadow-2xl" style="box-shadow:0 25px 50px rgba(0,0,0,0.5)">

      {{-- Alerts --}}
      @if (session('error'))
        <div class="mb-5 flex items-center gap-2 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-sm">
          <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
          {{ session('error') }}
        </div>
      @endif
      @if (session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm">
          {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-2 uppercase tracking-widest">Email Address</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
              </svg>
            </div>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
              class="w-full pl-10 pr-4 py-3 bg-white/5 border @error('email') border-red-500/50 @else border-white/10 @enderror
                     rounded-xl text-white placeholder-slate-600 text-sm
                     focus:outline-none focus:border-indigo-500/70 focus:bg-white/8 focus:ring-2 focus:ring-indigo-500/20 transition-all"
              placeholder="admin@school.edu.in"/>
          </div>
          @error('email') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Password --}}
        <div x-data="{ show: false }">
          <label class="block text-xs font-semibold text-slate-400 mb-2 uppercase tracking-widest">Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <input :type="show ? 'text' : 'password'" name="password" required
              class="w-full pl-10 pr-12 py-3 bg-white/5 border @error('password') border-red-500/50 @else border-white/10 @enderror
                     rounded-xl text-white placeholder-slate-600 text-sm
                     focus:outline-none focus:border-indigo-500/70 focus:bg-white/8 focus:ring-2 focus:ring-indigo-500/20 transition-all"
              placeholder="••••••••"/>
            <button type="button" @click="show = !show"
              class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
              <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
              </svg>
            </button>
          </div>
          @error('password') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Remember --}}
        <div class="flex items-center">
          <label class="flex items-center gap-2.5 cursor-pointer select-none">
            <input type="checkbox" name="remember"
              class="w-4 h-4 rounded border-white/20 bg-white/5 text-indigo-500 focus:ring-indigo-500/20 focus:ring-offset-0"/>
            <span class="text-sm text-slate-400">Remember me</span>
          </label>
        </div>

        {{-- Submit --}}
        <button type="submit"
          class="w-full py-3.5 gradient-brand text-white font-semibold rounded-xl text-sm tracking-wide transition-all duration-200"
          style="box-shadow:0 4px 20px rgba(99,102,241,0.4)"
          onmouseenter="this.style.boxShadow='0 8px 28px rgba(99,102,241,0.55)'"
          onmouseleave="this.style.boxShadow='0 4px 20px rgba(99,102,241,0.4)'">
          Sign In to Dashboard →
        </button>
      </form>
    </div>

    <p class="text-center text-slate-700 text-xs mt-6">
      {{ config('school.name', config('app.name')) }} &middot; School ERP v1.0
    </p>
  </div>
</div>
@endsection