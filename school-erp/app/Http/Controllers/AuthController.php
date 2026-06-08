<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ── Show login page ────────────────────────────────────
    public function showLogin()
    {
        return view('auth.login');
    }

    // ── Handle login POST ──────────────────────────────────
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt login
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid email or password.']);
        }

        $user = Auth::user();

        // Block inactive users immediately after auth
        if ($user->status !== 'active') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account is suspended. Contact administrator.'
            ]);
        }

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Regenerate session to prevent fixation attacks
        $request->session()->regenerate();

        // ── Role-based redirect ────────────────────────────
        return $this->redirectByRole($user);
    }

    // ── Logout ─────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    // ── Private: role → route map ──────────────────────────
    private function redirectByRole($user): mixed
    {
        return match(true) {
            $user->hasAnyRole(['super_admin', 'correspondent',
                               'principal', 'teacher', 'accountant'])
                => redirect()->route('admin.dashboard'),

            $user->hasRole('parent')
                => redirect()->route('parent.home'),

            $user->hasRole('student')
                => redirect()->route('student.home'),

            default
                => redirect()->route('login'),
        };
    }
}