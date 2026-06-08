<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    // POST /api/auth/login
    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
            'fcm_token'=> 'nullable|string',
        ]);

        $user = User::where('phone', $request->phone)
            ->orWhere('email', $request->phone)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Contact school.',
            ], 403);
        }

        // Save FCM token if provided
        if ($request->filled('fcm_token')) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $user->update(['last_login_at' => now()]);

        // Revoke old tokens to keep one session per device
        $user->tokens()->where('name', 'mobile-app')->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        $role = $user->getRoleNames()->first() ?? $user->user_type;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'role'    => $role,
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'phone'      => $user->phone,
                'email'      => $user->email,
                'avatar_url' => $user->avatar_url,
                'user_type'  => $user->user_type,
            ],
        ]);
    }

    // POST /api/auth/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out.']);
    }

    // GET /api/auth/me
    public function me(Request $request)
    {
        $user = $request->user();
        $role = $user->getRoleNames()->first() ?? $user->user_type;

        $profile = match ($role) {
            'student' => $user->student?->only([
                'id', 'admission_no', 'name', 'roll_number',
                'school_class_id', 'section_id', 'status',
            ]),
            'parent'  => [
                'children_count' => $user->children()->active()->count(),
            ],
            'teacher', 'correspondent', 'principal', 'accountant' => $user->staff?->only([
                'id', 'employee_id', 'designation', 'department_id', 'staff_type',
            ]),
            default => [],
        };

        return response()->json([
            'success' => true,
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'phone'      => $user->phone,
                'email'      => $user->email,
                'avatar_url' => $user->avatar_url,
                'role'       => $role,
                'profile'    => $profile,
            ],
        ]);
    }

    // POST /api/auth/change-password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        $user->tokens()->where('name', 'mobile-app')->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed. Please login again.',
        ]);
    }
}
