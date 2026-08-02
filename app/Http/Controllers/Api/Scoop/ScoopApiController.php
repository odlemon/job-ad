<?php

namespace App\Http\Controllers\Api\Scoop;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\Course;
use App\Models\JobAdvertisement;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobSeekerSetting;
use App\Models\JobSeekerSocialLink;
use App\Models\SavedJob;
use App\Models\TenderAd;
use App\Models\TrainingProvider;
use App\Services\JobSeeker\ApplicationService;
use App\Services\JobSeeker\FollowedCompanyService;
use App\Services\JobSeeker\JobSeekerService;
use App\Services\JobSeeker\SavedJobService;
use App\Services\NotificationService;
use App\Support\ScoopJobPresenter;
use App\Support\ScoopProfilePresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ScoopApiController extends Controller
{
    public function __construct(
        private JobSeekerService $jobSeekerService,
        private ApplicationService $applicationService,
        private SavedJobService $savedJobService,
        private FollowedCompanyService $followedCompanyService,
        private NotificationService $notificationService
    ) {
    }

    private function seekerOrFail(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->user_type !== 'job_seeker') {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }

        $seeker = $this->jobSeekerService->getByUserId($user->id);
        if (! $seeker) {
            abort(response()->json(['message' => 'Job seeker profile not found'], 404));
        }

        return $seeker;
    }

    public function summary(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $seekerId = $seeker->seeker_id;

        $applicationStats = JobApplication::query()
            ->where('seeker_id', $seekerId)
            ->selectRaw('COUNT(*) as applied')
            ->selectRaw('SUM(CASE WHEN invite_sent_at IS NOT NULL THEN 1 ELSE 0 END) as invited')
            ->first();

        return response()->json([
            'data' => [
                'applied' => (int) ($applicationStats->applied ?? 0),
                'saved' => SavedJob::where('seeker_id', $seekerId)->count(),
                'invited' => (int) ($applicationStats->invited ?? 0),
                'discovery' => $seeker->categoryPreferences()->count(),
                'companies' => $seeker->followedCompanies()->count(),
            ],
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $seeker->load('user');

        return response()->json([
            'data' => ScoopProfilePresenter::profile($seeker, $request->user()),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,non_binary,other,prefer_not_to_say',
            'date_of_birth' => 'nullable|date',
            'employment_status' => 'nullable|string|max:255',
            'highest_education' => 'nullable|string|max:255',
            'driving_license' => 'nullable|boolean',
            'bio' => 'nullable|string|max:5000',
            'job_preferences' => 'nullable|array',
            'job_discovery_categories' => 'nullable|array',
            'expected_salary_min' => 'nullable|numeric|min:0',
            'expected_salary_max' => 'nullable|numeric|min:0',
            'hobbies' => 'nullable|array',
            'hobbies.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'first_name', 'last_name', 'phone', 'location', 'gender', 'date_of_birth',
            'employment_status', 'highest_education', 'driving_license', 'bio',
            'job_preferences', 'expected_salary_min', 'expected_salary_max', 'hobbies',
        ]);

        $seeker = $this->jobSeekerService->updateProfile($seeker, $data);

        // Keep users.name in sync when seeker name fields change
        if ($request->hasAny(['first_name', 'last_name'])) {
            $seeker->loadMissing('user');
            $full = trim(($seeker->first_name ?? '').' '.($seeker->last_name ?? ''));
            if ($full !== '' && $seeker->user) {
                $seeker->user->update(['name' => $full]);
            }
        }

        if ($request->has('job_discovery_categories')) {
            $names = collect($request->input('job_discovery_categories', []))->filter()->values();
            $categoryIds = JobCategory::whereIn('name', $names)->pluck('id');
            $seeker->categoryPreferences()->delete();
            foreach ($categoryIds as $categoryId) {
                $seeker->categoryPreferences()->create(['category_id' => $categoryId]);
            }
        }

        $seeker->load('user');

        return response()->json([
            'data' => ScoopProfilePresenter::profile($seeker->fresh(), $request->user()),
        ]);
    }

    public function uploadPhoto(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);

        $file = $request->file('photo') ?? $request->file('profile_photo');
        if (! $file) {
            return response()->json(['message' => 'photo is required'], 422);
        }

        $seeker = $this->jobSeekerService->uploadProfilePhoto($seeker, $file);

        return response()->json([
            'data' => ['profile_photo' => $seeker->profile_photo],
        ]);
    }

    public function applyToJob(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);

        if ($this->applicationService->hasApplied($seeker->seeker_id, $id)) {
            return response()->json(['message' => 'Already applied to this job'], 422);
        }

        $job = JobAdvertisement::where('id', $id)->where('status', 'published')->first();
        if (! $job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $request->merge(['job_advertisement_id' => $id]);
        try {
            $application = $this->applicationService->apply($seeker, [
                'job_advertisement_id' => $id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Application submitted', 'data' => $application]);
    }

    public function saveJob(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $this->savedJobService->saveJob($seeker, $id);

        return response()->json(['message' => 'Job saved']);
    }

    public function unsaveJob(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $this->savedJobService->unsaveJob($seeker, $id);

        return response()->json(['message' => 'Job unsaved']);
    }

    public function reportJob(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:2000',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        if (! JobAdvertisement::where('id', $id)->exists()) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $user = $request->user();
        $reason = $request->input('reason');
        $payload = [
            'user_id' => $user->id,
            'job_advertisement_id' => $id,
            'reason' => $reason,
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('job_reports', 'status')) {
            $payload['status'] = 'pending';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('job_reports', 'category')) {
            $payload['category'] = 'other';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('job_reports', 'details')) {
            $payload['details'] = $reason;
        }

        $report = \App\Models\JobReport::create($payload);
        $job = JobAdvertisement::find($id);

        app(\App\Services\NotificationService::class)->notifyAdmins(
            'job_report',
            'Suspicious job report #'.$report->id,
            ($user->name ?? 'A user').' reported job "'.($job->title ?? '#'.$id).'"',
            [
                'report_id' => $report->id,
                'job_id' => $id,
                'job_title' => $job->title ?? null,
                'user_id' => $user->id,
            ]
        );

        return response()->json(['message' => 'Job reported', 'report_id' => $report->id]);
    }

    public function recommendedJobs(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $categoryIds = $seeker->categoryPreferences()->pluck('category_id');

        $query = JobAdvertisement::with(['company', 'category'])
            ->where('status', 'published');

        if ($categoryIds->isNotEmpty()) {
            $query->whereIn('category_id', $categoryIds);
        }

        $jobs = $query->latest()->limit(20)->get();

        return response()->json([
            'data' => ScoopJobPresenter::jobs($jobs, $seeker->seeker_id),
        ]);
    }

    public function invitations(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);

        $apps = JobApplication::with(['jobAdvertisement.company', 'jobAdvertisement.category'])
            ->where('seeker_id', $seeker->seeker_id)
            ->whereNotNull('invite_sent_at')
            ->latest('invite_sent_at')
            ->limit(50)
            ->get();

        $jobModels = $apps->pluck('jobAdvertisement')->filter()->values();
        $presented = collect(ScoopJobPresenter::jobs($jobModels, $seeker->seeker_id))->keyBy('id');

        $data = $apps->map(function (JobApplication $app) use ($presented) {
            return [
                'id' => $app->id,
                'status' => $app->interview_status ?? $app->status,
                'invited_at' => optional($app->invite_sent_at)->toIso8601String(),
                'job' => $app->jobAdvertisement
                    ? $presented->get($app->jobAdvertisement->id)
                    : null,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function acceptInvitation(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $app = JobApplication::where('id', $id)->where('seeker_id', $seeker->seeker_id)->first();
        if (! $app || ! $app->invite_sent_at) {
            return response()->json(['message' => 'Invitation not found'], 404);
        }

        $app->update(['interview_status' => 'accepted']);

        return response()->json(['message' => 'Invitation accepted']);
    }

    public function declineInvitation(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $app = JobApplication::where('id', $id)->where('seeker_id', $seeker->seeker_id)->first();
        if (! $app || ! $app->invite_sent_at) {
            return response()->json(['message' => 'Invitation not found'], 404);
        }

        $app->update(['interview_status' => 'declined']);

        return response()->json(['message' => 'Invitation declined']);
    }

    public function getSettings(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $defaults = [
            'app_notifications' => true,
            'email_notifications' => true,
            'job_alerts' => true,
            'application_updates' => true,
            'marketing_emails' => false,
            'two_factor_enabled' => false,
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('job_seeker_settings', 'show_activity_status')) {
            $defaults['show_activity_status'] = true;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('job_seeker_settings', 'allow_contact_by_recruiters')) {
            $defaults['allow_contact_by_recruiters'] = true;
        }

        $settings = JobSeekerSetting::firstOrCreate(
            ['seeker_id' => $seeker->seeker_id],
            $defaults
        );

        $keys = [
            'app_notifications', 'email_notifications', 'job_alerts',
            'application_updates', 'marketing_emails', 'two_factor_enabled',
        ];
        foreach (['show_activity_status', 'allow_contact_by_recruiters'] as $key) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('job_seeker_settings', $key)) {
                $keys[] = $key;
            }
        }

        $data = $settings->only($keys);
        $data['public_profile'] = (bool) ($seeker->public_profile ?? true);

        return response()->json(['data' => $data]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $keys = [
            'app_notifications', 'email_notifications', 'job_alerts',
            'application_updates', 'marketing_emails', 'two_factor_enabled',
            'show_activity_status', 'allow_contact_by_recruiters',
        ];
        $data = [];
        foreach ($keys as $key) {
            if ($request->has($key) && \Illuminate\Support\Facades\Schema::hasColumn('job_seeker_settings', $key)) {
                $data[$key] = $request->boolean($key);
            }
        }

        if ($request->has('public_profile')) {
            $seeker->public_profile = $request->boolean('public_profile');
            $seeker->save();
        }
        if ($request->has('allow_contact_by_recruiters')) {
            $seeker->open_to_opportunities = $request->boolean('allow_contact_by_recruiters');
            $seeker->save();
        }

        $settings = JobSeekerSetting::updateOrCreate(
            ['seeker_id' => $seeker->seeker_id],
            $data
        );

        $outKeys = [
            'app_notifications', 'email_notifications', 'job_alerts',
            'application_updates', 'marketing_emails', 'two_factor_enabled',
        ];
        foreach (['show_activity_status', 'allow_contact_by_recruiters'] as $key) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('job_seeker_settings', $key)) {
                $outKeys[] = $key;
            }
        }
        $payload = $settings->only($outKeys);
        $payload['public_profile'] = (bool) $seeker->fresh()->public_profile;

        return response()->json(['data' => $payload]);
    }

    public function contactSupport(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $data = $request->validate([
            'subject' => 'required|string|max:160',
            'message' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $user = $request->user();
        $ticket = \App\Models\SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => $data['priority'] ?? 'medium',
            'channel' => 'ticket',
            'status' => 'open',
        ]);

        app(\App\Services\NotificationService::class)->notifyAdmins(
            'support_ticket',
            'New support ticket #'.$ticket->id,
            ($user->name ?? 'A job seeker').' submitted a ticket: '.$ticket->subject,
            [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'seeker_id' => $seeker->seeker_id,
                'priority' => $ticket->priority,
                'subject' => $ticket->subject,
            ]
        );

        return response()->json([
            'message' => 'Support message received',
            'ticket_id' => $ticket->id,
        ], 201);
    }

    public function hobbiesIndex(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $hobbies = collect($seeker->hobbies ?? [])->values()->map(function ($name, $index) {
            return ['id' => $index + 1, 'name' => $name];
        });

        return response()->json(['data' => $hobbies]);
    }

    public function hobbiesStore(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $validator = Validator::make($request->all(), ['name' => 'required|string|max:255']);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $hobbies = $seeker->hobbies ?? [];
        $hobbies[] = $request->name;
        $seeker = $this->jobSeekerService->updateProfile($seeker, ['hobbies' => array_values($hobbies)]);

        $id = count($seeker->hobbies);

        return response()->json(['data' => ['id' => $id, 'name' => $request->name]], 201);
    }

    public function hobbiesDestroy(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $hobbies = array_values($seeker->hobbies ?? []);
        $index = $id - 1;
        if (! isset($hobbies[$index])) {
            return response()->json(['message' => 'Hobby not found'], 404);
        }
        unset($hobbies[$index]);
        $this->jobSeekerService->updateProfile($seeker, ['hobbies' => array_values($hobbies)]);

        return response()->json(['message' => 'Deleted']);
    }

    public function socialLinksIndex(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $links = JobSeekerSocialLink::where('seeker_id', $seeker->seeker_id)->get();

        if ($links->isEmpty()) {
            $legacy = [
                'linkedin' => $seeker->linkedin_url,
                'facebook' => $seeker->facebook_url,
                'instagram' => $seeker->instagram_url,
                'other' => $seeker->website_url,
            ];
            foreach ($legacy as $platform => $url) {
                if ($url) {
                    $links->push(JobSeekerSocialLink::create([
                        'seeker_id' => $seeker->seeker_id,
                        'platform' => $platform,
                        'url' => $url,
                    ]));
                }
            }
        }

        return response()->json(['data' => $links]);
    }

    public function socialLinksStore(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $validator = Validator::make($request->all(), [
            'platform' => 'required|in:linkedin,facebook,twitter,instagram,github,other',
            'url' => 'required|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $link = JobSeekerSocialLink::create([
            'seeker_id' => $seeker->seeker_id,
            'platform' => $request->platform,
            'url' => $request->url,
        ]);

        return response()->json(['data' => $link], 201);
    }

    public function socialLinksDestroy(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $link = JobSeekerSocialLink::where('id', $id)->where('seeker_id', $seeker->seeker_id)->first();
        if (! $link) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $link->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function notificationsIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = min(50, max(1, (int) $request->get('limit', $request->get('per_page', 20))));
        $isRead = $request->has('is_read')
            ? filter_var($request->get('is_read'), FILTER_VALIDATE_BOOLEAN)
            : null;
        $category = $request->filled('category') ? (string) $request->get('category') : null;

        $paginator = $this->notificationService->paginateForUser($user->id, $limit, $isRead, $category);
        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        $slice = collect($paginator->items())->map(function ($n) {
            $type = (string) ($n->type ?? '');
            $category = str_contains($type, 'application') || str_contains($type, 'interview') || str_contains($type, 'status')
                ? 'applications'
                : 'alerts';
            $data = is_array($n->data) ? $n->data : [];
            $message = $data['message'] ?? $data['body'] ?? $n->message ?? '';

            return [
                'id' => (string) $n->id,
                'type' => $type,
                'category' => $category,
                'title' => $data['title'] ?? $n->title ?? 'Notification',
                'message' => $message,
                'body' => $message,
                'read' => (bool) $n->is_read,
                'is_read' => (bool) $n->is_read,
                'data' => $data,
                'created_at' => optional($n->created_at)?->toIso8601String() ?? now()->toIso8601String(),
            ];
        })->values();

        return response()->json([
            'data' => $slice,
            'notifications' => $slice,
            'unread_count' => $unreadCount,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    public function notificationsUnreadCount(Request $request): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount($request->user()->id);

        return response()->json([
            'data' => ['count' => $count],
            'unread_count' => $count,
        ]);
    }

    public function notificationsMarkRead(Request $request, string $id): JsonResponse
    {
        $ok = $this->notificationService->markAsRead((int) $id, $request->user()->id);
        if (! $ok) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $count = $this->notificationService->getUnreadCount($request->user()->id);

        return response()->json([
            'message' => 'Marked read',
            'unread_count' => $count,
        ]);
    }

    public function notificationsMarkAllRead(Request $request): JsonResponse
    {
        $this->notificationService->markAllAsRead($request->user()->id);

        return response()->json([
            'message' => 'All marked read',
            'unread_count' => 0,
        ]);
    }

    public function notificationsDestroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();
        if (! $notification) {
            // try numeric id via database notifications
            $notification = DB::table('notifications')
                ->where('id', $id)
                ->where('notifiable_id', $user->id)
                ->first();
            if (! $notification) {
                return response()->json(['message' => 'Not found'], 404);
            }
            DB::table('notifications')->where('id', $id)->delete();
        } else {
            $notification->delete();
        }

        return response()->json(['message' => 'Deleted']);
    }

    public function notificationsClearRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->notifications()->whereNotNull('read_at')->delete();

        return response()->json(['message' => 'Cleared']);
    }

    public function companyShow(int $id): JsonResponse
    {
        $company = Company::find($id);
        if (! $company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $stats = CompanyReview::query()
            ->where('company_id', $company->id)
            ->selectRaw('COUNT(*) as reviews_count')
            ->selectRaw('AVG(rating) as avg_rating')
            ->selectRaw('AVG(work_life_balance) as work_life_balance')
            ->selectRaw('AVG(benefits_perks) as benefits_perks')
            ->selectRaw('AVG(work_environment_culture) as work_environment_culture')
            ->selectRaw('AVG(career_growth_development) as career_growth_development')
            ->selectRaw('AVG(management_leadership) as management_leadership')
            ->selectRaw('AVG(employee_support_wellbeing) as employee_support_wellbeing')
            ->first();

        $reviewCount = (int) ($stats->reviews_count ?? 0);
        $avg = $reviewCount > 0 ? round((float) ($stats->avg_rating ?? 0), 1) : 0;
        $jobsCount = JobAdvertisement::where('company_id', $company->id)->where('status', 'published')->count();
        $followers = DB::table('followed_companies')->where('company_id', $company->id)->count();

        $distribution = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];
        if ($reviewCount > 0) {
            $rows = CompanyReview::where('company_id', $company->id)
                ->selectRaw('rating, COUNT(*) as total')
                ->groupBy('rating')
                ->pluck('total', 'rating');
            foreach ($rows as $rating => $total) {
                $key = (string) (int) $rating;
                if (isset($distribution[$key])) {
                    $distribution[$key] = (int) $total;
                }
            }
        }

        $aspectAverages = [
            'work_life_balance' => $stats->work_life_balance !== null ? round((float) $stats->work_life_balance, 1) : null,
            'benefits_perks' => $stats->benefits_perks !== null ? round((float) $stats->benefits_perks, 1) : null,
            'work_environment_culture' => $stats->work_environment_culture !== null ? round((float) $stats->work_environment_culture, 1) : null,
            'career_growth_development' => $stats->career_growth_development !== null ? round((float) $stats->career_growth_development, 1) : null,
            'management_leadership' => $stats->management_leadership !== null ? round((float) $stats->management_leadership, 1) : null,
            'employee_support_wellbeing' => $stats->employee_support_wellbeing !== null ? round((float) $stats->employee_support_wellbeing, 1) : null,
        ];

        $isFollowing = false;
        $user = Auth::guard('sanctum')->user() ?? Auth::user();
        if ($user && $user->user_type === 'job_seeker') {
            $seeker = $user->jobSeeker;
            if ($seeker) {
                $isFollowing = $seeker->followedCompanies()
                    ->where('company_id', $company->id)->exists();
            }
        }

        return response()->json([
            'data' => [
                'id' => $company->id,
                'name' => $company->name,
                'location' => $company->location ?? $company->city,
                'logo_url' => $company->logo ?? $company->logo_url,
                'cover_url' => $company->cover_image ?? $company->cover_url,
                'industry' => $company->industry,
                'size' => $company->size,
                'rating' => (float) $avg,
                'reviews_count' => $reviewCount,
                'jobs_count' => $jobsCount,
                'followers_count' => $followers,
                'about_us' => $company->description ?? $company->about,
                'website' => $company->website,
                'email' => $company->email,
                'phone' => $company->phone,
                'working_hours' => $company->working_hours,
                'workplace_description' => $company->workplace_description,
                'culture_benefits' => $company->culture_benefits,
                'benefits' => $company->benefits ?? [],
                'values' => $company->company_values ?? [],
                'linkedin' => $company->linkedin,
                'twitter' => $company->twitter,
                'facebook' => $company->facebook,
                'instagram' => $company->instagram,
                'github' => null,
                'rating_distribution' => $distribution,
                'aspect_averages' => $aspectAverages,
                'is_following' => $isFollowing,
            ],
        ]);
    }

    public function companyJobs(Request $request, int $id): JsonResponse
    {
        $limit = min(30, max(1, (int) $request->get('limit', $request->get('per_page', 20))));
        $jobs = JobAdvertisement::with(['company', 'category'])
            ->where('company_id', $id)
            ->where('status', 'published')
            ->latest()
            ->limit($limit)
            ->get();

        $seekerId = null;
        $user = Auth::guard('sanctum')->user() ?? Auth::user();
        if ($user && $user->user_type === 'job_seeker') {
            $seekerId = $user->jobSeeker?->seeker_id;
        }

        return response()->json(['data' => ScoopJobPresenter::jobs($jobs, $seekerId)]);
    }

    public function companyReviews(int $id): JsonResponse
    {
        $reviews = CompanyReview::where('company_id', $id)->latest()->get();

        $distribution = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];
        foreach ($reviews as $review) {
            $key = (string) (int) $review->rating;
            if (isset($distribution[$key])) {
                $distribution[$key]++;
            }
        }

        $aspectKeys = [
            'work_life_balance',
            'benefits_perks',
            'work_environment_culture',
            'career_growth_development',
            'management_leadership',
            'employee_support_wellbeing',
        ];
        $aspectAverages = [];
        foreach ($aspectKeys as $key) {
            $vals = $reviews->pluck($key)->filter(fn ($v) => $v !== null);
            $aspectAverages[$key] = $vals->isNotEmpty() ? round((float) $vals->avg(), 1) : null;
        }

        return response()->json([
            'data' => $reviews,
            'meta' => [
                'rating_distribution' => $distribution,
                'aspect_averages' => $aspectAverages,
                'total' => $reviews->count(),
            ],
        ]);
    }

    public function companyReviewStore(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string|max:5000',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        if (! Company::find($id)) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $review = CompanyReview::create([
            'company_id' => $id,
            'seeker_id' => $this->seekerOrFail($request)->seeker_id,
            'rating' => (int) $request->rating,
            'role' => $request->title,
            'good_things' => $request->body ?? $request->comment,
        ]);

        return response()->json(['data' => $review], 201);
    }

    public function companyFollow(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $this->followedCompanyService->followCompany($seeker, $id);

        return response()->json(['message' => 'Followed']);
    }

    public function companyUnfollow(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $this->followedCompanyService->unfollowCompany($seeker, $id);

        return response()->json(['message' => 'Unfollowed']);
    }

    public function coursesIndex(Request $request): JsonResponse
    {
        $q = $request->get('q');
        $limit = min(50, max(1, (int) $request->get('limit', $request->get('per_page', 30))));
        $query = Course::query()->where('is_active', true);
        if ($q) {
            $query->where('title', 'like', "%{$q}%");
        }

        return response()->json(['data' => $query->latest()->limit($limit)->get()]);
    }

    public function coursesShow(int $id): JsonResponse
    {
        $course = Course::find($id);
        if (! $course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return response()->json(['data' => $course]);
    }

    public function coursesEnroll(Request $request, int $id): JsonResponse
    {
        $course = Course::find($id);
        if (! $course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $existing = \App\Models\CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $id)
            ->first();
        if ($existing) {
            return response()->json(['message' => 'Already enrolled in this course'], 422);
        }

        $enrollment = \App\Models\CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $id,
        ]);

        return response()->json([
            'message' => 'Enrolled successfully',
            'data' => [
                'id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
                'user_id' => $enrollment->user_id,
                'enrolled_at' => optional($enrollment->created_at)?->toIso8601String(),
            ],
        ], 201);
    }

    public function featuredProviders(): JsonResponse
    {
        $providers = \Illuminate\Support\Facades\Cache::remember('scoop_featured_providers', 1800, function () {
            return TrainingProvider::query()
                ->where('is_featured', true)
                ->orderBy('name')
                ->limit(12)
                ->get(['id', 'name', 'subtitle', 'courses_available', 'tagline', 'is_featured']);
        });

        return response()->json(['data' => $providers]);
    }

    public function shareCard(Request $request): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $user = $request->user();
        $links = JobSeekerSocialLink::where('seeker_id', $seeker->seeker_id)
            ->get(['id', 'platform', 'url']);

        return response()->json([
            'data' => [
                'name' => trim(($seeker->first_name ?? '').' '.($seeker->last_name ?? '')) ?: $user->name,
                'email' => $user->email,
                'phone' => $seeker->phone ?? $user->phone,
                'location' => $seeker->location,
                'social_links' => $links,
                'vcard' => $this->buildShareVCard(
                    trim(($seeker->first_name ?? '').' '.($seeker->last_name ?? '')) ?: $user->name,
                    $user->email,
                    $seeker->phone ?? $user->phone,
                    $seeker->location,
                    $links->pluck('url')->filter()->values()->all()
                ),
                // Fallback image URL (frontend prefers generating QR locally)
                'qr_url' => null,
            ],
        ]);
    }

    /**
     * Build a vCard 3.0 payload for QR contact sharing.
     */
    private function buildShareVCard(
        string $name,
        ?string $email,
        ?string $phone,
        ?string $location,
        array $urls = []
    ): string {
        $escape = static function (?string $value): string {
            $value = str_replace(["\r", "\n"], '', (string) $value);

            return str_replace(['\\', ';', ',', ':'], ['\\\\', '\\;', '\\,', '\\:'], $value);
        };

        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'FN:'.$escape($name),
            'N:'.$escape($name).';;;;',
        ];

        if ($email) {
            $lines[] = 'EMAIL;TYPE=INTERNET:'.$escape($email);
        }
        if ($phone) {
            $lines[] = 'TEL;TYPE=CELL:'.$escape($phone);
        }
        if ($location) {
            $lines[] = 'ADR;TYPE=HOME:;;'.$escape($location).';;;;';
        }
        foreach ($urls as $url) {
            if ($url) {
                $lines[] = 'URL:'.$escape($url);
            }
        }
        $lines[] = 'NOTE:Shared via Scoop';
        $lines[] = 'END:VCARD';

        return implode("\n", $lines);
    }

    public function metaBanners(): JsonResponse
    {
        return response()->json([
            'data' => [
                [
                    'image_url' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1200',
                    'title' => 'Advertise with us',
                    'link' => '/employer/register',
                    'placement' => 'home_advertise',
                ],
            ],
        ]);
    }

    public function metaJobCategories(): JsonResponse
    {
        $data = \Illuminate\Support\Facades\Cache::remember('scoop_meta_job_categories', 1800, function () {
            $emojiMap = [
                'Hospitality' => '🍽️',
                'Tourism' => '🏝️',
                'Technology' => '💻',
                'IT' => '💻',
                'Education' => '🎓',
                'Healthcare' => '🏥',
                'Medical' => '🏥',
                'Construction' => '🏗️',
                'Administration' => '📄',
                'Administrative' => '📄',
                'Customer Service' => '🎧',
                'Transportation' => '📦',
                'Logistics' => '📦',
                'Government' => '🏛️',
                'Finance' => '💰',
                'Sales' => '🛒',
            ];

            return JobCategory::query()->orderBy('name')->get(['id', 'name', 'icon'])->map(function ($cat) use ($emojiMap) {
                $icon = $cat->icon;
                if (! $icon) {
                    foreach ($emojiMap as $needle => $emoji) {
                        if (stripos($cat->name, $needle) !== false) {
                            $icon = $emoji;
                            break;
                        }
                    }
                }

                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'icon' => $icon ?: '📁',
                ];
            })->values()->all();
        });

        return response()->json(['data' => $data]);
    }

    public function metaLocations(): JsonResponse
    {
        $locations = \Illuminate\Support\Facades\Cache::remember('scoop_meta_locations', 1800, function () {
            return JobAdvertisement::query()
                ->where('status', 'published')
                ->whereNotNull('location')
                ->distinct()
                ->orderBy('location')
                ->pluck('location')
                ->values()
                ->all();
        });

        return response()->json(['data' => $locations]);
    }

    public function metaJobTypes(): JsonResponse
    {
        return response()->json([
            'data' => [
                ['id' => 1, 'name' => 'Full-time'],
                ['id' => 2, 'name' => 'Part-time'],
                ['id' => 3, 'name' => 'Contract'],
                ['id' => 4, 'name' => 'Internship'],
                ['id' => 5, 'name' => 'Temporary'],
            ],
        ]);
    }

    public function metaEducationLevels(): JsonResponse
    {
        return response()->json([
            'data' => [
                'high_school', 'certificate_diploma', 'associate', 'bachelor',
                'master', 'doctorate', 'other', 'prefer_not_to_say',
            ],
        ]);
    }

    public function tenderClarify(Request $request, $id): JsonResponse
    {
        $tender = TenderAd::where('id', $id)->orWhere('slug', $id)->first();
        if (! $tender) {
            return response()->json(['message' => 'Tender not found'], 404);
        }

        $validator = Validator::make($request->all(), ['message' => 'required|string|max:5000']);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        DB::table('tender_clarifications')->insert([
            'tender_ad_id' => $tender->id,
            'user_id' => $request->user()?->id,
            'message' => $request->message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Clarification sent']);
    }

    public function tenderReport(Request $request, $id): JsonResponse
    {
        $tender = TenderAd::where('id', $id)->orWhere('slug', $id)->first();
        if (! $tender) {
            return response()->json(['message' => 'Tender not found'], 404);
        }

        DB::table('tender_reports')->insert([
            'tender_ad_id' => $tender->id,
            'user_id' => $request->user()?->id,
            'reason' => $request->input('reason'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Tender reported']);
    }

    public function tenderAttachmentDownload($id, $attachmentId)
    {
        $tender = TenderAd::where('id', $id)->orWhere('slug', $id)->first();
        if (! $tender) {
            return response()->json(['message' => 'Tender not found'], 404);
        }

        $attachments = collect($tender->attachments ?? [])->values();
        $attachment = $attachments->first(function ($a, $i) use ($attachmentId) {
            $arr = is_array($a) ? $a : (array) $a;
            $aid = (string) ($arr['id'] ?? ($i + 1));

            return $aid === (string) $attachmentId || ($arr['name'] ?? null) === $attachmentId;
        });

        if (! $attachment) {
            return response()->json(['message' => 'Attachment not found'], 404);
        }

        $arr = is_array($attachment) ? $attachment : (array) $attachment;
        $url = $arr['url'] ?? $arr['path'] ?? null;
        $filename = $arr['name'] ?? basename((string) $url) ?: 'attachment';
        if (! $url) {
            return response()->json(['message' => 'Attachment file URL missing'], 404);
        }

        // Remote file: redirect so the PHP worker is not blocked streaming large files
        if (str_starts_with((string) $url, 'http://') || str_starts_with((string) $url, 'https://')) {
            return redirect()->away($url);
        }

        $relative = ltrim(str_replace(['/storage/', 'storage/'], '', $url), '/');
        $candidates = [
            public_path($url),
            public_path(ltrim($url, '/')),
            storage_path('app/public/'.$relative),
            base_path(ltrim($url, '/')),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return response()->download($path, $filename);
            }
        }

        return response()->json([
            'message' => 'Attachment file is not available on this server',
            'url' => $url,
        ], 404);
    }

    public function tenderDocumentsZip($id)
    {
        $tender = TenderAd::where('id', $id)->orWhere('slug', $id)->first();
        if (! $tender) {
            return response()->json(['message' => 'Tender not found'], 404);
        }

        $attachments = collect($tender->attachments ?? [])->values();
        if ($attachments->isEmpty()) {
            return response()->json(['message' => 'No attachments available for this tender'], 404);
        }

        $tempDir = storage_path('app/temp/tender-'.$tender->id.'-'.uniqid());
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $filesForZip = [];
        $remoteFetched = 0;
        foreach ($attachments as $i => $attachment) {
            if ($remoteFetched >= 5) {
                break;
            }
            $arr = is_array($attachment) ? $attachment : (array) $attachment;
            $url = $arr['url'] ?? $arr['path'] ?? null;
            if (! $url) {
                continue;
            }

            $name = $arr['name'] ?? ('attachment-'.($i + 1));
            $name = preg_replace('/[<>:"\\\\\\/|?*]+/', '_', $name) ?: ('attachment-'.($i + 1));

            // Local disk path
            if (! str_starts_with((string) $url, 'http://') && ! str_starts_with((string) $url, 'https://')) {
                $relative = ltrim(str_replace(['/storage/', 'storage/'], '', $url), '/');
                foreach ([
                    public_path($url),
                    public_path(ltrim($url, '/')),
                    storage_path('app/public/'.$relative),
                ] as $path) {
                    if (is_file($path)) {
                        $filesForZip[] = ['path' => $path, 'name' => $name];
                        break;
                    }
                }
                continue;
            }

            // Remote URL — short timeout; cap remote file count so worker stays responsive
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(8)
                    ->withOptions(['verify' => false])
                    ->get($url);
                $remoteFetched++;
                if (! $response->successful()) {
                    continue;
                }
                $localPath = $tempDir.DIRECTORY_SEPARATOR.$name;
                if (is_file($localPath)) {
                    $localPath = $tempDir.DIRECTORY_SEPARATOR.($i + 1).'-'.$name;
                }
                file_put_contents($localPath, $response->body());
                $filesForZip[] = ['path' => $localPath, 'name' => basename($localPath)];
            } catch (\Throwable $e) {
                $remoteFetched++;
                \Log::warning('Tender zip remote fetch failed', [
                    'tender_id' => $tender->id,
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($filesForZip === []) {
            $this->deleteDirectory($tempDir);

            return response()->json([
                'message' => 'No attachment files available to zip.',
            ], 422);
        }

        $zipPath = storage_path('app/temp/tender-'.$tender->id.'-'.uniqid().'.zip');
        if (! is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0777, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->deleteDirectory($tempDir);

            return response()->json(['message' => 'Could not create zip archive'], 500);
        }

        foreach ($filesForZip as $file) {
            $zip->addFile($file['path'], $file['name']);
        }
        $zip->close();
        $this->deleteDirectory($tempDir);

        return response()->download($zipPath, 'tender-'.$tender->id.'-documents.zip')->deleteFileAfterSend(true);
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir.DIRECTORY_SEPARATOR.$item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    public function downloadDocument(Request $request, int $id)
    {
        $seeker = $this->seekerOrFail($request);
        $doc = $seeker->documents()->where('id', $id)->first();
        if (! $doc) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        if ($doc->file_url && str_starts_with($doc->file_url, 'http')) {
            return redirect()->away($doc->file_url);
        }

        return response()->json(['data' => ['url' => $doc->file_url ?? $doc->path]]);
    }

    public function uploadCertificationDocument(Request $request, int $id): JsonResponse
    {
        $seeker = $this->seekerOrFail($request);
        $cert = $seeker->certifications()->where('id', $id)->first();
        if (! $cert) {
            return response()->json(['message' => 'Certification not found'], 404);
        }

        if (! $request->hasFile('file')) {
            return response()->json(['message' => 'file is required'], 422);
        }

        // Store path reference; remote upload may fail in local — keep stub-friendly
        $path = $request->file('file')->store('certifications', 'public');
        $cert->forceFill(['certificate_file_path' => '/storage/'.$path])->save();

        return response()->json(['message' => 'Uploaded', 'data' => ['document_url' => '/storage/'.$path]]);
    }
}
