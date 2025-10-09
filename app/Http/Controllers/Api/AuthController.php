<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Admin\User\RegisterRequest;
use App\Http\Requests\Admin\User\LoginRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private UserRepositoryInterface $users) {}

    // ✅ Register
    public function register(RegisterRequest $request)
    {
        $user = $this->users->create($request->validated());

        // تسجيل المستخدم مباشرة باستخدام session
        Auth::login($user);

        return response()->json([
            'success' => true,
            'user' => new UserResource($user)
        ], 201);
    }

    // ✅ Login بدون سكوب (أي مستخدم)
    public function userLogin(LoginRequest $request)
    {
        $user = $this->users->login($request->validated());

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // تسجيل المستخدم في الجلسة
        Auth::login($user);

        return response()->json([
            'success' => true,
            'user' => new UserResource($user)
        ]);
    }

    // ✅ Login خاص بالأدمن فقط
    public function adminLogin(LoginRequest $request)
    {
        $user = $this->users->login($request->validated(), 'admin');

        if (!$user) {
            return response()->json(['message' => 'Invalid admin credentials'], 401);
        }

        // تسجيل الأدمن في الجلسة
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login Successfully',
            'user' => new UserResource($user)
        ]);
    }

    // ✅ Logout
    public function logout(Request $request)
    {
        Auth::logout();

        // مسح الجلسة بالكامل
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }
}
