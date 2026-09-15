<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user and generate a Sanctum personal access token.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $deviceName = $validated['device_name'] ?? 'mobile_app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Authenticate a user and issue a Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Your account has been deactivated.',
            ], 403);
        }

        $deviceName = $validated['device_name'] ?? 'mobile_app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Revoke the current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get the authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Update the authenticated user's profile details.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'pronouns' => ['nullable', 'string', 'max:50'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'social_links' => ['nullable', 'array'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Change the authenticated user's password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Delete the authenticated user's account.
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password does not match our records.'],
            ]);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }
}
