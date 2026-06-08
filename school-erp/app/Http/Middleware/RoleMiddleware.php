<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }

        $user = Auth::user();

        // Block suspended / inactive accounts
        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been suspended. Contact admin.');
        }

        // Check role — supports pipe-separated: "role:admin|teacher"
        foreach ($roles as $roleGroup) {
            $allowed = explode('|', $roleGroup);
            if ($user->hasAnyRole($allowed)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to access this page.');
    }
}
