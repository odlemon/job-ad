<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Services\JobAdvertisementService;
use App\Services\CompanyService;
use App\Services\JobCategoryService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerJobController extends Controller
{
    public function __construct(
        private JobAdvertisementService $jobService,
        private CompanyService $companyService,
        private JobCategoryService $categoryService,
        private NotificationService $notificationService
    ) {
    }

    /**
     * Display a listing of the employer's job postings.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        // Load jobs with application counts (client-side filter for real-time UX)
        $jobs = \App\Models\JobAdvertisement::with(['company', 'category', 'campaigns'])
            ->withCount('applications')
            ->where('company_id', $employer->company_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $rawCounts = \App\Models\JobAdvertisement::query()
            ->where('company_id', $employer->company_id)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $stats = [
            'all' => (int) $rawCounts->sum(),
            'active' => (int) ($rawCounts['published'] ?? 0),
            'paused' => (int) ($rawCounts['draft'] ?? 0),
            'draft' => (int) ($rawCounts['draft'] ?? 0),
            'closed' => (int) ($rawCounts['closed'] ?? 0),
            'archived' => (int) ($rawCounts['archived'] ?? 0),
        ];
        
        $categories = $this->categoryService->getAll();
        
        return view('employer.jobs.index', [
            'jobs' => $jobs,
            'stats' => $stats,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new job posting.
     */
    public function create()
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $company = $this->companyService->getById($employer->company_id);
        $categories = $this->categoryService->getAll();
        
        return view('employer.jobs.create', [
            'company' => $company,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created job posting.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return redirect()->back()
                ->with('error', 'Please set up your company profile first.');
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

        $validated['company_id'] = $employer->company_id;
        $validated['hide_salary'] = $request->has('hide_salary') ? true : false;
        
        if (!isset($validated['status'])) {
            $validated['status'] = 'draft';
        }

        $job = $this->jobService->create($validated);
        
        // Load relationships and counts for JSON response
        $job->load(['company', 'category']);
        $job->applications_count = 0;
        $job->views_count = $job->views_count ?? 0;

        $this->notificationService->notifyAdmins(
            'new_job_post',
            'New Job Post Created',
            $job->title . ' at ' . ($job->company->name ?? 'Company') . ' was just posted.',
            ['job_id' => $job->id, 'company_id' => $job->company_id]
        );

        // If request expects JSON (AJAX), return JSON and redirect to campaign page
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Job posting created successfully',
                'job' => $job,
                'redirect' => route('employer.campaigns.create', ['job' => $job->id]),
            ], 201);
        }

        return redirect()->route('employer.campaigns.create', ['job' => $job->id])
            ->with('success', 'Job posting created successfully. Create a campaign to boost visibility.');
    }

    /**
     * Display the specified job posting.
     */
    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            abort(404, 'Job posting not found');
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            abort(403, 'Unauthorized');
        }
        
        // Load relationships
        $job->load(['company', 'category']);
        $job->applications_count = $job->applications()->count();
        $job->views_count = $job->views_count ?? 0;
        
        // If request expects JSON (AJAX), return JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'job' => $job,
            ]);
        }
        
        return view('employer.jobs.show', [
            'job' => $job,
        ]);
    }

    /**
     * Show the form for editing the specified job posting.
     */
    public function edit(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            abort(404, 'Job posting not found');
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            abort(403, 'Unauthorized');
        }

        $categories = $this->categoryService->getAll();
        
        // If request expects JSON (AJAX), return JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'job' => $job,
                'categories' => $categories,
            ]);
        }
        
        return view('employer.jobs.edit', [
            'job' => $job,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified job posting.
     */
    public function update(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            abort(404, 'Job posting not found');
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            abort(403, 'Unauthorized');
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

        $validated['hide_salary'] = $request->has('hide_salary') ? true : false;

        $this->jobService->update($job, $validated);
        
        // Reload job with relationships for JSON response (table real-time update)
        $job = $this->jobService->getById($id);
        $job->load(['company', 'category', 'campaigns']);
        $job->applications_count = $job->applications()->count();
        $job->views_count = $job->views_count ?? 0;

        // If request expects JSON (AJAX), return JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Job posting updated successfully',
                'job' => $job,
            ]);
        }

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job posting updated successfully.');
    }

    /**
     * Remove the specified job posting.
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            return response()->json(['message' => 'Job posting not found'], 404);
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->jobService->delete($job);

        return response()->json(['message' => 'Job posting deleted successfully'], 200);
    }

    /**
     * Toggle job status (pause/resume).
     */
    public function toggleStatus(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            return response()->json(['message' => 'Job posting not found'], 404);
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Toggle between published (active) and draft (paused)
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
        ], 200);
    }

    /**
     * Job Statistics & Performance page for a single job.
     */
    public function statistics(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;

        if (!$employer || !$employer->company_id) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $job = \App\Models\JobAdvertisement::where('id', $id)
            ->where('company_id', $employer->company_id)
            ->with([
                'company',
                'campaigns' => fn ($q) => $q->orderByDesc('launched_at'),
                'applications' => fn ($q) => $q->with(['jobSeeker.experiences', 'jobSeeker.educations'])->orderByDesc('created_at')->limit(10),
            ])
            ->firstOrFail();

        $primaryCampaign = $job->campaigns->first();
        $isPromoted = $primaryCampaign && $primaryCampaign->status === 'active';

        $stats = [
            'views' => $primaryCampaign ? ($primaryCampaign->views_count ?? 0) : ($job->views_count ?? 0),
            'applications' => $job->applications()->count(),
            'shares' => $primaryCampaign ? ($primaryCampaign->shares_count ?? 0) : 0,
            'messages' => $primaryCampaign ? ($primaryCampaign->messages_count ?? 0) : 0,
            'saved' => $primaryCampaign ? ($primaryCampaign->saved_count ?? 0) : 0,
            'invitations_sent' => $primaryCampaign ? ($primaryCampaign->invitation_sent_count ?? 0) : 0,
        ];

        return view('employer.jobs.statistics', [
            'job' => $job,
            'stats' => $stats,
            'isPromoted' => $isPromoted,
            'applications' => $job->applications,
        ]);
    }
}
