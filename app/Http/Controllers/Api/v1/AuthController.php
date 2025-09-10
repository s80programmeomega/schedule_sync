<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

/**
 * Authentication Controller
 *
 * Handles user authentication using Laravel Sanctum tokens.
 *
 * Authentication Strategy Comparison:
 *
 * 1. Session-based (Traditional Web):
 *    - Pros: Built-in CSRF protection, server-side session management
 *    - Cons: Not suitable for mobile apps, scaling issues with multiple servers
 *    - Use case: Traditional web applications like WordPress admin
 *
 * 2. JWT (JSON Web Tokens):
 *    - Pros: Stateless, works across microservices, contains user data
 *    - Cons: Cannot revoke tokens easily, larger payload size
 *    - Use case: Microservices architecture like Netflix, Uber
 *
 * 3. Laravel Sanctum (Chosen):
 *    - Pros: Simple, secure, can revoke tokens, works with SPA and mobile
 *    - Cons: Requires database storage for tokens
 *    - Use case: Modern web apps like GitHub, GitLab
 *
 * Real-world example: This is similar to how Calendly handles API authentication
 * for their mobile apps and third-party integrations.
 */
class AuthController extends ApiController
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validate registration data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'username' => 'required|string|max:255|unique:users|alpha_dash',
                'timezone' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:500',
            ]);

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'username' => $validated['username'],
                'timezone' => $validated['timezone'] ?? 'UTC',
                'bio' => $validated['bio'],
            ]);

            // Create authentication token
            $token = $user->createToken('auth-token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'User registered successfully', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed', 500);
        }
    }

    /**
     * Authenticate user and return token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Validate login credentials
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
                'device_name' => 'nullable|string|max:255',
            ]);

            // Find user by email
            $user = User::where('email', $validated['email'])->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->unauthorizedResponse('Invalid credentials');
            }

            // Revoke existing tokens for security (optional - depends on requirements)
            // $user->tokens()->delete();

            // Create new authentication token
            $deviceName = $validated['device_name'] ?? $request->userAgent();
            $token = $user->createToken($deviceName)->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed', 500);
        }
    }

    /**
     * Logout user and revoke token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(null, 'Logout successful', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed', 500);
        }
    }

    /**
     * Get authenticated user information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load(['eventTypes', 'availability']);

            return $this->successResponse($user, 'User data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user data', 500);
        }
    }

    /**
     * Refresh user token (create new token and revoke old one)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $deviceName = $request->input('device_name', $request->userAgent());

            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Create new token
            $token = $user->createToken($deviceName)->plainTextToken;

            return $this->successResponse([
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Token refresh failed', 500);
        }
    }

    /**
     * Send password reset link
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $status = Password::sendResetLink($validated);

            if ($status === Password::RESET_LINK_SENT) {
                return $this->successResponse(null, 'Password reset link sent to your email');
            }

            return $this->errorResponse('Failed to send password reset link');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Password reset failed', 500);
        }
    }

    /**
     * Reset password using token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                // 'password' => 'required|confirmed',
                'password' => 'required|min:8|confirmed',
            ]);

            $status = Password::reset(
                $validated,
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            // dd($status);

            if ($status === Password::PASSWORD_RESET) {
                return $this->successResponse(null, 'Password reset successfully');
            }

            return $this->errorResponse('Password reset failed');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Password reset failed', 500);
        }
    }
}
