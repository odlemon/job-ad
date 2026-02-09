<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $service
    ) {
    }

    /**
     * Register a new user (job seeker or employer).
     */
    public function register(Request $request): JsonResponse
    {
        // Base validation
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:job_seeker,employer',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
        ]);

        // Conditional validation based on user type
        if ($request->user_type === 'job_seeker') {
            $validator->sometimes(['first_name', 'last_name'], 'required|string|max:255', function ($input) {
                return $input->user_type === 'job_seeker';
            });
            $validator->sometimes(['location', 'bio'], 'nullable|string', function ($input) {
                return $input->user_type === 'job_seeker';
            });
        } elseif ($request->user_type === 'employer') {
            $validator->sometimes('company_name', 'required|string|max:255', function ($input) {
                return $input->user_type === 'employer';
            });
            $validator->sometimes(['company_description', 'industry', 'company_size', 'website', 'address'], 'nullable|string', function ($input) {
                return $input->user_type === 'employer';
            });
        }

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userType = $request->user_type;

        if ($userType === 'job_seeker') {
            $user = $this->service->registerJobSeeker(
                $request->only(['email', 'password', 'phone']),
                $request->only(['first_name', 'last_name', 'location', 'bio'])
            );
        } else {
            $user = $this->service->registerEmployer(
                $request->only(['email', 'password', 'phone']),
                $request->only(['company_name', 'company_description', 'industry', 'company_size', 'website', 'address'])
            );
        }

        // Regenerate session after registration/login
        $request->session()->regenerate();
        $request->session()->save();

        return response()->json([
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
        ], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $this->service->login($request->only(['email', 'password']));

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Login user for session-based auth
        Auth::login($user);
        $request->session()->regenerate();
        
        // Save the session to ensure it persists across requests
        $request->session()->save();

        // Ensure user_type is included in response
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'is_active' => $user->is_active,
            'is_verified' => $user->is_verified,
        ];

        return response()->json([
            'message' => 'Login successful',
            'user' => $userData,
        ])->withCookie(cookie(
            config('session.cookie'),
            $request->session()->getId(),
            config('session.lifetime'),
            config('session.path'),
            config('session.domain'),
            config('session.secure'),
            config('session.http_only'),
            false,
            config('session.same_site')
        ));
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->service->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->service->getAuthenticatedUser();

        if (!$user) {
            return response()->json([
                'message' => 'Not authenticated',
            ], 401);
        }

        return response()->json([
            'user' => $user,
        ]);
    }
}
