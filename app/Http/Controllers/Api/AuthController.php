<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Admin\User\RegisterRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private UserRepositoryInterface $users) {}

    // ✅ Register
    public function register(RegisterRequest $request)
    {
        $user = $this->users->create($request->validated());

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ], 201);
    }

    // ✅ Login بدون سكوب (أي مستخدم)
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = $this->users->login($credentials);

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    // ✅ Login خاص بالأدمن فقط
    public function adminLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = $this->users->login($credentials, 'admin');

        if (!$user) {
            return response()->json(['message' => 'Invalid admin credentials'], 401);
        }

        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    // ✅ Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
