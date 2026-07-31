<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Auth\OtpService;
use App\Services\NotificationService;
use App\Services\RemoteUploadService;
use App\Support\ScoopUserPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $service,
        private RemoteUploadService $uploadService,
        private NotificationService $notificationService,
        private OtpService $otpService
    ) {
    }

    /**
     * Register a new user (job seeker or employer).
     */
    public function register(Request $request): JsonResponse
    {
        // Base validation (shared fields for both user types)
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:job_seeker,employer',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:255',
        ]);

        // Conditional validation based on user type
        if ($request->user_type === 'job_seeker') {
            $validator->sometimes('date_of_birth', 'required|date', function ($input) {
                return $input->user_type === 'job_seeker';
            });
            $validator->sometimes('gender', 'required|in:male,female,non_binary,other,prefer_not_to_say', function ($input) {
                return $input->user_type === 'job_seeker';
            });
            $validator->sometimes('employment_status', 'required|in:currently_employed,employed_part_time,self_employed,unemployed,student,retired,prefer_not_to_say', function ($input) {
                return $input->user_type === 'job_seeker';
            });
            $validator->sometimes('highest_education', 'required|string|max:255', function ($input) {
                return $input->user_type === 'job_seeker';
            });
            $validator->sometimes('job_preferences', 'nullable|array', function ($input) {
                return $input->user_type === 'job_seeker';
            });
        } elseif ($request->user_type === 'employer') {
            $validator->sometimes('company_name', 'required|string|max:255', function ($input) {
                return $input->user_type === 'employer';
            });
            $validator->sometimes('industry', 'required|string|max:255', function ($input) {
                return $input->user_type === 'employer';
            });
            $validator->sometimes('company_size', 'required|string|max:255', function ($input) {
                return $input->user_type === 'employer';
            });
            $validator->sometimes('website', 'nullable|string', function ($input) {
                return $input->user_type === 'employer';
            });
            $validator->sometimes('business_certificate', 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', function ($input) {
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
            $seekerData = $request->only([
                'first_name', 'last_name', 'date_of_birth', 'gender',
                'employment_status', 'highest_education', 'job_preferences',
            ]);
            $user = $this->service->registerJobSeeker(
                $request->only(['email', 'password', 'phone']),
                $seekerData
            );
            $this->notificationService->notifyAdmins(
                'job_seeker_registered',
                'New Job Seeker Registered',
                $user->name . ' (' . $user->email . ') just registered as a job seeker.',
                ['user_id' => $user->id, 'email' => $user->email]
            );

            // TEMP (dev): skip email OTP while ZeptoMail credits are exhausted.
            // Re-enable OTP send + early return when mail delivery works again.
            $user->forceFill([
                'email_verified_at' => now(),
                'is_verified' => true,
            ])->save();
        } else {
            $employerData = $request->only(['first_name', 'last_name', 'company_name', 'industry', 'company_size', 'website']);

            // Handle business certificate upload
            if ($request->hasFile('business_certificate')) {
                try {
                    $result = $this->uploadService->uploadSingleFile(
                        $request->file('business_certificate'),
                        'application-documents'
                    );
                    $employerData['business_certificate_path'] = $result['downloadURL'] ?? $result['filePath'];
                } catch (\Exception $e) {
                    Log::error('Business certificate upload failed', ['error' => $e->getMessage()]);
                    return response()->json([
                        'message' => 'Failed to upload business certificate',
                        'errors' => ['business_certificate' => [$e->getMessage()]],
                    ], 422);
                }
            }

            $user = $this->service->registerEmployer(
                $request->only(['email', 'password', 'phone']),
                $employerData
            );
            $this->notificationService->notifyAdmins(
                'employer_registered',
                'New Employer Registered',
                $user->name . ' (' . $user->email . ') just registered as an employer.',
                ['user_id' => $user->id, 'email' => $user->email]
            );
        }

        // Create Sanctum token for API/Bearer auth
        $token = $user->createToken('api-token')->plainTextToken;

        $user->load('jobSeeker');

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => ScoopUserPresenter::user($user, false),
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

        // Mobile Scoop uses Bearer tokens only — skip session write I/O
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;
        $user->load('jobSeeker');

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => ScoopUserPresenter::user($user, false),
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        $hasToken = $user && method_exists($user, 'currentAccessToken') && $user->currentAccessToken();

        if ($hasToken) {
            // Revoke Sanctum token (Bearer token auth)
            $user->currentAccessToken()->delete();
        } else {
            // Session-based logout
            $this->service->logout();
        }

        // Invalidate session if present
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        // Use $request->user() which works for both Sanctum token and session auth
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Not authenticated',
            ], 401);
        }

        // Load appropriate profile based on user type
        if ($user->user_type === 'job_seeker') {
            $user->load('jobSeeker');
        } elseif ($user->user_type === 'employer') {
            $user->load('employer');
        }

        return response()->json([
            'user' => ScoopUserPresenter::user($user, false),
        ]);
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $this->otpService->send($request->email, 'verify');
        } catch (\Throwable $e) {
            Log::error('Failed to send OTP', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to send verification email. Please try again shortly.',
            ], 503);
        }

        return response()->json(['message' => 'OTP sent to your email']);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        if (! $this->otpService->verify($request->email, $request->code, 'verify')) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'is_verified' => true,
        ])->save();

        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;
        $user->load('jobSeeker');

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => ScoopUserPresenter::user($user, false),
        ]);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        return $this->sendOtp($request);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            Password::sendResetLink(['email' => $request->email]);
        } catch (\Throwable $e) {
            Log::warning('Password reset link email failed', ['error' => $e->getMessage()]);
        }

        $this->otpService->send($request->email, 'reset');

        return response()->json(['message' => 'If that email exists, a reset link/code was sent']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // Accept either Laravel reset token OR 6-digit OTP as token
        if (strlen($request->token) === 6 && ctype_digit($request->token)) {
            if (! $this->otpService->verify($request->email, $request->token, 'reset')) {
                return response()->json(['message' => 'Invalid or expired reset code'], 422);
            }

            $user = User::where('email', $request->email)->first();
            if (! $user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->update(['password' => Hash::make($request->password)]);
            $user->tokens()->delete();

            return response()->json(['message' => 'Password reset successful']);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 422);
        }

        return response()->json(['message' => 'Password reset successful']);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        if (! $this->service->changePassword($user, $request->current_password, $request->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($request->filled('password') && ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password is incorrect'], 422);
        }

        $this->service->deleteAccount($user);

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => 'Account deleted']);
    }

    public function enable2fa(Request $request): JsonResponse
    {
        $seeker = $request->user()?->jobSeeker;
        if (! $seeker) {
            return response()->json(['message' => 'Job seeker profile required'], 422);
        }

        $settings = \App\Models\JobSeekerSetting::updateOrCreate(
            ['seeker_id' => $seeker->seeker_id],
            ['two_factor_enabled' => true]
        );

        return response()->json([
            'message' => 'Two-factor authentication enabled',
            'data' => ['two_factor_enabled' => (bool) $settings->two_factor_enabled],
        ]);
    }

    public function disable2fa(Request $request): JsonResponse
    {
        $seeker = $request->user()?->jobSeeker;
        if (! $seeker) {
            return response()->json(['message' => 'Job seeker profile required'], 422);
        }

        $settings = \App\Models\JobSeekerSetting::updateOrCreate(
            ['seeker_id' => $seeker->seeker_id],
            ['two_factor_enabled' => false]
        );

        return response()->json([
            'message' => 'Two-factor authentication disabled',
            'data' => ['two_factor_enabled' => (bool) $settings->two_factor_enabled],
        ]);
    }
}
