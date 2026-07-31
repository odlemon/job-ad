<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobAdvertisement;
use App\Models\JobCategory;
use App\Services\JobAdvertisementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin: manage single job ad (modal data, delete), view applicants, edit, update, toggle status.
 */
class AdminJobAdController extends Controller
{
    public function __construct(
        private JobAdvertisementService $jobService
    ) {
    }

    /**
     * Get data for the "Manage Job Ad" modal: job details, link to job post, applicants who shared, applicants who saved.
     */
    public function manage(int $id): JsonResponse
    {
        $job = JobAdvertisement::with(['company', 'savedJobs.jobSeeker', 'jobShares.jobSeeker'])->find($id);

        if (!$job) {
            return response()->json(['message' => 'Job ad not found'], 404);
        }

        $jobPostUrl = route('jobs.show', ['id' => $job->id]);

        $applicantsWhoShared = $job->jobShares->map(function ($share) {
            $seeker = $share->jobSeeker;
            $name = $seeker ? trim($seeker->first_name . ' ' . $seeker->last_name) : 'Unknown';
            $initials = $seeker ? strtoupper(substr($seeker->first_name ?? '', 0, 1) . substr($seeker->last_name ?? '', 0, 1)) : '?';

            return [
                'seeker_id' => $share->seeker_id,
                'name' => $name,
                'initials' => $initials ?: '?',
                'platform' => $share->platform ?? 'Unknown',
                'shared_at' => $share->shared_at?->format('Y-m-d'),
                'profile_url' => $seeker ? url("/job-seekers/{$seeker->seeker_id}") : null,
            ];
        })->values()->all();

        $applicantsWhoSaved = $job->savedJobs->map(function ($saved) {
            $seeker = $saved->jobSeeker;
            $name = $seeker ? trim($seeker->first_name . ' ' . $seeker->last_name) : 'Unknown';
            $initials = $seeker ? strtoupper(substr($seeker->first_name ?? '', 0, 1) . substr($seeker->last_name ?? '', 0, 1)) : '?';

            return [
                'seeker_id' => $saved->seeker_id,
                'name' => $name,
                'initials' => $initials ?: '?',
                'saved_at' => $saved->saved_at?->format('Y-m-d'),
                'profile_url' => $seeker ? url("/job-seekers/{$seeker->seeker_id}") : null,
            ];
        })->values()->all();

        return response()->json([
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'slug' => $job->slug,
                'company' => $job->company ? ['id' => $job->company->id, 'name' => $job->company->name] : null,
                'location' => $job->location,
                'job_post_url' => $jobPostUrl,
            ],
            'applicants_who_shared' => $applicantsWhoShared,
            'applicants_who_saved' => $applicantsWhoSaved,
            'stats' => [
                'applicants_who_shared_count' => count($applicantsWhoShared),
                'applicants_who_saved_count' => count($applicantsWhoSaved),
            ],
        ]);
    }

    /**
     * Delete a job ad (soft delete).
     */
    public function destroy(int $id): JsonResponse
    {
        $job = JobAdvertisement::find($id);

        if (!$job) {
            return response()->json(['message' => 'Job ad not found'], 404);
        }

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job ad deleted successfully.',
        ]);
    }

    /**
     * View applicants for a job (same as employer job applicants list).
     */
    public function applicants(int $id): JsonResponse
    {
        $job = JobAdvertisement::with([
            'company',
            'category',
            'campaigns' => fn ($q) => $q->orderByDesc('launched_at'),
            'applications' => fn ($q) => $q->with(['jobSeeker']),
        ])->find($id);

        if (!$job) {
            return response()->json(['message' => 'Job ad not found'], 404);
        }

        $applications = $job->applications->map(function ($app) {
            $seeker = $app->jobSeeker;
            return [
                'id' => $app->id,
                'first_name' => $app->first_name,
                'last_name' => $app->last_name,
                'email' => $app->email,
                'phone' => $app->phone,
                'status' => $app->status,
                'in_talent_pool' => (bool) $app->in_talent_pool,
                'invite_sent_at' => $app->invite_sent_at?->toIso8601String(),
                'invited' => (bool) $app->invite_sent_at,
                'cover_letter' => $app->cover_letter,
                'resume_path' => $app->resume_path,
                'created_at' => $app->created_at?->toIso8601String(),
                'job_seeker' => $seeker ? [
                    'seeker_id' => $seeker->seeker_id,
                    'first_name' => $seeker->first_name,
                    'last_name' => $seeker->last_name,
                    'profile_photo' => $seeker->profile_photo,
                ] : null,
            ];
        })->values()->all();

        $primaryCampaign = $job->campaigns->first();
        $viewsCount = $primaryCampaign ? (int) ($primaryCampaign->views_count ?? 0) : (int) ($job->views_count ?? 0);

        return response()->json([
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'company' => $job->company ? ['id' => $job->company->id, 'name' => $job->company->name] : null,
                'location' => $job->location,
            ],
            'applications' => $applications,
            'stats' => [
                'total' => count($applications),
                'shortlisted' => $job->applications->where('status', 'shortlisted')->count(),
                'selected' => $job->applications->where('status', 'hired')->count(),
                'rejected' => $job->applications->where('status', 'rejected')->count(),
            ],
            'views_count' => $viewsCount,
        ]);
    }

    /**
     * Get job data + categories for edit form (same as employer edit).
     */
    public function editForm(int $id): JsonResponse
    {
        $job = $this->jobService->getById($id);
        if (!$job) {
            return response()->json(['message' => 'Job ad not found'], 404);
        }
        $job->load(['company', 'category']);
        $categories = JobCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return response()->json([
            'job' => $job,
            'categories' => $categories,
        ]);
    }

    /**
     * Update job post (same validation as employer update).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $job = $this->jobService->getById($id);
        if (!$job) {
            return response()->json(['message' => 'Job ad not found'], 404);
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:job_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'employment_type' => 'nullable|string|max:255',
            'experience_level' => 'nullable|string|max:255',
            'salary_min' => 'nullable|string|max:255',
            'salary_max' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:3',
            'hide_salary' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'island' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'is_remote' => 'nullable|boolean',
            'work_environment' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,published,closed,archived',
        ]);

        $validated['hide_salary'] = $request->boolean('hide_salary');
        $this->jobService->update($job, $validated);

        $job = $this->jobService->getById($id);
        $job->load(['company', 'category', 'campaigns']);

        return response()->json([
            'message' => 'Job posting updated successfully.',
            'job' => $job,
        ]);
    }

    /**
     * Toggle job status (post listing / pause): publish or unpublish.
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $job = $this->jobService->getById($id);
        if (!$job) {
            return response()->json(['message' => 'Job ad not found'], 404);
        }

        if ($job->status === 'published') {
            $job->status = 'draft';
            $message = 'Job paused successfully';
        } else {
            $job->status = 'published';
            if (!$job->published_at) {
                $job->published_at = now();
            }
            $message = 'Job activated successfully';
        }
        $job->save();

        return response()->json([
            'message' => $message,
            'status' => $job->status,
        ]);
    }

    /**
     * Get share URL for the job post and optionally increment share count (uses first campaign if any).
     */
    public function share(Request $request, int $id): JsonResponse
    {
        $job = JobAdvertisement::with('campaigns')->find($id);
        if (!$job) {
            return response()->json(['message' => 'Job ad not found'], 404);
        }

        $campaign = $job->campaigns->sortByDesc('launched_at')->first();
        if ($campaign) {
            $campaign->increment('shares_count');
        }

        $shareUrl = url('/jobs/' . $job->id);

        return response()->json([
            'url' => $shareUrl,
            'message' => 'Share count updated.',
        ]);
    }
}
