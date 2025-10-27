<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Admin\User\RegisterRequest;
use App\Http\Requests\Admin\User\LoginRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(private UserRepositoryInterface $users) {}

    /**
     * ✅ Register (API)
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->users->create($request->validated());

        // إنشاء توكن مباشر بعد التسجيل
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * ✅ Login لأي مستخدم (Token-based)
     */
    public function userLogin(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // حذف التوكنات القديمة (اختياري)
        $user->tokens()->delete();

        // إنشاء توكن جديد
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * ✅ Login للأدمن فقط (Token-based)
     */
    public function adminLogin(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        // فحص وجود المستخدم وصحة كلمة المرور
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // فحص إذا عنده role "admin"
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'You are not authorized as admin'], 403);
        }

        // حذف أي توكنات قديمة وإنشاء توكن جديد
        $user->tokens()->delete();
        $token = $user->createToken('admin_token', ['role:admin'])->plainTextToken;

        return response()->json([
            'message' => 'Admin login successful',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * ✅ Logout (Token-based)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * ✅ Get Authenticated User
     */
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }
}
