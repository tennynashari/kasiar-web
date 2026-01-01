<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive.'],
            ]);
        }

        // Create token for Sanctum
        $token = $user->createToken('pos-token')->plainTextToken;

        ActivityLog::log('login', $user);

        // Create response with user data
        $response = response()->json([
            'user' => $user->load('outlet'),
            'message' => 'Login successful',
        ]);

        // Attach HTTP-only cookie to response
        return $response->cookie(
            'auth_token',
            $token,
            config('sanctum.expiration') ?? 60 * 24 * 7, // 7 days in minutes
            '/',
            null, // domain
            false, // secure - set to true in production with HTTPS
            true, // httpOnly
            false, // raw
            'lax' // sameSite
        );
    }

    public function logout(Request $request)
    {
        ActivityLog::log('logout', $request->user());
        
        $request->user()->currentAccessToken()->delete();

        // Clear the auth token cookie
        cookie()->queue(cookie()->forget('auth_token'));

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('outlet'));
    }
}
