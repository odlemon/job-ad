<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobSeekerSetting;
use App\Services\JobSeeker\JobSeekerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private JobSeekerService $jobSeekerService
    ) {
    }

    public function index(): View
    {
        return view('job-seeker.settings');
    }

    public function bootstrap(): JsonResponse
    {
        $user = Auth::user();
        $seeker = $this->jobSeekerService->getByUserId($user->id);
        abort_unless($seeker, 404, 'Job seeker profile not found');

        $settings = $this->settingsFor($seeker->seeker_id);

        return response()->json([
            'notifications' => [
                'email_notifications' => (bool) $settings->email_notifications,
                'job_alerts' => (bool) $settings->job_alerts,
                'application_updates' => (bool) $settings->application_updates,
                'marketing_emails' => (bool) $settings->marketing_emails,
            ],
            'privacy' => [
                'public_profile' => (bool) ($seeker->public_profile ?? true),
                'show_activity_status' => (bool) ($settings->show_activity_status ?? true),
                'allow_contact_by_recruiters' => (bool) ($settings->allow_contact_by_recruiters ?? true),
            ],
            'security' => [
                'two_factor_enabled' => (bool) $settings->two_factor_enabled,
                'password_changed_at' => optional($user->password_changed_at)->toIso8601String(),
                'password_changed_label' => $this->passwordChangedLabel($user->password_changed_at),
            ],
            'pricing_url' => route('pricing.index'),
        ]);
    }

    public function updateNotifications(Request $request): JsonResponse
    {
        $seeker = $this->seeker();
        $validated = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'job_alerts' => 'sometimes|boolean',
            'application_updates' => 'sometimes|boolean',
            'marketing_emails' => 'sometimes|boolean',
        ]);

        $data = [];
        foreach (['email_notifications', 'job_alerts', 'application_updates', 'marketing_emails'] as $key) {
            if (array_key_exists($key, $validated)) {
                $data[$key] = (bool) $validated[$key];
            }
        }

        $settings = JobSeekerSetting::updateOrCreate(
            ['seeker_id' => $seeker->seeker_id],
            $data
        );

        return response()->json([
            'message' => 'Notification preferences saved',
            'notifications' => [
                'email_notifications' => (bool) $settings->email_notifications,
                'job_alerts' => (bool) $settings->job_alerts,
                'application_updates' => (bool) $settings->application_updates,
                'marketing_emails' => (bool) $settings->marketing_emails,
            ],
        ]);
    }

    public function updatePrivacy(Request $request): JsonResponse
    {
        $seeker = $this->seeker();
        $validated = $request->validate([
            'public_profile' => 'sometimes|boolean',
            'show_activity_status' => 'sometimes|boolean',
            'allow_contact_by_recruiters' => 'sometimes|boolean',
        ]);

        if (array_key_exists('public_profile', $validated)) {
            $seeker->public_profile = (bool) $validated['public_profile'];
            $seeker->save();
        }

        $settingsData = [];
        foreach (['show_activity_status', 'allow_contact_by_recruiters'] as $key) {
            if (array_key_exists($key, $validated) && Schema::hasColumn('job_seeker_settings', $key)) {
                $settingsData[$key] = (bool) $validated[$key];
            }
        }

        // Keep opportunity flag aligned so recruiters/job alerts respect contact preference
        if (array_key_exists('allow_contact_by_recruiters', $validated)) {
            $seeker->open_to_opportunities = (bool) $validated['allow_contact_by_recruiters'];
            $seeker->save();
        }

        $settings = JobSeekerSetting::updateOrCreate(
            ['seeker_id' => $seeker->seeker_id],
            $settingsData
        );

        return response()->json([
            'message' => 'Privacy settings saved',
            'privacy' => [
                'public_profile' => (bool) $seeker->fresh()->public_profile,
                'show_activity_status' => (bool) ($settings->show_activity_status ?? true),
                'allow_contact_by_recruiters' => (bool) ($settings->allow_contact_by_recruiters ?? true),
            ],
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
                'errors' => ['current_password' => ['Current password is incorrect']],
            ], 422);
        }

        // `password` cast hashes on set — pass plain text
        $user->password = $validated['password'];
        if (Schema::hasColumn('users', 'password_changed_at')) {
            $user->password_changed_at = now();
        }
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully',
            'security' => [
                'password_changed_at' => optional($user->password_changed_at)->toIso8601String(),
                'password_changed_label' => $this->passwordChangedLabel($user->password_changed_at),
            ],
        ]);
    }

    public function toggleTwoFactor(Request $request): JsonResponse
    {
        $seeker = $this->seeker();
        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);

        $settings = JobSeekerSetting::updateOrCreate(
            ['seeker_id' => $seeker->seeker_id],
            ['two_factor_enabled' => (bool) $validated['enabled']]
        );

        return response()->json([
            'message' => $settings->two_factor_enabled
                ? 'Two-factor authentication enabled'
                : 'Two-factor authentication disabled',
            'security' => [
                'two_factor_enabled' => (bool) $settings->two_factor_enabled,
            ],
        ]);
    }

    private function seeker()
    {
        $seeker = $this->jobSeekerService->getByUserId(Auth::id());
        abort_unless($seeker, 404, 'Job seeker profile not found');

        return $seeker;
    }

    private function settingsFor(int $seekerId): JobSeekerSetting
    {
        $defaults = [
            'app_notifications' => true,
            'email_notifications' => true,
            'job_alerts' => true,
            'application_updates' => true,
            'marketing_emails' => false,
            'two_factor_enabled' => false,
        ];
        if (Schema::hasColumn('job_seeker_settings', 'show_activity_status')) {
            $defaults['show_activity_status'] = true;
        }
        if (Schema::hasColumn('job_seeker_settings', 'allow_contact_by_recruiters')) {
            $defaults['allow_contact_by_recruiters'] = true;
        }

        return JobSeekerSetting::firstOrCreate(
            ['seeker_id' => $seekerId],
            $defaults
        );
    }

    private function passwordChangedLabel($changedAt): string
    {
        if (! $changedAt) {
            return 'Not changed recently';
        }

        return 'Last changed '.$changedAt->diffForHumans();
    }
}
