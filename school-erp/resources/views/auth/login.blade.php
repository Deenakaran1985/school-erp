@extends('layouts.auth')

@section('title', 'Login — School ERP')

@section('content')
<!-- Login card -->
<div class="min-h-screen bg-slate-900 flex items-center justify-center px-4">
  <div class="w-full max-w-md">

    <!-- Logo / School name -->
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600 mb-4">
        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-white">{{ config('app.name') }}</h1>
      <p class="text-slate-400 text-sm mt-1">School Management System</p>
    </div>

    <!-- Card -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 shadow-2xl">

      <!-- Error alert -->
      @if (session('error'))
        <div class="mb-4 p-3 bg-red-900/40 border border-red-700 rounded-lg text-red-300 text-sm">
          {{ session('error') }}
        </div>
      @endif

      <!-- Success alert -->
      @if (session('success'))
        <div class="mb-4 p-3 bg-green-900/40 border border-green-700 rounded-lg text-green-300 text-sm">
          {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <!-- Email -->
        <div class="mb-5">
          <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
          <input
            type="email" name="email"
            value="{{ old('email') }}"
            required autofocus
            class="w-full px-4 py-3 bg-slate-900 border rounded-xl text-white placeholder-slate-500
                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                   @error('email') border-red-500 @else border-slate-600 @enderror"
            placeholder="you@school.edu.in"
          />
          @error('email')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div class="mb-5" x-data="{ show: false }">
          <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
          <div class="relative">
            <input
              :type="show ? 'text' : 'password'"
              name="password" required
              class="w-full px-4 py-3 pr-12 bg-slate-900 border rounded-xl text-white placeholder-slate-500
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                     @error('password') border-red-500 @else border-slate-600 @enderror"
              placeholder="••••••••"
            />
            <button type="button" @click="show = !show"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
              <span x-show="!show">👁</span>
              <span x-show="show">🙈</span>
            </button>
          </div>
          @error('password')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
          @enderror
        </div>

        <!-- Remember me -->
        <div class="flex items-center justify-between mb-6">
          <label class="flex items-center gap-2 text-sm text-slate-400 cursor-pointer">
            <input type="checkbox" name="remember"
              class="rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500"/>
            Remember me
          </label>
        </div>

        <button type="submit"
          class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold
                 rounded-xl transition-colors duration-200 focus:outline-none focus:ring-2
                 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-800">
          Sign In
        </button>
      </form>
    </div>

    <p class="text-center text-slate-600 text-xs mt-6">
      {{ config('school.name', config('app.name')) }} · School ERP v1.0
    </p>
  </div>
</div>
@endsection